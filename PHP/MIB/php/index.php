<?php
session_start();
require_once('../config/config.php');
require_once('php/friend.class.php');
require_once('php/fb.class.php');
require_once('php/li.class.php');
require_once('php/import.class.php');
require_once('php/default.class.php');
require_once('php/sanitize.class.php');
require_once('php/user.class.php');
require_once('php/verify.class.php');
require_once('php/urlCreator.class.php');
require_once('php/login.class.php');
require_once('php/detail.class.php');
require_once("lib/facebooksdk/src/facebook.php");
$facebook = new Facebook(array(
  'appId'  => $config['facebook_appId'],
  'secret' => $config['facebook_secret'],
));

$userDataFb = $facebook->getUser();

//var_dump($userDataFb);
$fb = new fb;
echo 'created class';
$fb->login(true);
echo 'Importing';


?>