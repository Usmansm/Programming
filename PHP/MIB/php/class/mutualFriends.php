<?php
session_start();
require_once('../../config/config.php');
require_once('friend.class.php');
include ('../../includes/DisplayAvatar.php');

$friend = new friend;
$data = $friend->mutualFriendsColumn($_SESSION['lfid']);
$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
function checkviewable($fid){
	global $mysqli;
	$checkq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fid ."'";
	$cres = $mysqli->query($checkq);
	$cdat = $cres->fetch_assoc();
	return $cdat["ViewableRow"];
}
$fs = 0;
if(empty($data)){
echo ' No friends in Common!';
}
else {
foreach($data as $user){
	$link = DispFreProfilePic($user);
	if($fs < 6){
    echo' 
		<div class="opts_mutbox">
			<a class="a_noshow" target="_blank" href="'. $config["root"] .'friends/?a=f&f='. $user .'" >
				<img src="'.$link.'" style="width: 100%;height: 100%;" />
			</a>
		</div>';
	
	}
	$fs++;
	}
	}




?>