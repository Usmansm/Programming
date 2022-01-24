<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        // $this->load->library('encryption');
    }

    public function create_user($id = null) {
        $data['title'] = "Create User";

        if (!empty($id)) {
            $data['employee_id'] = $this->encryption->decrypt($id);
        } else {
            $data['employee_id'] = null;
        }

        $this->user_model->_table_name = "tbl_menu"; //table name
        $this->user_model->_order_by = "menu_id";
        $menu_info = $this->user_model->get();

        foreach ($menu_info as $items) {
            $menu['parents'][$items->parent][] = $items;
        }

        $data['result'] = $this->buildChild(0, $menu);

        $this->user_model->_table_name = "tbl_user"; //table name
        $this->user_model->_order_by = "user_id";

        $data['employee_login_details'] = $this->user_model->get_by(array('user_id' => $data['employee_id']), TRUE);


        if ($data['employee_login_details']) {

            $role = $this->user_model->select_user_roll_by_employee_id($data['employee_id']);

            if ($role) {
                foreach ($role as $value) {
                    $result[$value->menu_id] = $value->menu_id;
                }

                $data['roll'] = $result;
            }
        } else {
            $data['employee_login_details'] = $this->user_model->get_new_user();
        }


        $data['subview'] = $this->load->view('admin/user/create_user', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function buildChild($parent, $menu) {

        if (isset($menu['parents'][$parent])) {

            foreach ($menu['parents'][$parent] as $ItemID) {

                if (!isset($menu['parents'][$ItemID->menu_id])) {
                    $result[$ItemID->label] = $ItemID->menu_id;
                }
                if (isset($menu['parents'][$ItemID->menu_id])) {
                    $result[$ItemID->label][$ItemID->menu_id] = self::buildChild($ItemID->menu_id, $menu);
                }
            }
        }
        return $result;
    }

    public function user_list() {
        $data['menu'] = array("user_role" => 1, "c_user_role" => 1);
        $data['title'] = "Create User";

        $this->user_model->_table_name = "tbl_user"; //table name
        $this->user_model->_order_by = "user_id";

        $data['all_employee_info'] = $this->user_model->get();

        $data['subview'] = $this->load->view('admin/user/user_list', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_user() {
        $data = $this->user_model->array_from_post(array('user_name', 'name', 'email', 'flag'));
        $employee_id = $this->input->post('employee_id');
        $password = $this->input->post('password');
        $password == '****************' || $data['password'] = $this->encryption->hash($this->input->post('password'));

        //delete existing userroll by login id
        if (!empty($employee_id)) {
            $this->user_model->_table_name = "tbl_user_role"; //table name
            $this->user_model->_order_by = "user_id";
            $this->user_model->_primary_key = "user_role_id";

            $roll = $this->user_model->get_by(array('user_id' => $employee_id), FALSE);

            foreach ($roll as $v_roll) {
                $this->user_model->delete($v_roll->user_role_id);
            }
        }

        $this->user_model->_table_name = "tbl_user"; // table name
        $this->user_model->_primary_key = "user_id"; // $id

        if (!empty($employee_id)) {
            $id = $this->user_model->save($data, $employee_id);
        } else {
            $id = $this->user_model->save($data);
        }

        $this->user_model->_table_name = "tbl_user_role"; // table name
        $this->user_model->_primary_key = "user_role_id"; // $id
        $menu = $this->user_model->array_from_post(array('menu'));
        if (!empty($menu['menu'])) {
            foreach ($menu as $v_menu) {
                foreach ($v_menu as $value) {
                    $mdata['menu_id'] = $value;
                    $mdata['user_id'] = $id;
                    $this->user_model->save($mdata);
                }
            }
        }
        if (!empty($employee_id)) {
            $type = "success";
            $message = "User Login Information Update Successfully!";
            set_message($type, $message);
            redirect('admin/user/user_list'); //redirect page
        } else {
            $type = "success";
            $message = "New User Create Successfully!";
            set_message($type, $message);
            redirect('admin/user/user_list'); //redirect page
        }
    }

    public function delete_user($id = null) {
        if (!empty($id)) {
            $id = $this->encryption->decrypt($id);
            $user_id = $this->session->userdata('employee_id');

            //checking login user trying delete his own account
            if ($id == $user_id) {
                //same user can not delete his own account
                // redirect with error msg
                $type = "error";
                $message = "Sorry You can not delete your own account!";
                set_message($type, $message);
                redirect('admin/user/user_list'); //redirect page
            } else {
                //delete procedure run
                // Check user in db or not
                $this->user_model->_table_name = "tbl_user"; //table name
                $this->user_model->_order_by = "user_id";
                $result = $this->user_model->get_by(array('user_id' => $id), true);

                if (count($result)) {
                    //delete user roll id
                    $this->db->where('user_id', $id);
                    $this->db->delete('tbl_user_role');
                    //delete user by id
                    $this->db->where('user_id =', $id);
                    $this->db->delete('tbl_user');
                    //redirect successful msg
                    $type = "success";
                    $message = "User Delete Successfully!";
                    set_message($type, $message);
                    redirect('admin/user/user_list'); //redirect page
                } else {
                    //redirect error msg
                    $type = "error";
                    $message = "Sorry this user not find in database!";
                    set_message($type, $message);
                    redirect('admin/user/user_list'); //redirect page
                }
            }
        }
    }

}
