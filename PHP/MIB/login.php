<?php
    require_once('config/config.php');
	session_start();
	$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
	
	
	$result = $mysqli->query('SELECT EmailStatus, userId FROM user_email WHERE emailAddr="'.$_GET['email'].'"');
	if($result->num_rows > 0){
	 $user=$result->fetch_assoc();
	 if($user['EmailStatus']=="verified"){

	 $password= hash('sha256', $_GET['password']);

	 	$login = $mysqli->query('SELECT userPassword FROM users WHERE userId="'.$user['userId'].'" and userPassword="'.$password.'"');
		if($login->num_rows > 0){
		$_SESSION['userId']=$user['userId'];
		echo "success";
		}else{
		echo "unsuccessful";
		}
	 }else{
	 	echo "notverified";
	 	}
	
	}else{
		echo "invalidemail";
	}
	
?>