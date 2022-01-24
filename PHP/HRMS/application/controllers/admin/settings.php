<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('settings_model');

        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "90%",
                'height' => "400px"
            )
        );
    }

    public function general_settings($val = NULL) {
        $data['title'] = "General Settings"; //Page title
        //Query from DB
        $this->settings_model->_table_name = "tbl_gsettings"; //table name
        $this->settings_model->_order_by = "name";
        $val = $this->settings_model->get_by(array('id_gsettings' => 1,), TRUE);

        $this->settings_model->_table_name = "countries"; //table name
        $this->settings_model->_order_by = "countryName";
        $data['all_country'] = $this->settings_model->get();
        if ($val) { // get general info by id
            $data['ginfo'] = $val; // assign value from db    
        }
        // retrive country
        $this->settings_model->_table_name = "currency"; //table name
        $this->settings_model->_order_by = "currency_id";
        $data['all_currency'] = $this->settings_model->get(); // get result

        $data['subview'] = $this->load->view('admin/settings/general_settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_ginfo($id = NULL) {

        $this->settings_model->_table_name = "tbl_gsettings"; // table name
        $this->settings_model->_primary_key = "id_gsettings"; // $id

        $data = $this->settings_model->array_from_post(array('name', 'email', 'address', 'country_id', 'active_language', 'city', 'phone', 'mobile', 'hotline', 'fax', 'website', 'currency'));

        //image Process
        if (!empty($_FILES['logo']['name'])) {
            $old_path = $this->input->post('old_path');
            if ($old_path) {
                unlink($old_path);
            }
            $val = $this->settings_model->uploadImage('logo');
            $val == TRUE || redirect('admin/settings/general_settings');
            $data['logo'] = $val['path'];
            $data['full_path'] = $val['fullPath'];
        }

        $this->settings_model->save($data, $id);
        // messages for user
        $type = "success";
        $message = "Company Information Successfully Update!";
        set_message($type, $message);
        redirect('admin/settings/general_settings');
    }

    public function set_working_days() {

        $data['title'] = "Set Working Days";
        // get all days
        $this->settings_model->_table_name = "tbl_days"; //table name
        $this->settings_model->_order_by = "day_id";
        $data['days'] = $this->settings_model->get();
        // get all working days
        $data['working_days'] = $this->settings_model->get_working_days();

        $data['subview'] = $this->load->view('admin/settings/set_working_days', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_working_days() {
        // delete all working days after save and again save 
        $this->settings_model->delete_all('tbl_working_days');
        $day_id = $this->input->post('day', TRUE);
        $day = $this->input->post('day_id', TRUE);
        $this->settings_model->_table_name = "tbl_working_days"; // table name
        $this->settings_model->_primary_key = "working_days_id"; // $id     
        if (!empty($day)) {
            foreach ($day as $value) {
                $data['flag'] = 0;
                $data['day_id'] = $value;
                if (!empty($day_id)) {
                    foreach ($day_id as $days) {
                        if ($value == $days) {
                            $data['flag'] = 1;
                        }
                    }
                }
                $this->settings_model->save($data);
            }
        }
        //To display confimation message.
        $type = "success";
        $message = "Working Days Successfully Saved!";
        set_message($type, $message);
        redirect('admin/settings/set_working_days');
    }

    public function holiday_list($flag = NULL, $id = NULL) {
        $data['title'] = "Holiday List";
        $this->settings_model->_table_name = "tbl_holiday"; //table name
        $this->settings_model->_order_by = "holiday_id";

        // get holiday list by id
        if (!empty($id)) {
            $data['holiday_list'] = $this->settings_model->get_by(array('holiday_id' => $id,), TRUE);

            if (empty($data['holiday_list'])) {
                $type = "error";
                $message = "No Record Found";
                set_message($type, $message);
                redirect('admin/settings/holiday_list');
            }
        }// click add holiday theb show
        if (!empty($flag)) {
            $data['active_add_holiday'] = $flag;
        }
        // active check with current month
        $data['current_month'] = date('m');
        if ($this->input->post('year', TRUE)) { // if input year 
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
        // get all holiday list by year and month
        $data['all_holiday_list'] = $this->get_holiday_list($data['year']);  // get current year
        // retrive all data from db        
        $data['subview'] = $this->load->view('admin/settings/holiday_list', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function get_holiday_list($year) {// this function is to create get monthy recap report 
        for ($i = 1; $i <= 12; $i++) { // query for months
            if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $start_date = $year . "-" . '0' . $i . '-' . '01';
                $end_date = $year . "-" . '0' . $i . '-' . '31';
            } else {
                $start_date = $year . "-" . $i . '-' . '01';
                $end_date = $year . "-" . $i . '-' . '31';
            }
            $get_holiday_list[$i] = $this->settings_model->get_holiday_list_by_date($start_date, $end_date); // get all report by start date and in date 
        }
        return $get_holiday_list; // return the result
    }

    public function save_holiday($id = NULL) {

        $this->settings_model->_table_name = "tbl_holiday"; //table name        
        $this->settings_model->_primary_key = "holiday_id";    //id
        // input data
        $data = $this->settings_model->array_from_post(array('event_name', 'description', 'start_date', 'end_date')); //input post
        // dublicacy check into database
        if (!empty($id)) {
            $holiday_id = array('holiday_id !=' => $id);
        } else {
            $holiday_id = null;
        }
        $where = array('event_name' => $data['event_name'], 'start_date' => $data['start_date']); // where
        // check holiday by where
        // if not empty show alert message else save data
        $check_holiday = $this->settings_model->check_update('tbl_holiday', $where, $holiday_id);

        if (!empty($check_holiday)) {
            $type = "error";
            $message = "Holiday Information Already Exist!";
            set_message($type, $message);
        } else {
            $this->settings_model->save($data, $id);
            // messages for user
            $type = "success";
            $message = "Holiday Information Successfully Saved!";
            set_message($type, $message);
        }

        redirect('admin/settings/holiday_list'); //redirect page
    }

    public function delete_holiday_list($id) { // delete holiday list by id
        $this->settings_model->_table_name = "tbl_holiday"; //table name        
        $this->settings_model->_primary_key = "holiday_id";    //id
        $this->settings_model->delete($id);
        $type = "success";
        $message = "Holiday Information Successfully Delete!";
        set_message($type, $message);
        redirect('admin/settings/holiday_list'); //redirect page
    }

    public function leave_category($id = NULL) {
        $data['title'] = "Set Leave Category";


        $this->settings_model->_table_name = "tbl_leave_category"; //table name
        $this->settings_model->_order_by = "leave_category_id";

        // retrive data from db by id
        if (!empty($id)) {
            $data['leave_category'] = $this->settings_model->get_by(array('leave_category_id' => $id,), TRUE);

            if (empty($data['leave_category'])) {
                $type = "error";
                $message = "No Record Found";
                set_message($type, $message);
                redirect('admin/settings/leave_category');
            }
        }
        // retrive all data from db
        $data['all_leave_category_info'] = $this->settings_model->get();

        $data['subview'] = $this->load->view('admin/settings/leave_category', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_leave_category($id = NULL) {

        $this->settings_model->_table_name = "tbl_leave_category"; //table name        
        $this->settings_model->_primary_key = "leave_category_id";    //id
        // input data
        $data = $this->settings_model->array_from_post(array('category')); //input post
        // dublicacy check 
        if (!empty($id)) {
            $leave_category_id = array('leave_category_id !=' => $id);
        } else {
            $leave_category_id = null;
        }
        // check check_leave_category by where        
        // if not empty show alert message else save data
        $check_leave_category = $this->settings_model->check_update('tbl_leave_category', $where = array('category' => $data['category']), $leave_category_id);

        if (!empty($check_leave_category)) {
            $type = "error";
            $message = "Leave Category Already Exist!";
            set_message($type, $message);
        } else {
            $this->settings_model->save($data, $id);
            // messages for user
            $type = "success";
            $message = "Leave Category Successfully Saved!";
            set_message($type, $message);
        }

        redirect('admin/settings/leave_category'); //redirect page
    }

    public function delete_leave_category($id) {
        // check into application list
        $where = array('leave_category_id' => $id);
        // check existing leave category into tbl_application_list
        $check_existing_ctgry = $this->settings_model->check_by($where, 'tbl_application_list');
        // check existing leave category into tbl_attendance
        $check_into_attendace = $this->settings_model->check_by($where, ' tbl_attendance');
        if (!empty($check_into_attendace) || !empty($check_existing_ctgry)) { // if not empty do not delete this else delete
            // messages for user
            $type = "error";
            $message = "Leave Category Information Already Used!";
            set_message($type, $message);
        } else {
            $this->settings_model->_table_name = "tbl_leave_category"; //table name        
            $this->settings_model->_primary_key = "leave_category_id";    //id
            $this->settings_model->delete($id);
            $type = "success";
            $message = "Leave Category Information Successfully Delete!";
            set_message($type, $message);
        }
        redirect('admin/settings/leave_category'); //redirect page
    }

    public function notification_settings() {

        $data['title'] = "Set Notification Settings";
        // check notififation status by where
        $where = array('notify_me' => '1');
        // check email notification status
        $data['email_notiifation'] = $this->settings_model->check_by($where, 'tbl_inbox');
        // check notice notification status
        $data['notice_notiifation'] = $this->settings_model->check_by($where, 'tbl_notice');
        // check leave notification status
        $data['leave_notiifation'] = $this->settings_model->check_by($where, 'tbl_application_list');

        $data['subview'] = $this->load->view('admin/settings/notification_settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function set_noticifation() {
        // get input data
        $email = $this->input->post('email', TRUE);
        $award = $this->input->post('award', TRUE);
        $notice = $this->input->post('notice', TRUE);
        $leave = $this->input->post('leave', TRUE);
        $where = array('notify_me' => '0');
        $action = array('notify_me' => '1');

        // set notifucation into tbl inox
        // notify status 1= on and 0=off
        if (!empty($email)) {

            // check existing mail 
            $this->settings_model->_table_name = "tbl_inbox"; //table name        
            $this->settings_model->_order_by = "inbox_id";    //id
            $check_email = $this->settings_model->get();
            if (empty($check_email)) {
                $type = "danger";
                $message = "Notification status can not be changed yet - no mail received in inbox !";
                $error_message['error_type'][] = $type;
                $error_message['error_message'][] = $message;
            }
            $status['notify_me'] = $email;

            $this->settings_model->set_action($where, $status, 'tbl_inbox'); // get result
        } else {
            $this->settings_model->set_action($action, $where, 'tbl_inbox'); // get result
        }

        // set notification into tbl Notice
        if (!empty($notice)) {
            // check existing notice 
            $this->settings_model->_table_name = "tbl_notice"; //table name        
            $this->settings_model->_order_by = "notice_id";    //id
            $check_notice = $this->settings_model->get();
            if (empty($check_notice)) {
                $type = "danger";
                $message = "Notification status can not be changed yet - no notice created !";
                $error_message['error_type'][] = $type;
                $error_message['error_message'][] = $message;
            }

            $status['notify_me'] = $notice;
            $this->settings_model->set_action($where, $status, 'tbl_notice'); // get result
        } else {
            $this->settings_model->set_action($action, $where, 'tbl_notice'); // get result
        }
        // set notification into tbl Notice
        if (!empty($leave)) {
            // check existing application 
            $this->settings_model->_table_name = "tbl_application_list"; //table name        
            $this->settings_model->_order_by = "application_list_id";    //id
            $check_application_list = $this->settings_model->get();
            if (empty($check_application_list)) {
                $type = "danger";
                $message = "Notification status can not be changed yet - no application list !";
                $error_message['error_type'][] = $type;
                $error_message['error_message'][] = $message;
            }

            $status['notify_me'] = $leave;
            $this->settings_model->set_action($where, $status, 'tbl_application_list'); // get result
        } else {
            $this->settings_model->set_action($action, $where, 'tbl_application_list'); // get result
        }
        $type = "success";
        $message = "Notification Status Successfully Changed!";
        $error_message['error_type'][] = $type;
        $error_message['error_message'][] = $message;
        $this->session->set_userdata($error_message);
        redirect('admin/settings/notification_settings'); //redirect page
    }

    public function update_profile() {
        $data['title'] = "Change Password";
        $data['subview'] = $this->load->view('admin/settings/update_profile', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function profile_updated() {
        $employee_id = $this->session->userdata('employee_id');
        $data['first_name'] = $this->input->post('first_name');
        $data['last_name'] = $this->input->post('last_name');
        $data['user_name'] = $this->input->post('user_name');
        $this->settings_model->_table_name = 'tbl_user';
        $this->settings_model->_primary_key = 'user_id';
        $this->settings_model->save($data, $employee_id);
        $type = "success";
        $message = "Information Successfully Updated! Please login again to see new updates.";
        set_message($type, $message);
        redirect('admin/settings/update_profile'); //redirect page
    }

    public function set_password() {
        $employee_id = $this->session->userdata('employee_id');
        $data['password'] = $this->hash($this->input->post('new_password'));
        $this->settings_model->_table_name = 'tbl_user';
        $this->settings_model->_primary_key = 'user_id';
        $this->settings_model->save($data, $employee_id);
        $type = "success";
        $message = "Password Successfully Updated! Please logout to use new password.";
        set_message($type, $message);
        redirect('admin/settings/update_profile'); //redirect page
    }

    public function hash($string) {
        return hash('sha512', $string . config_item('encryption_key'));
    }

    public function view_event($id = NULL) {
        $data['menu'] = array("routine" => 1, "view_event" => 1);
        $data['title'] = "View Event";
        $employee_id = $this->session->userdata('employee_id');
        $this->settings_model->_table_name = "tbl_event"; // table name        
        $this->settings_model->_order_by = "event_id"; // $id 
        $data['event_details'] = $this->settings_model->get_by(array('employee_id' => $employee_id), FALSE);
        if (!empty($id)) {
            $this->settings_model->_table_name = "tbl_event"; // table name                
            $this->settings_model->_order_by = "event_id"; // $id 
            $data['event_info'] = $this->settings_model->get_by(array('event_id' => $id), TRUE);
        }
        $data['add_event'] = $this->input->post('add_event', TRUE);

        $data['subview'] = $this->load->view('admin/settings/view_personal_event', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_event($id = NULL) {
        $this->settings_model->_table_name = "tbl_event"; // table name
        $this->settings_model->_primary_key = "event_id"; // $id   

        $data['event_name'] = $this->input->post('event_name');
        $data['employee_id'] = $this->session->userdata('employee_id');
        $data['start_date'] = $this->input->post('start_date');
        $data['end_date'] = $this->input->post('end_date');

        $this->settings_model->save($data, $id);

        $type = "success";
        $message = "Save Event Successfully!";
        set_message($type, $message);
        if (!empty($id)) {
            redirect('admin/settings/view_event');
        } else {
            redirect('admin/dashboard');
        }
    }

    public function delete_personal_event($id) {
        $this->settings_model->_table_name = "tbl_event"; // table name        
        $this->settings_model->_primary_key = "event_id"; // $id 
        $this->settings_model->delete($id);
        $type = "success";
        $message = "Delete Event Successfully!";
        set_message($type, $message);
        redirect('admin/settings/view_event');
    }

    public function language_settings() {
        $data['title'] = "Language Settings";
        $data['subview'] = $this->load->view('admin/settings/language_settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function add_language() {
        $language = $this->input->post('language');
        $this->load->dbforge();
        $fields = array(
            $language => array(
                'type' => 'LONGTEXT'
            )
        );
        $this->dbforge->add_column('tbl_menu', $fields);
        $this->dbforge->add_column('tbl_form', $fields);
        $this->dbforge->add_column('tbl_form_body', $fields);
        $type = "success";
        $message = "New language Successfully Generate";
        set_message($type, $message);
        redirect('admin/settings/language_settings'); //redirect page
    }

    public function delete_language($fields) {
        $language = $fields;
        $this->load->dbforge();
        $this->dbforge->drop_column('tbl_menu', $language);
        $this->dbforge->drop_column('tbl_form', $language);
        $this->dbforge->drop_column('tbl_form_body', $language);
        $type = "success";
        $message = '<strong color:#000>' . $fields . '</strong>' . " Language Information Successfully Deleted ";
        set_message($type, $message);
        redirect('admin/settings/language_settings'); //redirect page
    }

    public function set_phrase($language) {
        $data['title'] = "Set Phrase";
        $data['language'] = $language;
        // get defualt manu 
        $data['all_menu_language'] = $this->settings_model->all_menu_language();

        $data['all_form_language'] = $this->settings_model->all_form_language();

        $data['subview'] = $this->load->view('admin/settings/set_phrase', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function add_menu_language($language) {
        $lan = $this->input->post($language, TRUE);
        $menu_id = $this->input->post('menu_id', TRUE);

        foreach ($lan as $key => $v_lanuage) {
            $id = $menu_id[$key];
            $this->db->where('menu_id', $id);
            $this->db->update('tbl_menu', array($language => $v_lanuage));
        }
        $type = "success";
        $message = '<strong >' . $language . '</strong>' . " Language For Menu Successfully Updated ! ";
        set_message($type, $message);
        redirect('admin/settings/set_phrase/' . $language); //redirect page
    }

    public function add_form_language($language) {
        $lan = $this->input->post($language, TRUE);
        $form_id = $this->input->post('form_id', TRUE);

        foreach ($lan as $key => $v_lanuage) {
            $id = $form_id[$key];
            $this->db->where('form_id', $id);
            $this->db->update('tbl_form', array($language => $v_lanuage));
        }
        $type = "success";
        $message = '<strong >' . $language . '</strong>' . " Language For Heading Successfully Updated ! ";
        set_message($type, $message);
        redirect('admin/settings/set_phrase/' . $language); //redirect page
    }



    public function database_backup() {


        $this->load->dbutil();
        $prefs = array(
            'format' => 'zip',
            'filename' => 'my_db_backup.sql'
        );
        $backup = & $this->dbutil->backup($prefs);
        $db_name = 'HRMS_database_backup_' . date("d-m-Y") . '.zip';
        //$save = 'img/uploads/' . $db_name;
        $this->load->helper('file');
        //write_file($save, $backup);
        $this->load->helper('download');
        force_download($db_name, $backup);

    }

    public function set_form($id = NULL) {
        $data['English'] = $this->input->post('English', true);

        $this->settings_model->_table_name = "tbl_form"; // table name        
        $this->settings_model->_primary_key = "form_id"; // $id 
        $this->settings_model->save($data, $id);
        $type = "success";
        $message = "Form Heading Successfully Created !";
        set_message($type, $message);
        redirect('admin/settings/database_backup');
    }

    public function set_form_body($id = NULL) {
        $data['form_id'] = $this->input->post('form_id', true);

        $English = $this->input->post('English', true);
        foreach ($English as $value) {
            $data['English'] = $value;
            $this->settings_model->_table_name = "tbl_form_body"; // table name        
            $this->settings_model->_primary_key = "form_body_id"; // $id 
            $this->settings_model->save($data, $id);
        }
        $type = "success";
        $message = "Form Body Successfully Created !";
        set_message($type, $message);
        redirect('admin/settings/database_backup');
    }

}
