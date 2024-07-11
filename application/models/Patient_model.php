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
        $this->db->select(
            'patient.id, patient.name, patient.dob, patient.address, patient.phone, patient.gender,
            diagnostic.date_created, diagnostic.diagnostic, diagnostic.id AS diagnostic_id, diagnostic.note'
        );
        $this->db->from('patient');
        $this->db->join('diagnostic', 'diagnostic.patient_id = patient.id', 'INNER');
        $this->db->where('diagnostic.user_id', $this->session->userdata('user_id'));
        $this->db->where('diagnostic.removed', 0);
        $this->db->where('patient.removed', 0);
        if (isset($search_param['patient_id']) && $search_param['patient_id']) {
            $this->db->where('patient.id', $search_param['patient_id']);
        } else {
            if (isset($search_param['name']) && $search_param['name']) {
                $this->db->like('patient.name', $search_param['name']);
            }
        }
        if (isset($search_param['date']) && $search_param['date']) {
            $this->db->where('STRFTIME("%d-%m-%Y", diagnostic.date_created) = ', $search_param['date']);
        }
        $this->db->order_by('diagnostic.date_created DESC');
        if ($limit) {
            $this->db->limit($limit, $start);
        }
        
        $query = $this->db->get();
        
        return $query->result();
    }

    function count($search_param)
    {
        $this->db->select('COUNT(*) AS cnt');
        $this->db->from('patient');
        $this->db->join('diagnostic', 'diagnostic.patient_id = patient.id', 'INNER');
        $this->db->where('diagnostic.removed', 0);
        $this->db->where('patient.removed', 0);
        if (isset($search_param['patient_id']) && $search_param['patient_id']) {
            $this->db->where('patient.id', $search_param['patient_id']);
        } else {
            if (isset($search_param['name']) && $search_param['name']) {
                $this->db->like('patient.name', $search_param['name']);
            }
        }
        if (isset($search_param['date']) && $search_param['date']) {
            $this->db->where('STRFTIME("%d-%m-%Y", diagnostic.date_created) = ', $search_param['date']);
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