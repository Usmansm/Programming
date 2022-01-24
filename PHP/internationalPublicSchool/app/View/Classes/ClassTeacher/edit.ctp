<?php
echo $this->Html->css('form', 'stylesheet', array('inline' => false));
echo $this->Html->css('../js/plugins/autocomplete/jquery-ui', array('inline' => false));
echo $this->Html->css('../js/plugins/autocomplete/autocomplete', array('inline' => false));
echo $this->Html->script('plugins/autocomplete/jquery-ui', array('inline' => false));
echo $this->Html->script('class.teacher', array('inline' => false));
echo $this->Html->script('app.form', array('inline' => false));
echo $this->element('classes/header');
?>

<div class="tab-content" url="Classes/teacher_ajax_find_teacher/">
	<h4 class="heading">
		<span><?php echo $this->Label->get('general.teachers'); ?></span>
		<?php echo $this->FormUtility->link('back', array('action' => 'teacher')); ?>
	</h4>

	<?php
	echo $this->element('layout/alert');
	echo $this->Form->create('ClassTeacher', array(
			'url' => array('controller' => 'Classes', 'action' => 'teacher_edit'),
			'class' => 'form-horizontal',
			'novalidate' => true,
				'inputDefaults' => array(
					'div' => 'form-group',
					'label' => array('class' => 'col-md-2 control-label'),
					'between' => '<div class="col-md-3">',
					'after' => '</div>',
					'class' => 'form-control'
				)
		));
    ?>
   	 	<?php echo $this->Form->hidden('ClassTeacher.id');  ?>
		<?php echo $this->Form->hidden('ClassTeacher.staff_id', array('class' => 'staff-id')); ?>
		<?php if(isset($invalid_teacher)){
			echo $this->Form->input('SecurityUser.identification_no', array('id' => 'searchId', 'class'=>'identification-no form-control', 'placeholder' => $this->Label->get('general.idPlaceholder2'), 'div'=>'form-group required error', 'after'=>'<div class="alert alert-danger form-error">'. $invalid_teacher .'</div></div>'));
		}else{
			echo $this->Form->input('SecurityUser.identification_no', array('id' => 'searchId', 'class'=>'identification-no form-control', 'placeholder' => $this->Label->get('general.idPlaceholder2')));
		}?>
		<?php echo $this->Form->input('Staff.staff_no', array('readonly'=>'readonly', 'class'=>'staff-no form-control')); ?>
		<?php echo $this->Form->input('SecurityUser.first_name', array('readonly'=>'readonly', 'class'=>'first-name form-control')); ?>
		<?php echo $this->Form->input('SecurityUser.last_name', array('readonly'=>'readonly', 'class'=>'last-name form-control')); ?>
		

    <?php
	echo $this->FormUtility->getFormButtons($this->Form);
	echo $this->Form->end();
	?>

</div>

<?php echo $this->element('students/footer'); ?>
