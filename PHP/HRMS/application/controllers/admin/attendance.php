<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of attendance
 *
 * @author NaYeM
 */
class Attendance extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('attendance_model');
    }

    public function manage_attendance() {
        //author By NaYeM        
        $data['title'] = "Set Attendance";
        $this->attendance_model->_table_name = "tbl_leave_category"; //table name
        $this->attendance_model->_order_by = "leave_category_id";
        $data['all_leave_category_info'] = $this->attendance_model->get();
        $this->attendance_model->_table_name = "tbl_department"; //table name
        $this->attendance_model->_order_by = "department_id";
        $data['all_department'] = $this->attendance_model->get();
        $data['department_id'] = $this->input->post('department_id');
        $data['date'] = $this->input->post('date', TRUE);
        $sbtnType = $this->input->post('sbtn');
        $flag = $this->session->userdata('flag');
        if ($sbtnType == 1 || $flag == 1) {
            if ($flag) {
                $data['date'] = $this->session->userdata('date');
                $data['department_id'] = $this->session->userdata('department_id');
                $this->session->unset_userdata('date');
                $this->session->unset_userdata('flag');
                $this->session->unset_userdata('department_id');
            } else {

                $data['date'] = $this->input->post('date');
                $data['department_id'] = $this->input->post('department_id');
            }
        }
        $data['employee_info'] = $this->attendance_model->get_employee_id_by_dept_id($data['department_id']);

        foreach ($data['employee_info'] as $v_employee) {
            $where = array('employee_id' => $v_employee->employee_id, 'date' => $data['date']);
            $data['atndnce'][] = $this->attendance_model->check_by($where, 'tbl_attendance');
        }
        $data['subview'] = $this->load->view('admin/attendance/manage_attendance', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_attendance() {
        $this->attendance_model->_table_name = "tbl_attendance"; // table name
        $this->attendance_model->_primary_key = "attendance_id"; // $id                    
        $attendance_status = $this->input->post('attendance', TRUE);

        $leave_category_id = $this->input->post('leave_category_id', TRUE);

        $employee_id = $this->input->post('employee_id', TRUE);

        $attendance_id = $this->input->post('attendance_id', TRUE);
        if (!empty($attendance_id)) {
            $key = 0;
            foreach ($employee_id as $empID) {
                $data['date'] = $this->input->post('date', TRUE);
                $data['attendance_status'] = 0;
                $data['employee_id'] = $empID;
                if (!empty($leave_category_id[$key])) {
                    $data['leave_category_id'] = $leave_category_id[$key];
                } else {
                    $data['leave_category_id'] = NULL;
                }
                if (!empty($attendance_status)) {
                    foreach ($attendance_status as $v_status) {
                        if ($empID == $v_status) {
                            $data['attendance_status'] = 1;
                            $data['leave_category_id'] = NULL;
                        }
                    }
                }
                $id = $attendance_id[$key];
                if (!empty($id)) {
                    $this->attendance_model->save($data, $id);
                } else {
                    $this->attendance_model->save($data, $id);
                }

                $key++;
            }
        } else {
            $key = 0;

            foreach ($employee_id as $empID) {
                $data['date'] = $this->input->post('date', TRUE);
                $data['attendance_status'] = 0;
                $data['employee_id'] = $empID;
                if (!empty($leave_category_id[$key])) {
                    $data['leave_category_id'] = $leave_category_id[$key];
                } else {
                    $data['leave_category_id'] = NULL;
                }
                if (!empty($attendance_status)) {
                    foreach ($attendance_status as $v_status) {
                        if ($empID == $v_status) {
                            $data['attendance_status'] = 1;
                            $data['leave_category_id'] = NULL;
                        }
                    }
                }
                $this->attendance_model->save($data);
                $key++;
            }
        }
        $fdata['department_id'] = $this->input->post('department_id', TRUE);
        $fdata['date'] = $this->input->post('date');
        $fdata['flag'] = 1;
        $this->session->set_userdata($fdata);
        // messages for user        
        $type = "success";
        $message = "Attendance Information Successfully Saved!";
        set_message($type, $message);
        redirect('admin/attendance/manage_attendance'); //redirect page
    }

    public function attendance_report() {
        $data['title'] = "Attendance Report";
        $this->attendance_model->_table_name = "tbl_department"; //table name
        $this->attendance_model->_order_by = "department_id";
        $data['all_department'] = $this->attendance_model->get();
        $data['subview'] = $this->load->view('admin/attendance/attendance_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function get_report() {
        $department_id = $this->input->post('department_id', TRUE);
        $date = $this->input->post('date', TRUE);
        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $data['employee'] = $this->attendance_model->get_employee_id_by_dept_id($department_id);
        $day = date('d', strtotime($date));
        for ($i = 1; $i <= $num; $i++) {
            $data['dateSl'][] = $i;
        }
        $holidays = $this->global_model->get_holidays(); //tbl working Days Holiday

        if ($month >= 1 && $month <= 9) {
            $yymm = $year . '-' . '0' . $month;
        } else {
            $yymm = $year . '-' . $month;
        }

        $public_holiday = $this->global_model->get_public_holidays($yymm);


        //tbl a_calendar Days Holiday        
        if (!empty($public_holiday)) {
            foreach ($public_holiday as $p_holiday) {
                for ($k = 1; $k <= $num; $k++) {

                    if ($k >= 1 && $k <= 9) {
                        $sdate = $yymm . '-' . '0' . $k;
                    } else {
                        $sdate = $yymm . '-' . $k;
                    }

                    if ($p_holiday->start_date == $sdate && $p_holiday->end_date == $sdate) {
                        $p_hday[] = $sdate;
                    }
                    if ($p_holiday->start_date == $sdate) {
                        for ($j = $p_holiday->start_date; $j <= $p_holiday->end_date; $j++) {
                            $p_hday[] = $j;
                        }
                    }
                }
            }
        }
        foreach ($data['employee'] as $sl => $v_employee) {
            $key = 1;
            $x = 0;
            for ($i = 1; $i <= $num; $i++) {

                if ($i >= 1 && $i <= 9) {

                    $sdate = $yymm . '-' . '0' . $i;
                } else {
                    $sdate = $yymm . '-' . $i;
                }
                $day_name = date('l', strtotime("+$x days", strtotime($year . '-' . $month . '-' . $key)));



                if (!empty($holidays)) {
                    foreach ($holidays as $v_holiday) {

                        if ($v_holiday->day == $day_name) {
                            $flag = 'H';
                        }
                    }
                }
                if (!empty($p_hday)) {
                    foreach ($p_hday as $v_hday) {
                        if ($v_hday == $sdate) {
                            $flag = 'H';
                        }
                    }
                }
                if (!empty($flag)) {
                    $data['attendance'][$sl][] = $this->attendance_model->attendance_report_by_empid($v_employee->employee_id, $sdate, $flag);
                } else {
                    $data['attendance'][$sl][] = $this->attendance_model->attendance_report_by_empid($v_employee->employee_id, $sdate);
                }

                $key++;
                $flag = '';
            }
        }        
        $data['title'] = "Attendance Report";
        $this->attendance_model->_table_name = "tbl_department"; //table name
        $this->attendance_model->_order_by = "department_id";
        $data['all_department'] = $this->attendance_model->get();
        $data['department_id'] = $this->input->post('department_id', TRUE);
        $data['date'] = $this->input->post('date', TRUE);
        $where = array('department_id' => $department_id);
        $data['dept_name'] = $this->attendance_model->check_by($where, 'tbl_department');

        $data['month'] = date('F-Y', strtotime($yymm));
        $data['subview'] = $this->load->view('admin/attendance/attendance_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function create_pdf($department_id, $date) {
        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $data['employee'] = $this->attendance_model->get_employee_id_by_dept_id($department_id);

        $day = date('d', strtotime($date));
        for ($i = 1; $i <= $num; $i++) {
            $data['dateSl'][] = $i;
        }
        $holidays = $this->global_model->get_holidays(); //tbl working Days Holiday
        if ($month >= 1 && $month <= 9) {
            $yymm = $year . '-' . '0' . $month;
        } else {
            $yymm = $year . '-' . $month;
        }
        $public_holiday = $this->global_model->get_public_holidays($yymm);
        if (!empty($public_holiday)) {
            //tbl a_calendar Days Holiday        
            foreach ($public_holiday as $p_holiday) {
                for ($k = 1; $k <= $num; $k++) {

                    if ($k >= 1 && $k <= 9) {
                        $sdate = $yymm . '-' . '0' . $k;
                    } else {
                        $sdate = $yymm . '-' . $k;
                    }
                    if ($p_holiday->start_date == $sdate && $p_holiday->end_date == $sdate) {
                        $p_hday[] = $sdate;
                    }
                    if ($p_holiday->start_date == $sdate) {
                        for ($j = $p_holiday->start_date; $j <= $p_holiday->end_date; $j++) {
                            $p_hday[] = $j;
                        }
                    }
                }
            }
        }
        foreach ($data['employee'] as $sl => $v_employee) {
            $key = 1;
            $x = 0;
            for ($i = 1; $i <= $num; $i++) {

                if ($i >= 1 && $i <= 9) {

                    $sdate = $yymm . '-' . '0' . $i;
                } else {
                    $sdate = $yymm . '-' . $i;
                }
                $day_name = date('l', strtotime("+$x days", strtotime($year . '-' . $month . '-' . $key)));
                if (!empty($holidays)) {
                    foreach ($holidays as $v_holiday) {

                        if ($v_holiday->day == $day_name) {
                            $flag = 'H';
                        }
                    }
                }
                if (!empty($p_hday)) {
                    foreach ($p_hday as $v_hday) {
                        if ($v_hday == $sdate) {
                            $flag = 'H';
                        }
                    }
                }
                if (!empty($flag)) {
                    $data['attendance'][$sl][] = $this->attendance_model->attendance_report_by_empid($v_employee->employee_id, $sdate, $flag);
                } else {
                    $data['attendance'][$sl][] = $this->attendance_model->attendance_report_by_empid($v_employee->employee_id, $sdate);
                }
                $key++;
                $flag = '';
            }
        }
        $where = array('department_id' => $department_id);
        $data['dept_name'] = $this->attendance_model->check_by($where, 'tbl_department');
        $data['date'] = date('F-Y', strtotime($yymm));
        $this->load->helper('dompdf');
        $view_file = $this->load->view('admin/attendance/Emp_report_pdf', $data, true);
        $file_name = pdf_create($view_file, date('F-Y', strtotime($yymm)));
        echo $file_name;
    }

    public function create_excel($department_id, $date) {
        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $employee = $this->attendance_model->get_employee_id_by_dept_id($department_id);
        $where = array('department_id' => $department_id);
        $dept_name = $this->attendance_model->check_by($where, 'tbl_department');

        $day = date('d', strtotime($date));
        for ($i = 1; $i <= $num; $i++) {
            $dateSl[] = $i;
        }
        $holidays = $this->global_model->get_holidays(); //tbl working Days Holiday
        if ($month >= 1 && $month <= 9) {
            $yymm = $year . '-' . '0' . $month;
        } else {
            $yymm = $year . '-' . $month;
        }
        $public_holiday = $this->global_model->get_public_holidays($yymm);
        if (!empty($public_holiday)) {
            //tbl a_calendar Days Holiday        
            foreach ($public_holiday as $p_holiday) {
                for ($k = 1; $k <= $num; $k++) {

                    if ($k >= 1 && $k <= 9) {
                        $sdate = $yymm . '-' . '0' . $k;
                    } else {
                        $sdate = $yymm . '-' . $k;
                    }
                    if ($p_holiday->start_date == $sdate && $p_holiday->end_date == $sdate) {
                        $p_hday[] = $sdate;
                    }
                    if ($p_holiday->start_date == $sdate) {
                        for ($j = $p_holiday->start_date; $j <= $p_holiday->end_date; $j++) {
                            $p_hday[] = $j;
                        }
                    }
                }
            }
        }
        foreach ($employee as $sl => $v_employee) {
            $key = 1;
            $x = 0;
            for ($i = 1; $i <= $num; $i++) {

                if ($i >= 1 && $i <= 9) {

                    $sdate = $yymm . '-' . '0' . $i;
                } else {
                    $sdate = $yymm . '-' . $i;
                }
                $day_name = date('l', strtotime("+$x days", strtotime($year . '-' . $month . '-' . $key)));
                if (!empty($holidays)) {
                    foreach ($holidays as $v_holiday) {

                        if ($v_holiday->day == $day_name) {
                            $flag = 'H';
                        }
                    }
                }
                if (!empty($p_hday)) {
                    foreach ($p_hday as $v_hday) {
                        if ($v_hday == $sdate) {
                            $flag = 'H';
                        }
                    }
                }
                if (!empty($flag)) {
                    $attendance[$sl][] = $this->attendance_model->attendance_report_by_empid($v_employee->employee_id, $sdate, $flag);
                } else {
                    $attendance[$sl][] = $this->attendance_model->attendance_report_by_empid($v_employee->employee_id, $sdate);
                }
                $key++;
                $flag = '';
            }
        }
        //load PHPExcel library
        $this->load->library('Excel');
        ob_start();
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $styleArray = array(
            'font' => array(
                'size' => 13,
                'name' => 'Verdana'
        ));
        $fontArray = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Verdana'
        ));
        $dateArray = array(
            'font' => array(
                'bold' => true,
        ));
        $bgcolor = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E7E7E7'),
        ));
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . '1', 'Date:')
                ->setCellValue('D' . '1', date('F-Y', strtotime($yymm)));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B1:C1');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D1:H1');
        $objPHPExcel->getActiveSheet()->getStyle('B1:C1')->applyFromArray($fontArray);

        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('J' . '1', 'Department:')
                ->setCellValue('N' . '1', $dept_name->department_name);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J1:M1');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N1:V1');
        $objPHPExcel->getActiveSheet()->getStyle('J1:L1')->applyFromArray($fontArray);

