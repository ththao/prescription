<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Remember_user_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('remember_users');

        // Call the CI_Model constructor
        parent::__construct();
    }
	
    public function getRememberUsers()
    {
    	$sql = '
            SELECT remember_users.*, users.username
            FROM remember_users
            LEFT OUTER JOIN users ON users.id = remember_users.user_id
            ORDER BY remember_users.created_at DESC
        ';
    	$query = $this->db->query($sql);
    	$data = $query->result();
    	
    	return $data;
    }
}