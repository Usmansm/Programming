<?php
include("../config/config.php");
//var_dump($config);
$errorCode = array();
$errorCode[0] = 'undefined';
session_start();

//Facebook Login
if($_GET['type'] == 'facebook'){
	require_once("../lib/facebooksdk/src/facebook.php");
	$facebook = new Facebook(array(
	  'appId'  => $config['facebook_appId'],
	  'secret' => $config['facebook_secret'],
	));
	echo 'yup';
	$userDataFb = $facebook->getUser();
	echo 'yup2';
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM source_import WHERE sourceUid = '".$userDataFb."'";
	$results = mysqli_query($conn, $query) or die (mysqli_error($conn));	
	$data = mysqli_fetch_assoc($results);
	if(mysqli_num_rows($results) != 1){
		//var_dump($userDataFb);
		//var_dump($data);
		$errorCode[0] = 'error';
		$errorCode[1] = 'Account is unknown in the system, please contact the webmasters';
		$errorCode[2] = 'login';
		//var_dump($errorCode);
		exit();
	}
	else {
		session_unset();
		$_SESSION['userId'] = $data['userId'];
		$_SESSION['facebookId'] = $data['sourceUid'];
	}
}

//Manual Login
if($_GET['type'] == 'login'){
	include('hashPass.php');
	$email = $_GET['email'];
	$pass = $_GET['password'];
	//Verify email input
	if(empty($email) || empty($pass)){
		$errorCode[0] = 'error';
		$errorCode[1] = 'Please fill in a all the required fields';
		$errorCode[2] = 'login';
		//var_dump($errorCode);
		exit();
	}
	require_once '../lib/htmlpurifier/library/HTMLPurifier.auto.php';
	//Load HtmlPurifier
	$HPConfig = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($HPConfig);
	$email = $purifier->purify($email);
	$pass = $purifier->purify($pass);
	//Connect to Mysql
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$email = mysqli_real_escape_string($conn, $email);
	$pass = mysqli_real_escape_string($conn, $pass);
	$encryptedPass = md5($pass);
	// Search for email with associated password in the database
	$query='select * from users where userPassword="'.$encryptedPass.'" and userId=
(select userId from user_email where EmailStatus="verified" emailAddr="'.$email.'")';
	//$query = 'SELECT * FROM users,user_email WHERE (emailAddr="'.$email.'" AND userPassword="'.$encryptedPass.'") AND (user.userId';
	$queryResults = mysqli_query($conn, $query);
	if(mysqli_num_rows($queryResults) == 1){
		$data = mysqli_fetch_row($queryResults);
		session_unset();
		$_SESSION['userId'] = $data[0];
		$errorCode[0] = 'succes';
	}
	else {
		$errorCode[0] = 'error';
		$errorCode[1] = 'The username or password is incorrect';
		$errorCode[2] = 'login';
		//var_dump($errorCode);
		exit();
	}
}
if($_GET['importLi'] == true){
	echo '<script type="text/javascript"> window.location = "'.$config['root'].'"friends/index.php?type=liImport" </script>';
}
else{
echo '<script type="text/javascript"> window.location = "'.$config['root'].'"friends" </script>';
}


?>
