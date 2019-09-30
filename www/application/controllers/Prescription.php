<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prescription extends My_Controller {
    public function __construct()
    {
        parent::__construct();

        $this->loadModel(array('patient_model', 'drug_model', 'prescription_model', 'diagnostic_model'));
    }

	public function index()
	{
        $drugs = $this->drug_model->findAll();
        $drug_names = array();
        foreach ($drugs as $drug) {
            $drug_names[] = $drug->name;
        }
        $js_name = json_encode($drug_names);
        
        if ($patient_id = $this->input->get('patient_id')) {
            $patient = $this->patient_model->findOne(array('id' => $patient_id));
            $diagnostic = $this->diagnostic_model->findOne(array('patient_id' => $patient->id));
            $prescriptions = $this->prescription_model->getList($diagnostic->id);
            
            $this->render('prescription/update', array(
                'drug_names' => $js_name,
                'patient' => $patient,
                'diagnostic' => $diagnostic,
                'prescriptions' => $prescriptions
            ));
        } else {
            $this->render('prescription/index', array('drug_names' => $js_name));
        }
	}

    public function printPrescription($id)
    {
        $this->layout('layout/print');
        $patient = $this->patient_model->findOne(array('id' => $id));
        $diagnostic = $this->diagnostic_model->findOne(array('patient_id' => $patient->id));
        $prescription = $this->prescription_model->findAll(array('diagnostic_id' => $diagnostic->id));

        $this->render('prescription/print_prescription', array('patient' => $patient, 'diagnostic' => $diagnostic, 'prescription' => $prescription));
    }

    public function bill($id)
    {
        $this->layout('layout/print');

        $patient = $this->patient_model->findOne(array('id' => $id));
        $diagnostic = $this->diagnostic_model->findOne(array('patient_id' => $patient->id));
        $prescription = $this->prescription_model->findAll(array('diagnostic_id' => $diagnostic->id));

        $this->render('prescription/bill', array('patient' => $patient, 'diagnostic' => $diagnostic, 'prescription' => $prescription));
    }

    public function save($id=null)
    {
        if (isset($_POST['patient']) && !empty($_POST['patient'])) {
            if (empty($_POST['patient']['name'])) {
                echo json_encode(array('error' => 'Vui lòng nhập tên.'));
                return;
            }
            if (empty($_POST['diagnostic']['diagnostic'])) {
                echo json_encode(array('error' => 'Vui lòng nhập chẩn đoán.'));
                return;
            }
            if (!isset($_POST['prescription']) || empty($_POST['prescription'])) {
                echo json_encode(array('error' => 'Vui lòng nhập tên thuốc và số lượng vào đơn thuốc'));
                return;
            }
            
            $this->db->trans_start();

            // Save new patient
            $patient = $_POST['patient'];
            $patient['date_created'] = date('Y-m-d H:i:s');
            if ($id) {
                $this->patient_model->update($id, $patient);
                $patient_id = $id;
            } else {
                $patient_id = $this->patient_model->save($patient);
                if (!$patient_id) {
                    echo json_encode(array('error' => 'Có lỗi. Vui lòng thử lại.'));
                    return;
                }
            }

            
            // Update diagnostic of patient
            $diag = $this->diagnostic_model->findOne(array('patient_id' => $patient_id));
            if ($diag) {
                $this->diagnostic_model->update($diag->id, array(
                    'diagnostic' => $_POST['diagnostic']['diagnostic'],
                    'note' => $patient['note']
                ));
                $diagnostic_id = $diag->id;
                
            } else {
                $diagnostic_id = $this->diagnostic_model->save(array(
                    'patient_id' => $patient_id,
                    'diagnostic' => $_POST['diagnostic']['diagnostic'],
                    'note' => $patient['note'],
                    'date_created' => date('Y-m-d H:i:s')
                ));
    
                if (!$diagnostic_id) {
                    echo json_encode(array('error' => 'Có lỗi. Vui lòng thử lại.'));
                    return;
                }
            }

            // Update prescription of patient
            $this->prescription_model->deleteAll(array('diagnostic_id' => $diagnostic_id));

            // Save prescription of patient
            for ($i = 1; $i <= $_POST['index_row']; $i++) {
                if (!isset($_POST['prescription'][$i])) {
                    break;
                }
                
                $prescription =  $_POST['prescription'][$i];
                if ($prescription['drug-name'] && $prescription['quantity'] && $prescription['time_in_day'] && $prescription['unit_in_time']) {
                    $drug = $this->drug_model->findOne(array('LOWER(name)' => strtolower($prescription['drug-name'])));
                    if ($drug) {
                        $prescription['drug_id'] = $drug->id;
                        $prescription['in_unit_price'] = $drug->in_price;
                        $prescription['unit_price'] = $drug->price;
                        $prescription['drug_name'] = $drug->name;
                        $prescription['diagnostic_id'] = $diagnostic_id;
                        $prescription['date_created'] = date('Y-m-d H:i:s');
                        unset($prescription['drug-name']);
                        $drug_id = $this->prescription_model->save($prescription);

                        if (!$drug_id) {
                            echo json_encode(array('error' => 'Có lỗi. Vui lòng thử lại.'));
                            return;
                        }
                    } else {
                        echo json_encode(array('error' => 'Loại thuốc ' . $prescription['drug-name'] . ' không có trong danh sách thuốc. Vui lòng thêm vào danh sách.'));
                        return;
                    }
                }
            }

            $this->db->trans_complete();
            echo json_encode(array('success' => 'Đã lưu thành công.', 'url' => '/prescription/index?patient_id=' . $patient_id));
            return;
        }
        
        echo json_encode(array('error' => 'Chưa có dữ liệu'));
    }
}
