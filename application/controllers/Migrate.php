<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migrate extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->loadModel(array('patient_model', 'drug_model', 'prescription_model', 'service_model', 'order_model', 'diagnostic_model', 'package_model', 'packageorder_model', 'packageprescription_model'));
    }
    
    public function import()
    {
        if (isset($_POST['submit']) && isset($_FILES['db_backup']) && isset($_FILES['db_backup']['tmp_name']) && $_FILES['db_backup']['tmp_name']) {
            
            try {
                set_time_limit(0);
                ini_set("memory_limit",-1);
                
                $this->db->trans_start();
                
                $handle = fopen($_FILES['db_backup']['tmp_name'], "r");
                
                $run = isset($_POST['run']) && $_POST['run'] ? $_POST['run'] : 'DRUGS';
                $type = '';
                $indexes = [];
                
                $backup_patients = [];
                $backup_drugs = [];
                $backup_diagnostics = [];
                $backup_services = [];
                
                if ($run == 'DIAGNOSTICS') {
                    $query = $this->db->select('id, backup_id')->from('patient')->get();
                    $data = $query->result();
                    if ($data) {
                        foreach ($data as $item) {
                            $backup_ids = explode(',', $item->backup_id);
                            if ($backup_ids) {
                                foreach ($backup_ids as $backup_id) {
                                    if ($backup_id) {
                                        $backup_patients[$backup_id] = $item->id;
                                    }
                                }
                            }
                        }
                    }
                }
                
                if ($run == 'ORDERS') {
                    $query = $this->db->select('id, backup_id')->from('services')->get();
                    $data = $query->result();
                    if ($data) {
                        foreach ($data as $item) {
                            $backup_ids = explode(',', $item->backup_id);
                            if ($backup_ids) {
                                foreach ($backup_ids as $backup_id) {
                                    if ($backup_id) {
                                        $backup_services[$backup_id] = $item->id;
                                    }
                                }
                            }
                        }
                    }
                }
                
                
                if ($run == 'PRESCRIPTIONS' || $run == 'ORDERS') {
                    $query = $this->db->select('id, backup_id')->from('diagnostic')->get();
                    $data = $query->result();
                    if ($data) {
                        foreach ($data as $item) {
                            $backup_ids = explode(',', $item->backup_id);
                            if ($backup_ids) {
                                foreach ($backup_ids as $backup_id) {
                                    if ($backup_id) {
                                        $backup_diagnostics[$backup_id] = $item->id;
                                    }
                                }
                            }
                        }
                    }
                }
                
                if ($run == 'PRESCRIPTIONS') {
                    $query = $this->db->select('id, backup_id')->from('drug')->get();
                    $data = $query->result();
                    if ($data) {
                        foreach ($data as $item) {
                            $backup_ids = explode(',', $item->backup_id);
                            if ($backup_ids) {
                                foreach ($backup_ids as $backup_id) {
                                    if ($backup_id) {
                                        $backup_drugs[$backup_id] = $item->id;
                                    }
                                }
                            }
                        }
                    }
                }
                
                $need_insert = [];
                while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                    if (count($data) == 1) {
                        $type = $data[0];
                        $indexes = [];
                        continue;
                    }
                    
                    if (empty($indexes)) {
                        foreach ($data as $index => $column) {
                            $indexes[$index] = $column;
                        }
                        continue;
                    }
                    
                    if ($run == 'DRUGS' && $type == 'DRUGS' && count($indexes) > 0) {
                        $save = [];
                        foreach ($indexes as $index => $column) {
                            $save[$column] = $data[$index];
                        }
                        $save['date_created'] = $save['date_created'] ? strtotime($save['date_created']) : time();
                        $save['date_updated'] = $save['date_updated'] ? strtotime($save['date_updated']) : null;
                        $save['user_id']      = $this->session->userdata('user_id');
                        $save['removed']      = 0;
                        $save['backup_id']    = $save['id'] . ',';
                        unset($save['id']);
                        
                        $drug = $this->drug_model->findOne(['user_id' => $this->session->userdata('user_id'), 'LOWER(name)' => strtolower($save['name'])]);
                        if (!$drug) {
                            $this->drug_model->save($save);
                        } else {
                            $save['backup_id'] = $drug->backup_id . ',' . $save['backup_id'];
                            $this->drug_model->update($drug->id, $save);
                        }
                    }
                    
                    if ($run == 'SERVICES' && $type == 'SERVICES' && count($indexes) > 0) {
                        $save = [];
                        foreach ($indexes as $index => $column) {
                            $save[$column] = $data[$index];
                        }
                        $save['date_created'] = $save['date_created'] ? strtotime($save['date_created']) : time();
                        $save['date_updated'] = isset($save['date_updated']) && $save['date_updated'] ? strtotime($save['date_updated']) : null;
                        $save['user_id']      = $this->session->userdata('user_id');
                        $save['removed']      = 0;
                        $save['backup_id']    = $save['id'] . ',';
                        unset($save['id']);
                        
                        $service = $this->service_model->findOne(['user_id' => $this->session->userdata('user_id'), 'LOWER(service_name)' => strtolower($save['service_name'])]);
                        if (!$service) {
                            $this->service_model->save($save);
                        } else {
                            $save['backup_id'] = $service->backup_id . ',' . $save['backup_id'];
                            $this->service_model->update($service->id, $save);
                        }
                    }
                    
                    if ($run == 'PATIENTS' && $type == 'PATIENTS' && count($indexes) > 0) {
                        $save = [];
                        foreach ($indexes as $index => $column) {
                            $save[$column] = $data[$index];
                        }
                        $save['date_created'] = $save['date_created'] ? strtotime($save['date_created']) : time();
                        $save['date_updated'] = $save['date_updated'] ? strtotime($save['date_updated']) : null;
                        $save['phone']        = filter_var($save['phone'], FILTER_SANITIZE_NUMBER_INT);
                        $save['removed']      = 0;
                        $save['backup_id']    = $save['id'] . ',';
                        unset($save['id']);
                        
                        $query = $this->db->select('id, backup_id')->from('patient')
                            ->where('LOWER(name) = "' . strtolower($save['name']) . '" AND LOWER(gender) = "' . strtolower($save['gender']) . '" AND (dob = "' . $save['dob'] . '" OR phone = "' . $save['phone'] . '")', null)
                            ->get();
                        $patient = $query->row();
                        if (!$patient) {
                            $this->patient_model->save($save);
                        } else {
                            $save['backup_id'] = $patient->backup_id . ',' . $save['backup_id'];
                            $this->patient_model->update($patient->id, $save);
                        }
                    }
                    
                    if ($run == 'DIAGNOSTICS' && $type == 'DIAGNOSTICS' && count($indexes) > 0) {
                        $save = [];
                        foreach ($indexes as $index => $column) {
                            $save[$column] = $data[$index];
                        }
                        $save['date_created'] = $save['date_created'] ? strtotime($save['date_created']) : time();
                        $save['user_id']      = $this->session->userdata('user_id');
                        $save['removed']      = 0;
                        $save['backup_id']    = $save['id'] . ',';
                        unset($save['id']);
                        
                        if (isset($backup_patients[$save['patient_id']])) {
                            /*$query = $this->db->select('id')->from('diagnostic')
                                ->where('user_id', $this->session->userdata('user_id'))
                                ->where('patient_id', $patient->id)
                                ->where('LOWER(diagnostic) = "' . strtolower($save['diagnostic']) . '"', null)
                                ->where('DATE_FORMAT(FROM_UNIXTIME(date_created), "%Y-%m-%d") = "' . date('Y-m-d', $save['date_created']) . '"', null)
                                ->get();
                            $diagnostic = $query->row();*/
                            $diagnostic = null;
                            
                            if (!$diagnostic) {
                                $save['patient_id'] = $backup_patients[$save['patient_id']];
                                $need_insert[]      = $save;
                            }
                        } else {
                            //print_r($save);
                        }
                        
                        if (count($need_insert) >= 100) {
                            $this->buildDiagnosticQuery($need_insert);
                            $need_insert = [];
                        }
                    }
                    
                    if ($run == 'PRESCRIPTIONS' && $type == 'PRESCRIPTIONS' && count($indexes) > 0) {
                        $save = [];
                        foreach ($indexes as $index => $column) {
                            $save[$column] = $data[$index];
                        }
                        $save['date_created'] = $save['date_created'] ? strtotime($save['date_created']) : time();
                        $save['date_updated'] = isset($save['date_updated']) && $save['date_updated'] ? strtotime($save['date_updated']) : null;
                        $save['user_id']      = $this->session->userdata('user_id');
                        $save['removed']      = 0;
                        $save['backup_id']    = $save['id'];
                        unset($save['id']);
                        
                        if (isset($backup_diagnostics[$save['diagnostic_id']]) && isset($backup_drugs[$save['drug_id']])) {
                            //$query = $this->db->select('id')->from('prescription')->where('user_id', $this->session->userdata('user_id'))->where('diagnostic_id', $diagnostic->id)->where('drug_id', $drug->id)->get();
                            //$prescription = $query->row();
                            $prescription = null;
                            
                            if (!$prescription) {
                                $save['diagnostic_id']   = $backup_diagnostics[$save['diagnostic_id']];
                                $save['drug_id']         = $backup_drugs[$save['drug_id']];
                                $need_insert[]           = $save;
                            }
                        } else {
                            //print_r($save);
                        }
                        
                        if (count($need_insert) >= 100) {
                            $this->buildPrescriptionQuery($need_insert);
                            $need_insert = [];
                        }
                    }
                    
                    if ($run == 'ORDERS' && $type == 'ORDERS' && count($indexes) > 0) {
                        $save = [];
                        foreach ($indexes as $index => $column) {
                            $save[$column] = $data[$index];
                        }
                        $save['date_created'] = $save['date_created'] ? strtotime($save['date_created']) : time();
                        $save['date_updated'] = isset($save['date_updated']) && $save['date_updated'] ? strtotime($save['date_updated']) : null;
                        $save['user_id']      = $this->session->userdata('user_id');
                        $save['removed']      = 0;
                        $save['backup_id']    = $save['id'];
                        unset($save['id']);
                        
                        if (isset($backup_diagnostics[$save['diagnostic_id']]) && isset($backup_services[$save['service_id']])) {
                            $save['diagnostic_id']   = $backup_diagnostics[$save['diagnostic_id']];
                            $save['service_id']      = $backup_services[$save['service_id']];
                            $need_insert[]           = $save;
                        } else {
                            //print_r($save);
                        }
                        
                        if (count($need_insert) >= 100) {
                            $this->buildOrderQuery($need_insert);
                            $need_insert = [];
                        }
                    }
                }
                
                if ($need_insert) {
                    if ($run == 'PRESCRIPTIONS') {
                        $this->buildPrescriptionQuery($need_insert);
                        $need_insert = [];
                    }
                    if ($run == 'ORDERS') {
                        $this->buildOrderQuery($need_insert);
                        $need_insert = [];
                    }
                    if ($run == 'DIAGNOSTICS') {
                        $this->buildDiagnosticQuery($need_insert);
                        $need_insert = [];
                    }
                }
                
                $this->db->trans_complete();
                fclose($handle);
            } catch (Exception $e) {
                print_r($e);
            }
        }
        redirect('about');
    }
    
    public function templates()
    {
        $query = $this->db->select('id, diagnostic')->from('diagnostic')->where('diagnostic_template_id IS NULL', null)->where('user_id', $this->session->userdata('user_id'))->limit(10)->get();
        $diagnostics = $query->result();
        
        if ($diagnostics) {
            foreach ($diagnostics as $diagnostic) {
                $diags = $this->replaceAbbreviations($diagnostic->diagnostic);
                if ($diags) {
                    foreach ($diags as $diag) {
                        $diag = trim($diag);
                        if ($diag != '') {
                            $query = $this->db->select('id, diagnostic')->from('diagnostic_template')->where('LOWER(diagnostic)', strtolower($diag))->get();
                            $diagnostic_template = $query->row();
                            
                            if ($diagnostic_template) {
                                $diagnostic_template_id = $diagnostic_template->id;
                            } else {
                                $this->db->insert('diagnostic_template', ['diagnostic' => $diag]);
                                $diagnostic_template_id = $this->db->insert_id();
                            }
                            
                            $this->prescription_by_diagnostic($diagnostic_template_id, 1);
                        }
                    }
                }
                
                $this->db->where('id', $diagnostic->id)->update('diagnostic', ['diagnostic_template_id' => 1]);
            }
        }
    }
    
    private function buildOrderQuery($data)
    {
        if ($data) {
            $sql = 'INSERT INTO orders(backup_id, user_id, diagnostic_id, service_id, quantity, notes, price, date_created, date_updated, removed) VALUES ';
            
            foreach ($data as $index => $item) {
                $sql .= '("' . $item['backup_id'] . '", "' . $item['user_id'] . '", "' . $item['diagnostic_id'] . '", "' . $item['service_id'] . '", "' . $item['quantity'] . '", "' . 
                    $item['notes'] . '", "' . $item['price'] . '", "' . (isset($item['date_created']) ? $item['date_created'] : time()) . '", "' . 
                    (isset($item['date_updated']) ? $item['date_updated'] : '') . '", "' . $item['removed'] . '")';
                if ($index < count($data) - 1) {
                    $sql .= ',';
                }
            }
            
            $this->db->query($sql);
        }
    }
    
    private function buildDiagnosticQuery($data)
    {
        if ($data) {
            $sql = 'INSERT INTO diagnostic(backup_id, user_id, patient_id, diagnostic, note, date_created, date_updated, removed) VALUES ';
            
            foreach ($data as $index => $item) {
                $sql .= '("' . $item['backup_id'] . '", "' . $item['user_id'] . '", "' . $item['patient_id'] . '", "' . $item['diagnostic'] . '", "' . $item['note'] . '", "' . 
                    (isset($item['date_created']) ? $item['date_created'] : time()) . '", "' . (isset($item['date_updated']) ? $item['date_updated'] : '') . '", "' . $item['removed'] . '")';
                if ($index < count($data) - 1) {
                    $sql .= ',';
                }
            }
            
            $this->db->query($sql);
        }
    }
    
    private function buildPrescriptionQuery($data)
    {
        if ($data) {
            $sql = 'INSERT INTO prescription(backup_id, user_id, diagnostic_id, drug_id, quantity, time_in_day, unit_in_time, unit_price, drug_name, in_unit_price, notes, date_created, date_updated, removed) VALUES ';
            
            foreach ($data as $index => $item) {
                $sql .= '("' . $item['backup_id'] . '", "' . $item['user_id'] . '", "' . $item['diagnostic_id'] . '", "' . $item['drug_id'] . '", "' . $item['quantity'] . '", "' . 
                    $item['time_in_day'] . '", "' . $item['unit_in_time'] . '", "' . $item['unit_price'] . '", "' . $item['drug_name'] . '", "' . 
                    $item['in_unit_price'] . '", "' . (isset($item['notes']) ? $item['notes'] : '') . '", "' . 
                    (isset($item['date_created']) ? $item['date_created'] : '') . '", "' . (isset($item['date_updated']) ? $item['date_updated'] : '') . '", "' . $item['removed'] . '")';
                if ($index < count($data) - 1) {
                    $sql .= ',';
                }
            }
            
            $this->db->query($sql);
        }
    }
}