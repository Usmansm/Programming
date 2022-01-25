<?php
if(@!$_SESSION){
	session_start();
 include("../../config/config.php");
}
include ('../../includes/DisplayAvatar.php');

function sourceiconurl($src,$uid){
	if($src == "facebook"){
		$iconurl = "<a href='http://facebook.com/". $uid ."' target='_blank' class='a_noshow' ><img src='images/facebook.png' style='width: 15px;height: 15px;' /></a>";
		return $iconurl;
	}
	elseif($src == "linkedin"){
		$iconurl = "<a href='#' target='_blank' class='a_noshow' ><img src='images/linkedin.png' style='width: 15px;height: 15px;' /></a>";
		return $iconurl;
	}
}

if(@$_SESSION["userId"] != ""){

	$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
	if(mysqli_connect_errno()) {
		die("Connect failed: \n".mysqli_connect_error());
	}
	$data=explode("~",$_GET['data']);
	$countRows=$mysql->query("SELECT * FROM user_friend_detail WHERE userId = '" . $_SESSION['userId'] . "' and FriendFirstName regexp '^[".$data[3]."]' ");
	$totalRows=$countRows -> num_rows;
	$limitFirst=$totalRows-1-$data[4];
	$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' and FriendFirstName regexp '^[".$data[3]."]' ORDER BY FriendFirstName ASC, FriendLastName ASC  limit ".$limitFirst.", ".$data[4];
	$result = $mysql->query($query);
	if($result->num_rows > 0){
		$_SESSION["dir_com"]["show_get_started_widget"] = false;
		$fid = 0;
		while($row = $result->fetch_assoc()){
			if($row["ViewableRow"] != "0"){
			$query2 =  "SELECT * FROM source_import WHERE userId = '".$row['friendId']."'";
			$res2 = $mysql->query($query2);
			$raw = $res2->fetch_assoc();
			if($row["FriendMiddleName"] != NULL){
				$fmn = " ".$row["FriendMiddleName"]." ";
				$mn = " ".substr($row["FriendMiddleName"], 0, 1).". ";
			}
			else{
				$mn = " ";
				$fmn = " ";
			}
			$firstLetterName = substr(ucfirst($row["FriendFirstName"]), 0, 1);
			if ($firstLetterName != $letter) {
				$letter = $firstLetterName;
				echo '<span class="AlphabetLetter" style="color: #227eb8;" >' . $letter . '</span><hr class="alpha_hr" /><a name="' . $letter . '"></a>';
			}
			
			
echo 	"<a name='". $row["friendId"] ."'></a>
			<div id=\"". $row["friendId"] ."\" class=\"friendsel_pic\" style=\"cursor: pointer;background: url('". DispFreProfilePic($row["friendId"]) ."');background-size: 100% 100%;\" >
				<div class='fsmallnamehold' ><input name='friend_checkbox' id='".$row['friendId']."'type='checkbox' />
				<span onclick=\"get_friend_detail('". $row["friendId"] ."','page2')\" >". $row["FriendFirstName"] ."</span>
				</div>
			</div>";
			}
		}
	}
	
	$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' and FriendFirstName like '".$data[0]."%' ORDER BY FriendFirstName ASC ";
	$result = $mysql->query($query);
	if($result->num_rows > 0){
		$_SESSION["dir_com"]["show_get_started_widget"] = false;
		$fid = 0;
		while($row = $result->fetch_assoc()){
			if($row["ViewableRow"] != "0"){
			$query2 =  "SELECT * FROM source_import WHERE userId = '".$row['friendId']."'";
			$res2 = $mysql->query($query2);
			$raw = $res2->fetch_assoc();
			if($row["FriendMiddleName"] != NULL){
				$fmn = " ".$row["FriendMiddleName"]." ";
				$mn = " ".substr($row["FriendMiddleName"], 0, 1).". ";
			}
			else{
				$mn = " ";
				$fmn = " ";
			}
			$firstLetterName = substr(ucfirst($row["FriendFirstName"]), 0, 1);
			if ($firstLetterName != $letter) {
				$letter = $firstLetterName;
				echo '<span class="AlphabetLetter" style="color: #227eb8;" >' . $letter . '</span><hr class="alpha_hr" /><a name="' . $letter . '"></a>';
			}
			
			
echo 	"<a name='". $row["friendId"] ."'></a>
			<div id=\"". $row["friendId"] ."\" class=\"friendsel_pic\" style=\"cursor: pointer;background: url('". DispFreProfilePic($row["friendId"]) ."');background-size: 100% 100%;\" >
				<div class='fsmallnamehold' ><input name='friend_checkbox' id='".$row['friendId']."'type='checkbox' />
				<span onclick=\"get_friend_detail('". $row["friendId"] ."','page2')\" >". $row["FriendFirstName"] ."</span>
				</div>
			</div>";
			}
		}
	}
	else{
		$_SESSION["dir_com"]["show_get_started_widget"] = true;
		echo "You do not have any friend names that start with this letter.";
	}
	$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' and FriendFirstName regexp '^[".$data[1]."]' ORDER BY FriendFirstName ASC  limit 0, ".$data[2];
	$result = $mysql->query($query);
	if($result->num_rows > 0){
		$_SESSION["dir_com"]["show_get_started_widget"] = false;
		$fid = 0;
		while($row = $result->fetch_assoc()){
			if($row["ViewableRow"] != "0"){
			$query2 =  "SELECT * FROM source_import WHERE userId = '".$row['friendId']."'";
			$res2 = $mysql->query($query2);
			$raw = $res2->fetch_assoc();
			if($row["FriendMiddleName"] != NULL){
				$fmn = " ".$row["FriendMiddleName"]." ";
				$mn = " ".substr($row["FriendMiddleName"], 0, 1).". ";
			}
			else{
				$mn = " ";
				$fmn = " ";
			}
			$firstLetterName = substr(ucfirst($row["FriendFirstName"]), 0, 1);
			if ($firstLetterName != $letter) {
				$letter = $firstLetterName;
				echo '<span class="AlphabetLetter" style="color: #227eb8;" >' . $letter . '</span><hr class="alpha_hr" /><a name="' . $letter . '"></a>';
			}
			
			
echo 	"<a name='". $row["friendId"] ."'></a>
			<div id=\"". $row["friendId"] ."\" class=\"friendsel_pic\" style=\"cursor: pointer;background: url('". DispFreProfilePic($row["friendId"]) ."');background-size: 100% 100%;\" >
				<div class='fsmallnamehold' ><input name='friend_checkbox' id='".$row['friendId']."'type='checkbox' />
				<span onclick=\"get_friend_detail('". $row["friendId"] ."','page2')\" >". $row["FriendFirstName"] ."</span>
				</div>
			</div>";
			}
		}
	}
	else{
		$_SESSION["dir_com"]["show_get_started_widget"] = true;
		echo "You do not have any friend names that start with this letter.";
	}
}
else{
	die("Unexpected error.");
}
echo "</div>";
echo "<script>fsscroll('". $_SESSION["lfid"] ."')</script>";
?>