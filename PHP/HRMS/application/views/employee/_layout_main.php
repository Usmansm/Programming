<?php $this->load->view('employee/component/header'); ?>
<body>
    <?php $this->load->view('employee/component/navigations'); ?>
    <div class="container">
        <div class="row">

            <div class="margin">    
                <div class="col-md-12">
                    <div class="main_content">
                        <div class="row">
                            <?php echo $subview ?>              
                        </div>
                    </div>
                </div>
            </div>                    
        </div>                    
    </div>                    
    <?php $this->load->view('employee/component/footer'); ?>