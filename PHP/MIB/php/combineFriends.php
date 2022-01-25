<?php
session_start();
include "../config/config.php";
$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);

$_SESSION["verify debug"] = array();
function getfrienddetail($fid){
	global $mysql;
	$query = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fid ."'";
	$result = $mysql->query($query);
	$data = $result->fetch_assoc();
	return $data;
}

/* ============================================================
 * Value of same/dif buttons should look like this:
 * same/dif-case(0,1)-uid1(verified)-uid2(imported, unverified)
 * E.g.: dif-0-1534-1923
 * Cases: 0 - multiple verifieds on left, 1 unverified on right
 * 		  1 - 1 verified on left, multiple unverifieds on right
 * ============================================================
 */

$f = $_GET["f"];
$f = str_replace("undefined", "", $f);
$fs = explode(",",$f);
unset($fs[0]);
foreach($fs as $ff){
	//For each set of data (same/dif-case-uid1-uid2), split the data into an array
$fd = explode("-",$ff);
//if case is 0 (multiple on left, 1 on right)
if($fd[1] == "0"){
	if($fd[0] != "dif"){
		$query1 = "UPDATE user_friend_detail SET FriendStatusCode='verified' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[2] ."'";
		$res1 = $mysql->query($query1);
		$query1a = "UPDATE user_friend_detail SET FriendStatusCode='verified' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
		$res1a = $mysql->query($query1a);
		array_push($_SESSION["verify debug"],"here1");
		//if user has not selected different, update userfriendetail and make unverified guy verfied and unviewable
		$query = "UPDATE user_friend_detail SET FriendStatusCode='verified', ViewableRow='0' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
		echo "----4        ".$query;
		$res = $mysql->query($query);
		//update userfrndsource set unverified guy as same friendid as verified guy
		$query = "UPDATE userfrnd_source SET friendId='". $fd[2] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
		$res = $mysql->query($query);
		//Get the friend detail of both users
		$detail2 = getfrienddetail($fd[2]);
		$detail3 = getfrienddetail($fd[3]);
		//foreach element check if unverified person has data in the field and verified guy does not, if so update verified with the data
		foreach($detail3 as $key => $val){
			
			if($detail2[$key] == "" && $val != "" && $key != "FriendStatusCode" && $key != "ViewableRow"){
				$query = "UPDATE user_friend_detail SET ". $key ."='". $val ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[2] ."'";
				$res = $mysql->query($query);
			}
		}
		$query = "UPDATE user_friend_detail SET FriendStatusCode='verified', ViewableRow='0', combinedTo='". $fd[2] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
		echo "----9        ".$query;
		$ressssss = $mysql->query($query);
		
		//Updated User EMail
				$queryemail = "UPDATE user_friend_email SET friendId='". $fd[2] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
	
		echo "----9        ".$queryemail;
		$remail = $mysql->query($queryemail);
		
		//Update User Friend Address
				$queryadd = "UPDATE user_friend_address SET friendId='". $fd[2] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
	
		echo "----9        ".$queryadd;
		$readd = $mysql->query($queryadd);
		//Done I believe
	}
	else{
		$query1 = "UPDATE user_friend_detail SET FriendStatusCode='verified' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[2] ."'";
		$res1 = $mysql->query($query1);
		$query1a = "UPDATE user_friend_detail SET FriendStatusCode='verified' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
		$res1a = $mysql->query($query1a);
		$query = "UPDATE user_friend_detail SET FriendStatusCode='verified' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
		echo "----3        ".$query;
		$res = $mysql->query($query);
	}
}//ff
if($fd[1] == "1"){
	if($fd[0] != "dif"){
		array_push($_SESSION["verify debug"],"here2");
		//if user has not selected different, update userfriendetail and make unverified guy verfied and unviewable
		$query = "UPDATE user_friend_detail SET FriendStatusCode='verified', ViewableRow='0' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
		echo "----2        ".$query;
		$res = $mysql->query($query);
		//update userfrndsource set unverified guy as same friendid as verified guy
		$query = "UPDATE userfrnd_source SET friendId='". $fd[2] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
		$res = $mysql->query($query);
		//Get the friend detail of both users
		$detail2 = getfrienddetail($fd[2]);
		$detail3 = getfrienddetail($fd[3]);
		//foreach element check if unverified person has data in the field and verified guy does not, if so update verified with the data
		foreach($detail3 as $key => $val){
			
			if($detail2[$key] == "" && $val != "" && $key != "FriendStatusCode" && $key != "ViewableRow"){
				$query = "UPDATE user_friend_detail SET ". $key ."='". $val ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[2] ."'";
				$res = $mysql->query($query);
			}
		}
		
		$query = "UPDATE user_friend_detail SET FriendStatusCode='verified', ViewableRow='0', combinedTo='". $fd[2] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
		echo "----10        ".$query;
		$ressss = $mysql->query($query);
		
			
		//Updated User EMail
	    $queryemail = "UPDATE user_friend_email SET friendId='". $fd[2] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
		echo "----9        ".$queryemail;
		$remail = $mysql->query($queryemail);
		
		//Update User Friend Address
		$queryadd = "UPDATE user_friend_address SET friendId='". $fd[2] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
	
		echo "----9        ".$queryadd;
		$readd = $mysql->query($queryadd);
		//Done I believe
	}
	else{
		
		$query = "UPDATE user_friend_detail SET FriendStatusCode='verified' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fd[3] ."'";
		echo "----1        ".$query;
		$res = $mysql->query($query);
	}
}
}
echo "p";
?>