<?php
echo $this->Html->css('../js/plugins/timepicker/bootstrap-timepicker', array('inline' => false));
echo $this->Html->script('plugins/timepicker/bootstrap-timepicker', array('inline' => false));
echo $this->Html->script('app.form', array('inline' => false));
echo $this->Html->script('app.attendance', array('inline' => false));

$this->extend('/Layouts/tabs');
$this->assign('contentHeader', $header);
$this->assign('portletHeader', '<h3><i class="fa fa-table"></i></h3>');

$this->start('tabActions');
$this->end();

$this->prepend('portletBody');
    //echo $this->element('attendance/datepicker');
$this->end();

$this->start('tabBody');
	echo $this->element('attendance/staff/edittable');
$this->end();
?>

			