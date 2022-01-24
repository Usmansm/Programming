
<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="row">
    <div class="col-sm-12" data-spy="scroll" data-offset="0">
        <div class="wrap-fpanel">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong>User List</strong>
                    </div>
                </div>
                <br />
                <!-- Table -->
                <table class="table table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr class="active" >
                            <th class="col-sm-1">SL</th>
                            <th>Employee</th>
                            <th>Department</th>

                            <th class="col-sm-1">User Name</th>                                             
                            <th>Create User</th>
                            <th class="col-sm-1">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $key = 1 ?>
                        <?php if (count($all_employee_info)): foreach ($all_employee_info as $v_employee) : ?>

                                <tr>
                                    <td><?php echo $key ?></td>
                                    <td><?php echo "$v_employee->first_name " . "$v_employee->last_name"; ?></td>
                                    <td><?php echo $v_employee->department_name ?></td>

                                    <td><?php echo $v_employee->user_name ?></td>                                
                                    <td> 
                                        <?php if (!empty($v_employee->employee_login_id)): ?>

                                            <a href=" <?php echo base_url() ?>admin/user/create_user/<?php echo $this->encryption->encrypt($v_employee->employee_id); ?>"><i class="fa fa-key"></i> 
                                                Edit User
                                            </a>
                                        <?php else : ?>
                                            <a href=" <?php echo base_url() ?>admin/user/create_user/<?php echo $this->encryption->encrypt($v_employee->employee_id); ?>"><i class="fa fa-key"></i> 
                                                Create User
                                            </a>
                                        <?php endif; ?>
                                    </td>   
                                    <td>
                                        
                                            <?php if (!empty($v_employee->employee_login_id)): ?>
                                                <?php if ($v_employee->activate == 1): ?>
                                        <a href="<?php echo base_url() ?>admin/user/user_deactive/<?php echo $this->encryption->encrypt($v_employee->employee_id); ?>">Active</a>
                                                <?php else: ?>
                                        <a href="<?php echo base_url() ?>admin/user/user_active/<?php echo $this->encryption->encrypt($v_employee->employee_id); ?>">Deactive</a>
                                                    <?php endif; ?>
                                            <?php endif; ?>
                                        
                                    </td>
                                </tr>
                                <?php
                                $key++;
                            endforeach;
                            ?>
                        <?php else : ?>
                        <td colspan="3">
                            <strong>There is no Record for display!</strong>
                        </td>
                    <?php endif; ?>
                    </tbody>
                </table>          
            </div>
        </div>
    </div>
</div>

