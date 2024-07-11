<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('orders');

        // Call the CI_Model constructor
        parent::__construct();
    }
    
    public function getList($diagnostic_id)
    {
        $this->db->select('orders.*, services.service_name');
        $this->db->from('orders');
        $this->db->join('services', 'services.id = orders.service_id', 'INNER');
        $this->db->where('orders.diagnostic_id', $diagnostic_id);
        
        $query = $this->db->get();
        
        return $query->result();
    }
}