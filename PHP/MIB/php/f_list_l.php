<?php
if(@!$_SESSION){
	session_start();
}
include "../config/config.php";

$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
function profilepicurl($src,$uid,$exinf){
	if($src == "facebook"){
		$profilepicurl = "https://graph.facebook.com/". $uid ."/picture?type=large";
		return $profilepicurl;
	}
	elseif($src == "linkedin"){
		//When Sams end complete:
		$profilepicurl = "WHERE-EVER".$exinf;
		//
		$profilepicurl = "images/noimage.png";
		return $profilepicurl;
	}
	else{
		$profilepicurl = "images/noimage.png";
		return $profilepicurl;
	}
}
function sourceiconurl($src,$uid,$exinf){
	if($src == "facebook"){
		$iconurl = "<a href='http://facebook.com/". $uid ."' target='_blank' class='a_noshow' ><img src='images/facebook.png' style='width: 15px;height: 15px;' /></a>";
		return $iconurl;
	}
	elseif($src == "linkedin"){
		//When Sams end complete:
		$iconurl = "<a href='". $exinf ."' target='_blank' class='a_noshow' ><img src='images/linkedin.png' style='width: 15px;height: 15px;' /></a>";
		//
		$iconurl = "<a href='#' target='_blank' class='a_noshow' ><img src='images/linkedin.png' style='width: 15px;height: 15px;' /></a>";
		return $iconurl;
	}
}
if(@$_SESSION["userId"] != ""){


$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);

	if(mysqli_connect_errno()) {
		die("Connect failed: \n".mysqli_connect_error());
	}
	$query = "SELECT * FROM user_categories WHERE userId = '".$_SESSION['userId']. "'";
	$result = $mysql->query($query);
	$cats = array();
	while($row = $result->fetch_assoc()){
		$catId = $row['catId'];
		$cats[$catId] = $row['catName'];	
	}
	$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' ORDER BY FriendFirstName ASC";
	$result = $mysql->query($query);
	if($result->num_rows > 0){
		$_SESSION["dir_com"]["show_get_started_widget"] = false;
		$fid = 0;
		while($row = $result->fetch_assoc()){
			$query2 =  "SELECT * FROM source_import WHERE userId = '".$row['friendId']."'";
			$res2 = $mysql->query($query2);
			$raw = $res2->fetch_assoc();
			$userCats = explode(',', $row['FriendCategory']);
			$catsEcho = '';
			foreach($userCats as $userCat){
				if(!empty($userCat)){
					$catsEcho .= $cats[$userCat].'<br>';
				}
			}
			
			if($row["FriendMiddleName"] != NULL){
				$fmn = " ".$row["FriendMiddleName"]." ";
				$mn = " ".substr($row["FriendMiddleName"], 0, 1).". ";
			}
			else{
				$mn = " ";
				$fmn = " ";
			}
			if(! in_array($row["friendId"], $_SESSION["incatusers"]) || $_SESSION["incatusers"] == ""){
			echo "<div oncontextmenu=\"show_cmenu();return false;\" class=\"friendsel_l2\" ><div id=\"". $row["friendId"] ."\" class=\"friendsel_pic\" style=\"background: url('". profilepicurl($raw["sourceName"],$raw["sourceUid"]) ."');background-size: 100% 100%;\" ><input name='friend_checkbox' id='".$row['friendId']."'type='checkbox' /></div><div class=\"friendsel_name\" ><div class=\"friendsel_namespan\" ><span onclick=\"get_friend_detail('". $row["id"] ."')\" title=\"". $row["FriendFirstName"] . $fmn . $row["FriendLastName"] ."\" class='fls'>". $row["FriendFirstName"] . $mn . $row["FriendLastName"] ."</span><br />". sourceiconurl($raw["sourceName"], $raw["sourceUid"]) ."</div><div class='friendsel_minf' >Mail</div><div class='friendsel_tags' ><b>Categories: </b><br>".$catsEcho."</div></div></div>";
			$fid++;
			}
			//<label for=\"". $fid ."_friend_checkbox\" ><img src=\"images/friend_checkbox.png\" id=\"". $fid ."_friend_checkboximg\" onclick=\"toggle_friend_checkboximg(". $fid .")\" class=\"friend_checbox_img\" /></label>
			}
	}
	else{
		$_SESSION["dir_com"]["show_get_started_widget"] = true;
		echo "You don't have any friends yet!";
	}
}
else{
	die("Unexpected error.");
}
?>
