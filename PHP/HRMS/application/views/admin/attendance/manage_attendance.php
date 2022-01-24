<?php include_once 'asset/admin-ajax.php'; ?>
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<div class="row">
    <div class="col-sm-12"> 

        <div class="row">
            <div class="col-sm-12" data-offset="0">    
                <div class="wrap-fpanel">
                    <div class="panel panel-default" data-collapsed="0">                    
                        <div class="panel-heading">
                            <div class="panel-title">
                                <strong><?php echo $this->language->form_heading()[14] ?></strong>
                            </div>
                        </div>
                        <div class="panel-body">
                            <form id="form" action="<?php echo base_url() ?>admin/attendance/manage_attendance" method="post"  enctype="multipart/form-data" class="form-horizontal">   
                                <div class="panel_controls">                         
                                    <div class="form-group margin">
                                        <label class="col-sm-3 control-label"><?php echo $this->language->from_body()[15][0] ?> <span class="required">*</span></label>

                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <input type="text" name="date" id="date"  placeholder="Enter Day"  class="form-control" value="<?php
                                                if (!empty($date)) {
                                                    echo $date;
                                                }
                                                ?>" data-format="dd-mm-yyyy">
                                                <div class="input-group-addon">
                                                    <a href="#"><i class="entypo-calendar"></i></a>
                                                </div>
                                            </div>
                                        </div>                                                                                                
                                    </div>
                                    <div class="form-group">
                                        <label for="field-1" class="col-sm-3 control-label"><?php echo $this->language->from_body()[15][1] ?> <span class="required">*</span></label>

                                        <div class="col-sm-5">
                                            <select name="department_id" id="department" class="form-control">
                                                <option value="" >Select Department...</option>
                                                <?php foreach ($all_department as $v_department) : ?>
                                                    <option value="<?php echo $v_department->department_id ?>"                                                     
                                                    <?php
                                                    if (!empty($department_id)) {
                                                        echo $v_department->department_id == $department_id ? 'selected' : '';
                                                    }
                                                    ?>                                                    
                                                            >
                                                        <?php echo $v_department->department_name ?></option>
                                                <?php endforeach; ?>

                                            </select>                            
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-5">
                                            <button type="submit" id="sbtn" name="sbtn" value="1" class="btn btn-primary">Go</button>                            
                                        </div>
                                    </div>
                                </div>
                            </form>  
                        </div>
                    </div>


                    <?php if (!empty($employee_info)): ?>
                        <form action="<?php echo base_url() ?>admin/attendance/save_attendance" method="post"  enctype="multipart/form-data" class="form-horizontal">   
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Employee Name</th>                                
                                        <th>Designation</th>                                
                                        <th><input type="checkbox" class="checkbox-inline select_one" id="parent_present" /> Attendance</th>
                                        <th><input type="checkbox" class="checkbox-inline select_one"  id="parent_absent"  /> Leave Category</th>                                                                                    
                                    </tr>
                                </thead>                             
                                <tbody>
                                <div class="col-sm-5" style="display: none">
                                    <select name="department_id" id="department" class="form-control">
                                        <option value="" >Select Department...</option>
                                        <?php foreach ($all_department as $v_department) : ?>
                                            <option value="<?php echo $v_department->department_id ?>"                                                     
                                            <?php
                                            if (!empty($department_id)) {
                                                echo $v_department->department_id == $department_id ? 'selected' : '';
                                            }
                                            ?>                                                    
                                                    >
                                                <?php echo $v_department->department_name ?></option>
                                        <?php endforeach; ?>

                                    </select>                            
                                </div>
                                <?php foreach ($employee_info as $v_employee) { ?>
                                    <tr><td>  
                                            <input type="hidden" name="date" value="<?php echo $date ?>">                                                
                                            <?php
                                            foreach ($atndnce as $atndnce_status) {
                                                if (!empty($atndnce_status)) {
                                                    if ($v_employee->employee_id == $atndnce_status->employee_id) {
                                                        ?>
                                                        <input type="hidden" name="attendance_id[]" value="<?php if ($atndnce_status) echo $atndnce_status->attendance_id ?>">
                                                        <?php
                                                    }
                                                }
                                            }
                                            ?>

                                            <input type="hidden" name="employee_id[]"  value="<?php echo $v_employee->employee_id ?>"> <?php echo $v_employee->first_name . ' ' . $v_employee->last_name; ?></td>                                                                             
                                        <td><?php echo $v_employee->designations ?></td>
                                        <td><input  name="attendance[]" 
                                            <?php
                                            foreach ($atndnce as $atndnce_status) {
                                                if ($atndnce_status) {
                                                    if ($v_employee->employee_id == $atndnce_status->employee_id) {
                                                        echo $atndnce_status->attendance_status == 1 ? 'checked ' : '';
                                                    }
                                                }
                                            }
                                            ?> id="<?php echo $v_employee->employee_id ?>" value="<?php echo $v_employee->employee_id ?>" type="checkbox" class="child_present"> </td>                                                                             
                                        <td style="width: 35%">                            
                                            <input id="<?php echo $v_employee->employee_id ?>" type="checkbox"
                                            <?php
                                            foreach ($atndnce as $atndnce_status) {
                                                if ($atndnce_status) {
                                                    if ($v_employee->employee_id == $atndnce_status->employee_id) {
                                                        echo $atndnce_status->leave_category_id ? 'checked ' : '';
                                                    }
                                                }
                                            }
                                            ?>
                                                   value="<?php echo $v_employee->employee_id ?>" class="child_absent" >
                                            <div id="l_category" class="col-sm-9">
                                                <select name="leave_category_id[]" class="form-control"  >
                                                    <option value="" >Select Leave Category...</option>
                                                    <?php foreach ($all_leave_category_info as $v_L_category) : ?>
                                                        <option value="<?php echo $v_L_category->leave_category_id ?>"
                                                        <?php
                                                        foreach ($atndnce as $atndnce_status) {
                                                            if ($atndnce_status) {
                                                                if ($v_employee->employee_id == $atndnce_status->employee_id) {
                                                                    echo $v_L_category->leave_category_id == $atndnce_status->leave_category_id ? 'selected ' : '';
                                                                }
                                                            }
                                                        }
                                                        ?> > 
                                                            <?php echo $v_L_category->category ?></option>;
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>   
                                        </td></tr>
                                <?php }
                                ?>  
                                </tbody>
                            </table>                               
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-11">
                            <button type="submit" id="sbtn" class="btn btn-primary">Update</button>                            
                        </div>
                    </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>                                            
</div>   
<script type="text/javascript">
    $(document).ready(function() {
        $(':checkbox').on('change', function() {
            var th = $(this), id = th.prop('id');

            if (th.is(':checked')) {
                $(':checkbox[id="' + id + '"]').not($(this)).prop('checked', false);
            }
        });
    });
</script>
<script>
    $(function() {
        $('#date').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
        });
    });

</script>