<div id="main-header" class="clearfix">
    <header id="header" class="clearfix">
        <div class="stripe_color"></div>
        <div class="stripe_image"></div>                 
        <div class="school-logo col-sm-12">                    
            <div class="container">
                <?php
                $genaral_info = $this->session->userdata('genaral_info');
                if (!empty($genaral_info)) {
                    foreach ($genaral_info as $info) {
                        ?>                        
                        <img src="<?php echo base_url() . $info->logo ?>" alt="" class="img-circle"/>
                        <div class="head">
                            <h2><?php echo $info->name ?></h2>
                        </div>

                        <?php
                    }
                } else {
                    ?>
                    <img src="<?php echo base_url() ?>img/logo.png" class="img-circle" alt="school_logo" >                    
                    <div class="head">
                        <h2>Human Resource Management System</h2>
                    </div>
                <?php }
                ?>
            </div>
        </div>                
        <div class="container">   
            <div class="row main">
                <nav class="navbar navbar-custom" id="header_menu" role="navigation">                        
                    <div class="menu-bg">                        
                        <nav class="main-menu navbar navbar-collapse menu-bg" role="navigation">

                            <!-- Brand and toggle get grouped for better mobile display -->
                            <div class="navbar-header menu-bg">
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                            </div>

                            <!-- Collect the nav links, forms, and other content for toggling -->
                            <div class="main-menu collapse navbar-collapse menu-bg" id="bs-example-navbar-collapse-1">
                                <ul class="nav navbar-nav">
                                    <li class="<?php if(!empty($menu['index'])){ echo $menu['index'] == 1 ? 'active' : '';} ?>">
                                        <a href="<?php echo base_url() ?>employee/dashboard">Home</a>
                                    </li>                                    
                                    <li class="dropdown <?php if(!empty($menu['mailbox'])){ echo $menu['mailbox'] == 1 ? 'active' : '';} ?>">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Mailbox<b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li class="<?php if(!empty($menu['inbox'])){ echo $menu['inbox'] == 1 ? 'active' : '';} ?>"><a href="<?php echo base_url() ?>employee/dashboard/inbox">Inbox</a></li>
                                            <li class="<?php if(!empty($menu['sent'])){ echo $menu['sent'] == 1 ? 'active' : '';} ?>"><a  href="<?php echo base_url() ?>employee/dashboard/sent">Sent</a></li>                                            
                                        </ul>
                                    </li>
                                    <li class="<?php if(!empty($menu['leave_application'])){ echo $menu['leave_application'] == 1 ? 'active' : '';} ?>"><a href="<?php echo base_url() ?>employee/dashboard/leave_application">Leave Application</a></li>
                                    <li class="<?php if(!empty($menu['notice'])){ echo $menu['notice'] == 1 ? 'active' : '';} ?>"><a href="<?php echo base_url() ?>employee/dashboard/all_notice">Notice</a></li>
                                    <li class="<?php if(!empty($menu['events'])){ echo $menu['events'] == 1 ? 'active' : '';} ?>"><a href="<?php echo base_url() ?>employee/dashboard/all_events">Events</a></li>
                                    <li class="<?php if(!empty($menu['awards'])){ echo $menu['awards'] == 1 ? 'active' : '';} ?>"><a href="<?php echo base_url() ?>employee/dashboard/all_award">Awards</a></li>

                                </ul>
                                <ul class="main-menu nav navbar-nav navbar-right">
                                    <li class="dropdown <?php if(!empty($menu['profile'])){ echo $menu['profile'] == 1 ? 'active' : '';} ?>">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> Profile<b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li class="<?php if(!empty($menu['change_password'])){ echo $menu['change_password'] == 1 ? 'active' : '';} ?>"><a href="<?php echo base_url() ?>employee/dashboard/change_password">Change Password</a></li>
                                            <li><a href="<?php echo base_url() ?>login/logout">Logout</a></li>                                            
                                        </ul>
                                    </li>
                                </ul>
                            </div><!-- /.navbar-collapse -->
                        </nav>
                    </div>  
                </nav>  
            </div>                                    
        </div> 
    </header>   
</div>


