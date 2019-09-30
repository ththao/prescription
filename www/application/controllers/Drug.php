<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Drug extends My_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->loadModel(array('drug_model'));
        $this->load->library('pagination');
        $this->load->helper('url');
    }

	public function index($page=0)
	{
	    $config = $this->pagination_config($this->drug_model->count(""), site_url('drug/index'));
        $this->pagination->initialize($config);
        
        $data['page'] = $page;
        //call the model function to get the department data
        $data['drugs'] = $this->drug_model->search("", $config["per_page"], $data['page']);

        $data['pagination'] = $this->pagination->create_links();

        $this->render('drug/index', array('models' => $data, 'search' => ''));
	}

    public function search()
    {
        $search = $this->input->get('search');
        $config = $this->pagination_config($this->drug_model->count($search), site_url('drug/search'));
        $this->pagination->initialize($config);
        
        $data['page'] = $this->uri->segment(3);
        //call the model function to get the department data
        $data['drugs'] = $this->drug_model->search($search, $config["per_page"], $data['page']);

        $data['pagination'] = $this->pagination->create_links();

        $this->render('drug/index', array('models' => $data, 'search' => $search));
    }

    public function create()
    {
        if (isset($_POST) && !empty($_POST['name']) && !empty($_POST['unit']) && !empty($_POST['price'])) {
            $drug = $this->drug_model->findOne(array('name' => $_POST['name']));
            if (!$drug) {
                $data['name'] = $_POST['name'];
                $data['unit'] = $_POST['unit'];
                $data['in_price'] = $_POST['in_price'];
                $data['price'] = $_POST['price'];
                $data['note'] = $_POST['note'];
                $data['date_created'] = date('Y-m-d H:i:s');
                
                if (!$this->drug_model->save($data)) {
                    echo json_encode(array('error' => 'Có lỗi. Vui lòng thử lại.'));
                    return;
                }

                echo json_encode(array('success' => 'Thuốc mới đã đc thêm vào danh sách'));
                return;
            } else {
                echo json_encode(array('error' => 'Loại thuốc này đã tồn tại trong danh sách'));
                return;
            }
        }
        echo json_encode(array('error' => 'Vui lòng điền đầy đủ thông tin'));
    }

    public function update($id)
    {
        $data = $_POST;
        if (isset($_POST) && !empty($_POST['name']) && !empty($_POST['unit']) && !empty($_POST['price'])) {
            $drug = $this->drug_model->findOne(array('id' => $id));
            if ($drug) {
                $data['date_updated'] = date('Y-m-d H:i:s');
                $data['price'] = $data['price'] ? $data['price'] : 0;
                $data['in_price'] = $data['in_price'] ? $data['in_price'] : 0;
                $this->drug_model->update($id, $data);
                
                if ($data['in_price']) {
                    $this->db->where('drug_id', $id);
                    $this->db->where('in_unit_price IS ', null);
                    $this->db->update('prescription', array('in_unit_price' => $data['in_price'])); 
                }

                echo json_encode(array('success' => 'Thuốc đã đc cập nhật'));
                return;
            }
        }
        echo json_encode(array('error' => 'Vui lòng điền đầy đủ thông tin'));
    }

    public function delete($id)
    {
        $drug = $this->drug_model->findOne(array('id' => $id));
        if ($drug) {
            $this->drug_model->delete($id);
        }
        redirect("/drug/index");
    }
}
