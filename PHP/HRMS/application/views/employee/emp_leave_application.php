
<div class="col-md-12">
    <?php include_once 'asset/admin-ajax.php'; ?>
    <?php echo message_box('success'); ?>
    <?php echo message_box('error'); ?>

    <h4><?php echo anchor('employee/dashboard/apply_leave_application', '<i class="fa fa-plus"></i> Apply New Leave Application'); ?></h4>
    <br/>

    <div class="row">
        <div class="col-sm-12">                            
            <div class="panel panel-info">
                <!-- Default panel contents -->
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong>Leave Applications You Applied</strong>
                    </div>
                </div>
                <table class="table table-bordered table-hover" id="dataTables-example">
                    <thead>                                     
                        <tr style="font-size: 13px;color: #000000">                            
                            <th class="col-sm-2">Leave Category</th>
                            <th class="col-sm-1">Start Date</th>
                            <th class="col-sm-1">End Date</th>
                            <th>Reason</th>
                            <th class="col-sm-1">Applied On</th>
                            <th class="col-sm-1">Status</th>
                        </tr>
                    </thead>                
                    <tbody style="margin-bottom: 0px;background: #FFFFFF;font-size: 12px;">                                                                   
                        <?php if (!empty($all_leave_applications)): foreach ($all_leave_applications as $v_application) : ?>

                                <tr>                                    
                                    <td><?php echo $v_application->category ?></td>
                                    <td><?php echo date('d M Y', strtotime($v_application->leave_start_date)) ?></td>
                                    <td><?php echo date('d M Y', strtotime($v_application->leave_end_date)) ?></td>
                                    <td><?php echo $v_application->reason ?></td>                                                                        
                                    <td><?php echo date('d M Y', strtotime($v_application->application_date)) ?></td>
                                    <td><?php
                                        if ($v_application->application_status == 1) {
                                            echo '<span class="label label-info">Pending</span>';
                                        } elseif ($v_application->application_status == 2) {
                                            echo '<span class="label label-success">Accepted</span>';
                                        } else {
                                            echo '<span class="label label-danger">Rejected</span>';
                                        }
                                        ?>
                                    </td>                                                                                      
                                </tr>
                                <?php
                            endforeach;
                            ?>
                        <?php else : ?>
                        <td colspan="3">
                            <strong>There is no data to display</strong>
                        </td>
                    <?php endif; ?>
                    </tbody>                    
                </table>
            </div>
        </div>
    </div>
</div>



