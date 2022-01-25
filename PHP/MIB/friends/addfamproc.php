<?php
if (@!$_SESSION) {
	session_start();
}
include "../config/config.php";

$mysqli = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
if (mysqli_connect_errno()) {
	die("Connect failed: \n" . mysqli_connect_error());
}
	//print_r($_GET);
	$qv = "INSERT INTO user_friend_family_details(
		userfrnd_detail_user_UserId, 
		userfrnd_detail_user_FriendUserId, 
		FamilyMember_Type, 
		FamilyMember_FirstName, 
		FamilyMember_LastName, 
		FamilyMember_BornOn, 
		FamilyMember_Email, 
		FamilyMember_PhoneCell, 
		FamilyMember_Notes
		)VALUES(
		'". $_SESSION["userId"] ."', 
		'". $_GET["fid"] ."', 
		'". $_GET["ftype"] ."', 
		'". $_GET["ffname"] ."', 
		'". $_GET["flname"] ."', 
		'". $_GET["fdob"] ."', 
		'". $_GET["femail"] ."', 
		'". $_GET["fphone"] ."', 
		'". $_GET["fnotes"] ."')";
	echo $qv;
	mysqli_query($mysqli, $qv);
	
	
}

?>