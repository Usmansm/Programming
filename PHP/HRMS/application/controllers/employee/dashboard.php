<?php

class Dashboard extends Employee_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->model('emp_model');
        $this->load->model('global_model');
        $this->load->model('mailbox_model');
        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "100%",
                'height' => "350px"
            )
        );
    }

    public function index() {
        $data['menu'] = array("index" => 1);
        $data['title'] = "Employee Panel";
        $employee_id = $this->session->userdata('employee_id');

        //get employee details by employee id
        $data['employee_details'] = $this->emp_model->all_emplyee_info($employee_id);
        //Total Attendance
        $this->emp_model->_table_name = "tbl_attendance"; //table name
        $this->emp_model->_order_by = "employee_id";
        $data['total_attendance'] = count($this->total_attendace_in_month($employee_id));
        // get working days holiday
        $holidays = count($this->global_model->get_holidays()); //tbl working Days Holiday
        // get public holiday
        $public_holiday = count($this->total_attendace_in_month($employee_id, TRUE));

        // get total days in a month
        $month = date('m');
        $year = date('Y');
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        // total attend days in a month without public holiday and working days
        $data['total_days'] = $days - $holidays - $public_holiday;

        //leave applied         
        $data['total_leave_applied'] = count($this->emp_model->get_approved_leave($employee_id));

        //award received
        $this->emp_model->_table_name = "tbl_employee_award"; //table name
        $this->emp_model->_order_by = "employee_id";
        $data['total_award_received'] = count($this->emp_model->get_by(array('employee_id' => $employee_id,), FALSE));

        //get all notice by flag       
        $data['notice_info'] = $this->emp_model->get_all_notice(TRUE);

        //get all upcomming events
        $data['event_info'] = $this->emp_model->get_all_events();

        // get upcoming birthday
        $this->admin_model->_table_name = "tbl_employee"; //table name
        $this->admin_model->_order_by = "employee_id"; // order by
        $data['employee'] = $this->admin_model->get_by(array('status' => 1)); // get resutl 
        // get recent email          
        $data['get_inbox_message'] = $this->mailbox_model->get_inbox_message($data['employee_details']->email, $flag = NULL, TRUE);
        $data['subview'] = $this->load->view('employee/main_content', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function total_attendace_in_month($employee_id, $flag = NULL) {
        $month = date('m');
        $year = date('Y');

        if ($month >= 1 && $month <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
            $start_date = $year . "-" . '0' . $month . '-' . '01';
            $end_date = $year . "-" . '0' . $month . '-' . '31';
        } else {
            $start_date = $year . "-" . $month . '-' . '01';
            $end_date = $year . "-" . $month . '-' . '31';
        }
        if (!empty($flag)) { // if flag is not empty that means get pulic holiday
            $get_public_holiday = $this->emp_model->get_public_holiday($start_date, $end_date);
            if (!empty($get_public_holiday)) { // if not empty the public holiday
                foreach ($get_public_holiday as $v_holiday) {
                    if ($v_holiday->start_date == $v_holiday->end_date) { // if start date and end date is equal return one data
                        $total_holiday[] = $v_holiday->start_date;
                    } else { // if start date and end date not equan return all date
                        for ($j = $v_holiday->start_date; $j <= $v_holiday->end_date; $j++) {
                            $total_holiday[] = $j;
                        }
                    }
                }
                return $total_holiday;
            }
        } else {
            $get_total_attendance = $this->emp_model->get_total_attendace_by_date($start_date, $end_date, $employee_id); // get all attendace by start date and in date 
            return $get_total_attendance;
        }
    }

    public function leave_application() {
        $data['menu'] = array("leave_application" => 1);
        $data['title'] = "Employee Panel";

        //get leave applied with category name
        $employee_id = $this->session->userdata('employee_id');
        $data['all_leave_applications'] = $this->emp_model->get_all_leave_applied($employee_id);

        $data['subview'] = $this->load->view('employee/emp_leave_application', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function apply_leave_application() {
        $data['title'] = "New Leave Application";

        //get leave category for dropdown
        $this->emp_model->_table_name = "tbl_leave_category"; // table name
        $this->emp_model->_order_by = "leave_category_id"; // $id
        $data['all_leave_category'] = $this->emp_model->get(); // get result

        $data['subview'] = $this->load->view('employee/apply_new_leave_application', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function save_leave_application() {
        $this->emp_model->_table_name = "tbl_application_list"; // table name
        $this->emp_model->_primary_key = "application_list_id"; // $id
        //receive form input by post
        $data['employee_id'] = $this->session->userdata('employee_id');
        $data['leave_category_id'] = $this->input->post('leave_category_id');
        $data['leave_start_date'] = $this->input->post('leave_start_date');
        $data['leave_end_date'] = $this->input->post('leave_end_date');
        $data['reason'] = $this->input->post('reason');

        //save data in database
        $this->emp_model->save($data);

        // messages for user
        $type = "success";
        $message = "Leave Application Successfully Submitted !";
        set_message($type, $message);
        redirect('employee/dashboard/leave_application');
    }

    public function all_notice() {
        $data['menu'] = array("notice" => 1);
        $data['title'] = "All Notice";

        // get all notice by flag       
        $data['notice_info'] = $this->emp_model->get_all_notice();

        $data['subview'] = $this->load->view('employee/all_notice', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function notice_detail($id) {
        $data['title'] = "Notice Details";

        //get leave category for dropdown
        $this->emp_model->_table_name = "tbl_notice"; // table name
        $this->emp_model->_order_by = "notice_id"; // $id
        $data['full_notice_details'] = $this->emp_model->get_by(array('notice_id' => $id,), TRUE); // get result


        $data['subview'] = $this->load->view('employee/notice_details', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function all_events() {
        $data['menu'] = array("events" => 1);
        $data['title'] = "All Events";

        // get all notice by flag       
        $data['event_info'] = $this->emp_model->get_all_events();

        $data['subview'] = $this->load->view('employee/events', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function event_detail($id) {
        $data['title'] = "Event Details";

        //get leave category for dropdown
        $this->emp_model->_table_name = "tbl_holiday"; // table name
        $this->emp_model->_order_by = "holiday_id"; // $id
        $data['event_details'] = $this->emp_model->get_by(array('holiday_id' => $id,), TRUE); // get result

        $data['subview'] = $this->load->view('employee/event_details', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function all_award() {
        $data['menu'] = array("awards" => 1);
        $data['title'] = "All Awards";

        // get all notice by flag       
        $data['award_info'] = $this->emp_model->get_all_awards();

        $data['subview'] = $this->load->view('employee/all_awards', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function award_detail($id) {
        $data['title'] = "All Awards";

        //get award detail info for particular employee    
        $data['employee_award_info'] = $this->emp_model->get_all_awards($id);

        $data['subview'] = $this->load->view('employee/award_details_page', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function profile() {
        $data['title'] = "User Profile";
        $employee_id = $this->session->userdata('employee_id');

        //get employee details
        $data['employee_details'] = $this->emp_model->all_emplyee_info($employee_id);

        $data['subview'] = $this->load->view('employee/user_profile', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    /*
     * Mailbox Controllers starts ------
     */

    public function inbox() {
        $data['menu'] = array("mailbox" => 1, "inbox" => 1);
        $data['title'] = "Employee Panel";
        $employee_id = $this->session->userdata('employee_id');

        //get leave category for dropdown
        $this->emp_model->_table_name = "tbl_employee"; // table name
        $this->emp_model->_order_by = "employee_id"; // $id
        $data['employee_details'] = $this->emp_model->get_by(array('employee_id' => $employee_id,), TRUE); // get result        
        $email = $data['employee_details']->email;

        // get all inbox by email 
        $data['get_inbox_message'] = $this->mailbox_model->get_inbox_message($email);
        $data['unread_mail'] = count($this->mailbox_model->get_inbox_message($email, TRUE));

        $data['subview'] = $this->load->view('employee/emp_inbox', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function read_inbox_mail($id) {
        $data['title'] = "Inbox";
        $this->mailbox_model->_table_name = 'tbl_inbox';
        $this->mailbox_model->_order_by = 'inbox_id';
        $data['read_mail'] = $this->mailbox_model->get_by(array('inbox_id' => $id), true);

        $this->mailbox_model->_primary_key = 'inbox_id';
        $updata['view_status'] = '1';
        $this->mailbox_model->save($updata, $id);

        $data['subview'] = $this->load->view('employee/emp_read_mail', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function delete_inbox_mail() {
        // get sellected id into inbox email page
        $selected_inbox_id = $this->input->post('selected_inbox_id', TRUE);
        if (!empty($selected_inbox_id)) { // check selected message is empty or not
            foreach ($selected_inbox_id as $v_inbox_id) {
                $this->mailbox_model->_table_name = 'tbl_inbox';
                $this->mailbox_model->delete_multiple(array('inbox_id' => $v_inbox_id));
            }
            $type = "success";
            $message = "Your message has been Deleted.";
        } else {
            $type = "error";
            $message = "Please Select a Message to Delete.";
        }
        set_message($type, $message);
        redirect('employee/dashboard/inbox');
    }

    public function sent() {
        $data['menu'] = array("mailbox" => 1, "sent" => 1);
        $data['title'] = "Send Email";

        $employee_id = $this->session->userdata('employee_id');
        $data['get_sent_message'] = $this->mailbox_model->get_sent_message($employee_id);

        $data['subview'] = $this->load->view('employee/emp_sent', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function read_send_mail($id) {
        $data['title'] = "Inbox";
        $this->mailbox_model->_table_name = 'tbl_send';
        $this->mailbox_model->_order_by = 'send_id';
        $data['read_mail'] = $this->mailbox_model->get_by(array('send_id' => $id), true);

        $data['subview'] = $this->load->view('employee/dashboard/emp_read_mail', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function delete_send_mail() {
        // get sellected id into send email page
        $selected_send_id = $this->input->post('selected_send_id', TRUE);

        if (!empty($selected_send_id)) {
            foreach ($selected_send_id as $v_send_id) {
                $this->mailbox_model->_table_name = 'tbl_send';
                $this->mailbox_model->delete_multiple(array('send_id' => $v_send_id));
            }
            $type = "success";
            $message = "Your message has been Deleted.";
        } else {
            $type = "error";
            $message = "Please Select a Message to Delete.";
        }
        set_message($type, $message);
        redirect('employee/dashboard/sent');
    }

    public function compose() {
        $data['title'] = "Inbox";
        $this->mailbox_model->_table_name = 'tbl_employee';
        $this->mailbox_model->_order_by = 'employee_id';
        $data['get_employee_email'] = $this->mailbox_model->get_by(array('status' => '1'), FALSE);
        $data['editor'] = $this->data;
        $data['subview'] = $this->load->view('employee/emp_compose_mail', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function send_mail() {

        $discard = $this->input->post('discard', TRUE);
        if ($discard) {
            redirect('employee/dashboard/inbox');
        }
        $all_email = $this->input->post('to', TRUE);

        // get all email address
        foreach ($all_email as $v_email) {
            $data = $this->mailbox_model->array_from_post(array('subject', 'message_body'));
            if (!empty($_FILES['attach_file']['name'])) {
                $old_path = $this->input->post('attach_file_path');
                if ($old_path) {
                    unlink($old_path);
                }
                $val = $this->mailbox_model->uploadAllType('attach_file');
                $val == TRUE || redirect('employee/emp_compose_mail');
                // save into send table
                $data['attach_filename'] = $val['fileName'];
                $data['attach_file'] = $val['path'];
                $data['attach_file_path'] = $val['fullPath'];
                // save into inbox table
                $idata['attach_filename'] = $val['fileName'];
                $idata['attach_file'] = $val['path'];
                $idata['attach_file_path'] = $val['fullPath'];
            } else {
                $data['attach_filename'] = NULL;
                $data['attach_file'] = NULL;
                $data['attach_file_path'] = NULL;
                // save into inbox table
                $idata['attach_filename'] = NULL;
                $idata['attach_file'] = NULL;
                $idata['attach_file_path'] = NULL;
            }
            $data['to'] = $v_email;
            /*
             * Email Configuaration 
             */
            $employee_id = $this->session->userdata('employee_id');

            //get employee email address by employee id
            $this->emp_model->_table_name = "tbl_employee"; // table name
            $this->emp_model->_order_by = "employee_id"; // $id
            $employee_details = $this->emp_model->get_by(array('employee_id' => $employee_id,), TRUE); // get result   

            $name = $employee_details->email;
            $info = $data['subject'];
            // set from email
            $from = array($name, $info);
            // set sender email
            $to = $v_email;
            //set subject
            $subject = $data['subject'];
            $data['employee_id'] = $employee_id;
            $data['message_time'] = date('Y-m-d H:i:s');
            // save into send 
            $this->mailbox_model->_table_name = 'tbl_send';
            $this->mailbox_model->_primary_key = 'send_id';
            $send_id = $this->mailbox_model->save($data);

            // get mail info by send id to send
            $this->mailbox_model->_table_name = 'tbl_send';
            $this->mailbox_model->_order_by = 'send_id';
            $data['read_mail'] = $this->mailbox_model->get_by(array('send_id' => $send_id), true);

            // set view page
            $view_page = $this->load->view('employee/read_mail', $data, TRUE);
            $send_email = $this->mail->sendEmail($from, $to, $subject, $view_page);
            // save into inbox table procees 
            $idata['to'] = $employee_details->email;
            $idata['from'] = $data['to'];
            $idata['subject'] = $data['subject'];
            $idata['message_body'] = $data['message_body'];
            $idata['message_time'] = date('Y-m-d H:i:s');
            // save into inbox
            $this->mailbox_model->_table_name = 'tbl_inbox';
            $this->mailbox_model->_primary_key = 'inbox_id';
            $this->mailbox_model->save($idata);
        }
        if ($send_email) {
            $type = "success";
            $message = "Your message has been sent.";
            set_message($type, $message);
            redirect('employee/dashboard/sent');
        } else {
            show_error($this->email->print_debugger());
        }
    }

    /*
     * Mailbox Controllers ends ------
     */

    public function change_password() {
        $data['menu'] = array("profile" => 1, "change_password" => 1);
        $data['title'] = "Change Password";
        $data['subview'] = $this->load->view('employee/change_password', $data, TRUE);
        $this->load->view('employee/_layout_main', $data);
    }

    public function check_employee_password($val) {
        $password = $this->hash($val);
        $check_dupliaction_id = $this->emp_model->check_by(array('password' => $password), 'tbl_employee_login');
        if (empty($check_dupliaction_id)) {
            $result = '<small style="padding-left:10px;color:red;font-size:10px">Your Entered Password Do Not Match !<small>';
        } else {
            $result = NULL;
        }
        echo $result;
    }

    public function set_password() {
        $employee_login_id = $this->session->userdata('employee_login_id');
        $data['password'] = $this->hash($this->input->post('new_password'));
        $this->emp_model->_table_name = 'tbl_employee_login';
        $this->emp_model->_primary_key = 'employee_login_id';
        $this->emp_model->save($data, $employee_login_id);
        $type = "success";
        $message = "Password Successfully Changed!";
        set_message($type, $message);
        redirect('employee/dashboard/change_password'); //redirect page
    }

    public function hash($string) {
        return hash('sha512', $string . config_item('encryption_key'));
    }

}
