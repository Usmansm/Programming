<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="wrap-fpanel">
            <div class="panel panel-default" data-collapsed="0">
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong><?php echo $this->language->form_heading()[20] ?></strong>
                    </div>
                </div>
                <form id="form" role="form" enctype="multipart/form-data" action="<?php echo base_url() ?>admin/payroll/generate_payslip" method="post" class="form-horizontal form-groups-bordered">
                    <div class="panel-body">
                        <div class="row"><br />
                            <div class="col-sm-12 form-groups-bordered">                                
                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-3 control-label"><?php echo $this->language->from_body()[14][0] ?>  <span class="required">*</span></label>
                                    <div class="col-sm-5">
                                        <select name="designations_id" class="form-control">                            
                                            <option value="">Select Designations.....</option>
                                            <?php if (!empty($all_department_info)): foreach ($all_department_info as $dept_name => $v_department_info) : ?>
                                                    <?php if (!empty($v_department_info)): ?>
                                                        <optgroup label="<?php echo $dept_name; ?>">
                                                            <?php foreach ($v_department_info as $designation) : ?>
                                                                <option value="<?php echo $designation->designations_id; ?>" 
                                                                <?php
                                                                if (!empty($designations_id)) {
                                                                    echo $designation->designations_id == $designations_id ? 'selected' : '';
                                                                }
                                                                ?>><?php echo $designation->designations ?></option>                            
                                                                    <?php endforeach; ?>
                                                        </optgroup>
                                                    <?php endif; ?>                            
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo $this->language->from_body()[14][5] ?>  <span class="required"> *</span></label>
                                    <div class="input-group col-sm-5">
                                        <input type="text"  value="<?php
                                        if (!empty($payment_date)) {
                                            echo $payment_date;
                                        }
                                        ?>" class="form-control monthyear" name="payment_date" data-format="yyyy/mm/dd">

                                        <div class="input-group-addon">
                                            <a href="#"><i class="entypo-calendar"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-3 control-label"></label>
                                    <div class="col-sm-5">
                                        <button id="submit" type="submit" name="sbtn" value="1" class="btn btn-primary btn-block">GO</button>
                                    </div>
                                </div>
                            </div><br />                            
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($flag)): ?>

    <div class="row">
        <div class="col-sm-12" data-offset="0">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        <span>
                            <strong>Generate Payslip for <?php
                                if (!empty($payment_date)) {
                                    echo '<span class="text-danger">' . date('F Y', strtotime($payment_date)) . '</span>';
                                }
                                ?></strong>
                        </span>
                    </div>
                </div>
                <!-- Table -->

                <table class="table table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>
                            <th class="col-sm-1">ID</th>
                            <th><strong>Full Name</strong></th>
                            <th><strong>Gross Salary</strong></th>
                            <th><strong>Deductions</strong></th>
                            <th><strong>Net Salary</strong></th>
                            <th><strong>Status</strong></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($check_salary_payment)): foreach ($check_salary_payment as $key => $v_emp_info): ?>
                                <?php if (!empty($v_emp_info)): ?>
                                    <tr>
                                        <td><?php echo $v_emp_info->employment_id; ?></td>
                                        <td><?php echo $v_emp_info->first_name . ' ' . $v_emp_info->last_name; ?></td>
                                        <td><?php echo $gross = $v_emp_info->basic_salary + $v_emp_info->house_rent_allowance + $v_emp_info->medical_allowance + $v_emp_info->special_allowance + $v_emp_info->fuel_allowance + $v_emp_info->phone_bill_allowance + $v_emp_info->other_allowance; ?></td>
                                        <td><?php echo $deduction = $v_emp_info->tax_deduction + $v_emp_info->provident_fund + $v_emp_info->other_deduction; ?></td>
                                        <td><?php echo $net_salary = $gross - $deduction; ?></td>
                                        <td><span class="label label-success">Paid</span></td>
                                        <td>
                                            <a class="text-success" href="<?php echo base_url()?>admin/payroll/receive_generated/<?php echo $v_emp_info->salary_payment_id;?>">Generate Payslip</a>
                                        </td>
                                    </tr>                    
                                <?php else: ?>    
                                    <tr>
                                        <td><?php echo $employee_info[$key]->employment_id; ?></td>
                                        <td><?php echo $employee_info[$key]->first_name . ' ' . $employee_info[$key]->last_name; ?></td>
                                        <td><?php echo $gross = $employee_info[$key]->basic_salary + $employee_info[$key]->house_rent_allowance + $employee_info[$key]->medical_allowance + $employee_info[$key]->special_allowance + $employee_info[$key]->fuel_allowance + $employee_info[$key]->phone_bill_allowance + $employee_info[$key]->other_allowance; ?></td>
                                        <td><?php echo $deduction = $employee_info[$key]->tax_deduction + $employee_info[$key]->provident_fund + $employee_info[$key]->other_deduction; ?></td>
                                        <td><?php echo $net_salary = $gross - $deduction; ?></td>
                                        <td><span class="label label-danger">Unpaid</span></td>
                                        <td>
                                            <a class="text-danger" href="<?php echo base_url()?>admin/payroll/make_payment">Make Payment</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>