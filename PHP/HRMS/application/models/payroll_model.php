<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of payroll_model
 *
 * @author NaYeM
 */
class Payroll_Model extends MY_Model {

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_add_department_by_id($department_id) {
        $this->db->select('tbl_department.department_name', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->from('tbl_department');
        $this->db->join('tbl_designations', 'tbl_department.department_id = tbl_designations.department_id', 'left');
        $this->db->where('tbl_department.department_id', $department_id);

        $query_result = $this->db->get();
        $result = $query_result->result();

        return $result;
    }

    public function get_emp_salary_list($id = NULL, $designation_id = NULL) {
        $this->db->select('tbl_employee_payroll.*', FALSE);
        $this->db->select('tbl_employee.*', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->select('tbl_department.department_name', FALSE);
        $this->db->from('tbl_employee_payroll');
        $this->db->join('tbl_employee', 'tbl_employee_payroll.employee_id = tbl_employee.employee_id', 'left');
        $this->db->join('tbl_designations', 'tbl_designations.designations_id  = tbl_employee.designations_id', 'left');
        $this->db->join('tbl_department', 'tbl_department.department_id  = tbl_designations.department_id', 'left');
        if (!empty($designation_id)) {
            $this->db->where('tbl_designations.designations_id', $designation_id);
        }
        if (!empty($id)) {
            $this->db->where('tbl_employee_payroll.employee_id', $id);
            $query_result = $this->db->get();
            $result = $query_result->row();
        } else {
            $query_result = $this->db->get();
            $result = $query_result->result();
        }
        return $result;
    }

    public function get_salary_payment_info($employee_id, $payment_date = NULL, $salary_payment_id = NULL) {

        $this->db->select('tbl_salary_payment.*', FALSE);
        $this->db->select('tbl_employee.*', FALSE);
        $this->db->select('tbl_designations.*', FALSE);
        $this->db->select('tbl_department.department_name', FALSE);
        $this->db->from('tbl_salary_payment');
        $this->db->join('tbl_employee', 'tbl_salary_payment.employee_id = tbl_employee.employee_id', 'left');
        $this->db->join('tbl_designations', 'tbl_designations.designations_id  = tbl_employee.designations_id', 'left');
        $this->db->join('tbl_department', 'tbl_department.department_id  = tbl_designations.department_id', 'left');
        if (!empty($salary_payment_id)) {
            $this->db->where("tbl_salary_payment.salary_payment_id", $salary_payment_id);
            $query_result = $this->db->get();
            $result = $query_result->row();
        } else {
            $this->db->where('tbl_salary_payment.employee_id', $employee_id);
            if (!empty($payment_date)) {
                $this->db->where('tbl_salary_payment.payment_for_month', $payment_date);
                $query_result = $this->db->get();
                $result = $query_result->row();
            } else {
                $this->db->order_by("tbl_salary_payment.salary_payment_id", "DESC");
                $query_result = $this->db->get();
                $result = $query_result->result();
            }
        }
        return $result;
    }

}
