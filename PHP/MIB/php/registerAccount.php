<?php

function registerAccount($username, $email, $password, $config){
	
	// Sanitize data
	require_once 'lib/htmlpurifier/library/HTMLPurifier.auto.php';
	//Load HtmlPurifier
	$HPConfig = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($HPConfig);
	$username = $purifier->purify($username);
	$email = $purifier->purify($email);
	$password = $purifier->purify($password);
	//Connect to Mysql
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$username = mysqli_real_escape_string($conn, $username);
	$email = mysqli_real_escape_string($conn, $email);
	$password = mysqli_real_escape_string($conn, $password);
	$encryptedPass = hashPass($password);
	$query = 'INSERT INTO users (userStatus, accountType, email, userName, userPassword) VALUES (0, 0, "'.$email.'", "'.$username.'", "'.$encryptedPass.'")';
	echo $query;
	mysqli_query($conn, $query) or die (mysqli_error($conn));

}
?>