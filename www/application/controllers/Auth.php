<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->helper('url');
    }

	public function index()
	{
	    if (ADMIN_PASSWORD == 'ON') {
    	    $message = '';
    	    $err_message = '';
    	    if ($_POST && isset($_POST['password'])) {
    	        $this->load->model('password_model');
    	        
    	        $password = $this->password_model->findOne();
    	        
    	        if (!$password || $password->password == md5($_POST['password'])) {
    	            redirect('/drug/index');
    	        }
    	        
    	        $err_message = 'Mật khẩu không khớp';
    	    }
            $this->render('auth/index', array('message' => $message, 'err_message' => $err_message));
	    } else {
	        redirect('/drug/index');
	    }
	}
	
	public function password()
	{
	    $message = '';
	    $err_message = '';
	    if ($_POST && isset($_POST['new_password'])) {
	        $this->load->model('password_model');
	         
	        $password = $this->password_model->findOne();
	        if (!$password) {
	            $this->password_model->save(array('password' => md5($_POST['new_password'])));
	            $message = 'Đã cập nhật mật khẩu';
	        } else {
	            if ($password->password == md5($_POST['old_password'])) {
	                $this->db->update('password', array('password' => md5($_POST['new_password'])));
	                
                    $message = 'Đã cập nhật mật khẩu';
	            } else {
	                $err_message = 'Mật khẩu cũ không khớp';
	            }
	            
	        }
	    }
	    
	    $this->render('auth/password', array('message' => $message, 'err_message' => $err_message));
	}
}