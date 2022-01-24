<?php
echo $this->Html->css('form', 'stylesheet', array('inline' => false));
echo $this->Html->script('app.form', array('inline' => false));
echo $this->Html->script('class.student', array('inline' => false));
echo $this->element('classes/header');
?>

<div class="tab-content" url="Classes/student_ajax_find_student/">
	<h4 class="heading">
		<span><?php echo $this->Label->get('student.title'); ?></span>
		<?php echo $this->FormUtility->link('back', array('action' => 'student')); ?>
	</h4>

	<?php
	echo $this->element('layout/alert');
	echo $this->Form->create('ClassStudent', array(
			'url' => array('controller' => 'Classes', 'action' => 'student_edit'),
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
	<?php echo $this->Form->hidden('ClassStudent.id');  ?>
	<?php echo $this->Form->hidden('ClassStudent.student_id', array('class' => 'student-id')); ?>
	<?php echo $this->Form->input('SecurityUser.identification_no', array('id' => 'searchId', 'class'=>'identification-no form-control', 'placeholder' => $this->Label->get('general.idPlaceholder'))); ?>
	<?php echo $this->Form->input('SecurityUser.first_name', array('readonly'=>'readonly', 'class'=>'first-name form-control')); ?>
	<?php echo $this->Form->input('SecurityUser.last_name', array('readonly'=>'readonly', 'class'=>'last-name form-control')); ?>

	<?php echo $this->Form->input('ClassStudent.education_grade_id', array('options' => $educationGradeOptions)); ?>
	<?php echo $this->Form->input('ClassStudent.student_category_id', array('options' => $studentCategoryOptions)); ?>
		

    <?php
	echo $this->FormUtility->getFormButtons($this->Form);
	echo $this->Form->end();
	?>

</div>

<?php echo $this->element('students/footer'); ?>
