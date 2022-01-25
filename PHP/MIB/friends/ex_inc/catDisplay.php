<?php
session_start();
include('../../config/config.php');

	$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]) or die('Connecting error');
function integrationactivity($src,$fid){
	global $mysql;
	$notescount = 0;
	$postscount = 0;
	if($src == "news"){
		$userctasquery = "SELECT * FROM user_friend_cat WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fid ."'";
		$usercatres = $mysql->query($userctasquery);
		while($userfrndcat = $usercatres->fetch_assoc()){
			$catid = $userfrndcat["catId"];
			$notesquery = "SELECT * FROM evn_notes_cat WHERE userId ='". $_SESSION["userId"] ."' AND catId ='". $catid ."'";
			$notesres = $mysql->query($notesquery);
			while($not = $notesres->fetch_assoc()){
				$notescount++;
			}		
		}
						echo $notescount;
		
	}
	else if($src == "posts"){
		$postsquery = "SELECT * FROM user_frnd_fbpost WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fid ."'";
		$postsres = $mysql->query($postsquery);
		$postscount = $postsres->num_rows;
		if($postscount == "" || $postscount == 0){
			$postscount = "0";
		}
		echo $postscount;
	}
}

$catId = $_GET['catId'];
if($catId == 'all' || $catId == 'catFb' || $catId == 'catLi'){
	if($catId == 'catFb'){
		foreach($_SESSION['catFb'] as $id){
			$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
			$query = "SELECT * FROM user_friend_detail WHERE friendId = '".$id."' AND userId = '".$_SESSION['userId']."'";
			$results = mysqli_query($conn, $query) or die(mysqli_error($conn));
			$row = mysqli_fetch_assoc($results);
			if($row["FriendMiddleName"] != NULL){
				$fmn = " ".$row["FriendMiddleName"]." ";
				$mn = " ".substr($row["FriendMiddleName"], 0, 1).". ";
			}
			else{
				$mn = " ";
				$fmn = " ";
			}
			/*
			$userCats = explode(',', $row['FriendCategory']);
			$catsEcho = '';
			foreach($userCats as $userCat){
				if(!empty($userCat)){
					$catsEcho .= $cats[$userCat].'<br>';
				}
			}
			*/
			$query2 =  "SELECT * FROM source_import WHERE userId = '".$row['friendId']."'";
			$results2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
			$raw = mysqli_fetch_assoc($results2);
			echo "<div class=\"friendsel_l2\" ><div id=\"". $row["friendId"] ."\" class=\"friendsel_pic\" style=\"background: url('".$config['FBGraph'] . $raw['sourceUid'] ."/picture?type=large');background-size: 100% 100%;\" ><input name='friend_checkbox' id='". $fid2[$key] ."_friend_checkbox' type='checkbox' /></div><div class=\"friendsel_name\" ><div class=\"friendsel_namespan\" ><span onclick=\"get_friend_detail('". $row["id"] ."')\" title=\"". $row["FriendFirstName"] . $fmn . $row["FriendLastName"] ."\" class='fls'>". $row["FriendFirstName"] . $mn . $row["FriendLastName"] ."</span></div><div class='friendsel_minf' ><b>Activity:</b><br />News stories: ";
			integrationactivity("news", $row["friendId"]);
			echo "<br />Social posts: ";
			integrationactivity("posts", $row["friendId"]);
			echo "</div><div class='friendsel_tags' >Categories: <br><br>".$catsEcho."</div></div></div>";
		}
	}
	if($catId == 'catLi'){
		
	}
	if($catId == 'all'){
		$query = "SELECT * FROM user_categories WHERE userId = '".$_SESSION['userId']. "'";
	$result = mysqli_query($conn, $query);
	$cats = array();
	while($row = mysqli_fetch_assoc($result)){
		$catId = $row['catId'];
		$cats[$catId] = $row['catName'];	
	}
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' ORDER BY FriendFirstName ASC";
	$results = mysqli_query($conn, $query);
	while($row = mysqli_fetch_assoc($results)){
		if(!$first){
		if($row["FriendMiddleName"] != NULL){
		$fmn = " ".$row["FriendMiddleName"]." ";
		$mn = " ".substr($row["FriendMiddleName"], 0, 1).". ";
		}
		else{
			$mn = " ";
			$fmn = " ";
		}
		$userCats = explode(',', $row['FriendCategory']);
		$catsEcho = '';
		foreach($userCats as $userCat){
			if(!empty($userCat)){
				$catsEcho .= $cats[$userCat].'<br>';
			}
		}
		$query2 =  "SELECT * FROM source_import WHERE userId = '".$row['friendId']."'";
		$results2 = mysqli_query($conn, $query2);
		$raw = mysqli_fetch_assoc($results2);
		array_push($_SESSION["incatusers"], $row["friendId"]);
		echo "<div class=\"friendsel_l2\" ><div id=\"". $row["friendId"] ."\" class=\"friendsel_pic\" style=\"background: url('".$config['FBGraph'] . $raw['sourceUid'] ."/picture?type=large');background-size: 100% 100%;\" ><input name='friend_checkbox' id='". $fid2[$key] ."_friend_checkbox' type='checkbox' /></div><div class=\"friendsel_name\" ><div class=\"friendsel_namespan\" ><span onclick=\"get_friend_detail('". $row["id"] ."')\" title=\"". $row["FriendFirstName"] . $fmn . $row["FriendLastName"] ."\" class='fls'>". $row["FriendFirstName"] . $mn . $row["FriendLastName"] ."</span></div><div class='friendsel_minf' ><b>Activity:</b><br/>News stories: ";
		integrationactivity("news", $row["friendId"]);
		echo "<br />Social posts: ";
		integrationactivity("posts", $row["friendId"]);
		
		echo "</div><div class='friendsel_tags' >Categories: <br><br>".$catsEcho."</div></div></div>";
	}
		else{
		$first = false;
	}

	}
	}
	echo '<span class="friend_div" >More friends below</span>';
}
else {

$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM user_categories WHERE userId = '".$_SESSION['userId']. "'";
	$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
	$cats = array();
	while($row = mysqli_fetch_assoc($result)){
		$catId2 = $row['catId'];
		$cats[$catId2] = $row['catName'];	
	}
$query = "SELECT * FROM user_categories WHERE catId = '".$catId."'";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$data = mysqli_fetch_row($result);
$array = explode(',', $data[4]);
$first = true;
foreach($array as $id){
	if(!$first){
	$query = "SELECT * FROM user_friend_detail WHERE friendId = '".$id."' AND userId = '".$_SESSION['userId']."'";
	$results = mysqli_query($conn, $query) or die(mysqli_error($conn));
	$row = mysqli_fetch_assoc($results);
	if($row["FriendMiddleName"] != NULL){
		$fmn = " ".$row["FriendMiddleName"]." ";
		$mn = " ".substr($row["FriendMiddleName"], 0, 1).". ";
	}
	else{
		$mn = " ";
		$fmn = " ";
	}
	$userCats = explode(',', $row['FriendCategory']);
	$catsEcho = '';
	foreach($userCats as $userCat){
		if(!empty($userCat)){
			$catsEcho .= $cats[$userCat].'<br>';
		}
	}
	$query2 =  "SELECT * FROM source_import WHERE userId = '".$row['friendId']."'";
	$results2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
	$raw = mysqli_fetch_assoc($results2);
	array_push($_SESSION["incatusers"], $row["friendId"]);
	echo "<div class=\"friendsel_l2\" ><div id=\"". $row["friendId"] ."\" class=\"friendsel_pic\" style=\"background: url('".$config['FBGraph'] . $raw['sourceUid'] ."/picture?type=large');background-size: 100% 100%;\" ><input name='friend_checkbox' id='". $fid2[$key] ."_friend_checkbox' type='checkbox' /></div><div class=\"friendsel_name\" ><div class=\"friendsel_namespan\" ><span onclick=\"get_friend_detail('". $row["id"] ."')\" title=\"". $row["FriendFirstName"] . $fmn . $row["FriendLastName"] ."\" class='fls'>". $row["FriendFirstName"] . $mn . $row["FriendLastName"] ."</span></div><div class='friendsel_minf' >Mail</div><div class='friendsel_tags' >Categories: <br><br>".$catsEcho."</div></div></div>";
	}
	else{
		$first = false;
	}
}
}

if(mysqli_num_rows($result) > 0){
echo '<span class="friend_div" >More friends below</span>';
}
include "f_list_l.php";
?>