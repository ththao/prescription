<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Drug extends My_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->loadModel(['drug_model', 'drug_template_model', 'ingredient_model']);
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
        
        $response = $this->prepare_data();
        $response['models'] = $data;
        $response['search'] = '';

        $this->render('drug/index', $response);
	}
	
	private function prepare_data()
	{
	    $query = $this->db->select('id, name')->from('drug_template')->get();
	    $drugs = $query->result();
	    $drug_names = [];
	    if ($drugs) {
	        foreach ($drugs as $drug) {
	            $drug_names[] = ['value' => $drug->name, 'label' => $drug->name, 'id' => $drug->id];
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
	    
	    
	    $ingredients = $this->ingredient_model->findAll();
	    $ingredient_names = [];
	    if ($ingredients) {
	        foreach ($ingredients as $ingredient) {
	            $ingredient_names[] = ['id' => $ingredient->id, 'value' => $ingredient->ingredient_name, 'label' => $ingredient->ingredient_name];
	        }
	    }
	    $js_ingredient_names = json_encode($ingredient_names);
	    
	    return ['drug_names' => $js_drug_names, 'drug_templates' => $drug_names, 'my_drug_names' => $js_my_drug_names, 'ingredient_names' => $js_ingredient_names];
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
	    
	    $response = $this->prepare_data();
	    $response['models'] = $data;
	    $response['search'] = $search;
	    
	    $this->render('drug/index', $response);
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

    public function create()
    {
        if (isset($_POST) && !empty($_POST['name']) && !empty($_POST['unit'])) {
            $drug = $this->drug_model->findOne(['name' => $_POST['name'], 'user_id' => $this->session->userdata('user_id'), 'removed' => 0]);
            if (!$drug) {
                $drug_template = $this->drug_template_model->findOne(['name' => $_POST['name']]);
                
                $data['user_id'] = $this->session->userdata('user_id');
                $data['name'] = $_POST['name'];
                $data['unit'] = $_POST['unit'];
                $data['in_price'] = $_POST['in_price'];
                $data['price'] = $_POST['price'];
                $data['note'] = $_POST['note'];
                $data['date_created'] = time();
                if ($drug_template) {
                    $data['drug_template_id'] = $drug_template->id;
                    if (!$data['note']) {
                        $data['note'] = $drug_template->description;
                    }
                    if (!$data['unit']) {
                        $data['unit'] = $drug_template->unit;
                    }
                }
                if (isset($_POST['category_name']) && $_POST['category_name']) {
                    $query = $this->db->select('id')->from('drug_category')->where('LOWER(category_name) = LOWER("' . $_POST['category_name'] . '")', null)->get();
                    if ($category = $query->row()) {
                        $data['drug_category_id'] = $category->id;
                    } else {
                        $this->db->insert('drug_category', ['category_name' => $_POST['category_name']]);
                        $data['drug_category_id'] = $this->db->insert_id();
                    }
                } else {
                    if ($drug_template) {
                        $data['drug_category_id'] = $drug_template->drug_category_id;
                    }
                }
                
                if (!$this->drug_model->save($data)) {
                    echo json_encode(array('error' => 'Có lỗi. Vui lòng thử lại.'));
                    return;
                }
                $drug_id = $this->db->insert_id();
                
                if (isset($_POST['ingredients']) && $_POST['ingredients']) {
                    $ingredients = explode(',', $_POST['ingredients']);
                    foreach ($ingredients as $ingredient) {
                        $this->add_or_update_ingredient(trim($ingredient), $drug_id);
                    }
                } else {
                    if ($drug_template) {
                        $this->link_template_ingredients($drug_id, $drug_template->id);
                    }
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
            $this->drug_model->update($id, ['removed' => 1, 'date_updated' => time()]);
        }
        redirect("/drug/index");
    }
    
    public function ingredients()
    {
        $drug = $this->drug_model->findOne(['id' => $_POST['drug_id'], 'user_id' => $this->session->userdata('user_id')]);
        
        if ($drug) {
            $this->db->distinct()->select('drug.id AS drug_id, ingredient.id AS ingredient_id, ingredient.ingredient_name');
            $this->db->from('drug');
            $this->db->join('drug_ingredients', 'drug_ingredients.drug_id = drug.id', 'INNER');
            $this->db->join('ingredient', 'ingredient.id = drug_ingredients.ingredient_id', 'INNER');
            $this->db->where('drug.id', $drug->id);
            $this->db->order_by('ingredient.ingredient_name');
            $query = $this->db->get();
            
            $ingredients = $query->result();
            
            $html = $this->load->view('drug/ingredients', ['drug' => $drug, 'ingredients' => $ingredients], true);
            
            echo json_encode(['status' => 1, 'drug_id' => $drug->id, 'html' => $html]);
            return;
        }
        
        echo json_encode(['status' => 0]);
        return;
    }
    
    private function add_or_update_ingredient($ingredient_name, $drug_id = null)
    {
        $query = $this->db->select('id')->from('ingredient')->where('LOWER(ingredient_name) = LOWER("' . $ingredient_name . '")', null)->get();
        $ingredient = $query->row();
        
        if (!$ingredient) {
            $this->db->insert('ingredient', ['ingredient_name' => $ingredient_name]);
            $ingredient_id = $this->db->insert_id();
        } else {
            $ingredient_id = $ingredient->id;
        }
        
        if ($drug_id) {
            $query = $this->db->select('id')->from('drug_ingredients')->where('drug_id', $drug_id)->where('ingredient_id', $ingredient_id)->get();
            if (!$query->row()) {
                $this->db->insert('drug_ingredients', ['ingredient_id' => $ingredient_id, 'drug_id' => $drug_id]);
            }
        }
        
        return $ingredient_id;
    }
    
    public function add_ingredient()
    {
        
        $drug = $this->drug_model->findOne(['id' => $_POST['drug_id'], 'user_id' => $this->session->userdata('user_id')]);
        
        if ($drug) {
            $ingredient_id = $this->add_or_update_ingredient($_POST['ingredient_name'], $drug->id);
            
            $html = '
                <tr>
                    <td align="left" style="padding: 5px;">' . $_POST['ingredient_name'] . '</td>
                	<td align="center" style="padding: 5px;">
                	<span class="glyphicon glyphicon-remove remove-drug-ingredient" title="Xóa thành phần" drug_id="' . $drug->id . '" ingredient_id="' . $ingredient_id . '" style="color: red; cursor: pointer; "></span>
                	</td>
                </tr>
            ';
            
            echo json_encode(['status' => 1, 'html' => $html]);
            return;
        }
        
        echo json_encode(['status' => 0]);
        return;
    }
    
    public function remove_ingredient()
    {
        
        $drug = $this->drug_model->findOne(['id' => $_POST['drug_id'], 'user_id' => $this->session->userdata('user_id')]);
        
        if ($drug) {
            $this->db->where('drug_id', $drug->id)->where('ingredient_id', $_POST['ingredient_id'])->delete('drug_ingredients');
            
            echo json_encode(['status' => 1]);
            return;
        }
        
        echo json_encode(['status' => 0]);
        return;
    }
    
    public function get_template()
    {
        $query = $this->db->select('drug_template.id, drug_template.name, drug_template.unit, drug_template.description, drug_category.category_name')
            ->from('drug_template')
            ->join('drug_category', 'drug_template.drug_category_id = drug_category.id', 'LEFT OUTER')
            ->where('drug_template.id', $_POST['id'])->get();
        $drug_template = $query->row_array();
        
        if ($drug_template) {
            $query = $this->db->select('ingredient.ingredient_name')->from('drug_template_ingredients')->join('ingredient', 'ingredient.id = drug_template_ingredients.ingredient_id', 'INNER')->where('drug_template_id', $drug_template['id'])->get();
            $ingredients = $query->result();
            
            $drug_template['ingredients'] = '';
            if ($ingredients) {
                foreach ($ingredients as $ingredient) {
                    $drug_template['ingredients'] .= ($drug_template['ingredients'] ? ', ' : '') . $ingredient->ingredient_name;
                }
            }
            $drug_template['status'] = 1;
            echo json_encode($drug_template);
            return;
        }
        
        echo json_encode(['status' => 0]);
        return;
    }
    
    public function link_with_template()
    {
        $drug = $this->drug_model->findOne(['id' => $_POST['drug_id'], 'user_id' => $this->session->userdata('user_id')]);
        $drug_template = $this->drug_template_model->findOne(['id' => $_POST['drug_template_id']]);
        
        if ($drug && $drug_template) {
            $this->db->where('id', $drug->id)->update('drug', ['drug_template_id' => $drug_template->id, 'unit' => $drug_template->unit, 'note' => $drug_template->description, 'date_updated' => time(), 'removed' => 0]);
            
            $this->link_template_ingredients($drug->id, $drug_template->id);
        }
        
        echo json_encode(['status' => 1]);
        return;
    }
    
    private function link_template_ingredients($drug_id, $drug_template_id)
    {
        $query = $this->db->select('id, ingredient_id')->from('drug_template_ingredients')->where('drug_template_id', $drug_template_id)->get();
        if ($ingredients = $query->result()) {
            foreach ($ingredients as $ingredient) {
                $query = $this->db->select('id')->from('drug_ingredients')->where('drug_id', $drug_id)->where('ingredient_id', $ingredient->id)->get();
                if (!$query->row()) {
                    $this->db->insert('drug_ingredients', ['ingredient_id' => $ingredient->ingredient_id, 'drug_id' => $drug_id]);
                }
            }
        }
    }
}