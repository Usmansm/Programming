<?php
session_start();
include('../config/config.php');
include('../includes/DisplayAvatar.php');
require('class/friend.class.php');
if($_GET['type'] == 'nmr'){
	$total = 0;
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);	
	$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' AND FriendStatusCode = 'unverified'";
	$results = mysqli_query($conn, $query);
	$num = mysqli_num_rows($results);
	echo $num;
	exit();
}

$friend = new friend;
$friend->verificationList();


/*
$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' AND ViewableRow != '0'";
$results = mysqli_query($conn, $query);
$fullNames = array();
$multipleNames = array();
while($row = mysqli_fetch_assoc($results)){
	if($row['FriendMiddleName'] != NULL){
		$fullName = $row['FriendFirstName'].' '.$row['FriendMiddleName'].' '.$row['FriendLastName'];
	}
	else {
		$fullName = $row['FriendFirstName'].' '.$row['FriendLastName'];	
	}
	if(!in_array($fullName, $fullNames)){
		array_push($fullNames, $fullName);
	}	
	else {
		$name = array($row['FriendFirstName'], $row['FriendMiddleName'], $row['FriendLastName']);
		array_push($multipleNames, $name);
	}
}
//var_dump($multipleNames);
$data = array();
$first = true;
$key2 = 2;
$key3 = 0;
foreach($multipleNames as $name){
	$query = "SELECT * FROM user_friend_detail WHERE FriendFirstName = '".mysqli_real_escape_string($conn, $name[0])."' AND	FriendLastName = '".mysqli_real_escape_string($conn, $name[2])."' AND userId = '".$_SESSION['userId']."'";
	$results = mysqli_query($conn, $query);
	$num = mysqli_num_rows($results);
	$key = 0;
	while($row = mysqli_fetch_row($results)){
		//var_dump($row);
		$data[$key] = $row;
		$key++;		
	}
	//var_dump($data);
	if($num == 2){
		$query =  "SELECT * FROM source_import WHERE userId = '".$data[0][2]."'";
		//echo $query.'<br>';
		$results = mysqli_query($conn, $query);
		$raw = mysqli_fetch_assoc($results);
		//var_dump($raw);		
		$query2 =  "SELECT * FROM source_import WHERE userId = '".$data[1][2]."'";
		$results2 = mysqli_query($conn, $query2);
		$raw2 = mysqli_fetch_assoc($results2);
		if($raw['sourceName'] == 'facebook'){
		$data[0]['image'] = 'https://graph.facebook.com/'.$raw["sourceUid"].'/picture?type=large';
		}
		if($raw2['sourceName'] == 'facebook'){
		$data[1]['image'] = 'https://graph.facebook.com/'.$raw2["sourceUid"].'/picture?type=large';
		}
		if($raw['sourceName'] == 'linkedin'){
		$data[0]['image'] = $raw["sourceProfilePicture"];
		}
		if($raw2['sourceName'] == 'linkedin'){
		$data[1]['image'] = $raw2["sourceProfilePicture"];
		}
		$selectorId = $data[0][0].'-'.$data[1][0];
		$query = "SELECT * FROM different_users WHERE userId='".$_SESSION['userId']."' AND selectorId = '".$selectorId."'";
		$results = mysqli_query($conn, $query) or die(mysqli_error($conn));
		$numDif = mysqli_num_rows($results);
		if($numDif == 0){
		
			if($first){
				if($_POST['type'] != 'nmr'){
		echo '<div class="vv" id="v1">
			  <div class="verify_l" ><img img src="'.$data[0]["image"].'" class="verify_avatarl" /><div class="verify_namel" >'.$data[0][5].' '.$data[0][7].'<br /><img src="images/facebook.png" style="width: 30px;height: 30px;" /><br /><span class="v2" ></span></div></div>
			  <div class="verify_m" >
				  <input type="radio" name="1" value="'.$data[0][0].'-'.$data[1][0].'" checked="checked" />Same<br /><input type="radio" name="1" value="dif'.$data[0][0].'-'.$data[1][0].'" />Different
			  </div>
			  <div class="verify_r" ><img img src="'.$data[1]["image"].'" class="verify_avatarr" /><div class="verify_namer" >'.$data[0][5].' '.$data[0][7].'<br /><img src="images/facebook.png" style="width: 30px;height: 30px;" /></div></div>
			  </div>';
				}
			}
			else {
				if($_POST['type'] != 'nmr'){
				echo '<div class="vv">
			  <div class="verify_l" ><img img src="'.$data[0]["image"].'" class="verify_avatarl" /><div class="verify_namel" >'.$data[0][5].' '.$data[0][7].'<br /><img src="images/facebook.png" style="width: 30px;height: 30px;" /><br /><span class="v2" ></span></div></div>
			  <div class="verify_m" >
				  <input type="radio" name="'.$key2.'" value="'.$data[0][0].'-'.$data[1][0].'" checked="checked" />Same<br /><input type="radio"  name="'.$key2.'"  value="dif'.$data[0][0].'-'.$data[1][0].'" />Different
			  </div>
			  <div class="verify_r" ><img img src="'.$data[1]["image"].'" class="verify_avatarr" /><div class="verify_namer" >'.$data[0][5].' '.$data[0][7].'<br /><img src="images/facebook.png" style="width: 30px;height: 30px;" /></div></div>
			  </div>';
				}
			  $key2++;
			  $key3++;
			}
	}
	$first = false;
}
}
//echo 'Key:'. $key2;
$nmr = $key2-1;
if($first){
	$nmr = 0;
}
if($key3 == 0){
	$empty = true;
}
else {
	$empty = false;
}
if($_POST['type'] != 'nmr'){
echo '<script> var nmrVerify = "'. $nmr .'"; var empty = "'.$empty.'"</script>';
}
if($_POST['type'] == 'nmr'){
	echo $nmr;
}
*/

?>
