<?php
session_start();
    require_once('../../config/config.php');
    require_once('friend.class.php');
	require_once('fb.class.php');
	require_once('li.class.php');
    require_once('import.class.php');
	require_once('default.class.php');
	require_once('sanitize.class.php');
	require_once('user.class.php');
	require_once('verify.class.php');
	require_once('urlCreator.class.php');
	require_once('login.class.php');
	require_once('detail.class.php');
	require_once("../../lib/facebooksdk/src/facebook.php");
    require_once('cookie.class.php');
    require_once('email.class.php');
    $user = new user;
    //var_dump($_GET);
    echo '3';

    $data = $user->manualUser($_GET['firstName'], $_GET['lastName'], $_GET['email'], $_GET['pass1'], $_GET['pass2'],$_GET['passcode']);
    echo $data;
?>