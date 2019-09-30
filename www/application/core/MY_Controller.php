<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class My_Controller extends CI_Controller
{
    const PER_PAGE = 7;
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
        $config["num_links"] = floor($config["total_rows"] / $config["per_page"]);
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
}