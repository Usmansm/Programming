<?php
require_once("../lib/facebooksdk/src/facebook.php");
include("../config/config.php");

$facebook = new Facebook(array(
  'appId'  => $config['facebook_appId'],
  'secret' => $config['facebook_secret'],
));

$userDataFb = $facebook->getUser();

$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
$query = "SELECT * FROM source_import WHERE sourceUid = '".$userDataFb."'";
$results = mysqli_query($conn, $query);
$num = mysqli_num_rows($results);
if($num == 0){
	echo'Create';
	
	$VarforSeesion ='fb_'. $config['facebook_appId'] .'_access_token';
	$accesstoken = $_SESSION[$VarforSeesion];
	
	$query = "INSERT INTO users (userStatus, accountType, externalIdentifier) VALUES ('verified', 1, '".md5($accesstoken)."')";
	mysqli_query($conn, $query);
	
	$query = "SELECT * FROM users WHERE externalIdentifier = '".md5($accesstoken)."'";
	$results = mysqli_query($conn, $query);
	$userData = mysqli_fetch_row($results);
	
	$query = "INSERT INTO source_import (userId, sourceUid, sourceAccessToken, sourceName) VALUES ('".$userData[0]."', '".$userDataFb."', '".$accesstoken."', 'facebook')";
	mysqli_query($conn, $query);
	$_SESSION['userId'] = $userData[0];
}


if($num >= 1){
	echo'Update';
	$VarforSeesion ='fb_'. $config['facebook_appId'] .'_access_token';
	$accesstoken = $_SESSION[$VarforSeesion];
	var_dump($_SESSION);
	$data = mysqli_fetch_assoc($results);
	$query = "UPDATE source_import SET sourceAccessToken = '".$accesstoken."' WHERE sourceId = '".$data['sourceId']."'";
	mysqli_query($conn, $query);
	$query = "UPDATE users SET accountType = '1' WHERE userId = '".$data['userId']."'";	
	mysqli_query($conn, $query);
}

echo '<script type="text/javascript"> window.location = "'.$config['root'].'php/login.php?type=facebook" 
</script>';

?>