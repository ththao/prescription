<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class My_Controller extends CI_Controller
{
    const PER_PAGE = 13;
    public $layout = 'layout/main';

    public function layout($layout)
    {
        $this->layout = $layout;
    }

    public function loadModel($listModel = array())
    {
        foreach($listModel as $model) {
            $this->load->model($model);
        }
    }

    public function render($link, $data = null)
    {
        $this->load->view($this->layout, array(
            'content' => array(
                'link' => $link,
                'data' => $data,
            )
        ));
    }

    protected function pagination_config($total_rows, $base_url)
    {
        //pagination settings
        $config['base_url'] = $base_url;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = self::PER_PAGE;
        //$config["uri_segment"] = $uri_segment;
        $config["num_links"] = 25;
        $config["enable_query_strings"] = true;
        $config["reuse_query_string"] = true;
    
        //config for bootstrap pagination class integration
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="prev">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
    
        return $config;
    }
    
    protected function prescription_by_diagnostic($diagnostic_template_id, $do_now = false)
    {
        set_time_limit(0);
        
        if (!$do_now) {
            $query = $this->db->select('id')->from('diagnostic_template_prescription')->where('diagnostic_template_id', $diagnostic_template_id)->get();
            $row = $query->row();
            if ($row) {
                return false;
            }
        }
        
        $this->db->select('diagnostic.id AS diagnostic_id, drug.id');
        $this->db->from('diagnostic');
        $this->db->join('prescription', 'prescription.diagnostic_id = diagnostic.id', 'INNER');
        $this->db->join('drug', 'prescription.drug_id = drug.id', 'INNER');
        $this->db->where('diagnostic.diagnostic_template_id', $diagnostic_template_id);
        $this->db->order_by('diagnostic.id, drug.id');
        $query = $this->db->get();
        
        $all_drugs = $query->result();
        
        $pres_drugs = [];
        $group_drugs = [];
        foreach ($all_drugs as $all_drug) {
            $pres_drugs[$all_drug->id] = $all_drug->id;
            if (isset($group_drugs[$all_drug->diagnostic_id])) {
                $group_drugs[$all_drug->diagnostic_id] .= ',' . $all_drug->id;
            } else {
                $group_drugs[$all_drug->diagnostic_id] = $all_drug->id;
            }
        }
        $count_group = [];
        foreach ($group_drugs as $group_drug) {
            if (isset($count_group[$group_drug])) {
                $count_group[$group_drug] ++;
            } else {
                $count_group[$group_drug] = 1;
            }
        }
        
        arsort($count_group);
        
        $most_used_group = key($count_group);
        $most_used_group = $most_used_group ? explode(',', $most_used_group) : [];
        
        foreach ($pres_drugs as $pres_drug) {
            $query = $this->db->select('id')->from('diagnostic_template_prescription')->where('diagnostic_template_id', $diagnostic_template_id)->where('drug_id', $pres_drug)->get();
            $row = $query->row();
            
            $most_used = in_array($pres_drug, $most_used_group) ? 1 : 0;
            
            if ($row) {
                $this->db->where('id', $row->id)->update('diagnostic_template_prescription', ['most_used' => $most_used]);
            } else {
                $this->db->insert('diagnostic_template_prescription', ['diagnostic_template_id' => $diagnostic_template_id, 'drug_id' => $pres_drug, 'most_used' => $most_used]);
            }
        }
    }
}