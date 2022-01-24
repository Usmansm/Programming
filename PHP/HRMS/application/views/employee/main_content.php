<!-- Here begin Main Content -->

<div class="col-md-12">
    <div class="main_content">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="panel panel-info" style="border: 1px solid #004884 ">
                        <div class="panel-body">
                            <?php if ($employee_details->photo): ?>
                                <img src="<?php echo base_url() . $employee_details->photo; ?>" style="height: 200px; width: 210px;"  class="img-responsive center-block" />
                            <?php else: ?>
                                <img src="<?php echo base_url() ?>/asset/img/user.jpg" style="height: 200px; width: 210px; "  class="img-responsive center-block" alt="Employee_Image" />
                            <?php endif; ?>
                        </div>
                        <div style="border-top: 1px solid #004884 ">
                            <h3 class="text-center"><?php echo $employee_details->first_name . $employee_details->last_name; ?></h3>
                            <h6 class="text-center">Employee ID: <?php echo $employee_details->employment_id ?></h6>
                            <h6 class="text-center"><?php echo $employee_details->department_name . " - " . $employee_details->designations; ?></h6>
                            <p></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h2 class="panel-title "><i class="fa fa-user"></i> <strong>Personal Details </strong><span class="pull-right"><a href="<?php echo base_url() ?>employee/dashboard/profile" class="view-all-front">View Profile</a></span></h2>
                        </div>
                        <div class="panel-body">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <td>
                                            <span class="primary-link">Name</span>
                                        </td>
                                        <td>
                                            <?php echo "$employee_details->first_name " . "$employee_details->last_name"; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="primary-link">Father's Name</span>
                                        </td>
                                        <td>
                                            <?php echo "$employee_details->father_name"; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="primary-link">DOB</span>
                                        </td>
                                        <td>
                                            <?php echo date('d M Y', strtotime($employee_details->date_of_birth)); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="primary-link">Gender</span>
                                        </td>
                                        <td>
                                            <?php echo "$employee_details->gender"; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="primary-link">Email</span>
                                        </td>
                                        <td>
                                            <?php echo "$employee_details->email"; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="primary-link">Phone</span>
                                        </td>
                                        <td>
                                            <?php echo "$employee_details->phone"; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="primary-link">Mobile</span>
                                        </td>
                                        <td>
                                            <?php echo "$employee_details->mobile"; ?>
                                        </td>
                                    </tr>
                                    <tr>																															<tr>
                                        <td>
                                            <span class="primary-link">Address</span>
                                        </td>
                                        <td>
                                            <?php echo "$employee_details->present_address"; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">                                          
                    <div class="well-custom">
                        <!-- STATISTICS -->
                        <div class="row">
                            <div class="col-md-4 col-sm-4 col-xs-4" style="border-right: 1px solid #46b8da">
                                <div class="uppercase text-center">
                                    <strong>
                                        <?php
                                        if (!empty($total_attendance)) {
                                            echo $total_attendance;
                                        } else {
                                            echo '0';
                                        }
                                        ?> / <?php echo $total_days; ?>
                                    </strong>
                                </div>
                                <div class="uppercase text-center">
                                    Attendance
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4" style="border-right: 1px solid #46b8da">
                                <a href="<?php echo base_url() ?>employee/dashboard/leave_application" style="color: white;">
                                    <div class="uppercase text-center">
                                        <strong>
                                            <?php echo $total_leave_applied; ?>
                                        </strong>
                                    </div>
                                    <div class="uppercase text-center">
                                        Leave
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <a href="<?php echo base_url() ?>employee/dashboard/all_award" style="color: white;">
                                    <div class="uppercase text-center">
                                        <strong>
                                            <?php echo $total_award_received; ?>
                                        </strong>
                                    </div>
                                    <div class="uppercase text-center">
                                        Awards
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>


                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-bell-o"></i> <strong>Notice Board</strong><span class="pull-right"><a href="<?php echo base_url() ?>employee/dashboard/all_notice" class=" view-all-front">View All</a></span></h2>
                        </div>               
                        <div class="panel-body tab-pane-notice">
                            <?php foreach ($notice_info as $v_notice) : ?>
                                <div class="notice-calendar-list clearfix">
                                    <div class="notice-calendar">
                                        <span class="month"><?php echo date('M', strtotime($v_notice->created_date)) ?></span>
                                        <span class="date"><?php echo date('d', strtotime($v_notice->created_date)) ?></span>
                                    </div>

                                    <div class="notice-calendar-heading">
                                        <h5 class="notice-calendar-heading-title">
                                            <a href="<?php echo base_url() ?>employee/dashboard/notice_detail/<?php echo $v_notice->notice_id; ?>"><?php echo $v_notice->title ?></a>                        
                                        </h5>
                                        <div class="notice-calendar-date">
                                            <?php
                                            $str = strlen($v_notice->short_description);
                                            if ($str > 90) {
                                                $ss = '<strong> ......</strong>';
                                            } else {
                                                $ss = '&nbsp';
                                            } echo substr($v_notice->short_description, 0, 90) . $ss;
                                            ?>
                                        </div>
                                    </div>
                                    <div style="margin-top: 5px; padding-top: 5px; padding-bottom: 10px;">
                                        <span style="font-size: 10px;" class="pull-right">
                                            <strong><a href="<?php echo base_url() ?>employee/dashboard/notice_detail/<?php echo $v_notice->notice_id; ?>" style="color: #004884;">View Details</a></strong>
                                        </span>
                                    </div>
                                </div>                               
                            <?php endforeach; ?>
                        </div>
                    </div>                     
                </div>
            </div>
            <hr/>
            <div class="col-md-12">
                <div class="col-md-8">
                    <div class="panel panel-info">
                        <div class="panel-heading ">
                            <h2 class="panel-title "><i class="fa fa-mail-reply-all"></i> <strong>Recent Mails</strong><span class="pull-right"><a href="<?php echo base_url() ?>employee/dashboard/all_notice" class="view-all-front">Go To Mailbox</a></span></h2>
                        </div>
                        <div class="panel-body tab-pane">
                            <div class="table-responsive mailbox-messages">
                                <table class="table table-hover table-striped">
                                    <tbody>
                                        <?php if (!empty($get_inbox_message)):foreach ($get_inbox_message as $v_inbox_msg): ?>
                                                <tr>
                                                    <td><a href="<?php echo base_url() ?>employee/dashboard/read_inbox_mail/<?php echo $v_inbox_msg->inbox_id ?>"><?php
                                                            if ($v_inbox_msg->view_status == 1) {
                                                                echo '<span style="color:#000">' . $v_inbox_msg->to . '</span>';
                                                            } else {
                                                                echo '<b style="color:#000;font-size:14px;">' . $v_inbox_msg->to . '</b>';
                                                            }
                                                            ?></a></td>
                                                    <td><b class="pull-left"><?php echo $v_inbox_msg->subject ?> - &nbsp;</b> <span class="pull-left "> <?php
                                                            $str = strlen($v_inbox_msg->message_body);
                                                            if ($str > 35) {
                                                                $ss = '<strong> ......</strong>';
                                                            } else {
                                                                $ss = '&nbsp';
                                                            } echo substr($v_inbox_msg->message_body, 0, 35) . $ss;
                                                            ?></span></td>                                                
                                                    <td>
                                                        <?php
                                                        //$oldTime = date('h:i:s', strtotime($v_inbox_msg->send_time));
                                                        // Past time as MySQL DATETIME value
                                                        $oldtime = date('Y-m-d H:i:s', strtotime($v_inbox_msg->message_time));

                                                        // Current time as MySQL DATETIME value
                                                        $csqltime = date('Y-m-d H:i:s');
                                                        // Current time as Unix timestamp
                                                        $ptime = strtotime($oldtime);
                                                        $ctime = strtotime($csqltime);

                                                        //Now calc the difference between the two
                                                        $timeDiff = floor(abs($ctime - $ptime) / 60);

                                                        //Now we need find out whether or not the time difference needs to be in
                                                        //minutes, hours, or days
                                                        if ($timeDiff < 2) {
                                                            $timeDiff = "Just now";
                                                        } elseif ($timeDiff > 2 && $timeDiff < 60) {
                                                            $timeDiff = floor(abs($timeDiff)) . " minutes ago";
                                                        } elseif ($timeDiff > 60 && $timeDiff < 120) {
                                                            $timeDiff = floor(abs($timeDiff / 60)) . " hour ago";
                                                        } elseif ($timeDiff < 1440) {
                                                            $timeDiff = floor(abs($timeDiff / 60)) . " hours ago";
                                                        } elseif ($timeDiff > 1440 && $timeDiff < 2880) {
                                                            $timeDiff = floor(abs($timeDiff / 1440)) . " day ago";
                                                        } elseif ($timeDiff > 2880) {
                                                            $timeDiff = floor(abs($timeDiff / 1440)) . " days ago";
                                                        }
                                                        echo $timeDiff;
                                                        ?>
                                                    </td>
                                                </tr>  
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td><strong>There is no email to display</strong></td>
                                            </tr> 
                                        <?php endif; ?>
                                    </tbody>
                                </table><!-- /.table -->
                            </div><!-- /.mail-box-messages -->
                        </div>
                    </div> 
                </div>
                <div class="col-md-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-binoculars"></i><strong> Upcoming Events</strong><span class="pull-right"><a href="<?php echo base_url() ?>employee/dashboard/all_events" class=" view-all-front">View All</a></span></h2>
                        </div>               
                        <div class="panel-body event">
                            <?php foreach ($event_info as $v_events) : ?>
                                <div class="notice-calendar-list clearfix">
                                    <div class="notice-calendar">
                                        <span class="month"><?php echo date('M', strtotime($v_events->start_date)) ?></span>
                                        <span class="date"><?php echo date('d', strtotime($v_events->start_date)) ?></span>
                                    </div>

                                    <div class="notice-calendar-heading">
                                        <h5 class="notice-calendar-heading-title">
                                            <a href="<?php echo base_url() ?>employee/dashboard/event_detail/<?php echo $v_events->holiday_id ?>"><?php echo $v_events->event_name ?></a>                        
                                        </h5>
                                        <div class="notice-calendar-date"><span class="text-danger">End Date: </span>
                                            <?php echo date('d M Y', strtotime($v_events->end_date)); ?>

                                        </div>
                                    </div>
                                    <div style="margin-top: 5px; padding-top: 5px; padding-bottom: 5px;">
                                        <span style="font-size: 10px;" class="pull-right">
                                            <strong><a href="<?php echo base_url() ?>employee/dashboard/event_detail/<?php echo $v_events->holiday_id ?>" style="color: #004884;">View Details</a></strong>
                                        </span>
                                    </div>
                                </div>                               
                            <?php endforeach; ?>
                        </div>
                    </div> 

                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h2 class="panel-title"><strong><?php echo 'Upcomming Birhday' ?> </strong>- <?php echo date("F"); ?></h2>
                        </div>               
                        <div class="panel-body event">
                            <ul class="leave_apps">
                                <?php
                                $m = date("m"); // Month value
                                $y = date("Y"); // Year value
                                $num = cal_days_in_month(CAL_GREGORIAN, $m, $y);
                                ?>    
                                <?php
                                for ($i = 0; $i < count($employee); $i++) :

                                    $mem_bod_explode = explode("-", $employee[$i]->date_of_birth);
                                    $m_bday = mktime(0, 0, 0, $mem_bod_explode[1], $mem_bod_explode[2], $y);

                                    $start_date = date('Y-m', $m_bday) . '-01';
                                    $end_date = date('Y-m', $m_bday) . '-' . $num;


                                    if (date('Y-m-d') == date('Y-m-d', $m_bday)) {
                                        $present_bday[] = $employee[$i];
                                        $date = date('Y-m-d', $m_bday);
                                        $pdate[] = date('d M Y', strtotime($date));
                                    } else if (date('Y-m-d', $m_bday) > $start_date && date('Y-m-d', $m_bday) <= $end_date) {
                                        $future_bday[] = $employee[$i];
                                        $date = date('Y-m-d', $m_bday);
                                        $fdate[] = date('d M Y', strtotime($date));
                                    }
                                    ?>       
                                <?php endfor; ?>                                
                                <?php if (!empty($present_bday)):foreach ($present_bday as $key => $v_bday): ?>
                                        <li> 
                                            <a   href="<?php echo base_url() ?>admin/employee/view_employee/<?php echo $v_bday->employee_id ?>">
                                                <h5>
                                                    <div class="pull-left">
                                                        <img class="img-circle" src="<?php echo base_url() . $v_bday->photo ?>">
                                                    </div>
                                                    <span><?php echo $v_bday->first_name . ' ' . $v_bday->last_name ?></span>
                                                    <small class="apps_category" style="color:red">(Today)</small>
                                                    <p class="leave_para"><?php
                                                        echo $pdate[$key];
                                                        ?></p>

                                                </h5>
                                            </a>
                                        </li>                                
                                    <?php endforeach; ?>                                
                                <?php endif; ?>                                
                                <?php if (!empty($future_bday)):foreach ($future_bday as $key => $v_fbday): ?>
                                        <li> 
                                            <a   href="<?php echo base_url() ?>admin/employee/view_employee/<?php echo $v_fbday->employee_id ?>">
                                                <h5>
                                                    <div class="pull-left">
                                                        <img class="img-circle" src="<?php echo base_url() . $v_fbday->photo ?>">
                                                    </div>
                                                    <span><?php echo $v_fbday->first_name . ' ' . $v_fbday->last_name ?></span>                                            
                                                    <p class="leave_para"><?php
                                                        echo $fdate[$key];
                                                        ?></p>

                                                </h5>
                                            </a>
                                        </li>                                
                                    <?php endforeach; ?>                                
                                <?php endif; ?>                                
                            </ul>
                        </div>
                    </div>                 
                </div>
            </div>
        </div>
    </div> <!-- /.col-md-12 -->
</div> <!-- /.col-md-12 -->