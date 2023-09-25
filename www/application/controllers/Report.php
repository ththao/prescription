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
	
	private function getReportData($date, $search_name)
	{
	    
	    $where1 = '';
	    $where2 = '';
	    if ($search_name) {
	        $where1 .= ' WHERE prescription.drug_name LIKE "%' . $search_name . '%"';
	        $where2 .= ' WHERE services.service_name LIKE "%' . $search_name . '%"';
	    }
	    if ($date) {
	        $where1 .= ($where1 ? ' AND' : ' WHERE') . ' prescription.date_created LIKE "%' . $date . '%"';
	        $where2 .= ($where2 ? ' AND' : ' WHERE') . ' orders.date_created LIKE "%' . $date . '%"';
	    }
	    
	    if (SERVICES == 'ON') {
    	    $sql = '
                SELECT name, quantity, in_price, price FROM
                (
                SELECT prescription.drug_name AS name, SUM(prescription.quantity) AS quantity, SUM(COALESCE(prescription.unit_price, drug.price) * prescription.quantity) AS in_price, SUM(COALESCE(prescription.unit_price, drug.price) * quantity) AS price
                FROM prescription LEFT OUTER JOIN drug ON prescription.drug_id = drug.id' . $where1 . '
                GROUP BY LOWER(prescription.drug_name)
                UNION
                SELECT services.service_name AS name, SUM(orders.quantity) AS quantity, 0 AS in_price, SUM(COALESCE(orders.price, services.price) * orders.quantity) AS price
                FROM orders LEFT OUTER JOIN services ON orders.service_id = services.id' . $where2 . '
                GROUP BY LOWER(services.service_name)
                ) report
                ORDER BY LOWER(report.name)
            ';
	    } else {
	        $sql = '
                SELECT prescription.drug_name AS name, SUM(prescription.quantity) AS quantity, SUM(COALESCE(prescription.unit_price, drug.price) * prescription.quantity) AS in_price, SUM(COALESCE(prescription.unit_price, drug.price) * quantity) AS price
                FROM prescription LEFT OUTER JOIN drug ON prescription.drug_id = drug.id' . $where1 . '
                GROUP BY LOWER(prescription.drug_name)
                ORDER BY LOWER(prescription.drug_name)
            ';
	    }
	    
	    $query = $this->db->query($sql);
	    return $query->result();
	}
}