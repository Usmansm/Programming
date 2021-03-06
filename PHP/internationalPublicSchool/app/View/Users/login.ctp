<?php
$description = __d('openemis_school', 'International Public School');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $lang_locale; ?>" dir="<?php echo $lang_dir; ?>">

<head>
	<?php echo $this->Html->charset(); ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>
		<?php echo $description ?>
	</title>
	
	<?php
		echo $this->Html->meta('favicon', $this->webroot . 'favicon.ico?v=2', array('type' => 'icon'));
		echo $this->Html->css('default/googleapis/font.css', array('media' => 'screen'));
		echo $this->Html->css('default/bootstrap.min', array('media' => 'screen'));
		echo $this->Html->css('default/font-awesome.min', array('inline' => false));
		echo $this->Html->css('main', array('media' => 'screen'));
		echo $this->Html->css('default/App', array('inline' => false));
		echo $this->Html->script('default/jquery-1.9.1.min', array('inline' => false));
		echo $this->Html->script('default/bootstrap.min', array('inline' => false));
		echo $this->Html->script('css_browser_selector', array('inline' => false));

	?>
</head>

<body onload="$('#SecurityUserUsername').focus()">
	<div class="header index-header">
    	<div class="school-title">
        	<img src="img/openemisschool.png" alt="OpenEMIS School" /><span class="visible-desktop">International Public School</span>
        </div>
    </div>
	
	<div class="content index">
		<div class="index-content">
			<div class="bg-img">
				<div class="login">
					<div class="school">
						<br />
						<?php
						$schoolName = 'International PUBLIC School';
						echo $schoolName;
						?>
					</div>
					<?php 
					if($loginAlert) {
						echo $this->element('layout/alert');
					}
					echo $this->Form->create('SecurityUser', array(
						'url' => array('controller' => 'Users', 'action' => 'login'),
						'inputDefaults' => array(
							'div' => 'form-group',
							'label' => array('class' => 'control-label'),
							'between' => '<div class="controls">',
							'after' => '</div>',
							'class' => 'form-control'
						)
					));
					?>
					<?php echo $this->Form->input('username', array('value' => $username)); ?>
					<?php echo $this->Form->input('password', array('value' => $password)); ?>
					
					<div class="form-group">
						<div class="controls login-btn">
							<!--a href="#">Forgot password?</a-->
							<?php echo $this->Form->button('Login', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
						</div>
					</div>
					<?php echo $this->Form->end() ?>
				</div>
			</div>
		</div>
	</div>
	<div class="footer index-footer">
		<span>&copy; <?php echo date('Y'); ?> Konnect Tech | Version <?php echo $version; ?></span>
	</div>
</body>
</html>
