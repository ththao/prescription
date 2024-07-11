<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patient extends My_Controller {
    public function __construct()
    {
        parent::__construct();

        $this->loadModel(array('patient_model', 'diagnostic_model', 'prescription_model', 'drug_model'));
        $this->load->helper('url');
        $this->load->library('pagination');
    }

    public function index($page=0)
	{
	    $param = array();
	    if (isset($_GET['patient-search'])) {
	        $param['name'] = $_GET['patient-search'];
	    }
	    if (isset($_GET['date']) && $_GET['date']) {
	        $param['date'] = date('d-m-Y', strtotime($_GET['date']));
	    }
	    if (isset($_GET['patient_id']) && $_GET['patient_id']) {
	        $param['patient_id'] = $_GET['patient_id'];
	    }
	    $count = $this->patient_model->count($param);
        $config = $this->pagination_config($count, site_url('patient/index'));
        $this->pagination->initialize($config);

        $data['page'] = $this->uri->segment(3);
        //call the model function to get the department data
        $data['patients'] = $this->patient_model->search($param, $config["per_page"], $data['page']);
        
        $data['pagination'] = $this->pagination->create_links();
        
        $this->render('patient/index', array('models' => $data, 'param' => $param, 'history' => 0));
	}

    public function view($id)
    {
        $diagnostic = $this->diagnostic_model->findOne(['id' => $id, 'removed' => 0, 'user_id' => $this->session->userdata('user_id')]);
        $patient = $this->patient_model->findOne(['id' => $diagnostic->patient_id, 'removed' => 0]);
        $prescription = $this->prescription_model->getList($diagnostic->id);
        
        $this->render('patient/view', array('patient' => $patient, 'diagnostic' => $diagnostic, 'prescription' => $prescription));
    }

    public function delete($id)
    {
        $diagnostic = $this->diagnostic_model->findOne(['id' => $id, 'removed' => 0, 'user_id' => $this->session->userdata('user_id')]);
        
        $this->prescription_model->deleteAll(array('diagnostic_id' => $diagnostic->id));
        $this->diagnostic_model->delete($diagnostic->id);
        
        redirect("/patient");
    }
    
    public function history()
    {
        if (isset($_POST['patient_id']) && $_POST['patient_id']) {
            $patients = $this->patient_model->search(['patient_id' => $_POST['patient_id']], null, null);
            if ($patients) {
                foreach ($patients as $patient) {
                    $prescription = $this->prescription_model->getList($patient->diagnostic_id);
                    
                    $pres_html = '';
                    foreach ($prescription as $drug) {
                        $pres_html .= ($pres_html ? '&#013;' : '') . (' - ' . $drug->drug_name . ' (' . $drug->quantity . ' ' . $drug->unit . ' - ngày ' . $drug->time_in_day . ' lần)');
                    }
                    $patient->prescription = $pres_html;
                }
            }
            $data['patients'] = $patients;
            
            $html = $this->load->view('patient/index', array('models' => $data, 'history' => 1), true);
            
            echo json_encode(['success' => 1, 'html' => $html]);
            exit();
        }
        
        echo json_encode(['success' => 0]);
        exit();
    }
}