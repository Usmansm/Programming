<?php

/**
 * Description of employee_model
 *
 * @author NaYeM
 */
class Employee_Model extends MY_Model {

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

    public function get_employee_award_by_id($id=NULL) {

        $this->db->select('tbl_employee_award.*', FALSE);
        $this->db->select('tbl_employee.*', FALSE);
        $this->db->from('tbl_employee_award');
        $this->db->join('tbl_employee', 'tbl_employee_award.employee_id = tbl_employee.employee_id', 'left');
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

}
