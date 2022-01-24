<?php include_once 'asset/admin-ajax.php'; ?>
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="row">
    <div class="col-sm-12 wrap-fpanel" data-offset="0">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>
                        <strong><?php echo $this->language->form_heading()[18] ?></strong>
                    </span>
                </div>
            </div>
            <!-- Table -->

            <table class="table table-bordered table-hover" id="dataTables-example">
                <thead>
                    <tr>
                        <th class="col-sm-1">ID</th>
                        <th>Full Name</th>
                        <th>Gross Salary</th>
                        <th>Deductions</th>
                        <th>Net Salary</th>
                        <th>Emp Type</th>
                        <th class="col-sm-1">Details</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($emp_salary_info)):foreach ($emp_salary_info as $v_emp_salary): ?>                    
                            <tr>
                                <td><?php echo $v_emp_salary->employment_id; ?></td>
                                <td><?php echo $v_emp_salary->first_name . ' ' . $v_emp_salary->last_name ?></td>
                                <td><?php echo $gross = $v_emp_salary->basic_salary + $v_emp_salary->house_rent_allowance + $v_emp_salary->medical_allowance + $v_emp_salary->special_allowance + $v_emp_salary->fuel_allowance + $v_emp_salary->phone_bill_allowance + $v_emp_salary->other_allowance ?></td>
                                <td><?php echo $deduction = $v_emp_salary->tax_deduction + $v_emp_salary->provident_fund + $v_emp_salary->other_deduction ?></td>
                                <td><?php echo $gross - $deduction ?></td>
                                <td><?php
                                    if ($v_emp_salary->employment_type == 1) {
                                        echo 'Provision';
                                    } else {
                                        echo 'Permanent';
                                    }
                                    ?></td>
                                <td><?php echo btn_view('admin/payroll/view_salary_details/' . $v_emp_salary->employee_id); ?></td>
                                <td>
                                    <?php echo btn_edit('admin/payroll/manage_salary_details/' . $v_emp_salary->employee_id . '/' . $v_emp_salary->designations_id); ?>                                    
                                </td>
                            </tr>                
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#date').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
        });
    });
</script>