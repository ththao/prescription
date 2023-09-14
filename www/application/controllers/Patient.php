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
	        $param[] = $_GET['patient-search'];
	    }
	    if (isset($_GET['date']) && $_GET['date']) {
	        $param[] = date('d-m-Y', strtotime($_GET['date']));
	    }
	    $count = $this->patient_model->count($param);
        $config = $this->pagination_config($count, site_url('patient/index'));
        $this->pagination->initialize($config);

        $data['page'] = $this->uri->segment(3);
        //call the model function to get the department data
        $data['patients'] = $this->patient_model->search($param, $config["per_page"], $data['page']);
        
        $data['pagination'] = $this->pagination->create_links();
        
        $this->render('patient/index', array('models' => $data, 'param' => $param));
	}

    public function view($id)
    {
        $diagnostic = $this->diagnostic_model->findOne(array('id' => $id));
        $patient = $this->patient_model->findOne(array('id' => $diagnostic->patient_id));
        $prescription = $this->prescription_model->findAll(array('diagnostic_id' => $diagnostic->id));

        $this->render('patient/view', array('patient' => $patient, 'diagnostic' => $diagnostic, 'prescription' => $prescription));
    }

    public function delete($id)
    {
        $diagnostic = $this->diagnostic_model->findOne(array('id' => $id));
        
        $this->prescription_model->deleteAll(array('diagnostic_id' => $diagnostic->id));
        $this->diagnostic_model->delete($diagnostic->id);
        
        redirect("/patient");
    }
}