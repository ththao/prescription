<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migrate extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $query = $this->db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='migration'");
        $data = $query->result();
        if (empty($data)) {
            $this->db->query('CREATE TABLE migration (version TEXT, apply_time INTEGER);');
        }
        
        $this->load->model('Migration_model');
    }

    public function getLastestMigration()
    {
        $allMigrations = $this->Migration_model->findAll();
        if (count($allMigrations) > 0) {
            $migrationFileName = $allMigrations[count($allMigrations) - 1]->version;
            return explode('_', $migrationFileName)[0];
        } else {
            return 0;
        }

    }

    public function index()
    {
        set_time_limit(0);
        
        $path            = APPPATH . 'migrations';
        $result          = '';
        $totalMigrations = 0;

        foreach (glob($path . '/[0-9]*.sql') as $migration) {
            $arrPath           = explode('/', $migration);
            $migrationFileName = $arrPath[count($arrPath) - 1];
            $migrationIndex    = explode('_', $migrationFileName)[0];

            if ($migrationIndex > $this->getLastestMigration()) {
                $totalMigrations += 1;
                $sqlQuery = file_get_contents($migration);
                $queries  = explode(';', $sqlQuery);

                foreach ($queries as $query) {
                    if (trim($query) != '') {
                        try {
                            $this->db->query($query . ';');
                        } catch (Exception $e) {
                            $result .= $migration . "<br>";
                            $result .= $e->getMessage();
                        }
                    }
                }

                $this->Migration_model->save(['version' => $migrationFileName, 'apply_time' => time()]);
            }
        };
        
        $query = $this->db->select('id, name, address, dob, gender, phone')->from('patient')->get();
        $patients = $query->result();
        
        if ($patients) {
            $used_patients = [];
            foreach ($patients as $patient) {
                if (isset($used_patients[$patient->name])) {
                    if (strtolower($used_patients[$patient->name]['address']) == strtolower($patient->address) || strtolower($used_patients[$patient->name]['phone']) == strtolower($patient->phone)) {
                        $used_patients[$patient->name]['duplicated_ids'][] = $patient->id;
                        continue;
                    }
                }
                
                $used_patients[$patient->name] = ['id' => $patient->id, 'address' => $patient->address, 'dob' => $patient->dob, 'phone' => $patient->phone, 'duplicated_ids' => []];
            }
            
            foreach ($used_patients as $used_patient) {
                if ($used_patient['duplicated_ids']) {
                    $this->db->where('patient_id IN (' . implode(',', $used_patient['duplicated_ids']) . ')', null)->update('diagnostic', ['patient_id' => $used_patient['id']]);
                    //print_r($this->db->last_query());
                    $this->db->where('id IN (' . implode(',', $used_patient['duplicated_ids']) . ')', null)->delete('patient');
                    //print_r($this->db->last_query());
                }
            }
        }
        
        $query = $this->db->select('id')->from('diagnostic_template')->get();
        $templates = $query->result();
        
        foreach ($templates as $template) {
            $this->prescription_by_diagnostic($template->id, 0);
        }

        $this->render('migrate/index', array(
            'result'          => $result,
            'totalMigrations' => $totalMigrations
        ));
    }
    
    public function export()
    {
        $type = 'DRUGS';
        
        
        $data = [];
        if ($type == 'DRUGS') {
            $this->loadModel(['drug_model']);
            $drugs = $this->drug_model->findAll();
            if ($drugs) {
                $data[] = ['DRUGS'];
                $data[] = ['id', 'name', 'unit', 'price', 'note', 'date_created', 'date_updated', 'in_price'];
                foreach ($drugs as $drug) {
                    $data[] = [$drug->id, $drug->name, $drug->unit, $drug->price, $drug->note, $drug->date_created, $drug->date_updated, $drug->in_price];
                }
                $this->array_to_csv_download($data, 'drugs.csv', ',');
                exit();
            }
        }
        if ($type == 'PATIENTS') {
            $this->loadModel(['patient_model']);
            $patients = $this->patient_model->findAll();
            if ($patients) {
                $data[] = ['PATIENTS'];
                $data[] = ['id', 'name', 'dob', 'gender', 'phone', 'address', 'note', 'date_created', 'date_updated'];
                foreach ($patients as $item) {
                    $data[] = [$item->id, $item->name, $item->dob, $item->gender, $item->phone, $item->address, $item->note, $item->date_created, $item->date_updated];
                }
                $this->array_to_csv_download($data, 'patients.csv', ',');
                exit();
            }
        }
        if ($type == 'DIAGNOSTICS') {
            $this->loadModel(['diagnostic_model']);
            $diagnostics = $this->diagnostic_model->findAll();
                if ($diagnostics) {
                $data[] = ['DIAGNOSTICS'];
                $data[] = ['id', 'patient_id', 'diagnostic', 'note', 'date_created'];
                foreach ($diagnostics as $item) {
                    $data[] = [$item->id, $item->patient_id, $item->diagnostic, $item->note, $item->date_created];
                }
                $this->array_to_csv_download($data, 'diagnostics.csv', ',');
                exit();
            }
        }
        if ($type == 'PRESCRIPTIONS') {
            $this->loadModel(['prescription_model']);
            $prescriptions = $this->prescription_model->findAll();
            if ($prescriptions) {
                $data[] = ['PRESCRIPTIONS'];
                $data[] = ['id', 'diagnostic_id', 'drug_id', 'quantity', 'time_in_day', 'unit_in_time', 'date_created', 'unit_price', 'drug_name', 'in_unit_price'];
                foreach ($prescriptions as $item) {
                    $data[] = [$item->id, $item->diagnostic_id, $item->drug_id, $item->quantity, $item->time_in_day, $item->unit_in_time, $item->date_created, $item->unit_price, $item->drug_name, $item->in_unit_price];
                }
                $this->array_to_csv_download($data, 'prescriptions.csv', ',');
                exit();
            }
        }
        if ($type == 'SERVICES') {
            $query = $this->db->select('*')->from('services')->order_by('id', 'ASC')->get();
            $data = $query->result();
            if ($data) {
                $data[] = ['SERVICES'];
                $data[] = ['id', 'service_name', 'notes', 'price', 'date_created', 'date_updated'];
                foreach ($data as $item) {
                    $data[] = [$item->id, $item->service_name, $item->notes, $item->price, $item->date_created, $item->date_updated];
                }
                $this->array_to_csv_download($data, 'services.csv', ',');
                exit();
            }
        }
        if ($type == 'ORDERS') {
            $query = $this->db->select('*')->from('orders')->order_by('id', 'ASC')->get();
            $data = $query->result();
            if ($data) {
                $data[] = ['ORDERS'];
                $data[] = ['id', 'diagnostic_id', 'service_id', 'price', 'date_created', 'notes', 'quantity'];
                foreach ($data as $item) {
                    $data[] = [$item->id, $item->diagnostic_id, $item->service_id, $item->price, $item->date_created, $item->notes, $item->quantity];
                }
                $this->array_to_csv_download($data, 'orders.csv', ',');
                exit();
            }
        }
    }
    
    private function arrayToCsv(array &$fields, $delimiter = ',', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false )
    {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');
        
        $output = array();
        foreach ( $fields as $field ) {
            if ($field === null && $nullToMysqlNull) {
                $output[] = 'NULL';
                continue;
            }
            
            // Enclose fields containing $delimiter, $enclosure or whitespace
            if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
                $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
            }
            else {
                $output[] = $field;
            }
        }
        
        return implode( $delimiter, $output );
    }
    
    private function array_to_csv_download($array, $filename = "export.csv", $delimiter=",") {
        ob_start();
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        
        // open the "output" stream
        $f = fopen('php://output', 'w');
        foreach ($array as $line) {
            $l = $this->arrayToCsv($line, $delimiter, '"', true, false);
            
            fwrite($f, $l.PHP_EOL);
        }
        //to get content length for download progress on frontend
        register_shutdown_function(function() {
            header('Content-Length: ' . ob_get_length());
            ob_end_flush();
        });
    }
}