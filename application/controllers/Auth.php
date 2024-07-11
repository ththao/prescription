<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model(['user_model', 'remember_user_model']);
    }

	public function login()
	{
	    if ($this->checkLoggedIn() || $this->login_by_hash()) {
	        redirect('/prescription');
	    }
        
	    $err_message = '';
	    if (isset($_POST['username']) && isset($_POST['password'])) {
	        $user = $this->user_model->findOne(['username' => strtolower($_POST['username']), 'password' => md5($_POST['password'])]);
	        
	        if (!$user) {
	        	$user = $this->user_model->findOne(['email' => strtolower($_POST['username']), 'password' => md5($_POST['password'])]);
	        }
	        if ($user) {
	            if ($user->removed) {
	                $err_message = 'Tài khoản của bạn đã hết hạn, vui lòng <a href="#" user_id="' . $user->id . '" class="btn-pay-request">liên hệ admin</a> để gia hạn.';
	            } else {
    	            $this->session->set_userdata('user_id', $user->id);
    	            $this->session->set_userdata('fullname', $user->fullname);
    	            
    	            $this->save_remember_hash($user->id, null);
    	            
    	            redirect('/prescription');
	            }
	        } else {
	            $err_message = 'Tên đăng nhập hoặc mật khẩu không khớp';
	        }
	    }
	    $this->layout = 'layout/main_2';
        $this->render('auth/login', array('err_message' => $err_message));
	}
	
	private function login_by_hash()
	{
		$siteAuth = get_cookie('siteAuth');
		if ($siteAuth) {
			$remember = $this->remember_user_model->findOne(['remember_hash' => $siteAuth]);
			
			if ($remember) {
				$user = $this->user_model->findOne(['id' => $remember->user_id, 'removed' => 0]);
				
				if ($user) {
					$this->session->set_userdata('user_id', $user->id);
					$this->session->set_userdata('fullname', $user->fullname);
		            
		            $this->save_remember_hash($user->id, $remember);
		            return true;
				}
			}
		}
		
		delete_cookie('siteAuth');
		return false;
	}
	
	private function save_remember_hash($user_id, $remember)
	{
		if ($remember) {
			set_cookie('siteAuth', $remember->remember_hash, 30*86400);
			$this->db->where('user_id', $user_id);
			$this->db->where('remember_hash', $remember->remember_hash);
        	$this->db->update('remember_users', array('created_at' => time()));
		} else {
			$hash = md5($user_id . '_' . $this->generateRandomString());
			set_cookie('siteAuth', $hash, 30*86400);
			$browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
			
			$this->db->insert('remember_users', ['user_id' => $user_id, 'browser' => $browser, 'remember_hash' => $hash, 'created_at' => time()]);
		}
	}
    
	public function admin()
	{
	    if (!$this->checkLoggedIn()) {
            redirect('/auth/login');
        }
        
	    if ($this->session->has_userdata('logged_in') && $this->session->userdata('logged_in')) {
	        redirect('/drug/index');
	    }
	    
	    $message = '';
	    $err_message = '';
	    if ($_POST && isset($_POST['password'])) {
	        $user = $this->user_model->findOne(['id' => $this->session->userdata('user_id')]);
	        if (!$user->admin_password || $user->admin_password == md5($_POST['password'])) {
	            $this->session->set_userdata('logged_in', 1);
	            redirect('/drug/index');
	        }
	        
	        $err_message = 'Mật khẩu không khớp';
	    }
        $this->render('auth/admin', array('message' => $message, 'err_message' => $err_message));
	}
	
	public function password()
	{
	    if (!$this->checkLoggedIn()) {
            redirect('/auth/login');
        }
	     
	    $message = '';
	    $err_message = '';
	    if (isset($_POST['new_password'])) {
	        if ($_POST['new_password'] == $_POST['new_re_password']) {
	            $user = $this->user_model->findOne(['id' => $this->session->userdata['user_id']]);
	            if ($user) {
	                if (!$user->password) {
	                	$this->user_model->update($user->id, ['password' => md5($_POST['new_password'])]);
	                	
	                	$this->db->where('user_id', $user->id)->delete('remember_users');
	                    $message = 'Đã cập nhật mật khẩu';
	                } else {
	                    if ($user->password == md5($_POST['old_password'])) {
	                        $this->user_model->update($user->id, ['password' => md5($_POST['new_password'])]);
	                        
	                        $this->db->where('user_id', $user->id)->delete('remember_users');
	                        $message = 'Đã cập nhật mật khẩu';
	                    } else {
	                        $err_message = 'Mật khẩu cũ không khớp';
	                    }
	                }
	            }
	        } else {
	            $err_message = 'Mật khẩu mới lần 2 không khớp';
	        }
	    }
	     
	    $this->render('auth/password', ['message' => $message, 'err_message' => $err_message]);
	}
	
	public function admin_password()
	{
	    if (!$this->checkLoggedIn()) {
            redirect('/auth/login');
        }
        
	    if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) {
	        redirect('/auth/manage');
	    }
	    
	    $message = '';
	    $err_message = '';
	    if (isset($_POST['new_password'])) {
	        if ($_POST['new_password'] == $_POST['new_re_password']) {
    	        $user = $this->user_model->findOne(['id' => $this->session->userdata['user_id']]);
    	        if ($user) {
        	        if (!$user->admin_password) {
        	            $this->user_model->update($user->id, ['admin_password' => md5($_POST['new_password'])]);
        	            $message = 'Đã cập nhật mật khẩu quản lý';
        	        } else {
        	            if ($user->admin_password == md5($_POST['old_password'])) {
        	                $this->user_model->update($user->id, ['admin_password' => md5($_POST['new_password'])]);
                            $message = 'Đã cập nhật mật khẩu  quản lý';
        	            } else {
        	                $err_message = 'Mật khẩu quản lý cũ không khớp';
        	            }   
        	        }
    	        }
	        } else {
	            $err_message = 'Mật khẩu quản lý mới lần 2 không khớp';
	        }
	    }
	    
	    $this->render('auth/admin_password', ['message' => $message, 'err_message' => $err_message]);
	}
	
	public function logout()
	{
	    
	    $this->db->where('user_id', $this->session->userdata('user_id'));
	    $this->db->where('remember_hash', get_cookie('siteAuth'));
		$this->db->delete('remember_users');
		
	    $this->session->unset_userdata('user_id');
	    $this->session->unset_userdata('fullname');
	    $this->session->unset_userdata('logged_in');
	    
	    delete_cookie('siteAuth');
	    redirect('/auth/login');
	}
}