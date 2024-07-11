<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Prescription_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('prescription');

        // Call the CI_Model constructor
        parent::__construct();
    }
    
    public function getList($diagnostic_id)
    {
        $this->db->select('prescription.*, drug.unit');
        $this->db->from('prescription');
        $this->db->join('drug', 'drug.id = prescription.drug_id', 'INNER');
        $this->db->where('prescription.user_id', $this->session->userdata('user_id'));
        $this->db->where('prescription.removed', 0);
        $this->db->where('drug.user_id', $this->session->userdata('user_id'));
        $this->db->where('drug.removed', 0);
        $this->db->where('prescription.diagnostic_id', $diagnostic_id);
        
        $query = $this->db->get();
        
        return $query->result();
    }
}