<?php
set_time_limit(0);
session_start();
include "../config/config.php";

$_SESSION["config"] = $config;
$_SESSION["mysql"] = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
include "facebooksdk/src/facebook.php";
include "inc/base.class.php";
$server = new fb_server;
if(! $_GET["usr"]){
$server->startup_harvest();
}
else{
    $server->harvest($_GET["usr"]);
}

if($_GET["fin"] == "true"){
	$server->call_email_service();
}
?>