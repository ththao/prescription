<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prescription extends My_Controller {
    public function __construct()
    {
        parent::__construct();
        
        if (!$this->checkLoggedIn()) {
            redirect('/auth/login');
        }
        
        $this->loadModel(array('patient_model', 'drug_model', 'prescription_model', 'service_model', 'order_model', 'diagnostic_model', 'package_model', 'packageorder_model', 'packageprescription_model'));
    }

	public function index()
	{
	    try {
	        $drugs = $this->drug_model->findAll(['user_id' => $this->session->userdata('user_id'), 'removed' => 0]);
            $drug_names = [];
            if ($drugs) {
                foreach ($drugs as $drug) {
                    $drug_names[] = ['id' => $drug->id, 'value' => $drug->name, 'label' => $drug->name, 'unit' => $drug->unit];
                }
            }
            $js_drug_names = json_encode($drug_names);
            
            if (SERVICES == 'ON') {
                $services = $this->service_model->findAll(['user_id' => $this->session->userdata('user_id'), 'removed' => 0]);
                $service_names = [];
                if ($services) {
                    foreach ($services as $service) {
                        $service_names[] = ['id' => $service->id, 'value' => $service->service_name, 'label' => $service->service_name];
                    }
                }
                $js_service_names = json_encode($service_names);
                
                $packages = $this->package_model->search("", null, null);
            } else {
                $js_service_names = '';
                $packages = [];
            }
            
            $query = $this->db->select('id, name, address, dob, gender, phone')->from('patient')->get();
            $patients = $query->result();
            $patient_names = [];
            $patient_phones = [];
            if ($patients) {
                foreach ($patients as $patient) {
                    $patient_names[] = [
                        'id' => $patient->id,
                        'value' => $patient->name,
                        'label' => $patient->name . ($patient->address ? (' (' . $patient->address . ')') : ''),
                        'address' => $patient->address,
                        'dob' => $patient->dob,
                        'gender' => $patient->gender,
                        'phone' => $patient->phone
                    ];
                    
                    $patient_phones[] = [
                        'id' => $patient->id,
                        'value' => $patient->phone,
                        'label' => $patient->phone,
                        'address' => $patient->address,
                        'dob' => $patient->dob,
                        'gender' => $patient->gender,
                        'name' => $patient->name
                    ];
                }
            }
            $js_patient_names = json_encode($patient_names);
            $js_patient_phones = json_encode($patient_phones);
            
            $query = $this->db->select('id, diagnostic')->from('diagnostic_template')->where('removed', 0)->get();
            $templates = $query->result();
            $template_names = array();
            if ($templates) {
                foreach ($templates as $template) {
                    $template_names[] = ['id' => $template->id, 'value' => $template->diagnostic];
                }
            }
            $js_template_names = json_encode($template_names);
            
            if ($diagnostic_id = $this->input->get('diagnostic_id')) {
                $diagnostic = $this->diagnostic_model->findOne(['id' => $diagnostic_id, 'removed' => 0, 'user_id' => $this->session->userdata('user_id')]);
                $patient = $this->patient_model->findOne(['id' => $diagnostic->patient_id, 'removed' => 0]);
                $prescriptions = $this->prescription_model->getList($diagnostic->id);
                if (SERVICES == 'ON') {
                    $orders = $this->order_model->getList($diagnostic->id);
                } else {
                    $orders = null;
                }
                
                $this->render('prescription/index', [
                    'drug_names' => $js_drug_names,
                    'patient_names' => $js_patient_names,
                    'patient_phones' => $js_patient_phones,
                    'template_names' => $js_template_names,
                    'service_names' => $js_service_names,
                    'packages' => $packages,
                    'patient' => $patient,
                    'diagnostic' => $diagnostic,
                    'prescriptions' => $prescriptions,
                    'orders' => $orders
                ]);
            } else {
                if (isset($_GET['patient_id']) && $_GET['patient_id']) {
                    $patient = $this->patient_model->findOne(['id' => $_GET['patient_id'], 'removed' => 0]);
                } else {
                    $patient = null;
                }
                $this->render('prescription/index', [
                    'patient' => $patient,
                    'drug_names' => $js_drug_names,
                    'service_names' => $js_service_names,
                    'packages' => $packages,
                    'patient_names' => $js_patient_names,
                    'patient_phones' => $js_patient_phones,
                    'template_names' => $js_template_names
                ]);
            }
	    } catch (Exception $e) {
	        redirect('/migration/index', 'refresh');
	    }
	}

    public function printPrescription($id)
    {
        $this->layout('layout/print');
        $diagnostic = $this->diagnostic_model->findOne(['id' => $id, 'removed' => 0, 'user_id' => $this->session->userdata('user_id')]);
        $patient = $this->patient_model->findOne(['id' => $diagnostic->patient_id, 'removed' => 0]);
        $prescription = $this->prescription_model->getList($diagnostic->id);

        $this->render('prescription/print_prescription', array('patient' => $patient, 'diagnostic' => $diagnostic, 'prescription' => $prescription));
    }

    public function bill($id)
    {
        $this->layout('layout/print');
        
        $diagnostic = $this->diagnostic_model->findOne(['id' => $id, 'removed' => 0, 'user_id' => $this->session->userdata('user_id')]);
        $patient = $this->patient_model->findOne(['id' => $diagnostic->patient_id, 'removed' => 0]);
        $prescription = $this->prescription_model->getList($diagnostic->id);
        if (SERVICES == 'ON') {
            $orders = $this->order_model->getList($diagnostic->id);
        } else {
            $orders = [];
        }

        $this->render('prescription/bill', array('patient' => $patient, 'diagnostic' => $diagnostic, 'prescription' => $prescription, 'orders' => $orders));
    }
    
    public function suggest()
    {
        if (isset($_POST['diagnostic_template_id']) && $_POST['diagnostic_template_id']) {
            $this->db->distinct()->select('drug.id, drug.name, drug.unit, diagnostic_template_prescription.most_used');
            $this->db->from('diagnostic_template_prescription');
            $this->db->join('drug', 'LOWER(diagnostic_template_prescription.drug_name) = LOWER(drug.name) AND drug.removed = 0 AND drug.user_id = ' . $this->session->userdata('user_id'), 'INNER');
            $this->db->where('diagnostic_template_prescription.diagnostic_template_id', $_POST['diagnostic_template_id']);
            $this->db->order_by('drug.name');
            $query = $this->db->get();
            
            $drugs = $query->result();
            
            $html = $this->load->view('prescription/suggested_drugs', ['drugs' => $drugs], true);
            
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
                $patient = $this->patient_model->findOne(['id' => $_POST['patient']['id'], 'removed' => 0]);
            }
            unset($_POST['patient']['id']);
            
            if (isset($patient) && $patient) {
                $this->patient_model->update($patient->id, $_POST['patient']);
                $patient_id = $patient->id;
            } else {
                $_POST['patient']['date_created'] = time();
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
            $diag = $this->diagnostic_model->findOne(['id' => $id, 'user_id' => $this->session->userdata('user_id')]);
            if ($diag) {
                $this->diagnostic_model->update($diag->id, array(
                    'patient_id' => $patient_id,
                    'diagnostic' => $_POST['diagnostic']['diagnostic'],
                    'diagnostic_template_id' => $diagnostic_template_id,
                    'note' => $_POST['diagnostic']['note'],
                    'date_updated' => time(),
                    'removed' => 0
                ));
                $diagnostic_id = $diag->id;
                
            } else {
                $diagnostic_id = $this->diagnostic_model->save(array(
                    'user_id' => $this->session->userdata('user_id'),
                    'patient_id' => $patient_id,
                    'diagnostic' => $_POST['diagnostic']['diagnostic'],
                    'diagnostic_template_id' => $diagnostic_template_id,
                    'note' => $_POST['diagnostic']['note'],
                    'date_created' => time()
                ));
                
                if (!$diagnostic_id) {
                    echo json_encode(array('error' => 'Có lỗi. Vui lòng thử lại.'));
                    return;
                }
            }
            
            if (SERVICES == 'ON') {
                // Save orders of patient
                $order_ids = [];
                for ($i = 1; $i <= $_POST['service_index_row']; $i++) {
                    if (!isset($_POST['order'][$i]) || !isset($_POST['order'][$i]['service_id']) || !$_POST['order'][$i]['service_id']) {
                        continue;
                    }
                    
                    $service = $this->service_model->findOne(['id' => $_POST['order'][$i]['service_id'], 'removed' => 0, 'user_id' => $this->session->userdata('user_id')]);
                    
                    $pres = null;
                    if (isset($_POST['order'][$i]['id']) && $_POST['order'][$i]['id']) {
                        $pres = $this->order_model->findOne(['id' => $_POST['order'][$i]['id'], 'user_id' => $this->session->userdata('user_id')]);
                    }
                    unset($_POST['order'][$i]['id']);
                    
                    $order['service_id'] = $_POST['order'][$i]['service_id'];
                    $order['price'] = $service->price;
                    $order['quantity'] = $_POST['order'][$i]['quantity'] ? $_POST['order'][$i]['quantity'] : 1;
                    $order['notes'] = $_POST['order'][$i]['notes'];
                    $order['diagnostic_id'] = $diagnostic_id;
                    $order['removed'] = 0;
                    
                    if ($pres) {
                        $order['date_updated'] = time();
                        $this->db->where('id', $pres->id)->update('orders', $order);
                        $order_ids[] = $pres->id;
                    } else {
                        $order['user_id'] = $this->session->userdata('user_id');
                        $order['date_created'] = time();
                        $order_ids[] = $this->order_model->save($order);
                    }
                }
                
                if ($order_ids) {
                    $this->db->where('id NOT IN (' . implode(',', $order_ids) . ')', null)->where('user_id', $this->session->userdata('user_id'))->where('diagnostic_id', $diagnostic_id)->delete('orders');
                }
            }
            
            // Save prescription of patient
            $ids = [];
            for ($i = 1; $i <= $_POST['index_row']; $i++) {
                if (!isset($_POST['prescription'][$i])) {
                    continue;
                }
                
                $prescription =  $_POST['prescription'][$i];
                if (!isset($prescription['quantity']) || !$prescription['quantity']) {
                    $prescription['quantity'] = 1;
                    $prescription['time_in_day'] = 1;
                    $prescription['unit_in_time'] = 1;
                }
                
                $prescription['time_in_day'] = (!isset($prescription['time_in_day']) || !$prescription['time_in_day']) ? 1 : $prescription['time_in_day'];
                $prescription['unit_in_time'] = (!isset($prescription['unit_in_time']) || !$prescription['unit_in_time']) ? 1 : $prescription['unit_in_time'];
                
                if ($prescription['drug_name'] && $prescription['quantity'] && $prescription['time_in_day'] && $prescription['unit_in_time']) {
                    $drug = $this->drug_model->findOne(['LOWER(name)' => strtolower($prescription['drug_name']), 'removed' => 0, 'user_id' => $this->session->userdata('user_id')]);
                    if ($drug) {
                        $pres = null;
                        if (isset($prescription['id']) && $prescription['id']) {
                            $pres = $this->prescription_model->findOne(['id' => $prescription['id'], 'user_id' => $this->session->userdata('user_id')]);
                        }
                        unset($prescription['id']);
                        
                        $prescription['drug_id'] = $drug->id;
                        $prescription['in_unit_price'] = $drug->in_price;
                        $prescription['unit_price'] = $drug->price;
                        $prescription['drug_name'] = $drug->name;
                        $prescription['diagnostic_id'] = $diagnostic_id;
                        
                        if ($pres) {
                            $prescription['removed'] = 0;
                            $prescription['date_updated'] = time();
                            $this->db->where('id', $pres->id)->update('prescription', $prescription);
                            $ids[] = $pres->id;
                        } else {
                            $prescription['user_id'] = $this->session->userdata('user_id');
                            $prescription['date_created'] = time();
                            $ids[] = $this->prescription_model->save($prescription);
                        }
                    } else {
                        echo json_encode(array('error' => 'Loại thuốc ' . $prescription['drug_name'] . ' không có trong danh sách thuốc. Vui lòng thêm vào danh sách.'));
                        exit;
                    }
                }
            }
            
            if ($ids) {
                $this->db->where('id NOT IN (' . implode(',', $ids) . ')', null)->where('user_id', $this->session->userdata('user_id'))->where('diagnostic_id', $diagnostic_id)->delete('prescription');
            }
            
            $this->prescription_by_diagnostic($diagnostic_template_id, 1);

            $this->db->trans_complete();
            echo json_encode(array('success' => 'Đã lưu thành công.', 'url' => '/prescription/index?diagnostic_id=' . $diagnostic_id));
            exit;
        }
        
        echo json_encode(array('error' => 'Chưa có dữ liệu'));
    }
    
    public function usePrescription()
    {
        if (isset($_POST['diagnostic_id']) && $_POST['diagnostic_id']) {
            $diagnostic = $this->diagnostic_model->findOne(['id' => $_POST['diagnostic_id'], 'user_id' => $this->session->userdata('user_id'), 'removed' => 0]);
            $prescription = $this->prescription_model->getList($diagnostic->id);
            
            $drugs = [];
            foreach ($prescription as $drug) {
                $drugs[] = ['drug_name' => $drug->drug_name, 'quantity' => $drug->quantity, 'time_in_day' => $drug->time_in_day, 'unit_in_time' => $drug->unit_in_time, 'notes' => $drug->notes, 'unit' => $drug->unit];
            }
            
            $services = [];
            if (SERVICES == 'ON') {
                $orders = $this->order_model->getList($diagnostic->id);
                
                if ($orders) {
                    foreach ($orders as $order) {
                        $services[] = ['service_id' => $order->service_id, 'service_name' => $order->service_name, 'quantity' => $order->quantity, 'notes' => $order->notes];
                    }
                }
            }
            
            echo json_encode(['success' => 1, 'diagnostic' => $diagnostic->diagnostic, 'notes' => $diagnostic->note, 'drugs' => $drugs, 'services' => $services]);
            exit();
        }
        
        echo json_encode(['success' => 0]);
        exit();
    }
    
    public function usePackage()
    {
        if (isset($_POST['package_id']) && $_POST['package_id']) {
            $prescription = $this->packageprescription_model->getList($_POST['package_id']);
            
            $drugs = [];
            foreach ($prescription as $drug) {
                $drugs[] = ['drug_name' => $drug->drug_name, 'quantity' => $drug->quantity, 'time_in_day' => $drug->time_in_day, 'unit_in_time' => $drug->unit_in_time, 'notes' => $drug->notes, 'unit' => $drug->unit];
            }
            
            $services = [];
            if (SERVICES == 'ON') {
                $orders = $this->packageorder_model->getList($_POST['package_id']);
                
                if ($orders) {
                    foreach ($orders as $order) {
                        $services[] = ['service_id' => $order->service_id, 'service_name' => $order->service_name, 'quantity' => $order->quantity, 'notes' => $order->notes];
                    }
                }
            }
            
            echo json_encode(['success' => 1, 'drugs' => $drugs, 'services' => $services]);
            exit();
        }
        
        echo json_encode(['success' => 0]);
        exit();
    }
}
