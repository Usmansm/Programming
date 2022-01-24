<style type="text/css" media="print">
    @media print{@page {size: landscape}}
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="wrap-fpanel">
            <div class="panel panel-default" data-collapsed="0">
                <div class="panel-heading">
                    <div class="panel-title">
                        <strong><?php echo $this->language->form_heading()[15] ?></strong>
                    </div>                
                </div>

                <div class="panel-body">
                    <form id="attendance-form" role="form" enctype="multipart/form-data" action="<?php echo base_url(); ?>admin/attendance/get_report" method="post" class="form-horizontal form-groups-bordered">                    
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo $this->language->from_body()[16][0] ?><span class="required">*</span></label>

                            <div class="col-sm-5">
                                <select name="department_id" class="form-control" >
                                    <option value="" >Select Department...</option>                                  
                                    <?php if (!empty($all_department)): foreach ($all_department as $department): ?>
                                            <option value="<?php echo $department->department_id; ?>"
                                            <?php if (!empty($department_id)): ?>
                                                <?php echo $department->department_id == $department_id ? 'selected ' : '' ?>
                                                    <?php endif; ?>>
                                                        <?php echo $department->department_name; ?>
                                            </option>
                                            <?php
                                        endforeach;
                                    endif;
                                    ?> 
                                </select>                            
                            </div>
                        </div>   
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo $this->language->from_body()[16][1] ?> <span class="required">*</span></label>
                            <div class="input-group col-sm-5">
                                <input type="text" class="form-control monthyear" value="<?php
                                if (!empty($date)) {
                                    echo date('Y-n', strtotime($date));
                                }
                                ?>" name="date" >
                                <div class="input-group-addon">
                                    <a href="#"><i class="entypo-calendar"></i></a>
                                </div>
                            </div>
                        </div>                     
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-5 pull-right">
                                <button type="submit" id="sbtn" class="btn btn-primary"><?php echo $this->language->from_body()[1][14] ?></button>                            
                            </div>
                        </div>   
                    </form>
                </div>                        
            </div>                        
        </div>                
    </div>   
</div>
<div id="EmpprintReport">
    <div class="show_print" style="width: 100%; border-bottom: 2px solid black;">
        <table style="width: 100%; vertical-align: middle;">
            <tr>
                <?php
                $genaral_info = $this->session->userdata('genaral_info');
                if (!empty($genaral_info)) {
                    foreach ($genaral_info as $info) {
                        ?>
                        <td style="width: 35px; border: 0px;">
                            <img style="width: 50px;height: 50px" src="<?php echo base_url() . $info->logo ?>" alt="" class="img-circle"/>
                        </td>
                        <td style="border: 0px;">
                            <p style="margin-left: 10px; font: 14px lighter;"><?php echo $info->name ?></p>
                        </td>
                        <?php
                    }
                } else {
                    ?>
                    <td style="width: 35px; border: 0px;">
                        <img style="width: 50px;height: 50px" src="<?php echo base_url() ?>img/logo.png" alt="Logo" class="img-circle"/>
                    </td>
                    <td style="border: 0px;">
                        <p style="margin-left: 10px; font: 14px lighter;">Human Resource Management System</p>
                    </td>
                    <?php
                }
                ?>
            </tr>
        </table>
    </div>
    <br/>
    <br/>
    <?php if (!empty($attendance)): ?>
        <div class="std_heading" hidden style="background-color: rgb(224, 224, 224);margin-bottom: 5px;padding: 5px;">           
            <table style="margin: 3px 10px 0px 24px; width:100%;">                    
                <tr>

                    <td style="font-size: 15px"><strong>Department: </strong><?php echo $dept_name->department_name ?></td>                    
                    <td style="font-size: 15px"><strong>Date:</strong><?php echo $month ?></td>
                </tr>                                      
            </table>
        </div>
        <div class="row">
            <div class="col-sm-12 std_print"> 
                <div class="wrap-fpanel">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><strong>Attendance List </strong>
                                <div class="pull-right hidden-print" >
                                    <a href="<?php echo base_url() ?>admin/attendance/create_pdf/<?php echo $department_id . '/' . $date ?>" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top" title="Pdf"><span><i class="fa fa-file-pdf-o"></i></span></a>
                                    <a href="<?php echo base_url() ?>admin/attendance/create_excel/<?php echo $department_id . '/' . $date ?>" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top" title="Excel"><span><i class="fa fa-file-excel-o"></i></span></a>                                                
                                    <button  class="btn-print" title="Print" data-toggle="tooltip" type="button" onclick="printEmp_report('EmpprintReport')"><?php echo btn_print(); ?></button>                                                              
                                </div>
                            </h4>
                        </div>                                                  
                        <table id="" class="table table-bordered std_table">
                            <thead>
                                <tr>
                                    <th style="width: 100%" class="col-sm-3">Name</th>                

                                    <?php foreach ($dateSl as $edate) : ?>                                
                                        <th class="std_p"><?php echo $edate ?></th>
                                    <?php endforeach; ?>                        

                                </tr>  

                            </thead>      

                            <tbody>

                                <?php foreach ($attendance as $key => $v_employee): ?>
                                    <tr>  

                                        <td style="width: 100%" class="col-sm-3"><?php echo $employee[$key]->first_name . ' ' . $employee[$key]->last_name ?></td>   
                                        <?php foreach ($v_employee as $v_result): ?>
                                            <?php foreach ($v_result as $emp_attendance): ?>
                                                <td>
                                                    <?php
                                                    if ($emp_attendance->attendance_status == 1) {
                                                        echo '<span  style="padding:2px; 4px" class="label label-success std_p">P</span>';
                                                    }if ($emp_attendance->attendance_status == '0') {
                                                        echo '<span style="padding:2px; 4px" class="label label-danger std_p">A</span>';
                                                    }if ($emp_attendance->attendance_status == 'H') {
                                                        echo '<span style="padding:2px; 4px" class="label label-info std_p">H</span>';
                                                    }
                                                    ?>
                                                </td>
                                            <?php endforeach; ?>   


                                        <?php endforeach; ?>        
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>

                    </div>
                </div>    
            </div>
        </div>    
    <?php endif; ?>
</div>

<script type="text/javascript">
    function printEmp_report(EmpprintReport) {
        var printContents = document.getElementById(EmpprintReport).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
