<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of payroll
 *
 * @author NaYeM
 */
class Payroll extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('payroll_model');
    }

    public function manage_salary_details($id = NULL, $designations_id = NULL) {
        $data['title'] = "Manage Salery Details";
        // retrive all data from department table
        $this->payroll_model->_table_name = "tbl_department"; //table name
        $this->payroll_model->_order_by = "department_id";
        $all_dept_info = $this->payroll_model->get();
        // get all department info and designation info
        foreach ($all_dept_info as $v_dept_info) {
            $data['all_department_info'][$v_dept_info->department_name] = $this->payroll_model->get_add_department_by_id($v_dept_info->department_id);
        }

        $flag = $this->input->post('sbtn', TRUE);
        if (!empty($flag) || !empty($id)) { // check employee id is empty or not 
            $data['flag'] = 1;
            if (!empty($id)) {
                $data['employee_id'] = $id;
                $data['designations_id'] = $designations_id;
            } else {
                $data['employee_id'] = $this->input->post('employee_id', TRUE);
                $data['designations_id'] = $this->input->post('designations_id', TRUE);
            }
            // get employee salary info by employee id and check 
            $data['emp_salary'] = $this->payroll_model->get_emp_salary_list($data['employee_id']);
            // get all employee info by designation id
            $this->payroll_model->_table_name = 'tbl_employee';
            $this->payroll_model->_order_by = 'designations_id';
            $data['employee_info'] = $this->payroll_model->get_by(array('designations_id' => $data['designations_id']), FALSE);
        }
        $data['subview'] = $this->load->view('admin/payroll/manage_salary_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_salary_details($id = NULL) {
        // inout data
        $data = $this->payroll_model->array_from_post(array('employment_type', 'basic_salary', 'house_rent_allowance', 'medical_allowance',
            'special_allowance', 'fuel_allowance', 'phone_bill_allowance', 'other_allowance', 'provident_fund', 'tax_deduction', 'other_deduction', 'employee_id'));
        // save into tbl employee payroll
        $this->payroll_model->_table_name = "tbl_employee_payroll"; // table name
        $this->payroll_model->_primary_key = "payroll_id"; // $id
        $this->payroll_model->save($data, $id);

        $type = 'success';
        $message = 'Salary Details Information Successfully Save';
        set_message($type, $message);
        redirect('admin/payroll/employee_salary_list');
    }

    public function employee_salary_list() {
        $data['title'] = "Employee Salery Details";
        // get all employee salary info  
        $data['emp_salary_info'] = $this->payroll_model->get_emp_salary_list();
        $data['subview'] = $this->load->view('admin/payroll/employee_salary_list', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function view_salary_details($id) {
        $data['title'] = "View Salery Details";
        // get all employee salary info   by id
        $data['emp_salary_info'] = $this->payroll_model->get_emp_salary_list($id);
        $data['subview'] = $this->load->view('admin/payroll/employee_salary_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function make_pdf($id) {
        $data['title'] = "View Salery Details";
        // get all employee salary info  by id
        $data['emp_salary_info'] = $this->payroll_model->get_emp_salary_list($id);
        $viewfile = $this->load->view('admin/payroll/employee_salary_pdf', $data, TRUE);
        $this->load->helper('dompdf');
        pdf_create($viewfile, 'Salary Details - ' . $data['emp_salary_info']->first_name . ' ' . $data['emp_salary_info']->last_name);
    }

    public function make_payment($id = NULL) {
        $data['title'] = "Make Payment";
        // retrive all data from department table
        $this->payroll_model->_table_name = "tbl_department"; //table name
        $this->payroll_model->_order_by = "department_id";
        $all_dept_info = $this->payroll_model->get();
        // get all department info and designation info
        foreach ($all_dept_info as $v_dept_info) {
            $data['all_department_info'][$v_dept_info->department_name] = $this->payroll_model->get_add_department_by_id($v_dept_info->department_id);
        }

        $flag = $this->input->post('sbtn', TRUE);
        if (!empty($flag)) { // check employee id is empty or not 
            $data['flag'] = 1;
            if (!empty($id)) {
                $data['employee_id'] = $id;
            } else {
                $data['employee_id'] = $this->input->post('employee_id', TRUE);
                $data['designations_id'] = $this->input->post('designations_id', TRUE);
                $data['payment_date'] = $this->input->post('payment_date', TRUE);
            }
            // check  existing salary payment information if exist or not
            // by employee id and payment date
            $data['check_salary_payment'] = $this->payroll_model->get_salary_payment_info($data['employee_id'], $data['payment_date']);

            // get employee salary info by employee id and check 
            $check_salary_info = $this->payroll_model->get_emp_salary_list($data['employee_id']);
            if (!empty($check_salary_info)) {
                $data['emp_salary_info'] = $check_salary_info;
            } else {
                // get all employee info by designation id
                $this->payroll_model->_table_name = 'tbl_employee';
                $this->payroll_model->_order_by = 'employee_id';
                $employee_info = $this->payroll_model->get_by(array('employee_id' => $data['employee_id']), TRUE);

                $type = 'error';
                $message = 'You Did not Set Salary Details For <strong style="color:#000000">' .$employee_info->first_name . ' ' . $employee_info->last_name . '</strong> Please Set And Make Payment !';
                set_message($type, $message);
                redirect('admin/payroll/manage_salary_details');
            }

            // get award info by employee id and payment date
            $this->payroll_model->_table_name = 'tbl_employee_award';
            $this->payroll_model->_order_by = 'employee_id';
            $data['award_info'] = $this->payroll_model->get_by(array('employee_id' => $data['employee_id'], 'award_date' => $data['payment_date']), FALSE);

            // get all employee info by designation id
            $this->payroll_model->_table_name = 'tbl_employee';
            $this->payroll_model->_order_by = 'designations_id';
            $data['employee_info'] = $this->payroll_model->get_by(array('designations_id' => $data['designations_id']), FALSE);

            // get payment history by employee id
            $data['payment_history'] = $this->payroll_model->get_salary_payment_info($data['employee_id']);
        }

        $data['subview'] = $this->load->view('admin/payroll/make_payment', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function get_payment($id = NULL) {

        // input data 
        $data = $this->payroll_model->array_from_post(array('payment_for_month', 'fine_deduction', 'payment_type', 'comments'));
        $data['employee_id'] = $this->input->post('employee_id', TRUE);
        //get employee salary info by employee id
        $emp_salary_info = $this->payroll_model->get_emp_salary_list($data['employee_id']);
        // set all info individualy
        $data['basic_salary'] = $emp_salary_info->basic_salary;
        $data['house_rent_allowance'] = $emp_salary_info->house_rent_allowance;
        $data['medical_allowance'] = $emp_salary_info->medical_allowance;
        $data['special_allowance'] = $emp_salary_info->special_allowance;
        $data['fuel_allowance'] = $emp_salary_info->fuel_allowance;
        $data['phone_bill_allowance'] = $emp_salary_info->phone_bill_allowance;
        $data['other_allowance'] = $emp_salary_info->other_allowance;
        $data['tax_deduction'] = $emp_salary_info->tax_deduction;
        $data['provident_fund'] = $emp_salary_info->provident_fund;
        $data['other_deduction'] = $emp_salary_info->other_deduction;

        // get award amount by array and get total amount by foreach query
        $total = 0;
        $award = $this->input->post('award', TRUE);
        if (!empty($award)) {
            foreach ($award as $v_award) {
                $total+=$v_award;
            }
        }
        $data['award_amount'] = $total;
        // save into tbl employee paymenet
        $this->payroll_model->_table_name = "tbl_salary_payment"; // table name
        $this->payroll_model->_primary_key = "salary_payment_id"; // $id
        $this->payroll_model->save($data, $id);

        $type = 'success';
        $message = 'Payment Information Successfully Updated !';
        set_message($type, $message);
        redirect('admin/payroll/make_payment');
    }

    public function salary_payment_details($salary_payment_id) {
        $data['title'] = "Manage Salery Details";
        $data['salary_payment_info'] = $this->payroll_model->get_salary_payment_info($emp_id = NULL, $payment_date = NULL, $salary_payment_id);
        $data['subview'] = $this->load->view('admin/payroll/salary_payment_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function salary_payment_info_pdf($employee_id) {
        // get all employee salary info  by id
        $data['emp_salary_info'] = $this->payroll_model->get_salary_payment_info($employee_id);
        $viewfile = $this->load->view('admin/payroll/salary_payment_pdf', $data, TRUE);
        $this->load->helper('dompdf');
        pdf_create($viewfile, 'Salary Details - ' . $data['emp_salary_info']->first_name . ' ' . $data['emp_salary_info']->last_name);
    }

    public function payment_history_pdf($employee_id) {
        $data['title'] = "Manage Salery Details";
        // get employee salary info by employee id and check 
        $data['emp_salary_info'] = $this->payroll_model->get_emp_salary_list($employee_id);

        // get payment history by employee id
        $data['payment_history'] = $this->payroll_model->get_salary_payment_info($employee_id);

        $viewfile = $this->load->view('admin/payroll/payment_history_pdf', $data, TRUE);
        $this->load->helper('dompdf');
        pdf_create($viewfile, 'Payment History - ' . $data['emp_salary_info']->first_name . ' ' . $data['emp_salary_info']->last_name);
    }

    public function generate_payslip() {
        $data['title'] = "Generate Payslip";
        // retrive all data from department table
        $this->payroll_model->_table_name = "tbl_department"; //table name
        $this->payroll_model->_order_by = "department_id";
        $all_dept_info = $this->payroll_model->get();
        // get all department info and designation info
        foreach ($all_dept_info as $v_dept_info) {
            $data['all_department_info'][$v_dept_info->department_name] = $this->payroll_model->get_add_department_by_id($v_dept_info->department_id);
        }
        $flag = $this->input->post('sbtn', TRUE);
        if (!empty($flag)) { // check employee id is empty or not 
            $data['flag'] = 1;
            $data['designations_id'] = $this->input->post('designations_id', TRUE);
            $data['payment_date'] = $this->input->post('payment_date', TRUE);

            // get all employee info by designation id            
            $data['employee_info'] = $this->payroll_model->get_emp_salary_list($emp = NULL, $data['designations_id']);

            if (!empty($data['employee_info'])) {
                foreach ($data['employee_info'] as $v_emp_info) {
                    // check  existing salary payment information if exist or not
                    // by employee id and payment date
                    $data['check_salary_payment'][] = $this->payroll_model->get_salary_payment_info($v_emp_info->employee_id, $data['payment_date']);
                }
            }
        }

        $data['subview'] = $this->load->view('admin/payroll/generate_payslip', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function receive_generated($salary_payment_id) {
        // check existing_recept_no by where 
        $where = array('salary_payment_id' => $salary_payment_id);
        $check_existing_recipt_no = $this->payroll_model->check_by($where, 'tbl_salary_payslip');
        if (!empty($check_existing_recipt_no)) {
            $data['payslip_number'] = $check_existing_recipt_no->payslip_number;
        } else {
            $this->payroll_model->_table_name = "tbl_salary_payslip"; //table name
            $this->payroll_model->_primary_key = "payslip_id";
            $payslip_id = $this->payroll_model->save($where);

            $pdata['payslip_number'] = date('Ym') . $payslip_id;
            $this->payroll_model->save($pdata, $payslip_id);
            redirect('admin/payroll/receive_generated/' . $salary_payment_id, 'refresh');
        }
        $data['title'] = "Generate Payslip";
        $data['employee_salary_info'] = $this->payroll_model->get_salary_payment_info($emp_id = NULL, $payment_date = NULL, $salary_payment_id);

        $data['subview'] = $this->load->view('admin/payroll/payslip_info', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

}
