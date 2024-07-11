<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Package extends My_Controller {
    public function __construct()
    {
        parent::__construct();
        
        $this->loadModel(array('package_model', 'drug_model', 'service_model', 'order_model', 'packageorder_model', 'packageprescription_model'));
        $this->load->library('pagination');
        $this->load->helper('url');
    }
    
    public function index($page=0)
    {
        $config = $this->pagination_config($this->package_model->count(""), site_url('package/index'));
        $this->pagination->initialize($config);
        $data['page'] = $page;
        //call the model function to get the department data
        $data['packages'] = $this->package_model->search("", $config["per_page"], $data['page']);
        
        $data['pagination'] = $this->pagination->create_links();
        
        $this->render('package/index', array('models' => $data, 'search' => ''));
    }
    
    public function search()
    {
        $search = $this->input->get('search');
        $config = $this->pagination_config($this->package_model->count($search), site_url('package/search'));
        $this->pagination->initialize($config);
        
        $data['page'] = $this->uri->segment(3);
        //call the model function to get the department data
        $data['packages'] = $this->package_model->search($search, $config["per_page"], $data['page']);
        
        $data['pagination'] = $this->pagination->create_links();
        
        $this->render('package/index', array('models' => $data, 'search' => $search));
    }

	public function update($id=null)
	{
	    try {
            $drugs = $this->drug_model->findAll();
            $drug_names = array();
            if ($drugs) {
                foreach ($drugs as $drug) {
                    $drug_names[] = [
                        'id' => $drug->id,
                        'value' => $drug->name,
                        'label' => $drug->name,
                        'unit' => $drug->unit
                    ];
                }
            }
            $js_drug_names = json_encode($drug_names);
            
            if (SERVICES == 'ON') {
                $services = $this->service_model->findAll();
                $service_names = array();
                if ($services) {
                    foreach ($services as $service) {
                        $service_names[] = [
                            'id' => $service->id,
                            'value' => $service->service_name,
                            'label' => $service->service_name
                        ];
                    }
                }
                $js_service_names = json_encode($service_names);
            } else {
                $js_service_names = '';
            }
            
            $package = null;
            $prescriptions = null;
            $orders = null;
            if ($id) {
                $package = $this->package_model->findOne(array('id' => $id));
                if ($package) {
                    $prescriptions = $this->packageprescription_model->getList($package->id);
                    if (SERVICES == 'ON') {
                        $orders = $this->packageorder_model->getList($package->id);
                    }
                }
            }
            
            $this->render('package/update', array(
                'drug_names' => $js_drug_names,
                'service_names' => $js_service_names,
                'package' => $package,
                'prescriptions' => $prescriptions,
                'orders' => $orders
            ));
	    } catch (Exception $e) {
	        redirect('/migration/index', 'refresh');
	    }
	}
	
	public function delete($id=null)
	{
	    $this->db->where('package_id', $id)->delete('package_orders');
	    $this->db->where('package_id', $id)->delete('package_prescription');
	    $this->db->where('id', $id)->delete('package');
	    
	    redirect('/package/index', 'refresh');
	}

    public function save($id=null)
    {
        if (isset($_POST['package']) && !empty($_POST['package'])) {
            if (empty($_POST['package']['package_name'])) {
                echo json_encode(array('error' => 'Vui lòng nhập tên gói.'));
                return;
            }
            
            $this->db->trans_start();
            
            // Save new patient
            if (isset($_POST['package']['id']) && $_POST['package']['id']) {
                $package = $this->package_model->findOne(array('id' => $_POST['package']['id']));
            }
            unset($_POST['package']['id']);
            
            if (isset($package) && $package) {
                $this->package_model->update($package->id, $_POST['package']);
                $package_id = $package->id;
            } else {
                $package_id = $this->package_model->save($_POST['package']);
                if (!$package_id) {
                    echo json_encode(array('error' => 'Có lỗi. Vui lòng thử lại.'));
                    return;
                }
            }
            
            if (SERVICES == 'ON') {
                // Save orders of patient
                $order_ids = [];
                for ($i = 1; $i <= $_POST['service_index_row']; $i++) {
                    if (!isset($_POST['order'][$i]) || !isset($_POST['order'][$i]['service_id']) || !$_POST['order'][$i]['service_id']) {
                        continue;
                    }
                    
                    $service = $this->service_model->findOne(array('id' => $_POST['order'][$i]['service_id']));
                    
                    $pres = null;
                    if (isset($_POST['order'][$i]['id']) && $_POST['order'][$i]['id']) {
                        $pres = $this->packageorder_model->findOne(array('id' => $_POST['order'][$i]['id']));
                    }
                    unset($_POST['order'][$i]['id']);
                    
                    $order['service_id'] = $service->id;
                    $order['quantity'] = $_POST['order'][$i]['quantity'] ? $_POST['order'][$i]['quantity'] : 1;
                    $order['notes'] = $_POST['order'][$i]['notes'];
                    $order['package_id'] = $package_id;
                    
                    if ($pres) {
                        $this->db->where('id', $pres->id)->update('package_orders', $order);
                        $order_ids[] = $pres->id;
                    } else {
                        $order_ids[] = $this->packageorder_model->save($order);
                    }
                }
                $this->db->where('id NOT IN (' . implode(',', $order_ids) . ')', null)->where('package_id', $package_id)->delete('package_orders');
            }
            
            // Save prescription of patient
            $ids = [];
            for ($i = 1; $i <= $_POST['index_row']; $i++) {
                if (!isset($_POST['prescription'][$i])) {
                    continue;
                }
                
                $prescription =  $_POST['prescription'][$i];
                if (!isset($prescription['quantity']) || !$prescription['quantity']) {
                    $prescription['quantity'] = 1;
                    $prescription['time_in_day'] = 1;
                    $prescription['unit_in_time'] = 1;
                }
                
                $prescription['time_in_day'] = (!isset($prescription['time_in_day']) || !$prescription['time_in_day']) ? 1 : $prescription['time_in_day'];
                $prescription['unit_in_time'] = (!isset($prescription['unit_in_time']) || !$prescription['unit_in_time']) ? 1 : $prescription['unit_in_time'];
                
                if ($prescription['drug_name'] && $prescription['quantity'] && $prescription['time_in_day'] && $prescription['unit_in_time']) {
                    $drug = $this->drug_model->findOne(array('LOWER(name)' => strtolower($prescription['drug_name'])));
                    if ($drug) {
                        $pres = null;
                        if (isset($prescription['id']) && $prescription['id']) {
                            $pres = $this->packageprescription_model->findOne(array('id' => $prescription['id']));
                        }
                        unset($prescription['id']);
                        
                        $prescription['drug_id'] = $drug->id;
                        unset($prescription['drug_name']);
                        $prescription['package_id'] = $package_id;
                        
                        if ($pres) {
                            $this->db->where('id', $pres->id)->update('package_prescription', $prescription);
                            $ids[] = $pres->id;
                        } else {
                            $ids[] = $this->packageprescription_model->save($prescription);
                        }
                    } else {
                        echo json_encode(array('error' => 'Loại thuốc ' . $prescription['drug_name'] . ' không có trong danh sách thuốc. Vui lòng thêm vào danh sách.'));
                        exit;
                    }
                }
            }
            $this->db->where('id NOT IN (' . implode(',', $ids) . ')', null)->where('package_id', $package_id)->delete('package_prescription');

            $this->db->trans_complete();
            echo json_encode(array('success' => 'Đã lưu thành công.', 'url' => '/package/index'));
            exit;
        }
        
        echo json_encode(array('error' => 'Chưa có dữ liệu'));
    }
}
