<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Remember extends My_Controller {
    public function __construct()
    {
        parent::__construct();
        
	    if (!$this->checkLoggedIn()) {
	    	if ($this->input->is_ajax_request()) {
	    		echo json_encode(array('status' => 0));
	    	} else {
            	redirect('/auth/login');
	    	}
        }
        
        if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) {
	    	if ($this->input->is_ajax_request()) {
	    		echo json_encode(array('status' => 0));
	    	} else {
            	redirect('/auth/admin');
	    	}
        }
        
        if ($this->session->userdata('user_id') != 1) {
	    	if ($this->input->is_ajax_request()) {
	    		echo json_encode(array('status' => 0));
	    	} else {
        		redirect('/auth/manage');
	    	}
        }

        $this->loadModel(array('remember_user_model'));
    }
    
    public function index()
    {
        $items = $this->remember_user_model->getRememberUsers();
        $this->render('remember/index', array('items' => $items));
    }
    
    public function delete($user_id, $hash)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('remember_hash', $hash);
        $this->db->delete('remember_users');
        
        redirect("/remember");
    }
}