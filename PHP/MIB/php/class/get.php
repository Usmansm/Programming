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

    $li = new li;
	$li->login();

     echo '<script type="text/javascript"> window.location = "'.$config["root"].'friends/" 
</script>';

//test

?>
