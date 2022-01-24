<nav id="top-bar" class="collapse top-bar-collapse">
	<ul class="nav navbar-nav pull-right" style='margin-top:6px'>
		<i class="fa fa-user"></i>
		<?php 
		echo $userFullName;
		echo ' [ ';
		echo $this->Html->link($this->Label->get('general.logout'), array('controller' => 'Users', 'action' => 'logout', 'plugin' => false), array('escape' => false));
		echo ' ] ';
		?>
	</ul>
</nav>