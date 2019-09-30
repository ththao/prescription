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
	    $count = $this->patient_model->count(array());
        $config = $this->pagination_config($count, site_url('patient/index'), 3);

        $this->pagination->initialize($config);
        $data['page'] = $page;
        //call the model function to get the department data
        $data['patients'] = $this->patient_model->search(array(), $config["per_page"], $data['page']);

        $data['pagination'] = $this->pagination->create_links();
        
        $this->render('patient/index', array('models' => $data));
	}

	public function search()
	{
        // get search string
	    $param = array();
	    if (isset($_GET['patient-search'])) {
	        $param[] = $_GET['patient-search'];
	    }
	    if (isset($_GET['date']) && $_GET['date']) {
	        $param[] = date('d-m-Y', strtotime($_GET['date']));
	    }
	    $count = $this->patient_model->count($param);
        
        $config = $this->pagination_config($count, site_url('patient/search'));
        $this->pagination->initialize($config);

        $data['page'] = $this->uri->segment(3);
        //call the model function to get the department data
        $data['patients'] = $this->patient_model->search($param, $config["per_page"], $data['page']);

        $data['pagination'] = $this->pagination->create_links();

        $this->render('patient/index', array('models' => $data, 'param' => $param));
    }

    public function view($id)
    {
        $patient = $this->patient_model->findOne(array('id' => $id));
        $diagnostic = $this->diagnostic_model->findOne(array('patient_id' => $patient->id));
        $prescription = $this->prescription_model->findAll(array('diagnostic_id' => $diagnostic->id));

        $this->render('patient/view', array('patient' => $patient, 'diagnostic' => $diagnostic, 'prescription' => $prescription));
    }

    public function create()
    {
        if (isset($_POST) && !empty($_POST['name']) && !empty($_POST['dob']) && !empty($_POST['gender'])) {
            $patient = $this->patient_model->findOne(array('name' => $_POST['name'], 'dob' => $_POST['dob'], 'gender' => $_POST['gender']));
            if (!$patient) {
                $data['name'] = $_POST['name'];
                $data['dob'] = $_POST['dob'];
                $data['gender'] = $_POST['gender'];
                $data['address'] = $_POST['address'];
                $data['phone'] = $_POST['phone'];
                $data['note'] = $_POST['note'];
                $data['date_created'] = date('Y-m-d H:i:s');
                $this->patient_model->save($data);
                echo json_encode(array('success' => 'Patient created success'));
                return;
            } else {
                echo json_encode(array('error' => 'Bệnh nhân này đã tồn tại trong danh sách'));
                return;
            }
        }
        echo json_encode(array('error' => 'Vui lòng điền đầy đủ thông tin'));
    }

    public function update($id)
    {
        $data = $_POST;
        if (isset($_POST) && !empty($_POST['name']) && !empty($_POST['dob']) && !empty($_POST['gender'])) {
            $patient = $this->patient_model->findOne(array('id' => $id));
            if ($patient) {
                $data['date_updated'] = date('Y-m-d H:i:s');
                $this->patient_model->update($id, $data);

                echo json_encode(array('success' => 'Patient updated success'));
                return;
            }
        }
    }

    public function delete($id)
    {
        $patient = $this->patient_model->findOne(array('id' => $id));
        if ($patient) {
            $diagnostic = $this->diagnostic_model->findOne(array('patient_id' => $patient->id));
            
            $this->prescription_model->deleteAll(array('diagnostic_id' => $diagnostic->id));
            $this->diagnostic_model->delete($diagnostic->id);
            $this->patient_model->delete($id);
        }
        
        redirect("/patient");
    }
}