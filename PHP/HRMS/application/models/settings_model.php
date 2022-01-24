<?php

class Settings_Model extends MY_Model {

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_working_days() {
        $this->db->select('tbl_working_days.*', FALSE);
        $this->db->select('tbl_days.day', FALSE);
        $this->db->from('tbl_working_days');
        $this->db->join('tbl_days', 'tbl_working_days.day_id = tbl_days.day_id', 'left');
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_holiday_list_by_date($start_date, $end_date) {
        $this->db->select('tbl_holiday.*', FALSE);
        $this->db->from('tbl_holiday');
        $this->db->where('start_date >=', $start_date);
        $this->db->where('end_date <=', $end_date);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function delete_all($table) {
        $this->db->empty_table($table);
    }

    public function select_general_info() {
        $this->db->select('*');
        $this->db->from('tbl_gsettings');

        $query_result = $this->db->get();
        $result = $query_result->row();
        return $result;
    }
    

}
