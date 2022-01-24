<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of emp_model
 *
 * @author nayem
 */
class Emp_Model extends MY_Model {

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function all_emplyee_info($id = NULL) {
        $this->db->select('tbl_employee.*', FALSE);
        $this->db->select('tbl_employee_bank.*', FALSE);
        $this->db->select('tbl_employee_document.*', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->select('tbl_department.department_name', FALSE);
        $this->db->select('countries.countryName', FALSE);
        $this->db->from('tbl_employee');
        $this->db->join('tbl_employee_bank', 'tbl_employee_bank.employee_id = tbl_employee.employee_id', 'left');
        $this->db->join('tbl_employee_document', 'tbl_employee_document.employee_id  = tbl_employee.employee_id', 'left');
        $this->db->join('tbl_designations', 'tbl_designations.designations_id  = tbl_employee.designations_id', 'left');
        $this->db->join('tbl_department', 'tbl_department.department_id  = tbl_designations.department_id', 'left');
        $this->db->join('countries', 'countries.idCountry  = tbl_employee.country_id', 'left');
        if (!empty($id)) {
            $this->db->where('tbl_employee.employee_id', $id);
            $query_result = $this->db->get();
            $result = $query_result->row();
        } else {
            $query_result = $this->db->get();
            $result = $query_result->result();
        }
        if (!empty($id)) {
            $this->db->select('tbl_employee.nationality', FALSE);
            $this->db->select('countries.countryName', FALSE);
            $this->db->from('tbl_employee');
            $this->db->join('countries', 'countries.idCountry  =  tbl_employee.nationality ', 'left');
            $query_nationality = $this->db->get();
            $nationality = $query_nationality->row();
            if (!empty($nationality)) {
                $result->nationality = $nationality->countryName;
            }
        }

        return $result;
    }

    public function get_all_notice($limit = NULL) {
        $this->db->select('tbl_notice.*', FALSE);
        $this->db->from('tbl_notice');
        if (!empty($limit)) {
            $this->db->limit('5');
        }
        $this->db->where('flag', 1);
        $this->db->order_by("notice_id", "desc");
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_all_events() {
        $this->db->select('*');
        $this->db->from('tbl_holiday');
        $this->db->limit('7');
        $this->db->order_by("start_date", "desc");
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_all_awards($id = NULL) {
        $this->db->select('tbl_employee_award.*', FALSE);
        $this->db->select('tbl_employee.*', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->select('tbl_department.department_name', FALSE);
        $this->db->from('tbl_employee_award');
        $this->db->join('tbl_employee', 'tbl_employee_award.employee_id  = tbl_employee.employee_id', 'left');
        $this->db->join('tbl_designations', 'tbl_designations.designations_id  = tbl_employee.designations_id', 'left');
        $this->db->join('tbl_department', 'tbl_department.department_id  = tbl_designations.department_id', 'left');
        $this->db->order_by("award_date", "desc");
        if (!empty($id)) {
            $this->db->where('tbl_employee_award.employee_award_id', $id);
            $query_result = $this->db->get();
            $result = $query_result->row();
        } else {
            $query_result = $this->db->get();
            $result = $query_result->result();
        }

        return $result;
    }

//    public function get_employee_award_info($id) {
//        $this->db->select('tbl_employee_award.*', FALSE);
//        $this->db->select('tbl_employee.*', FALSE);
//        $this->db->select('tbl_designations.*', FALSE);
//        $this->db->select('tbl_department.department_name', FALSE);
//        $this->db->from('tbl_employee_award');
//        $this->db->join('tbl_employee', 'tbl_employee_award.employee_id  = tbl_employee.employee_id', 'left');
//        $this->db->join('tbl_designations', 'tbl_designations.designations_id  = tbl_employee.designations_id', 'left');
//        $this->db->join('tbl_department', 'tbl_department.department_id  = tbl_designations.department_id', 'left');
//        $this->db->where('tbl_employee_award.employee_award_id', $id);
//        $query_result = $this->db->get();
//        $result = $query_result->row();
//        return $result;
//    }

    public function get_all_leave_applied($employee_id) {
        $this->db->select('tbl_application_list.*', FALSE);
        $this->db->select('tbl_leave_category.*', FALSE);
        $this->db->from('tbl_application_list');
        $this->db->join('tbl_leave_category', 'tbl_leave_category.leave_category_id  = tbl_application_list.leave_category_id', 'left');
        $this->db->where('tbl_application_list.employee_id', $employee_id);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_approved_leave($employee_id) {
        $this->db->select('tbl_application_list.*', FALSE);
        $this->db->select('tbl_leave_category.*', FALSE);
        $this->db->from('tbl_application_list');
        $this->db->join('tbl_leave_category', 'tbl_leave_category.leave_category_id  = tbl_application_list.leave_category_id', 'left');
        $this->db->where('tbl_application_list.employee_id', $employee_id);
        $this->db->where('tbl_application_list.application_status', 2);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_total_attendace_by_date($start_date, $end_date, $employee_id) {
        $this->db->select('tbl_attendance.*', FALSE);
        $this->db->from('tbl_attendance');
        $this->db->where('employee_id', $employee_id);
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_public_holiday($start_date, $end_date) {
        $this->db->select('tbl_holiday.*', FALSE);
        $this->db->from('tbl_holiday');        
        $this->db->where('start_date >=', $start_date);
        $this->db->where('end_date <=', $end_date);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

}
