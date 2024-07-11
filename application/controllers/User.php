<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends My_Controller {
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
        
        if ($this->session->userdata('user_id') != 1 && $this->router->method != 'settings') {
	    	if ($this->input->is_ajax_request()) {
	    		echo json_encode(array('status' => 0));
	    	} else {
        		redirect('/auth/manage');
	    	}
        }

        $this->loadModel(array('user_model'));
    }
    
    public function index()
    {
        if (isset($_GET['t'])) {
            $t = strtolower($_GET['t']);
        } else {
            $t = '';
        }
        $this->db->from('users');
        $this->db->where('username <> "admin"');
        $this->db->where('LOWER(username) LIKE "%' . $t . '%" OR LOWER(name) LIKE "%' . $t . '%" OR LOWER(phone) LIKE "%' . $t . '%"');
        $query = $this->db->get();
        
        $users = $query->result();
        $this->render('user/index', array('users' => $users, 't' => $t));
    }
    
    public function update($id)
    {
        if (isset($_POST) && !empty($_POST)) {
            $this->saveAcc($id);
        }
        
        $user = $this->user_model->findOne(array('id' => $id));
        $this->render('user/update', array('user' => $user));
    }
    
    public function settings()
    {
        if (isset($_POST) && !empty($_POST)) {
            $this->db->where('user_id', $this->session->userdata('user_id'));
            $this->db->update('user_settings', [
                'half_hour_minutes' => intval($this->input->post('half_hour_minutes')) > 0 ? intval($this->input->post('half_hour_minutes')) : 10,
                'full_hour_minutes' => intval($this->input->post('full_hour_minutes')) > 0 ? intval($this->input->post('full_hour_minutes')) : 30,
                'hourly_hours' => intval($this->input->post('hourly_hours')) > 0 ? intval($this->input->post('hourly_hours')) : 6,
                'full_day_hours' => intval($this->input->post('full_day_hours')) > 0 ? intval($this->input->post('full_day_hours')) : 18
            ]);
        }
        
        $query = $this->db->select('*')->from('user_settings')->where('user_id', $this->session->userdata('user_id'))->get();
        $settings = $query->row();
        
        if (!$settings) {
            $this->db->insert('user_settings', ['user_id' => $this->session->userdata('user_id')]);
        }
        
        $this->render('user/settings', array('settings' => $settings));
    }
    
    public function create()
    {
        if (isset($_POST) && !empty($_POST)) {
            if ($id = $this->saveAcc(null)) {
                redirect('/user/update/' . $id);
            }
        }
        $this->render('user/update', array('user' => null));
    }
    
    private function saveAcc($id=null)
    {
        $dup = $this->user_model->findOne(array(
            'username' => $this->input->post('username'),
            'id <> ' . $id => null
        ));
        if ($dup) {
            return false;
        }
        
        $expired = trim($this->input->post('expired_at'));
        $data = array(
            'email' => $this->input->post('email'),
            'username' => $this->input->post('username'),
            'phone' => $this->input->post('phone'),
            'address' => $this->input->post('address'),
            'name' => $this->input->post('name'),
            'fullname' => $this->input->post('fullname'),
            'tax_id' => $this->input->post('tax_id'),
            'expired_at' => $expired ? strtotime($expired) : ''
        );
        if ($this->input->post('password')) {
            $data['password'] = md5($this->input->post('password'));
        }
        if ($this->input->post('admin_password')) {
            $data['admin_password'] = md5($this->input->post('admin_password'));
        }
        if ($id) {
            $this->user_model->update($id, $data);
            return $id;
        } else {
            $data['created_at'] = time();
            $id = $this->user_model->save($data);
            
            return $id;
        }
            
        return false;
    }
    
    public function enable_report($user_id)
    {
        $user = $this->user_model->findOne(array('id' => $user_id));
        
        if ($user) {
            if ($user->report_enable) {
                $this->user_model->update($user_id, array('report_enable' => 0));
                echo json_encode(array('status' => 1, 'report_enable' => 0));
                exit;
            } else {
                $this->user_model->update($user_id, array('report_enable' => 1));
                echo json_encode(array('status' => 1, 'report_enable' => 1));
                exit;
            }
        }
        
        echo json_encode(array('status' => 0));
        exit;
    }
    
    public function delete($user_id)
    {
        $user = $this->user_model->findOne(array('id' => $user_id));
        
        if ($user) {
            if ($user->deleted_at) {
                $this->user_model->update($user_id, array('deleted_at' => null));
            } else {
                $this->user_model->update($user_id, array('deleted_at' => time()));
            }
        }
        
        redirect("/user");
    }
}