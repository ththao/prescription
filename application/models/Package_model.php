<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Package_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('package');

        // Call the CI_Model constructor
        parent::__construct();
    }
    
    public function search($search_param, $limit, $start)
    {
        $this->db->select('*');
        $this->db->from('package');
        $this->db->where('user_id', $this->session->userdata('user_id'));
        $this->db->where('removed', 0);
        $this->db->like('package_name', $search_param);
        $this->db->order_by('package_name');
        $this->db->limit($limit, $start);
        
        $query = $this->db->get();
        
        $packages = $query->result();
        foreach ($packages as $package) {
            $package->prescriptions = $this->packageprescription_model->getList($package->id);
            if (SERVICES == 'ON') {
                $package->orders = $this->packageorder_model->getList($package->id);
            }
        }
        
        return $packages;
    }
    
    function count($search_param)
    {
        $this->db->select('COUNT(*) AS cnt');
        $this->db->from('package');
        $this->db->where('user_id', $this->session->userdata('user_id'));
        $this->db->where('removed', 0);
        $this->db->like('package_name', $search_param);
        
        $query = $this->db->get();
        $data = $query->result();
        
        if (!empty($data)) {
            $item = $data[0];
            return intval($item->cnt);
        }
        return 0;
    }
}