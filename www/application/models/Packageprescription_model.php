<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Packageprescription_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('package_prescription');

        // Call the CI_Model constructor
        parent::__construct();
    }
    
    public function getList($package_id)
    {
        $this->db->select('package_prescription.*, drug.name AS drug_name, drug.price, drug.unit');
        $this->db->from('package_prescription');
        $this->db->join('drug', 'drug.id = package_prescription.drug_id', 'INNER');
        $this->db->where('package_prescription.package_id', $package_id);
        
        $query = $this->db->get();
        
        return $query->result();
    }
}