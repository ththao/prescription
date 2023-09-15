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
}