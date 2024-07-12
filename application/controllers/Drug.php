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
        
        $query = $this->db->select('drug.name')->from('drug')->where('user_id <> ' . $this->session->userdata('user_id'), null)->where('removed', 0)->get();
        $drugs = $query->result();
        $drug_names = [];
        if ($drugs) {
            foreach ($drugs as $drug) {
                $drug_names[] = ['value' => $drug->name, 'label' => $drug->name];
            }
        }
        $js_drug_names = json_encode($drug_names);
        
        $my_drugs = $this->drug_model->findAll(['user_id' => $this->session->userdata('user_id'), 'removed' => 0]);
        $my_drug_names = [];
        if ($my_drugs) {
            foreach ($my_drugs as $drug) {
                $my_drug_names[] = ['value' => $drug->name, 'label' => $drug->name];
            }
        }
        $js_my_drug_names = json_encode($my_drug_names);

        $this->render('drug/index', ['drug_names' => $js_drug_names, 'my_drug_names' => $js_my_drug_names, 'models' => $data, 'search' => '']);
	}
	
	public function import()
	{
	    if (isset($_POST['submit']) && isset($_FILES['drugs']) && isset($_FILES['drugs']['tmp_name']) && $_FILES['drugs']['tmp_name']) {
            $handle = fopen($_FILES['drugs']['tmp_name'], "r");
            $headers = fgetcsv($handle, null, ",");
            
            $name_index = 1;
            $unit_index = 2;
            $price_index = 3;
            $note_index = 4;
            $in_price_index = 7;
            
            foreach ($headers as $index => $header) {
                if (strtolower($header) == 'name') {
                    $name_index = $index;
                }
                if (strtolower($header) == 'unit') {
                    $unit_index = $index;
                }
                if (strtolower($header) == 'price') {
                    $price_index = $index;
                }
                if (strtolower($header) == 'in_price') {
                    $in_price_index = $index;
                }
                if (strtolower($header) == 'note') {
                    $note_index = $index;
                }
            }
            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                $drug = $this->drug_model->findOne(['user_id' => $this->session->userdata('user_id'), 'LOWER(name)' => strtolower($data[$name_index])]);
                
                $save['user_id'] = $this->session->userdata('user_id');
                $save['name'] = $data[$name_index];
                $save['unit'] = $data[$unit_index];
                $save['price'] = $data[$price_index];
                $save['in_price'] = $data[$in_price_index];
                $save['note'] = $data[$note_index];
                $save['removed'] = 0;
                
                if (!$drug) {
                    $save['date_created'] = time();
                    $this->drug_model->save($save);
                } else {
                    $save['date_updated'] = time();
                    $this->drug_model->update($drug->id, $save);
                }
            }
            fclose($handle);
        }
	    redirect('drug/index');
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
        
        $query = $this->db->select('drug.name')->from('drug')->where('user_id <> ' . $this->session->userdata('user_id'), null)->where('removed', 0)->get();
        $drugs = $query->result();
        $drug_names = [];
        if ($drugs) {
            foreach ($drugs as $drug) {
                $drug_names[] = ['value' => $drug->name, 'label' => $drug->name];
            }
        }
        $js_drug_names = json_encode($drug_names);
        
        $my_drugs = $this->drug_model->findAll(['user_id' => $this->session->userdata('user_id'), 'removed' => 0]);
        $my_drug_names = [];
        if ($my_drugs) {
            foreach ($my_drugs as $drug) {
                $my_drug_names[] = ['value' => $drug->name, 'label' => $drug->name];
            }
        }
        $js_my_drug_names = json_encode($my_drug_names);

        $this->render('drug/index', ['drug_names' => $js_drug_names, 'my_drug_names' => $js_my_drug_names, 'models' => $data, 'search' => $search]);
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
