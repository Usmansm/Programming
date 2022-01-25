<?php
session_start();
include('../../config/config.php');
$letter = $_GET['letter'];
$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."'";
$results = mysqli_query($conn, $query);
$array = array();
$fid = 0;
while($row = mysqli_fetch_row($results)){
	$firstL = substr($row[5], 0, 1); 
	$lastL = substr($row[7], 0, 1); 
	if($firstL == $letter || $firstL == strtoupper($letter)){
		if(!in_array($row[0], $array)){
			$array[] = $row[0];	
			$fid2[] = $fid;
		}	
	}
	if($lastL == $letter || $lastL == strtoupper($letter)){
		if(!in_array($row[0], $array)){
			$array[] = $row[0];
			$fid2[] = $fid;	
		}
	}
	$fid++;
}
$key = 0;
foreach($array as $id){
	$query = "SELECT * FROM user_friend_detail WHERE id = '".$id."' ORDER BY FriendFirstName ASC";
	$results = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($results);
	if($row["FriendMiddleName"] != NULL){
		$fmn = " ".$row["FriendMiddleName"]." ";
		$mn = " ".substr($row["FriendMiddleName"], 0, 1).". ";
	}
	else{
		$mn = " ";
		$fmn = " ";
	}
	$query2 =  "SELECT * FROM source_import WHERE userId = '".$row['friendId']."'";
	$results2 = mysqli_query($conn, $query2);
	$raw = mysqli_fetch_assoc($results2);
	echo "<div class=\"friendsel_l2\" ><div id=\"". $row["friendId"] ."\" class=\"friendsel_pic\" style=\"background: url('https://graph.facebook.com/". $raw['sourceUid'] ."/picture?type=large');background-size: 100% 100%;\" ><input name='friend_checkbox' id='". $fid2[$key] ."_friend_checkbox' type='checkbox' /></div><div class=\"friendsel_name\" ><div class=\"friendsel_namespan\" ><span onclick=\"get_friend_detail('". $row["id"] ."')\" title=\"". $row["FriendFirstName"] . $fmn . $row["FriendLastName"] ."\" class='fls'>". $row["FriendFirstName"] . $mn . $row["FriendLastName"] ."</span><br /><a href='http://facebook.com/". $raw['sourceUid'] ."' target='_blank' class='a_noshow' ><img src='images/facebook.png' style='width: 15px;height: 15px;' /></a></div><div class='friendsel_minf' >Mail</div><div class='friendsel_tags' >Categories</div></div></div>";
	$key++;
}
echo '<span class="friend_div" >More friends below</span>';
?>