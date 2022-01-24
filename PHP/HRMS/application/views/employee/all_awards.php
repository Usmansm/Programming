
<div class="col-md-12">
    <div class="row">
        <div class="col-sm-12" data-offset="0">                            
            <div class="panel panel-info">
                <!-- Default panel contents -->

                <div class="panel-heading">
                    <div class="panel-title">                 
                        <strong>List of All Awards</strong>
                    </div>
                </div>
                <!-- Table -->
                <table class="table table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr>                                                                               
                            <th class="col-sm-1">Award Date</th> 
                            <th>Employee Name</th>                                                                
                            <th>Designation</th>  
                            <th>Award Name</th>                                                                
                            <th>Gift Item</th>                                                                
                            <th class="col-sm-1">$ Received</th>                                                                                                                                                                                      
                            <th class="col-sm-1">Action</th>                                                                
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (!empty($award_info)): foreach ($award_info as $v_award) : ?>                            
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($v_award->award_date)); ?></td>
                                    <td><?php if ($v_award->employee_id == $this->session->userdata('employee_id')) { ?>
                                        <span class="text-success"><strong><?php echo $v_award->first_name . " " . $v_award->last_name; ?></strong></span>
                                        <?php }else{ ?>
                                            <?php echo $v_award->first_name . " " . $v_award->last_name; ?>
                                        <?php } ?>
                                    </td>                                                                                                                                        
                                    <td><?php echo $v_award->department_name . " - " . $v_award->designations; ?></td>                                                                                                                                        
                                    <td><?php echo $v_award->award_name ?></td>                                                                                                                                        
                                    <td><?php echo $v_award->gift_item ?></td>                                                                                                                                        
                                    <td><?php echo $v_award->award_amount ?></td>                                                                                                                                        
                                    <td><?php echo btn_view('employee/dashboard/award_detail/' . $v_award->employee_award_id); ?></td>                                                                                                                                        
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

