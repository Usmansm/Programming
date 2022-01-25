<?php
require_once('config/config.php');
   

       
		 global $config;
		//echo 'user initiated';
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']) or die($mysqli->connect_error);
		// To check if the user exist Already
		$pass = hash('sha256', $_GET['password']);
		$mysqli->query('update users set userPassword="'.$pass.'" WHERE userId = "'.$_GET['user'].'"') or die ($mysqli->error);
		
?>