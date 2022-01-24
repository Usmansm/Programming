<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of attendance_model
 *
 * @author NaYeM
 */
class Attendance_Model extends MY_Model {

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_employee_id_by_dept_id($department_id) {
        $this->db->select('tbl_employee.*', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->select('tbl_department.*', FALSE);
        $this->db->from('tbl_employee');
        $this->db->join('tbl_designations', 'tbl_designations.designations_id = tbl_employee.designations_id', 'left');
        $this->db->join('tbl_department', 'tbl_department.department_id = tbl_designations.department_id', 'left');
        $this->db->where('tbl_department.department_id', $department_id);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function attendance_report_by_empid($employee_id = null, $sdate = null, $flag = NULL) {

        $this->db->select('tbl_attendance.date,tbl_attendance.attendance_status', FALSE);
        $this->db->select('tbl_employee.first_name, tbl_employee.last_name ', FALSE);
        $this->db->from('tbl_attendance');
        $this->db->join('tbl_employee', 'tbl_attendance.employee_id  = tbl_employee.employee_id', 'left');
        $this->db->where('tbl_attendance.employee_id', $employee_id);
        $this->db->where('tbl_attendance.date', $sdate);
        $query_result = $this->db->get();
        $result = $query_result->result();

        if (empty($result)) {
            $val['attendance_status'] = $flag;
            $val['date'] = $sdate;
            $result[] = (object) $val;
        } else {
            if ($result[0]->attendance_status == 0) {
                if ($flag == 'H') {
                    $result[0]->attendance_status = 'H';
                }
            }
        }


        return $result;
    }

}
