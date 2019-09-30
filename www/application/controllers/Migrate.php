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

        $this->render('migrate/index', array(
            'result'          => $result,
            'totalMigrations' => $totalMigrations
        ));
    }
}