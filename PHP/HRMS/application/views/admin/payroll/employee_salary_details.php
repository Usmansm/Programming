<div id="printableArea"> 
    <div class="row">
        <div class="col-sm-12 wrap-fpanel" data-spy="scroll" data-offset="0">                            
            <div class="panel panel-default">            
                <!-- main content -->
                <div class="panel-heading hidden-print">
                    <div class="row">
                        <div  class="col-lg-12 panel-title">
                            <strong>Employee Salary Detail</strong>
                            <div class="pull-right">                               
                                <span><?php echo btn_edit('admin/payroll/manage_salary_details/' . $emp_salary_info->employee_id . '/' . $emp_salary_info->designations_id); ?></span>
                                <span><?php echo btn_pdf('admin/payroll/make_pdf/' . $emp_salary_info->employee_id); ?></span>
                                <button class="btn-print" type="button" data-toggle="tooltip" title="Print" onclick="printDiv('printableArea')"><?php echo btn_print(); ?></button>                                                              
                            </div>
                        </div>
                    </div>
                </div>                                  
                <div class="show_print" style="width: 100%; border-bottom: 2px solid black;">
                    <table style="width: 100%; vertical-align: middle;">
                        <tr>
                            <?php
                            $genaral_info = $this->session->userdata('genaral_info');
                            if (!empty($genaral_info)) {
                                foreach ($genaral_info as $info) {
                                    ?>
                                    <td style="width: 35px; border: 0px;">
                                        <img style="width: 50px;height: 50px" src="<?php echo base_url() . $info->logo ?>" alt="" class="img-circle"/>
                                    </td>
                                    <td style="border: 0px;">
                                        <p style="margin-left: 10px; font: 14px lighter;"><?php echo $info->name ?></p>
                                    </td>
                                    <?php
                                }
                            } else {
                                ?>
                                <td style="width: 35px; border: 0px;">
                                    <img style="width: 50px;height: 50px" src="<?php echo base_url() ?>img/logo.png" alt="Logo" class="img-circle"/>
                                </td>
                                <td style="border: 0px;">
                                    <p style="margin-left: 10px; font: 14px lighter;">Human Resource Management System</p>
                                </td>
                                <?php
                            }
                            ?>
                        </tr>
                    </table>
                </div><!--            show when print start-->            
                <div class="col-lg-12" style="background: #ECF0F1;margin-bottom: 20px;" >
                    <div class="row">                            
                        <div class="col-lg-2 col-sm-2">
                            <div class="fileinput-new thumbnail" style="width: 144px; height: 158px; margin-top: 14px; margin-left: 16px; background-color: #EBEBEB;">
                                <?php if ($emp_salary_info->photo): ?>
                                    <img src="<?php echo base_url() . $emp_salary_info->photo; ?>" style="width: 142px; height: 148px; border-radius: 3px;" >  
                                <?php else: ?>
                                    <img src="<?php echo base_url() ?>/img/user.png" alt="Employee_Image">
                                <?php endif; ?>         
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            &nbsp;
                        </div>
                        <div class="col-lg-8 col-sm-8 ">
                            <div>
                                <div style="margin-left: 20px;">                                        
                                    <h3><?php echo "$emp_salary_info->first_name " . "$emp_salary_info->last_name"; ?></h3>
                                    <hr />
                                    <table style="border: none">
                                        <tr>
                                            <td><strong>Employee ID</strong></td>
                                            <td>&nbsp;&nbsp;&nbsp;</td>
                                            <td><?php echo "$emp_salary_info->employment_id"; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Department</strong></td>
                                            <td>&nbsp;&nbsp;&nbsp;</td>
                                            <td><?php echo "$emp_salary_info->department_name"; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Designation</strong></td>
                                            <td>&nbsp;&nbsp;&nbsp;</td>
                                            <td><?php echo "$emp_salary_info->designations"; ?></td>
                                        </tr>                                                                                
                                        <tr>
                                            <td><strong>Joining Date</strong></td>
                                            <td>&nbsp;&nbsp;&nbsp;</td>
                                            <td><?php echo date('d M Y', strtotime($emp_salary_info->joining_date)); ?></td>
                                        </tr>                                            
                                    </table>                                                                           
                                </div>
                            </div>
                        </div>

                    </div>
                </div>                
            </div>                
        </div>                
    </div>                

    <div class="row">
        <div class="wrap-fpanel form-horizontal">
            <!-- ********************************* Salary Details Panel ***********************-->
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong>Salary Details</strong>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="">
                            <label for="field-1" class="col-sm-3 control-label"><strong>Employment Type :</strong></label>
                            <p class="form-control-static"><?php
                                if ($emp_salary_info->employment_type == 1) {
                                    echo 'Provision';
                                } else {
                                    echo 'Permanent';
                                }
                                ?></p>
                        </div>
                        <div class="">
                            <label for="field-1" class="col-sm-3 control-label"><strong>Basic Salary :</strong> </label>                    
                            <p class="form-control-static"><?php
                                if (!empty($genaral_info[0]->currency)) {
                                    $currency = $genaral_info[0]->currency;
                                } else {
                                    $currency = '$';
                                }
                                echo $currency . ' ' . number_format($emp_salary_info->basic_salary, 2);
                                ?></p>                    
                        </div>
                    </div>
                </div>
            </div><!-- ***************** Salary Details  Ends *********************-->

            <!-- ******************-- Allowance Panel Start **************************-->
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong>Allowances</strong>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (!empty($emp_salary_info->house_rent_allowance)): ?>
                            <div class="">
                                <label class="col-sm-6 control-label" ><strong>House Rent Allowance  : </strong></label>
                                <p class="form-control-static"><?php
                                    if (!empty($genaral_info[0]->currency)) {
                                        $currency = $genaral_info[0]->currency;
                                    } else {
                                        $currency = '$';
                                    }
                                    echo $currency . ' ' . number_format($emp_salary_info->house_rent_allowance, 2);
                                    ?></p>                         
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($emp_salary_info->medical_allowance)): ?>
                            <div class="">
                                <label class="col-sm-6 control-label" ><strong>Medical Allowance  : </strong></label>
                                <p class="form-control-static"><?php
                                    if (!empty($genaral_info[0]->currency)) {
                                        $currency = $genaral_info[0]->currency;
                                    } else {
                                        $currency = '$';
                                    }
                                    echo $currency . ' ' . number_format($emp_salary_info->medical_allowance, 2);
                                    ?></p>                        
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($emp_salary_info->special_allowance)): ?>
                            <div class="">
                                <label class="col-sm-6 control-label" ><strong>Special Allowance  : </strong></label>
                                <p class="form-control-static"><?php
                                    if (!empty($genaral_info[0]->currency)) {
                                        $currency = $genaral_info[0]->currency;
                                    } else {
                                        $currency = '$';
                                    }
                                    echo $currency . ' ' . number_format($emp_salary_info->special_allowance, 2);
                                    ?></p>                         
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($emp_salary_info->fuel_allowance)): ?>
                            <div class="">
                                <label class="col-sm-6 control-label" ><strong>Fuel Allowance  : </strong></label>
                                <p class="form-control-static"><?php
                                    if (!empty($genaral_info[0]->currency)) {
                                        $currency = $genaral_info[0]->currency;
                                    } else {
                                        $currency = '$';
                                    }
                                    echo $currency . ' ' . number_format($emp_salary_info->fuel_allowance, 2);
                                    ?></p>                         
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($emp_salary_info->phone_bill_allowance)): ?>
                            <div class="">
                                <label class="col-sm-6 control-label" ><strong>Phone Bill Allowance  : </strong></label>
                                <p class="form-control-static"><?php
                                    if (!empty($genaral_info[0]->currency)) {
                                        $currency = $genaral_info[0]->currency;
                                    } else {
                                        $currency = '$';
                                    }
                                    echo $currency . ' ' . number_format($emp_salary_info->phone_bill_allowance, 2);
                                    ?></p>                         
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($emp_salary_info->other_allowance)): ?>
                            <div class="">
                                <label class="col-sm-6 control-label" ><strong>Other Allowance  : </strong></label>
                                <p class="form-control-static"><?php
                                    if (!empty($genaral_info[0]->currency)) {
                                        $currency = $genaral_info[0]->currency;
                                    } else {
                                        $currency = '$';
                                    }
                                    echo $currency . ' ' . number_format($emp_salary_info->other_allowance, 2);
                                    ?></p>                        
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div><!-- ********************Allowance End ******************-->

            <!-- ************** Deduction Panel Column  **************-->
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong>Deductions</strong>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (!empty($emp_salary_info->provident_fund)): ?>
                            <div class="">
                                <label class="col-sm-6 control-label" ><strong>Provident Fund  : </strong></label>
                                <p class="form-control-static"><?php
                                    if (!empty($genaral_info[0]->currency)) {
                                        $currency = $genaral_info[0]->currency;
                                    } else {
                                        $currency = '$';
                                    }
                                    echo $currency . ' ' . number_format($emp_salary_info->provident_fund, 2);
                                    ?></p>                         
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($emp_salary_info->tax_deduction)): ?>
                            <div class="">
                                <label class="col-sm-6 control-label" ><strong>Tax Deduction  : </strong></label>
                                <p class="form-control-static"><?php
                                    if (!empty($genaral_info[0]->currency)) {
                                        $currency = $genaral_info[0]->currency;
                                    } else {
                                        $currency = '$';
                                    }
                                    echo $currency . ' ' . number_format($emp_salary_info->tax_deduction, 2);
                                    ?></p>                        
                            </div>    
                        <?php endif; ?>
                        <?php if (!empty($emp_salary_info->other_deduction)): ?>
                            <div class="">
                                <label class="col-sm-6 control-label" ><strong>Other Deduction  : </strong></label>
                                <p class="form-control-static"><?php
                                    if (!empty($genaral_info[0]->currency)) {
                                        $currency = $genaral_info[0]->currency;
                                    } else {
                                        $currency = '$';
                                    }
                                    echo $currency . ' ' . number_format($emp_salary_info->other_deduction, 2);
                                    ?></p>                       
                            </div>
                        <?php endif; ?>
                    </div>
                </div>                    
            </div><!-- ****************** Deduction End  *******************-->

            <!-- ************** Total Salary Details Start  **************-->
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <strong>Total Salary Details</strong>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="">
                            <label class="col-sm-6 control-label" ><strong>Gross Salary  : </strong></label>
                            <p class="form-control-static"><?php
                                if (!empty($genaral_info[0]->currency)) {
                                    $currency = $genaral_info[0]->currency;
                                } else {
                                    $currency = '$';
                                }
                                $gross = $emp_salary_info->basic_salary + $emp_salary_info->house_rent_allowance + $emp_salary_info->medical_allowance + $emp_salary_info->special_allowance + $emp_salary_info->fuel_allowance + $emp_salary_info->phone_bill_allowance + $emp_salary_info->other_allowance;
                                echo $currency . ' ' . number_format($gross, 2);
                                ?></p>
                        </div>
                        <div class="">
                            <label class="col-sm-6 control-label" ><strong>Total Deduction  : </strong></label>
                            <p class="form-control-static"><?php
                                if (!empty($genaral_info[0]->currency)) {
                                    $currency = $genaral_info[0]->currency;
                                } else {
                                    $currency = '$';
                                }
                                $deduction = $emp_salary_info->tax_deduction + $emp_salary_info->provident_fund + $emp_salary_info->other_deduction;
                                echo $currency . ' ' . number_format($deduction, 2);
                                ?></p>
                        </div>                                                        
                        <div class="">
                            <label class="col-sm-6 control-label" ><strong>Net Salary  : </strong></label>
                            <p class="form-control-static"><?php
                                if (!empty($genaral_info[0]->currency)) {
                                    $currency = $genaral_info[0]->currency;
                                } else {
                                    $currency = '$';
                                }
                                $net_salary = $gross - $deduction;
                                echo $currency . ' ' . number_format($net_salary, 2);
                                ?></p>
                        </div>                                                        
                    </div>
                </div>                    
            </div><!-- ****************** Total Salary Details End  *******************-->
        </div>  
    </div>  

    <script type="text/javascript">
        function printDiv(printableArea) {
            var printContents = document.getElementById(printableArea).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>

