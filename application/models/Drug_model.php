<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Drug_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('drug');

        // Call the CI_Model constructor
        parent::__construct();
    }

    public function search($search_param, $limit, $start)
    {
        $this->db->select('drug.*, drug_template.name AS template_name, drug_category.category_name');
        $this->db->from('drug');
        $this->db->join('drug_template', 'drug_template.id = drug.drug_template_id', 'LEFT OUTER');
        $this->db->join('drug_category', 'drug_category.id = COALESCE(drug.drug_category_id, drug_template.drug_category_id)', 'LEFT OUTER');
        $this->db->where('drug.user_id', $this->session->userdata('user_id'));
        $this->db->where('drug.removed', 0);
        $this->db->like('drug.name', $search_param);
        $this->db->order_by('drug.name ASC');
        $this->db->limit($limit, $start);
        
        $query = $this->db->get();
        
        $data = $query->result();
        if ($data) {
            foreach ($data as $item) {
                $this->db->distinct()->select('ingredient.ingredient_name');
                $this->db->from('drug_ingredients');
                $this->db->join('ingredient', 'ingredient.id = drug_ingredients.ingredient_id', 'INNER');
                $this->db->where('drug_ingredients.drug_id', $item->id);
                $this->db->order_by('ingredient.ingredient_name');
                $query = $this->db->get();
                
                $ingredients = $query->result();
                
                $ing = '';
                if ($ingredients) {
                    foreach ($ingredients as $ingredient) {
                        $ing .= ($ing ? '<br/>' : '') . $ingredient->ingredient_name;
                    }
                }
                $item->ingredients = $ing;
            }
        }
        
        return $data;
    }

    function count($search_param)
    {
        $this->db->select('COUNT(*) AS cnt');
        $this->db->from('drug');
        $this->db->where('removed', 0);
        $this->db->where('user_id', $this->session->userdata('user_id'));
        $this->db->like('name', $search_param);
        
        $query = $this->db->get();
        $data = $query->result();
        
        if (!empty($data)) {
            $item = $data[0];
            return intval($item->cnt);
        }
        return 0;
    }
}