// Set document properties
        $objPHPExcel->getProperties()->setCreator("Comprehensive School Management")
                ->setLastModifiedBy("Comprehensive School Management")
                ->setTitle("Office  XLSX Test Document")
                ->setSubject("Office XLSX Test Document")
                ->setDescription("Test document for Office XLSX, generated by PHP classes.")
                ->setKeywords("office openxml php")
                ->setCategory("Excel Sheet");


// Add some data                
        $cl = 'B';
        $bg = 'A';
        foreach ($dateSl as $date) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($cl . '3', $date);
            $objPHPExcel->getActiveSheet()->getColumnDimension($cl)->setWidth(3);
            $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getStyle($cl . '3')->getFont()->setSize(9);
            $objPHPExcel->getActiveSheet()->getStyle($cl . '3')->applyFromArray($dateArray);
            $objPHPExcel->getActiveSheet()->getStyle($cl . '4')->applyFromArray($bgcolor);
            $objPHPExcel->getActiveSheet()->getStyle($cl . '1')->applyFromArray($bgcolor);
            $objPHPExcel->getActiveSheet()->getStyle($cl . '2')->applyFromArray($bgcolor);
            $objPHPExcel->getActiveSheet()->getStyle($bg . '4')->applyFromArray($bgcolor);
            $objPHPExcel->getActiveSheet()->getStyle($bg . '1')->applyFromArray($bgcolor);
            $objPHPExcel->getActiveSheet()->getStyle($bg . '2')->applyFromArray($bgcolor);
            $objPHPExcel->getActiveSheet()->getStyle($cl . '3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $cl++;
        }

        $row = 5;
        $c = 0;
        foreach ($attendance as $name => $v_Emp) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A3', 'Name')
                    ->setCellValue('A' . $row, $employee[$name]->first_name . ' ' . $employee[$name]->last_name);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A1')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
            $col = 1;
            foreach ($v_Emp as $v_result) {
                if (!empty($v_result)) {
                    foreach ($v_result as $Emp_atndnce) {
                        $objPHPExcel->getActiveSheet()->getStyle($row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle($row, $col)->getFont()->setSize(10);
                        if ($Emp_atndnce->attendance_status == '0') {
                            $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValueByColumnAndRow($col, $row, 'A');
                        }
                        if ($Emp_atndnce->attendance_status == '1') {
                            $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValueByColumnAndRow($col, $row, 'P');
                        }
                        if ($Emp_atndnce->attendance_status == 'H') {
                            $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValueByColumnAndRow($col, $row, 'H');
                        }
                        $col++;
                    }
                }
            }
            $row++;
            $c++;
        }
// Rename worksheet (worksheet, not filename)
        $objPHPExcel->getActiveSheet()->setTitle('Student Attendance');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
//clean the output buffer
        ob_end_clean();
//this is the header given from PHPExcel examples. but the output seems somewhat corrupted in some cases.
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//so, we use this header instead.
        $filename = date('M-Y', strtotime($yymm)) . '  ' . 'Employee Attendance.xls'; //save our workbook as this file name
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

}
