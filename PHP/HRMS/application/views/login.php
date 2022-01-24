<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login</title>        
        <link href="<?php echo base_url(); ?>asset/css/main.css" rel="stylesheet" type="text/css" /> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>asset/css/font-icons/entypo/css/entypo.css" >
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>       
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link href="<?php echo base_url(); ?>asset/css/login.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>asset/css/animation.css" rel="stylesheet">
    </head>
    <body>


        <!--Main Body.-->
        <section class="container-fluid img-responsive" id="section-main">              
            <!--- Header end here------>
            <div class="row animated fadeInUp" data-animation="fadeInUp">
                <div class="login-logo">
                    <div class="col-md-4 col-md-offset-4 text-center">
                        <?php
                        $genaral_info = $this->session->userdata('genaral_info');
                        if (!empty($genaral_info)) {
                            foreach ($genaral_info as $info) {
                                ?>
                                <img src="<?php echo base_url() . $info->logo ?>" alt="" class="img-responsive"/>
                                <?php
                            }
                        } else {
                            ?>
                            <img class="img-responsive" alt="school-logo" src="<?php echo base_url(); ?>img/logo.png">                            
                            <?php
                        }
                        ?>                    
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-3 col-xs-12 screen_lock">                    
                    <form role="form" action="<?php echo base_url() ?>login" method="post">
                        <div class="error_login">
                            <?php echo validation_errors(); ?>
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>

<!--                        <div class="form-group">
                            <select name="type" id="form-select" required>
                                <option value="">Select User...</option>
                                <option value="1">Admin</option>
                                <option value="2">Employee</option>                                
                            </select> 
                        </div>-->
                        <div class="form-group">
                            <input type="text" name="user_name" placeholder="User Name">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" id="exampleInputPassword1" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-login btn-block">Submit</button>
                    </form>
                </div>
            </div>
        </section>        
    </body>
</html>