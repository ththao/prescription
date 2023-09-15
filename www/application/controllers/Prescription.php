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
	    try {
            $drugs = $this->drug_model->findAll();
            $drug_names = array();
            if ($drugs) {
                foreach ($drugs as $drug) {
                    $drug_names[] = $drug->name;
                }
            }
            $js_drug_names = json_encode($drug_names);
            
            $query = $this->db->select('id, name, address, dob, gender, phone')->from('patient')->get();
            $patients = $query->result();
            $patient_names = array();
            if ($patients) {
                foreach ($patients as $patient) {
                    $patient_names[] = [
                        'id' => $patient->id,
                        'value' => $patient->name,
                        'label' => $patient->name . ' (' . $patient->address . ')',
                        'address' => $patient->address,
                        'dob' => $patient->dob,
                        'gender' => $patient->gender,
                        'phone' => $patient->phone
                    ];
                }
            }
            $js_patient_names = json_encode($patient_names);
            
            $query = $this->db->select('id, diagnostic')->from('diagnostic_template')->get();
            $templates = $query->result();
            $template_names = array();
            if ($templates) {
                foreach ($templates as $template) {
                    $template_names[] = ['id' => $template->id, 'value' => $template->diagnostic];
                }
            }
            $js_template_names = json_encode($template_names);
            
            if ($diagnostic_id = $this->input->get('diagnostic_id')) {
                $diagnostic = $this->diagnostic_model->findOne(array('id' => $diagnostic_id));
                $patient = $this->patient_model->findOne(array('id' => $diagnostic->patient_id));
                $prescriptions = $this->prescription_model->getList($diagnostic->id);
                
                $this->render('prescription/update', array(
                    'drug_names' => $js_drug_names,
                    'patient_names' => $js_patient_names,
                    'template_names' => $js_template_names,
                    'patient' => $patient,
                    'diagnostic' => $diagnostic,
                    'prescriptions' => $prescriptions
                ));
            } else {
                if (isset($_GET['patient_id']) && $_GET['patient_id']) {
                    $patient = $this->patient_model->findOne(array('id' => $_GET['patient_id']));
                } else {
                    $patient = null;
                }
                $this->render('prescription/update', array(
                    'patient' => $patient,
                    'drug_names' => $js_drug_names,
                    'patient_names' => $js_patient_names,
                    'template_names' => $js_template_names
                ));
            }
	    } catch (Exception $e) {
	        redirect('/migration/index', 'refresh');
	    }
	}

    public function printPrescription($id)
    {
        $this->layout('layout/print');
        $diagnostic = $this->diagnostic_model->findOne(array('id' => $id));
        $patient = $this->patient_model->findOne(array('id' => $diagnostic->patient_id));
        $prescription = $this->prescription_model->findAll(array('diagnostic_id' => $diagnostic->id));

        $this->render('prescription/print_prescription', array('patient' => $patient, 'diagnostic' => $diagnostic, 'prescription' => $prescription));
    }

    public function bill($id)
    {
        $this->layout('layout/print');
        
        $diagnostic = $this->diagnostic_model->findOne(array('id' => $id));
        $patient = $this->patient_model->findOne(array('id' => $diagnostic->patient_id));
        $prescription = $this->prescription_model->findAll(array('diagnostic_id' => $diagnostic->id));

        $this->render('prescription/bill', array('patient' => $patient, 'diagnostic' => $diagnostic, 'prescription' => $prescription));
    }
    
    public function suggest()
    {
        if (isset($_POST['diagnostic_template_id']) && $_POST['diagnostic_template_id']) {
            $this->db->distinct()->select('drug.id, drug.name, diagnostic_template_prescription.most_used');
            $this->db->from('diagnostic_template_prescription');
            $this->db->join('drug', 'diagnostic_template_prescription.drug_id = drug.id', 'INNER');
            $this->db->where('diagnostic_template_prescription.diagnostic_template_id', $_POST['diagnostic_template_id']);
            $this->db->order_by('drug.name');
            $query = $this->db->get();
            
            $drugs = $query->result();
            
            $html = $this->load->view('prescription/drugs', ['drugs' => $drugs], true);
            
            echo json_encode(['success' => 1, 'html' => $html]);
            exit();
        }
        
        echo json_encode(['success' => 0]);
        exit();
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
            if (isset($_POST['patient']) && isset($_POST['patient']['id']) && $_POST['patient']['id']) {
                $patient = $this->patient_model->findOne(array('id' => $_POST['patient']['id']));
            }
            unset($_POST['patient']['id']);
            
            if (isset($patient) && $patient) {
                $this->patient_model->update($patient->id, $_POST['patient']);
                $patient_id = $patient->id;
            } else {
                $_POST['patient']['date_created'] = date('Y-m-d H:i:s');
                $patient_id = $this->patient_model->save($_POST['patient']);
                if (!$patient_id) {
                    echo json_encode(array('error' => 'Có lỗi. Vui lòng thử lại.'));
                    return;
                }
            }
            
            if (isset($_POST['diagnostic']['diagnostic_template_id']) && $_POST['diagnostic']['diagnostic_template_id']) {
                $diagnostic_template_id = $_POST['diagnostic']['diagnostic_template_id'];
            } else {
                $query = $this->db->select('id, diagnostic')->from('diagnostic_template')->where('LOWER(diagnostic)', strtolower($_POST['diagnostic']['diagnostic']))->get();
                $diagnostic_template = $query->row();
                
                if ($diagnostic_template) {
                    $diagnostic_template_id = $diagnostic_template->id;
                } else {
                    $this->db->insert('diagnostic_template', ['diagnostic' => $_POST['diagnostic']['diagnostic']]);
                    $diagnostic_template_id = $this->db->insert_id();
                }
            }
            
            // Update diagnostic of patient
            $diag = $this->diagnostic_model->findOne(array('id' => $id));
            if ($diag) {
                $this->diagnostic_model->update($diag->id, array(
                    'patient_id' => $patient_id,
                    'diagnostic' => $_POST['diagnostic']['diagnostic'],
                    'diagnostic_template_id' => $diagnostic_template_id,
                    'note' => $_POST['diagnostic']['note']
                ));
                $diagnostic_id = $diag->id;
                
            } else {
                $diagnostic_id = $this->diagnostic_model->save(array(
                    'patient_id' => $patient_id,
                    'diagnostic' => $_POST['diagnostic']['diagnostic'],
                    'diagnostic_template_id' => $diagnostic_template_id,
                    'note' => $_POST['diagnostic']['note'],
                    'date_created' => date('Y-m-d H:i:s')
                ));
                
                if (!$diagnostic_id) {
                    echo json_encode(array('error' => 'Có lỗi. Vui lòng thử lại.'));
                    return;
                }
            }
            
            // Save prescription of patient
            $ids = [];
            for ($i = 1; $i <= $_POST['index_row']; $i++) {
                if (!isset($_POST['prescription'][$i])) {
                    continue;
                }
                
                $prescription =  $_POST['prescription'][$i];
                if ($prescription['drug_name'] && $prescription['quantity'] && $prescription['time_in_day'] && $prescription['unit_in_time']) {
                    $drug = $this->drug_model->findOne(array('LOWER(name)' => strtolower($prescription['drug_name'])));
                    if ($drug) {
                        $pres = $this->prescription_model->findOne(array('id' => $prescription['id']));
                        
                        $prescription['drug_id'] = $drug->id;
                        $prescription['in_unit_price'] = $drug->in_price;
                        $prescription['unit_price'] = $drug->price;
                        $prescription['drug_name'] = $drug->name;
                        $prescription['diagnostic_id'] = $diagnostic_id;
                        $prescription['date_created'] = date('Y-m-d H:i:s');
                        
                        if ($pres) {
                            $this->db->where('id', $pres->id)->update('prescription', $prescription);
                            $ids[] = $pres->id;
                        } else {
                            unset($prescription['id']);
                            $ids[] = $this->prescription_model->save($prescription);
                        }
                    } else {
                        echo json_encode(array('error' => 'Loại thuốc ' . $prescription['drug_name'] . ' không có trong danh sách thuốc. Vui lòng thêm vào danh sách.'));
                        exit;
                    }
                }
            }
            $this->db->where('id NOT IN (' . implode(',', $ids) . ')', null)->delete('prescription');
            
            $this->prescription_by_diagnostic($diagnostic_template_id, 1);

            $this->db->trans_complete();
            echo json_encode(array('success' => 'Đã lưu thành công.', 'url' => '/prescription/index?diagnostic_id=' . $diagnostic_id));
            exit;
        }
        
        echo json_encode(array('error' => 'Chưa có dữ liệu'));
    }
}
