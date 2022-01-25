<?php
if (@!$_SESSION) {
	session_start();
}
require_once ('../../config/config.php');
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
if (mysqli_connect_error()) {
	die("Connect failed: \n" . mysqli_connect_error());
}

	//$_SESSION["addfamdebug"] = "isspouse";
	//print_r($_GET);
	
 
 $qv = "INSERT INTO user_friend_family_details(
 userId, 
 friendId, 
 FamilyMember_Type, 
 FamilyMember_FirstName, 
 FamilyMember_LastName, 
 FamilyMember_BornOn, 
 FamilyMember_Email, 
 FamilyMember_PhoneCell,
 OnlineLink1,
 OnlineLink2,
 FamilyMember_Notes) 
 
 VALUES(
 '". $_SESSION["userId"] ."', 
 '". $_GET["fid"] ."', 
 '". $_GET["ftype"] ."', 
 '". $_GET["ffname"] ."', 
 '". $_GET["flname"] ."', 
 '". $_GET["fdob"] ."', 
 '". $_GET["femail"] ."', 
 '". $_GET["fphone"] ."', 
 '". $_GET["Link1"] ."',
 '". $_GET["Link2"] ."',
 '". $_GET["fnotes"] ."')";
	
 
 mysqli_query($mysql, $qv);
	
	

?>