<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Packageorder_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('package_orders');

        // Call the CI_Model constructor
        parent::__construct();
    }
    
    public function getList($package_id)
    {
        $this->db->select('package_orders.*, services.service_name, services.price');
        $this->db->from('package_orders');
        $this->db->join('services', 'services.id = package_orders.service_id', 'INNER');
        $this->db->where('package_orders.package_id', $package_id);
        
        $query = $this->db->get();
        
        return $query->result();
    }
}