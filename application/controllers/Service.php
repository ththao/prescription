<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends My_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->loadModel(array('service_model'));
        $this->load->library('pagination');
        $this->load->helper('url');
    }

	public function index($page=0)
	{
	    $config = $this->pagination_config($this->service_model->count(""), site_url('service/index'));
        $this->pagination->initialize($config);
        
        $data['page'] = $page;
        //call the model function to get the department data
        $data['services'] = $this->service_model->search("", $config["per_page"], $data['page']);

        $data['pagination'] = $this->pagination->create_links();
        
        $query = $this->db->select('services.service_name')->from('services')->where('user_id <> ' . $this->session->userdata('user_id'), null)->where('removed', 0)->get();
        $services = $query->result();
        $service_names = [];
        if ($services) {
            foreach ($services as $service) {
                $service_names[] = ['value' => $service->service_name, 'label' => $service->service_name];
            }
        }
        $js_service_names = json_encode($service_names);
        
        $my_services = $this->service_model->findAll(['user_id' => $this->session->userdata('user_id'), 'removed' => 0]);
        $my_service_names = [];
        if ($my_services) {
            foreach ($my_services as $service) {
                $my_service_names[] = ['value' => $service->service_name, 'label' => $service->service_name];
            }
        }
        $js_my_service_names = json_encode($my_service_names);

        $this->render('service/index', array('models' => $data, 'service_names' => $js_service_names, 'my_service_names' => $js_my_service_names, 'search' => ''));
	}

    public function search()
    {
        $search = $this->input->get('search');
        $config = $this->pagination_config($this->service_model->count($search), site_url('service/search'));
        $this->pagination->initialize($config);
        
        $data['page'] = $this->uri->segment(3);
        //call the model function to get the department data
        $data['services'] = $this->service_model->search($search, $config["per_page"], $data['page']);

        $data['pagination'] = $this->pagination->create_links();

        $this->render('service/index', array('models' => $data, 'search' => $search));
    }

    public function create()
    {
        if (isset($_POST) && !empty($_POST['service_name'])) {
            $service = $this->service_model->findOne(['service_name' => $_POST['service_name'], 'user_id' => $this->session->userdata('user_id'), 'removed' => 0]);
            if (!$service) {
                $data['user_id'] = $this->session->userdata('user_id');
                $data['service_name'] = $_POST['service_name'];
                $data['price'] = $_POST['price'] ? $_POST['price'] : 0;
                $data['notes'] = $_POST['notes'];
                $data['date_created'] = time();
                
                if (!$this->service_model->save($data)) {
                    echo json_encode(array('error' => 'Có lỗi. Vui lòng thử lại.'));
                    return;
                }

                echo json_encode(array('success' => 'Kỹ thuật mới đã đc thêm vào danh sách'));
                return;
            } else {
                echo json_encode(array('error' => 'Kỹ thuật này đã tồn tại trong danh sách'));
                return;
            }
        }
        echo json_encode(array('error' => 'Vui lòng điền đầy đủ thông tin'));
    }

    public function update($id)
    {
        if (isset($_POST) && !empty($_POST['service_name'])) {
            $service = $this->service_model->findOne(['id' => $id, 'user_id' => $this->session->userdata('user_id')]);
            
            if ($service) {
                $data['service_name'] = $_POST['service_name'];
                $data['price'] = $_POST['price'] ? $_POST['price'] : 0;
                $data['notes'] = $_POST['notes'];
                $data['date_updated'] = time();
                $data['removed'] = 0;
                $this->service_model->update($id, $data);
                
                echo json_encode(array('success' => 'Kỹ thuật đã đc cập nhật'));
                return;
            }
        }
        echo json_encode(array('error' => 'Vui lòng điền đầy đủ thông tin'));
    }

    public function delete($id)
    {
        $service = $this->service_model->findOne(['id' => $id, 'user_id' => $this->session->userdata('user_id')]);
        if ($service) {
            $this->service_model->delete($service->id);
        }
        redirect("/service/index");
    }
}
