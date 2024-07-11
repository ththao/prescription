<?php

class MY_Model extends CI_Model
{
    private $tbl_name = "";

    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }

    public function set_table_name($tbl_name)
    {
        $this->tbl_name = $tbl_name;
    }

    public function get_table_name()
    {
        return $this->tbl_name;
    }

    public function save($data = array())
    {
        $this->db->insert($this->tbl_name, $data);

        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->tbl_name, $data);
    }

    public function delete($id)
    {
        $this->db->delete($this->tbl_name, array('id' => $id));
    }

    public function findOne($condition = array())
    {
        $res = $this->db->get_where($this->tbl_name, $condition)->result();
        if ($res) {
            return $res[0];
        }

        return null;
    }

    public function findAll($condition = array())
    {
        $this->db->order_by('id', 'ASC');
        return $this->db->get_where($this->tbl_name, $condition)->result();
    }

    public function all()
    {
        return $this->db->get($this->tbl_name)->result();
    }
}