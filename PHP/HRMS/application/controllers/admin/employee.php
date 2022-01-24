<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of employee
 *
 * @author NaYeM
 */
class Employee extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('employee_model');
    }

    public function add_employee($id = NULL) {
        //Author: Asemployeeaf                
        $data['title'] = "Add Employee";

        if (!empty($id)) {// retrive data from db by id            
            $data['employee_info'] = $this->employee_model->all_emplyee_info($id);

            if (empty($data['employee_info'])) {
                $type = "error";
                $message = "No Record Found";
                set_message($type, $message);
                redirect('admin/employee/add_employee');
            }
        }

        // retrive all data from department table
        $this->employee_model->_table_name = "tbl_department"; //table name
        $this->employee_model->_order_by = "department_id";
        $all_dept_info = $this->employee_model->get();
        // get all department info and designation info
        foreach ($all_dept_info as $v_dept_info) {
            $data['all_department_info'][$v_dept_info->department_name] = $this->employee_model->get_add_department_by_id($v_dept_info->department_id);
        }
        // retrive country
        $this->employee_model->_table_name = "countries"; //table name
        $this->employee_model->_order_by = "countryName";
        $data['all_country'] = $this->employee_model->get();

        //page load
        $data['subview'] = $this->load->view('admin/employee/add_employee', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_employee($id = NULL) {
        // **** Employee Personal Details,Contact Details and Official Status Save And Update Start *** 
        //input post
        $data = $this->employee_model->array_from_post(array('first_name', 'last_name', 'date_of_birth', 'gender', 'maratial_status', 'father_name', 'nationality',
            'passport_number', 'present_address', 'city', 'country_id', 'mobile', 'phone', 'email', 'employment_id', 'designations_id', 'joining_date'));
        //image upload

        if (!empty($_FILES['photo']['name'])) {
            $old_path = $this->input->post('old_path');
            if ($old_path) {
                unlink($old_path);
            }

            $val = $this->employee_model->uploadImage('photo');
            $val == TRUE || redirect('admin/employee/add_employee');
            $data['photo'] = $val['path'];
            $data['photo_a_path'] = $val['fullPath'];
        }

        // ************* Save into Employee Table 
        $this->employee_model->_table_name = "tbl_employee"; // table name
        $this->employee_model->_primary_key = "employee_id"; // $id
        if (!empty($id)) {
            $employee_id = $id;
            $data['status'] = $this->input->post('status', TRUE);
            $this->employee_model->save($data, $id);
        } else {
            $data['status'] = 1;
            $employee_id = $this->employee_model->save($data);
        }
        // save into tbl employee login 
        $this->employee_model->_table_name = "tbl_employee_login"; // table name
        $this->employee_model->_primary_key = "employee_login_id"; // $id
        // check employee login details exsist or not 
        // if existing do not save 
        // else save the login details
        $check_existing_data = $this->employee_model->check_by(array('employee_id' => $employee_id), 'tbl_employee_login');
        $ldata['employee_id'] = $employee_id;
        $ldata['user_name'] = $data['employment_id'];
        $ldata['password'] = $this->hash('employee');
        $ldata['activate'] = $data['status'];

        if (!empty($check_existing_data)) {
            $this->employee_model->save($ldata, $check_existing_data->employee_login_id);
        } else {
            $this->employee_model->save($ldata);
        }
        // 
        // **** Employee Personal Details,Contact Details and Official Status Save And Update End *** 
        // ** Employee Bank Information Save and Update Start  **
        $bank_data = $this->employee_model->array_from_post(array('bank_name', 'branch_name', 'account_name', 'account_number'));
        $bank_data['employee_id'] = $employee_id;
        $this->employee_model->_table_name = "tbl_employee_bank"; // table name
        $this->employee_model->_primary_key = "employee_bank_id"; // $id

        $employee_bank_id = $this->input->post('employee_bank_id', TRUE);
        if (!empty($employee_bank_id)) {
            $this->employee_model->save($bank_data, $employee_bank_id);
        } else {
            $this->employee_model->save($bank_data);
        }
        // * Employee Bank Information Save and Update End   *
        // ** Employee Document Information Save and Update Start  **
        // Resume File upload
        if (!empty($_FILES['resume']['name'])) {
            $old_path = $this->input->post('resume_path');
            if ($old_path) {
                unlink($old_path);
            }
            $val = $this->employee_model->uploadFile('resume');
            $val == TRUE || redirect('admin/employee/add_employee');
            $document_data['resume_filename'] = $val['fileName'];
            $document_data['resume'] = $val['path'];
            $document_data['resume_path'] = $val['fullPath'];
        }

        // offer_letter File upload
        if (!empty($_FILES['offer_letter']['name'])) {
            $old_path = $this->input->post('offer_letter_path');
            if ($old_path) {
                unlink($old_path);
            }
            $val = $this->employee_model->uploadFile('offer_letter');
            $val == TRUE || redirect('admin/employee/add_employee');
            $document_data['offer_letter_filename'] = $val['fileName'];
            $document_data['offer_letter'] = $val['path'];
            $document_data['offer_letter_path'] = $val['fullPath'];
        }
        // joining_letter File upload
        if (!empty($_FILES['joining_letter']['name'])) {
            $old_path = $this->input->post('joining_letter_path');
            if ($old_path) {
                unlink($old_path);
            }
            $val = $this->employee_model->uploadFile('joining_letter');
            $val == TRUE || redirect('admin/employee/add_employee');
            $document_data['joining_letter_filename'] = $val['fileName'];
            $document_data['joining_letter'] = $val['path'];
            $document_data['joining_letter_path'] = $val['fullPath'];
        }

        // contract_paper File upload
        if (!empty($_FILES['contract_paper']['name'])) {
            $old_path = $this->input->post('contract_paper_path');
            if ($old_path) {
                unlink($old_path);
            }
            $val = $this->employee_model->uploadFile('contract_paper');
            $val == TRUE || redirect('admin/employee/add_employee');
            $document_data['contract_paper_filename'] = $val['fileName'];
            $document_data['contract_paper'] = $val['path'];
            $document_data['contract_paper_path'] = $val['fullPath'];
        }
        // id_proff File upload
        if (!empty($_FILES['id_proff']['name'])) {
            $old_path = $this->input->post('id_proff_path');
            if ($old_path) {
                unlink($old_path);
            }
            $val = $this->employee_model->uploadFile('id_proff');
            $val == TRUE || redirect('admin/employee/add_employee');
            $document_data['id_proff_filename'] = $val['fileName'];
            $document_data['id_proff'] = $val['path'];
            $document_data['id_proff_path'] = $val['fullPath'];
        }
        // id_proff File upload
        if (!empty($_FILES['other_document']['name'])) {
            $old_path = $this->input->post('other_document_path');
            if ($old_path) {
                unlink($old_path);
            }
            $val = $this->employee_model->uploadFile('other_document');
            $val == TRUE || redirect('admin/employee/add_employee');
            $document_data['other_document_filename'] = $val['fileName'];
            $document_data['other_document'] = $val['path'];
            $document_data['other_document_path'] = $val['fullPath'];
        } else {
            
        }

        $document_data['employee_id'] = $employee_id;

        $this->employee_model->_table_name = "tbl_employee_document"; // table name
        $this->employee_model->_primary_key = "document_id"; // $id
        $document_id = $this->input->post('document_id', TRUE);
        if (!empty($document_id)) {
            $this->employee_model->save($document_data, $document_id);
        } else {
            $this->employee_model->save($document_data);
        }
        // ***Employee Document Information Save and Update End   ***
        // messages for user
        $type = "success";
        $message = "Employee Information Successfully Saved!";
        set_message($type, $message);
        redirect('admin/employee/employee_list'); //redirect page
    }

    public function hash($string) {
        return hash('sha512', $string . config_item('encryption_key'));
    }

    public function delete_employee($id, $bank_id, $doc_id) {
        // ************* Delete into Employee Table 
        $this->employee_model->_table_name = "tbl_employee"; // table name
        $this->employee_model->_primary_key = "employee_id"; // $id
        $this->employee_model->delete($id);
        // delete into tbl bank 
        $this->employee_model->_table_name = "tbl_employee_bank"; // table name
        $this->employee_model->_primary_key = "employee_bank_id"; // $id
        $this->employee_model->delete($bank_id);

        // delete into tbl employee document
        $this->employee_model->_table_name = "tbl_employee_document"; // table name
        $this->employee_model->_primary_key = "document_id"; // $id
        $this->employee_model->delete($doc_id);

        // delete into tbl employee login
        $this->employee_model->_table_name = "tbl_employee_login"; // table name
        $this->employee_model->_order_by = "employee_id"; // table name        
        $this->employee_model->_primary_key = "employee_login_id"; // $id
        $login_id = $this->employee_model->get_by(array('employee_id'=> $id), TRUE);
        
        $this->employee_model->delete($login_id->employee_login_id);

        // messages for user
        $type = "success";
        $message = "Employee Information Successfully Delete!";
        set_message($type, $message);
        redirect('admin/employee/employee_list'); //redirect page
    }

    public function employee_list($id = NULL) {
        $data['title'] = "Employee List";
        $data['all_employee_info'] = $this->employee_model->all_emplyee_info();
        $data['subview'] = $this->load->view('admin/employee/employee_list', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function employee_list_pdf() {
        $data['title'] = "Employee List";
        $data['all_employee_info'] = $this->employee_model->all_emplyee_info();
        $this->load->helper('dompdf');
        $view_file = $this->load->view('admin/employee/employee_list_pdf', $data, true);
        pdf_create($view_file, 'Employee List');
    }

    public function view_employee($id = NULL) {
        $data['title'] = "View Employee";
        if (!empty($id)) {// retrive data from db by id
            $data['employee_info'] = $this->employee_model->all_emplyee_info($id);
            if (empty($data['employee_info'])) {
                $type = "error";
                $message = "No Record Found";
                set_message($type, $message);
                redirect('admin/employee/employee_list');
            }
        }
        $data['subview'] = $this->load->view('admin/employee/view_employee', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function employee_award($id = NULL, $designations_id = NULL) {
        $data['title'] = "Employee List";
        // retrive all data from department table
        $this->employee_model->_table_name = "tbl_department"; //table name
        $this->employee_model->_order_by = "department_id";
        $all_dept_info = $this->employee_model->get();
        // get all department info and designation info
        foreach ($all_dept_info as $v_dept_info) {
            $data['all_department_info'][$v_dept_info->department_name] = $this->employee_model->get_add_department_by_id($v_dept_info->department_id);
        }
        /// edit and update get employee award info
        if (!empty($id)) {
            $data['award_info'] = $this->employee_model->get_employee_award_by_id($id);

            // get all employee info by designation id
            $this->employee_model->_table_name = 'tbl_employee';
            $this->employee_model->_order_by = 'designations_id';
            $data['employee_info'] = $this->employee_model->get_by(array('designations_id' => $designations_id), FALSE);
        }
        // get all_employee_award_info
        $data['all_employee_award_info'] = $this->employee_model->get_employee_award_by_id();

        $data['subview'] = $this->load->view('admin/employee/employee_award', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_employee_award($id = NULL) {
        $data = $this->employee_model->array_from_post(array('award_name', 'employee_id', 'gift_item', 'award_amount', 'award_date'));

        $this->employee_model->_table_name = "tbl_employee_award"; // table name
        $this->employee_model->_primary_key = "employee_award_id"; // $id
        $this->employee_model->save($data, $id);
        // messages for user
        $type = "success";
        $message = "Employee Award Information Successfully Saved!";
        set_message($type, $message);
        redirect('admin/employee/employee_award'); //redirect page
    }

    public function delete_employee_award($id = NULL) {

        $this->employee_model->_table_name = "tbl_employee_award"; // table name
        $this->employee_model->_primary_key = "employee_award_id"; // $id
        $this->employee_model->delete($id); // delete 
        // messages for user
        $type = "success";
        $message = "Employee Award Information Successfully Delete !";
        set_message($type, $message);
        redirect('admin/employee/employee_award'); //redirect page
    }

    public function make_pdf($employee_id) {
        $data['title'] = "Employee List";
        $data['employee_info'] = $this->employee_model->all_emplyee_info($employee_id);
        $this->load->helper('dompdf');
        $view_file = $this->load->view('admin/employee/employee_view_pdf', $data, true);
        pdf_create($view_file, $data['employee_info']->first_name);
    }

}
