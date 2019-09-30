<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends My_Controller
{   
    public function __construct()
    {
        parent::__construct();

        $this->loadModel(array('drug_model'));
        $this->load->library('pagination');
        $this->load->helper('url');
    }

	public function daily()
	{
	    $date = $this->input->get('date') ? $this->input->get('date') : date('d-m-Y');
	    $drug_name = $this->input->get('drug_name') ? $this->input->get('drug_name') : '';
        $data = $this->getReportData(date('Y-m-d', strtotime($date)), $drug_name);

        $this->render('report/daily', array('data' => $data, 'date' => $date, 'drug_name' => $drug_name));
	}

	public function monthly()
	{
	    $month = $this->input->get('month') ? $this->input->get('month') : date('m');
	    $year = $this->input->get('year') ? $this->input->get('year') : date('Y');
	    $drug_name = $this->input->get('drug_name') ? $this->input->get('drug_name') : '';
        $data = $this->getReportData($year . '-' . ($month < 10 ? '0' . intval($month) : $month), $drug_name);

        $this->render('report/monthly', array('data' => $data, 'month' => $month, 'year' => $year, 'drug_name' => $drug_name));
	}
	
	private function getReportData($date, $drug_name)
	{
	    $this->db->select('prescription.drug_name, SUM(prescription.quantity) AS drug_quantity, SUM(COALESCE(in_unit_price, in_price) * quantity) AS in_price, SUM(COALESCE(unit_price, price) * quantity) AS price');
	    $this->db->from('prescription');
	    $this->db->join('drug', 'prescription.drug_id = drug.id', 'LEFT OUTER');
	    if ($drug_name) {
	       $this->db->like('prescription.drug_name', $drug_name);
	    }
	    if ($date) {
	        $this->db->like('prescription.date_created', $date);
	    }
	    $this->db->group_by('prescription.drug_name');
	    $this->db->order_by('prescription.drug_name');
	    
	    $query = $this->db->get(); 
	    return $query->result();
	}
}