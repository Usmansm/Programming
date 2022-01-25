<?php
if(@! $_SESSION){
	session_start();
}
include "../../config/config.php";
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
$bq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND FriendStatusCode='unverified'";
$res = $mysql->query($bq);
$num = $res->num_rows;
echo $num;
?>