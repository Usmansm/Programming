<header class="header" >
    <div  class="logo">
        <!-- Add the class icon to your logo image or logo icon to add the margining -->
        Admin Panel
    </div>
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="row">
            <div class="col-md-7 col-sm-12">
                <div class="heading">
                    <?php
                    $genaral_info = $this->session->userdata('genaral_info');
                    if (!empty($genaral_info)) {
                        foreach ($genaral_info as $info) {
                            ?>
                            <div class="heading-logo">
                                <img src="<?php echo base_url() . $info->logo ?>" alt="" class="img-circle"/>
                                <h2><?php echo $info->name ?></h2>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="heading-logo">
                            <img src="<?php echo base_url() ?>img/logo.png" alt="Logo" class="img-circle"/>
                            <h2>Human Resource Management System</h2>
                        </div>
                    <?php }
                    ?>

                </div>
            </div>
            <div class="navbar-right">
                <ul class="nav navbar-nav custom-nav">
                    <!-- Messages: Email -->
                    <?php $total_email = $_SESSION['notify']['total_email_notification']; ?>
                    <li class="dropdown messages-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-envelope"></i>
                            <span class="label label-success"><?php
                                if (!empty($total_email)) {
                                    echo $total_email;
                                } else {
                                    echo '0';
                                }
                                ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have <?php
                                if (!empty($total_email)) {
                                    echo $total_email;
                                } else {
                                    echo '0';
                                }
                                ?> messages
                                <small><a href="<?php echo base_url() ?>admin/mailbox/inbox">All Messages</a></small>
                            </li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <?php
                                    $email_notification = $_SESSION['notify']['email_notification'];
                                    if (!empty($email_notification)):
                                        foreach ($email_notification as $v_email_info) :
                                            ?>
                                            <li><!-- start message -->
                                                <a href="<?php echo base_url() ?>admin/mailbox/read_inbox_mail/<?php echo $v_email_info->inbox_id ?>">                                                
                                                    <h4>
                                                        <?php echo $v_email_info->subject ?>
                                                        <small><i class="fa fa-clock-o"></i> 
                                                            <?php
                                                            //$oldTime = date('h:i:s', strtotime($v_inbox_msg->send_time));
                                                            // Past time as MySQL DATETIME value
                                                            $oldtime = date('Y-m-d H:i:s', strtotime($v_email_info->message_time));

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
                                                        </small>
                                                    </h4>
                                                    <p><?php echo $v_email_info->to ?></p>
                                                </a>
                                            </li><!-- end message -->                       
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="text-center"><p>
                                                <strong>There is no message</strong>  
                                            </p>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>                            
                        </ul>
                    </li>                    
                    <?php
                    $total_notice = $_SESSION['notify']['total_notice_notification'];
                    ?>
                    <!-- Notifications: -->
                    <li class="dropdown messages-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning"><?php
                                if (!empty($total_notice)) {
                                    echo $total_notice;
                                } else {
                                    echo '0';
                                }
                                ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have <?php
                                if (!empty($total_notice)) {
                                    echo $total_notice;
                                } else {
                                    echo '0';
                                }
                                ?> Notice
                                <small><a href="<?php echo base_url() ?>admin/notice/manage_notice">All Notice</a></small>
                            </li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <?php
                                    $notice_notification = $_SESSION['notify']['notice_notification'];
                                    if (!empty($notice_notification)):
                                        foreach ($notice_notification as $v_notice_info) :
                                            ?>
                                            <li><!-- start message -->
                                                <a href="<?php echo base_url() ?>admin/notice/notice_details/<?php echo $v_notice_info->notice_id ?>">                                                
                                                    <h4>
                                                        <?php echo $v_notice_info->title ?>
                                                        <small><i class="fa fa-clock-o"></i> 
                                                            <?php
                                                            //$oldTime = date('h:i:s', strtotime($v_inbox_msg->send_time));
                                                            // Past time as MySQL DATETIME value
                                                            $oldtime = date('Y-m-d H:i:s', strtotime($v_notice_info->created_date));

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
                                                        </small>
                                                    </h4>
                                                    <p><?php
                                                        $str = strlen($v_notice_info->short_description);
                                                        if ($str > 40) {
                                                            $ss = '<strong> ......</strong>';
                                                        } else {
                                                            $ss = '&nbsp';
                                                        } echo substr($v_notice_info->short_description, 0, 40) . $ss;
                                                        ?></p>
                                                </a>
                                            </li><!-- end message -->                       
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="text-center"><p>
                                                <strong>There is no Notice</strong>  
                                            </p>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>                            
                        </ul>
                    </li>                    
                    <?php $total_leave = $_SESSION['notify']['total_leave_notifation']; ?>
                    <!-- Notifications: -->
                    <li class="dropdown messages-menu leave">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-flag-o"></i>
                            <span class="label label-danger"><?php
                                if (!empty($total_leave)) {
                                    echo $total_leave;
                                } else {
                                    echo '0';
                                }
                                ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have <?php
                                if (!empty($total_leave)) {
                                    echo $total_leave;
                                } else {
                                    echo '0';
                                };
                                ?> Application <small><a  href="<?php echo base_url() ?>admin/application_list">All Application</a></small></li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <?php
                                    $leave_notification = $_SESSION['notify']['leave_notification'];
                                    if (!empty($leave_notification)):
                                        foreach ($leave_notification as $v_leave_info) :
                                            ?>
                                            <li><!-- start message -->
                                                <a href="<?php echo base_url() ?>admin//application_list/view_application/<?php echo $v_leave_info->application_list_id ?>">                                                
                                                    <div class="pull-left">
                                                        <img src="<?php echo base_url() . $v_leave_info->photo; ?>" class="img-circle" alt="User Image"/>
                                                    </div>
                                                    <h4>
                                                        <?php echo $v_leave_info->first_name . ' ' . $v_leave_info->last_name ?>
                                                        <small><i class="fa fa-clock-o"></i> 
                                                            <?php
                                                            //$oldTime = date('h:i:s', strtotime($v_inbox_msg->send_time));
                                                            // Past time as MySQL DATETIME value
                                                            $oldtime = date('Y-m-d H:i:s', strtotime($v_leave_info->application_date));

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
                                                        </small>
                                                    </h4>
                                                    <p ><?php
                                                        $str = strlen($v_leave_info->reason);
                                                        if ($str > 40) {
                                                            $ss = '<strong> ......</strong>';
                                                        } else {
                                                            $ss = '&nbsp';
                                                        } echo substr($v_leave_info->reason, 0, 40) . $ss;
                                                        ?></p>
                                                </a>
                                            </li><!-- end message -->                       
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="text-center"><p>
                                                <strong>There is no Application</strong>  
                                            </p>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>                            
                        </ul>
                    </li>                    
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="glyphicon glyphicon-user"></i>
                            <span><?php echo $this->session->userdata('last_name'); ?><i class="caret"></i></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header bg-light-blue">
                                   <img src="<?php echo base_url() ?>img/admin.png" class="img-circle" alt="User Image" />                                
                                <p>
                                    <?php echo $this->session->userdata('first_name') . ' ' . $this->session->userdata('last_name') ?>
                                    <small>Username : <?php echo $this->session->userdata('user_name') ?></small>
                                </p>
                            </li>                            
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?php echo base_url() ?>admin/settings/update_profile" class="btn btn-default btn-flat">Update Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?php echo base_url() ?>login/logout" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>                                        
                </ul>
            </div>            
        </div>

    </nav>
</header>

