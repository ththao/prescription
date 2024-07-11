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
	
	public function import()
	{
	    if (isset($_POST['submit'])) {
            $handle = fopen($_FILES['drugs']['tmp_name'], "r");
            $headers = fgetcsv($handle, null, ",");
            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                $drug = $this->drug_model->findOne(array('name' => $data['1']));
                if (!$drug) {
                    $drug['user_id'] = $this->session->userdata('user_id');
                    $drug['name'] = $data[1];
                    $drug['unit'] = $data[2];
                    $drug['price'] = 0;
                    $drug['in_price'] = $data[6];
                    $drug['note'] = $data[4];
                    $drug['date_created'] = time();
                    
                    $this->drug_model->save($drug);
                }
            }
            fclose($handle);
        }
	    $this->render('drug/import');
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
        if (isset($_POST) && !empty($_POST['name']) && !empty($_POST['unit'])) {
            $drug = $this->drug_model->findOne(['name' => $_POST['name'], 'user_id' => $this->session->userdata('user_id'), 'removed' => 0]);
            if (!$drug) {
                $data['user_id'] = $this->session->userdata('user_id');
                $data['name'] = $_POST['name'];
                $data['unit'] = $_POST['unit'];
                $data['in_price'] = $_POST['in_price'];
                $data['price'] = $_POST['price'];
                $data['note'] = $_POST['note'];
                $data['date_created'] = time();
                
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
        if (isset($_POST) && !empty($_POST['name']) && !empty($_POST['unit'])) {
            $data = $_POST;
            $drug = $this->drug_model->findOne(['id' => $id, 'user_id' => $this->session->userdata('user_id')]);
            if ($drug) {
                $data['date_updated'] = time();
                $data['price'] = $data['price'] ? $data['price'] : 0;
                $data['in_price'] = $data['in_price'] ? $data['in_price'] : 0;
                $data['removed'] = 0;
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
        $drug = $this->drug_model->findOne(['id' => $id, 'user_id' => $this->session->userdata('user_id')]);
        if ($drug) {
            $this->drug_model->delete($id);
        }
        redirect("/drug/index");
    }
}
