<?php

	//Verify email input
	if(empty($email) || empty($pass)){
		$errorCode[0] = 'error';
		$errorCode[1] = 'Please fill in a all the required fields';
		$errorCode[2] = 'verifyLogin';
		return $errorCode;
	}
	require_once 'lib/htmlpurifier/library/HTMLPurifier.auto.php';
	//Load HtmlPurifier
	$HPConfig = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($HPConfig);
	$email = $purifier->purify($email);
	$pass = $purifier->purify($pass);
	//Connect to Mysql
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$email = mysqli_real_escape_string($conn, $email);
	$pass = mysqli_real_escape_string($conn, $pass);
	$encryptedPass = hashPass($pass);
	// Search for email with associated password in the database
	$query = 'SELECT * FROM users WHERE email="'.$email.'" AND userPassword="'.$encryptedPass.'"';
	$queryResults = mysqli_query($conn, $query);
	if(mysqli_num_rows($queryResults) == 1){
		$data = mysqli_fetch_row($queryResults);
		$_SESSION['userid'] = $data[0];
		$_SESSION['userName'] = $data[5];
		return $data;
	}
	else {
		$errorCode[0] = 'error';
		$errorCode[1] = 'The username or password is incorrect';
		$errorCode[2] = 'verifyLogin';
		return $errorCode;
	}

}


?>