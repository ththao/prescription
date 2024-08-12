<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class My_Controller extends CI_Controller
{
    const PER_PAGE = 13;
    public $layout = 'layout/main';

    public function layout($layout)
    {
        $this->layout = $layout;
        
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        
        if ($this->session->has_userdata('user_id') && $this->session->userdata('user_id')) {
            if ($this->session->userdata('user_id') == 1) {
                $this->session->set_userdata('logged_in', 1);
            }
            
            $this->load->model('user_model');
            $user = $this->user_model->findOne(array('id' => $this->session->userdata('user_id')));
            $this->session->set_userdata('expired_at', $user->expired_at);
        }
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
    
    public function checkLoggedIn()
    {
        if (!$this->session->has_userdata('user_id') || !$this->session->userdata('user_id')) {
            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('fullname');
            $this->session->unset_userdata('logged_in');
            return false;
        }
        
        $this->load->model('user_model');
        $user = $this->user_model->findOne(['id' => $this->session->userdata('user_id'), 'removed' => 0]);
        if (!$user) {
            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('fullname');
            $this->session->unset_userdata('logged_in');
            delete_cookie('siteAuth');
            return false;
        } else {
            $query = $this->db->select('id')->from('remember_users')->where('user_id', $user->id)->where('remember_hash', get_cookie('siteAuth'))->get();
            $remember = $query->row();
            if (!$remember) {
                $this->session->unset_userdata('user_id');
                $this->session->unset_userdata('fullname');
                $this->session->unset_userdata('logged_in');
                delete_cookie('siteAuth');
                return false;
            }
        }
        return true;
    }
    
    protected function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    protected function pagination_config($total_rows, $base_url)
    {
        //pagination settings
        $config['base_url'] = $base_url;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = self::PER_PAGE;
        //$config["uri_segment"] = $uri_segment;
        $config["num_links"] = 10;
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
    
    protected function replaceAbbreviations($user_id, $string)
    {
        
        $parts = explode('/', $string);
        
        if ($user_id == 3) {
            
            if ($parts) {
                foreach ($parts as $index => $part) {
                    $part = strtolower(trim($part));
                    
                    $part = str_replace('votn', 'Viêm ống tai ngoài', $part);
                    $part = str_replace('otn', 'ống tai ngoài', $part);
                    $part = str_replace('vtg', 'Viêm tai giữa', $part);
                    $part = str_replace('rlth', 'Rối loạn tiêu hóa', $part);
                    $part = str_replace('rlgn', 'Rối loạn giấc ngủ', $part);
                    $part = str_replace('vhm', 'viêm họng mạn', $part);
                    $part = str_replace('vhc', 'viêm họng cấp', $part);
                    $part = str_replace('vmhc', 'viêm mũi họng cấp', $part);
                    $part = str_replace('vmdu', 'Viêm mũi dị ứng', $part);
                    $part = str_replace('vmxdu', 'Viêm mũi xoang dị ứng', $part);
                    $part = str_replace('vmxm', 'Viêm mũi xoang mạn', $part);
                    $part = str_replace('vmxc', 'Viêm mũi xoang cấp', $part);
                    $part = str_replace('hpq', 'Hen phế quản', $part);
                    $part = str_replace('rltktg', 'Rối loạn thần kinh trung gian', $part);
                    $part = str_replace('rltktv', 'Rối loạn thần kinh thực vật', $part);
                    
                    $parts[$index] = $part;
                }
            }
        }
        
        return $parts;
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
        
        $sql = 'SELECT `drug_template`.`name` AS `drug_template_name`, `drug`.`name` AS `drug_name`, `drug_category`.`category_name`, COUNT(DISTINCT diagnostic.id) AS drug_used_count 

        FROM `diagnostic` 
        INNER JOIN `prescription` ON `prescription`.`diagnostic_id` = `diagnostic`.`id` 
        INNER JOIN `drug` ON `prescription`.`drug_id` = `drug`.`id` 
        INNER JOIN `diagnostic_template` ON LOWER(diagnostic_template.diagnostic) = LOWER(diagnostic.diagnostic) 
        INNER JOIN `drug_template` ON (LOWER(drug_template.name) = LOWER(drug.name) OR 
        (LOWER(drug_template.name) LIKE CONCAT(LOWER(drug.name), "%") AND NOT EXISTS (SELECT id FROM drug_template d1 WHERE LOWER(d1.name) = LOWER(drug.name)))) 
        
        LEFT OUTER JOIN `drug_category` ON `drug_template`.`drug_category_id` = `drug_category`.`id` 
        WHERE `diagnostic_template`.`id` = ' . $diagnostic_template_id . ' AND `prescription`.`removed` =0 AND `diagnostic`.`removed` =0 AND `drug`.`removed` =0 
        GROUP BY `drug_template`.`name`, `drug`.`name` ORDER BY `drug_category`.`category_name`, `drug_used_count` DESC';
        
        $query = $this->db->query($sql);
        $all_drugs = $query->result();
        
        foreach ($all_drugs as $drug) {
            $query = $this->db->select('id')->from('diagnostic_template_prescription')->where('diagnostic_template_id', $diagnostic_template_id)->where('LOWER(drug_name)', strtolower($drug->drug_template_name))->get();
            $row = $query->row();
            
            if (!$row) {
                $this->db->insert('diagnostic_template_prescription', ['diagnostic_template_id' => $diagnostic_template_id, 'drug_name' => $drug->drug_template_name, 'drug_category_name' => $drug->category_name, 'used_count' => $drug->drug_used_count]);
            } else {
                $this->db->where('id', $row->id)->update('diagnostic_template_prescription', ['used_count' => $drug->drug_used_count]);
            }
        }
    }
}