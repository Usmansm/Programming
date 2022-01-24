<link href="<?php echo base_url() ?>asset/css/fullcalendar.css" rel="stylesheet" type="text/css" >
<style type="text/css">
    .datepicker{z-index:1151 !important;}   
</style>
<?php echo message_box('success'); ?>

<div class="dashboard row" >
    <div class="container-fluid">
        <!-- Info boxes -->
        <!--get total view-->
        <div class="row">         
            <!--Total Order-->
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-pie-chart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Award</span>
                        <span class="info-box-number"><?php
                            if (!empty($total_award)) {
                                echo $total_award;
                            }
                            ?></span>
                    </div>
                </div>
            </div>
            <!--/ Total Order-->

            <!--Total Invoice-->
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Expense</span>
                        <span class="info-box-number"><?php
                            $genaral_info = $this->session->userdata('genaral_info');
                            if (!empty($total_expense)) {
                                if (!empty($genaral_info[0]->currency)) {
                                    $currency = $genaral_info[0]->currency;
                                } else {
                                    $currency = '$';
                                }
                                echo $currency . ' ' . number_format($total_expense, 2);
                            }
                            ?></span>
                    </div>
                </div>
            </div>
            <!--/ Total Invoice-->

            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>
            <!--Total Customer-->
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Employee</span>
                        <span class="info-box-number"><?php
                            if (!empty($total_employee)) {
                                echo $total_employee;
                            }
                            ?></span>
                    </div>
                </div>
            </div>
            <!--/ Total Customer-->            
        </div> 
        <!--/ get total view-->

        <!--Monthly Recap Report And Latest Order and Total Revenue,Cost,Profit,Tax -->

        <div class="row">
            <div class="col-sm-7">      
                <div class="panel panel-default">
                    <div class="panel-heading">

                    </div>
                    <div id="calendar"></div>            
                </div>    
                <div class="wrap-fpanel margin">                           
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo 'Upcomming Birhday' ?> - <?php echo date("F"); ?></h3>
                        </div>               
                        <div class="panel-body bithday">
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
            <div class="col-md-5 wrap-fpanel">
                <div class="panel panel-default">
                    <div class="panel-heading with-border">
                        <h3 class="panel-title">Expense Report</h3>                       
                    </div><!-- /.box-header -->
                    <!--Monthly Recap Report And Latest Order  -->
                    <div class="panel-body">
                        <div class="row">
                            <!-- Monthly Recap Report-->
                            <div class="col-md-12">
                                <!-- Start select input year -->
                                <p class="text-center">
                                <form role="form" id="form" action="<?php echo base_url(); ?>admin/dashboard" method="post" class="form-horizontal form-groups-bordered">
                                    <div class="form-group">
                                        <label  class="col-sm-3 control-label">Select Year<span class="required">*</span></label>                                        
                                        <div class="col-sm-5"> 
                                            <div class="input-group">
                                                <input type="text" name="year" value="<?php
                                                if (!empty($year)) {
                                                    echo $year;
                                                }
                                                ?>" class="form-control years"><span class="input-group-addon"><a href="#"><i class="glyphicon glyphicon-calendar"></i></a></span>
                                            </div>
                                        </div>
                                        <button type="submit" data-toggle="tooltip" data-placement="top" title="Search" class="btn btn-custom"><i class="fa fa-search"></i></button>
                                    </div>
                                </form>
                                </p>
                                <!-- End select input year -->
                                <div class="chart-responsive">
                                    <!-- Sales Chart Canvas -->
                                    <canvas id="buyers" class="col-sm-12"></canvas>
                                </div><!-- /.chart-responsive -->
                            </div><!-- /.col -->
                            <!-- / Monthly Recap Report -->                            
                        </div><!-- /.row -->
                    </div><!-- ./box-body -->
                    <!--End Monthly Recap Report And Latest Order  -->


                    <!-- / Monthly Recap Report And Latest Order and Total Revenue,Cost,Profit,Tax -->

                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Recent Applications<span class="pull-right text-white"><a href="<?php echo base_url() ?>admin/application_list/" class=" view-all-front">View All</a></span></h3>
                    </div>               
                    <div class="panel-body appls-scroll">
                        <ul class="leave_apps">
                            <?php if (!empty($recent_application)): ?>    
                                <?php
                                foreach ($recent_application as $v_recent_apps) :
                                    ?>                                    
                                    <li><!-- start message -->
                                        <a   href="<?php echo base_url() ?>admin/application_list/view_application/<?php echo $v_recent_apps->application_list_id ?>">
                                            <h5>
                                                <div class="pull-left">
                                                    <img class="img-circle" src="<?php echo base_url() . $v_recent_apps->photo ?>">
                                                </div>
                                                <span><?php echo $v_recent_apps->first_name . ' ' . $v_recent_apps->last_name ?></span>
                                                <small class="apps_category">(<?php echo $v_recent_apps->category ?>)</small>
                                                <p class="leave_para"><?php
                                                    $str = strlen($v_recent_apps->reason);
                                                    if ($str > 55) {
                                                        $ss = '<strong> ......</strong>';
                                                    } else {
                                                        $ss = '&nbsp';
                                                    } echo substr($v_recent_apps->reason, 0, 55) . $ss;
                                                    ?></p>
                                            </h5>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif ?>
                        </ul>
                    </div>
                </div> 
            </div>
        </div>
    </div>  

    <div id="event_modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" id="form" enctype="multipart/form-data" action="<?php echo base_url(); ?>admin/settings/save_event" method="post" class="form-horizontal form-groups-bordered">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 id="myModalLabel">Personal Event</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label">Event Name<span class="required">*</span></label>
                            <div class="col-sm-5">
                                <input type="text" name="event_name"  class="form-control" id="field-1" value=""/>                                        
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Start Date<span class="required">*</span></label>
                            <div class="input-group col-sm-5">
                                <input type="text" value="<?php echo date('Y/m/d') ?>" class="form-control datepicker" id="apptStartTime" name="start_date" data-format="yyyy/mm/dd">

                                <div class="input-group-addon">
                                    <a href="#"><i class="entypo-calendar"></i></a>
                                </div>
                            </div>
                        </div>                               
                        <div class="form-group">
                            <label class="col-sm-3 control-label">End Date<span class="required">*</span></label>
                            <div class="input-group col-sm-5">
                                <input type="text" value="<?php echo date('Y/m/d') ?>" class="form-control datepicker" id="apptEndTime" name="end_date" data-format="yyyy/mm/dd">
                                <div class="input-group-addon">
                                    <a href="#"><i class="entypo-calendar"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>                                                        
                    <input type="hidden" id="apptAllDay" />
                    <!--                            <div class="control-group">
                                                    <label class="control-label" for="when">When:</label>
                                                    <div class="controls controls-row" id="when" style="margin-top:5px;">
                                                    </div>
                                                </div>-->
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                        <button class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>   
        </div>
    </div>


    <!--Calendar-->
    <script type="text/javascript">
        $(document).ready(function() {             if ($('#calendar').length) {
        var date = new Date();
                var d = date.getDate();
                var m = date.getMonth(); var y = date.getFullYear();
                var calendar = $('#calendar').fullCalendar({
        header: {
        center: 'prev title next',
                left: 'month agendaWeek agendaDay today',
                right: ''
        },
                buttonText: {
                prev: '<i class="fa fa-angle-left" />',
                        next: '<i class="fa fa-angle-right" />'
                },
                selectable: true,
                selectHelper: true,
                select: function(start, end, allDay) {
                var endtime = $.fullCalendar.formatDate(end, 'h:mm tt');
                        var starttime = $.fullCalendar.formatDate(start, 'yyyy/MM/dd');
                        var mywhen = starttime + ' - ' + endtime;
                        $('#event_modal #apptStartTime').val(starttime);
                        $('#event_modal #apptEndTime').val(starttime);
                        $('#event_modal #apptAllDay').val(allDay);
                        $('#event_modal #when').text(mywhen);
                        $('#event_modal').modal('show');
                },
                events: [
<?php
foreach ($get_holiday as $holiday) :
    $start_day = date('d', strtotime($holiday->start_date));
    $smonth = date('n', strtotime($holiday->start_date));
    $start_month = $smonth - 1;
    $start_year = date('Y', strtotime($holiday->start_date));
    $end_year = date('Y', strtotime($holiday->end_date));
    $end_day = date('d', strtotime($holiday->end_date));
    $emonth = date('n', strtotime($holiday->end_date));
    $end_month = $emonth - 1;
    ?>
                    {
                    title: '<?php echo $holiday->event_name; ?>',
                            start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                            end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                            color: '#5BC0DE',
                    },
<?php endforeach ?>
<?php
foreach ($absent_employee as $v_absnt_info) :
    $start_day = date('d', strtotime($v_absnt_info->date));
    $smonth = date('n', strtotime($v_absnt_info->date));
    $start_month = $smonth - 1;
    $start_year = date('Y', strtotime($v_absnt_info->date));
    $end_year = date('Y', strtotime($v_absnt_info->date));
    $end_day = date('d', strtotime($v_absnt_info->date));
    $emonth = date('n', strtotime($v_absnt_info->date));
    $end_month = $emonth - 1;
    ?>
                    {
                    title  : '<?php echo $v_absnt_info->first_name . ' ' . $v_absnt_info->last_name ?>',
                            start: new Date(<?php echo $start_year . ',' . $start_month . ',' . $start_day; ?>),
                            end: new Date(<?php echo $end_year . ',' . $end_month . ',' . $end_day; ?>),
                            color  : '#F0AD4E',
                    },
<?php endforeach ?>
                ],
                eventColor: '#3A87AD',
        });
        }

        });</script>
    <script src="<?php echo base_url(); ?>asset/js/fullcalendar.js"></script>
    <script src="<?php echo base_url(); ?>asset/js/jquery-ui.min.js"></script>
    <!-- / Chart.js Script -->
    <script src="<?php echo base_url(); ?>asset/js/chart.min.js" type="text/javascript"></script>

    <script>
                // line chart data
                var buyerData = {

                labels: [
<?php
// yearle result name = month name 
foreach ($all_expense as $name => $v_expense):
    $month_name = date('F', strtotime($year . '-' . $name)); // get full name of month by date query
    ?>
                    "<?php echo $month_name; ?>", // echo the whole month of the year
<?php endforeach; ?>
                ],
                        datasets: [
                        {
                        fillColor: "rgba(172,194,132,0.4)",
                                strokeColor: "#ACC26D",
                                pointColor: "#fff",
                                pointStrokeColor: "#9DB86D",
                                data: [
<?php
// get monthly result report 
foreach ($all_expense as $v_expense):
    ?>
                                    "<?php
    if (!empty($v_expense)) { // if the report result is exist 
        $total_expense = 0;
        foreach ($v_expense as $exoense) {
            $total_expense += $exoense->amount;
        }
        echo $total_expense; // view the total report in a  month
    }
    ?>",
    <?php
endforeach;
?>
                                ]
                        }
                        ]
                }

        // get line chart canvas
        var buyers = document.getElementById('buyers').getContext('2d');
                // draw line chart
                new Chart(buyers).Line(buyerData);
    </script>