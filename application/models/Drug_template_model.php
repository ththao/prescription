<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Drug_template_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('drug_template');

        // Call the CI_Model constructor
        parent::__construct();
    }
}