<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Patient_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('patient');

        // Call the CI_Model constructor
        parent::__construct();
    }

    public function search($search_param, $limit, $start)
    {
        $this->db->select('*, diagnostic.diagnostic, diagnostic.id AS diagnostic_id');
        $this->db->from('patient');
        $this->db->join('diagnostic', 'diagnostic.patient_id = patient.id', 'INNER');
        if (isset($search_param[0]) && $search_param[0]) {
            $this->db->like('patient.name', $search_param[0]);
        }
        if (isset($search_param[1]) && $search_param[1]) {
            $this->db->where('STRFTIME("%d-%m-%Y", patient.date_created) = ', $search_param[1]);
        }
        $this->db->order_by('id DESC');
        $this->db->limit($limit, $start);
        
        $query = $this->db->get();
        
        return $query->result();
    }

    function count($search_param)
    {
        $this->db->select('COUNT(*) AS cnt');
        $this->db->from('patient');
        $this->db->join('diagnostic', 'diagnostic.patient_id = patient.id', 'INNER');
        if (isset($search_param[0]) && $search_param[0]) {
            $this->db->like('patient.name', $search_param[0]);
        }
        if (isset($search_param[1]) && $search_param[1]) {
            $this->db->where('STRFTIME("%d-%m-%Y", patient.date_created) = ', $search_param[1]);
        }
        
        $query = $this->db->get();
        $data = $query->result();
        
        if (!empty($data)) {
            $item = $data[0];
            return intval($item->cnt);
        }
        return 0;
    }
}