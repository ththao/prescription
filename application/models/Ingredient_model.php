<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ingredient_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('ingredient');

        // Call the CI_Model constructor
        parent::__construct();
    }

    public function search($search_param, $limit, $start)
    {
        $this->db->select('*');
        $this->db->from('ingredient');
        $this->db->like('ingredient_name', $search_param);
        $this->db->order_by('ingredient_name ASC');
        $this->db->limit($limit, $start);
        
        $query = $this->db->get();
        
        return $query->result();
    }

    function count($search_param)
    {
        $this->db->select('COUNT(*) AS cnt');
        $this->db->from('ingredient');
        $this->db->like('ingredient_name', $search_param);
        
        $query = $this->db->get();
        $data = $query->result();
        
        if (!empty($data)) {
            $item = $data[0];
            return intval($item->cnt);
        }
        return 0;
    }
}