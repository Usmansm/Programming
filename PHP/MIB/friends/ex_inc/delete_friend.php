<?php
session_start();
include "../../config/config.php";
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);

$friends = $_GET["fs"];
$friends = explode(",",$friends);
unset($friends[0]);
foreach($friends as $fid){
	$query = "UPDATE user_friend_detail SET ViewableRow='0' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fid ."'";
	$res = $mysql->query($query);
}

?>