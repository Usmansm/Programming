<?php
session_start();

//var_dump($_SESSION);
include ('../../config/config.php');
include ('../../includes/DisplayAvatar.php');
$friend_status_word = "unverified";
$act = $_GET["act"];
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

function display_user_categories($fid) {
	global $mysql;
	$lq = "SELECT * FROM user_friend_cat WHERE userId='" . $_SESSION["userId"] . "' AND friendId='" . $fid . "'";
	$res = $mysql -> query($lq) or die($mysql -> error);
	while ($raw = $res -> fetch_array()) {
		$nq = "SELECT * FROM user_categories WHERE catId='" . $raw["catId"] . "'";
		$nres = $mysql -> query($nq);
		$row = $nres -> fetch_assoc();
		echo ucfirst($row["catName"]) . "<br />";
	}
}

function sourceiconurl($src, $uid, $exinf) {
	$sources = explode(",", $src);
	foreach ($sources as $k => $v) {
		if ($v == "facebook") {
			$srci = $srci . "<a href='http://facebook.com/" . $uid . "' target='_blank' class='a_noshow' ><img src='images/facebook.png' style='width: 15px;height: 15px;' /></a>";
		} else if ($v == "linkedin") {
			$srci = $srci . "<a href='" . $exinf . "' target='_blank' class='a_noshow' ><img src='images/linkedin.png' style='width: 15px;height: 15px;' /></a>";
		}
	}
	return $srci;
}



if ($_SESSION["userId"] != "") {
$_SESSION['scroll']=1;
	if(isset($_GET['scroll'])){
		$_SESSION['scroll']=2;
		}

	$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]) or die('Connecting error');
	if (mysqli_connect_errno()) {
		die("Connect failed: \n" . mysqli_connect_error());
	}
	//Ching2 temp
	if($act != "cat"){
	$query = "SELECT * FROM user_categories WHERE userId = '" . $_SESSION['userId'] . "'";
	$result = $mysql -> query($query);
	$cats = array();
	while ($row = $result -> fetch_assoc()) {
		$catId = $row['catId'];
		$cats[$catId] = $row['catName'];
	}
	$catFb = array();
	$catLi = array();
		$query = "SELECT * FROM user_friend_detail WHERE userId = '" . $_SESSION['userId'] . "' ORDER BY FriendFirstName ASC, FriendLastName ASC limit ".$_GET['firstLimit'].",25";
$result = $mysql -> query($query);
	if ($result -> num_rows > 0) {
		$_SESSION["dir_com"]["show_get_started_widget"] = false;
		$fid = 0;
		while ($row = $result -> fetch_assoc()) {
			if ($row['ViewableRow'] != '0') {
				$query2 = "SELECT * FROM source_import WHERE userId = '" . $row['friendId'] . "'";
				$res2 = $mysql -> query($query2);
				$raw = $res2 -> fetch_assoc();
				$userCats = explode(',', $row['FriendCategory']);
				$catsEcho = '';
				$cats2 = explode(',', $row['importSources']);
				foreach ($cats2 as $cat2) {
					if (!empty($cat2)) {
						$catsEcho .= $cat2 . '<br>';
					}
				}
				foreach ($userCats as $userCat) {
					if (!empty($userCat)) {
						$catsEcho .= $cats[$userCat] . '<br>';
					}
				}
				/* $result5 = $mysqli->query('SELECT * FROM user_friend_cat  WHERE userId="'.$_SESSION["userId"].'" AND friendId="'.$row["friendId"].'"');
				 while($rliuerhferiuhfqeriufheriuherp;ihqer;phqprhow4 = $result5->fetch_array()){
				 //$catsEcho .= $row4['catName']."<br>";
				 }*/

				if ($row["FriendMiddleName"] != NULL) {
					$fmn = " " . $row["FriendMiddleName"] . " ";
					$mn = " " . substr($row["FriendMiddleName"], 0, 1) . ". ";
				} else {
					$mn = " ";
					$fmn = " ";
				}
				$firstLetterName = substr(ucfirst($row["FriendFirstName"]), 0, 1);
				//var_dump($firstLetterName);
				if ($firstLetterName != $letter && $_SESSION['scroll']==1) {
					$letter = $firstLetterName;
					echo '<a name="' . $letter . '"></a><span style="color: #227eb8;" >' . $letter . '</span><hr class="alpha_hr" />';
				}
				if($_SESSION['scroll']!=1){
					$letter = $firstLetterName;
				}

				if (!in_array($row["friendId"], $_SESSION["incatusers"]) || $_SESSION["incatusers"] == "") {
					if ($row["FriendStatusCode"] != $friend_status_word) {
						//Normal user box

						echo "<div class=\"friendsel_l2\" >
                    <div id=\"" . $row["friendId"] . "\" class=\"friendsel_pic\" style=\"background: url('" .DispFreProfilePic($row['friendId'])."');background-size: 100% 100%;\" >
                    <input name='friend_checkbox' id='" . $row['friendId'] . "' type='checkbox' />
                    </div>
                        <div class=\"friendsel_name\" >
                        <div class=\"friendsel_namespan\" >
                        <span onclick=\"get_friend_detail('" . $row["friendId"] . "','page2');\" title=\"" . $row["FriendFirstName"] . $fmn . $row["FriendLastName"] . "\" class='fls'>
                        " . $row["FriendFirstName"] . $mn . $row["FriendLastName"] . "
                        </span>
                        <br />
                        ";
						sourceicons($row["friendId"]);
						echo "
                </div>
                    <div class='friendsel_minf' ><b>Activity:</b><br />News posts: ";
                                                 integrationactivity("news", $row["friendId"]);	 
                                                 echo "<br />Social posts: ";	 
                                                 integrationactivity("posts", $row["friendId"]);	 
                                                 echo "</div><div class='friendsel_tags' >
                    <b>Categories: </b>
                <br/>
                ";
						display_user_categories($row["friendId"]);
						echo "
</div>
</div>
        </div>";
					} else {
						//Unverified user box
						/* " . DispFreProfilePic($raw["sourceName"], $raw["sourceUid"], $raw["sourceProfilePicture"]) . " */
						echo "<div style='opacity: 0.5;' ><div class=\"friendsel_l2\" ><div id=\"" . $row["friendId"] . "\" class=\"friendsel_pic\" style=\"background: url('" . DispFreProfilePic($row['friendId']) . "');background-size: 100% 100%;\" ></div><div class=\"friendsel_name\" ><div class=\"friendsel_namespan\" ><span onclick='mod_gr()' title=\"" . $row["FriendFirstName"] . $fmn . $row["FriendLastName"] . "\" class='fls' >" . $row["FriendFirstName"] . $mn . $row["FriendLastName"] . "</span><br />";
						echo "</div><div class='friendsel_minf' ><b>Activity:</b><br />News posts: ";
                                                 integrationactivity("news", $row["friendId"]);	 
                                                 echo "<br />Social posts: ";	 
                                                 integrationactivity("posts", $row["friendId"]);	 
                                                 echo "</div><div class='friendsel_tags' ><b>Categories: </b><br></div></div></div></div>";
					}
					$fid++;

				}
				//<label for=\"". $fid ."_friend_checkbox\" ><img src=\"images/friend_checkbox.png\" id=\"". $fid ."_friend_checkboximg\" onclick=\"toggle_friend_checkboximg(". $fid .")\" class=\"friend_checbox_img\" /></label>
			}
		}
	} else {
		$_SESSION["dir_com"]["show_get_started_widget"] = true;
		//echo "You do not have any friend names that start with this letter.";
	}
	}
else{
	//if it is cat view
	$fincat = array();
	$query = "SELECT * FROM user_categories WHERE userId = '" . $_SESSION['userId'] . "'";
	$result = $mysql -> query($query);
	$cats = array();
	while ($row = $result -> fetch_assoc()) {
		$catId = $row['catId'];
		$cats[$catId] = $row['catName'];
	}
	$catFb = array();
	$catLi = array();
	$query = "SELECT * FROM user_friend_cat,user_detail_public WHERE user_friend_cat.userId = '" . $_SESSION['userId'] . "' AND catId='". $_GET["catid"] ."' AND user_detail_public.userId=user_friend_cat.friendId ORDER BY firstName  limit ".$_GET['firstLimit'].",25";
	//echo $query;
	$result = $mysql -> query($query);
	if ($result -> num_rows > 0) {
		$_SESSION["dir_com"]["show_get_started_widget"] = false;
		$fid = 0;
		while ($row2 = $result -> fetch_assoc()) {
			if ($row['ViewableRow'] != '0') {
				$query2 = "SELECT * FROM user_friend_detail WHERE userId = '" . $_SESSION['userId'] . "' AND friendId='". $row2["friendId"] ."'";
				array_push($fincat, $row2["friendId"]);
				$result2 = $mysql -> query($query2);
				$row = $result2->fetch_assoc();
				$query2 = "SELECT * FROM source_import WHERE userId = '" . $row['friendId'] . "'";
				$res2 = $mysql -> query($query2);
				$raw = $res2 -> fetch_assoc();
				$userCats = explode(',', $row['FriendCategory']);
				$catsEcho = '';
				$cats2 = explode(',', $row['importSources']);
				foreach ($cats2 as $cat2) {
					if (!empty($cat2)) {
						$catsEcho .= $cat2 . '<br>';
					}
				}
				foreach ($userCats as $userCat) {
					if (!empty($userCat)) {
						$catsEcho .= $cats[$userCat] . '<br>';
					}
				}
				/* $result5 = $mysqli->query('SELECT * FROM user_friend_cat  WHERE userId="'.$_SESSION["userId"].'" AND friendId="'.$row["friendId"].'"');
				 while($rliuerhferiuhfqeriufheriuherp;ihqer;phqprhow4 = $result5->fetch_array()){
				 //$catsEcho .= $row4['catName']."<br>";
				 }*/

				if ($row["FriendMiddleName"] != NULL) {
					$fmn = " " . $row["FriendMiddleName"] . " ";
					$mn = " " . substr($row["FriendMiddleName"], 0, 1) . ". ";
				} else {
					$mn = " ";
					$fmn = " ";
				}
				$firstLetterName = substr(ucfirst($row["FriendFirstName"]), 0, 1);
				//var_dump($firstLetterName);
				if ($firstLetterName != $letter) {
					$letter = $firstLetterName;
					echo '<div id="' . $letter . '" style="color: #227eb8;">' . $letter . '</div><hr class="alpha_hr" />';
					//echo '<a name="' . $letter . '"></a><span style="color: #227eb8;" >' . $letter . '</span><hr class="alpha_hr" />';
				}

				if (!in_array($row["friendId"], $_SESSION["incatusers"]) || $_SESSION["incatusers"] == "") {
					if ($row["FriendStatusCode"] != $friend_status_word) {
						//Normal user box
						/* " . DispFreProfilePic($raw["sourceName"], $raw["sourceUid"], $raw["sourceProfilePicture"]) . " */
						echo "<div class=\"friendsel_l2\" >
                    <div id=\"" . $row["friendId"] . "\" class=\"friendsel_pic1\" style=\"background: url('" . DispFreProfilePic($row['friendId']) . " ');background-size: 100% 100%;\" >
                     <input name='friend_checkbox' id='" . $row['friendId'] . "' type='checkbox' />
                </div>
                        <div class=\"friendsel_name\" >
                        <div class=\"friendsel_namespan\" >
                        <span onclick=\"get_friend_detail('" . $row["friendId"] . "','page2');\" title=\"" . $row["FriendFirstName"] . $fmn . $row["FriendLastName"] . "\" class='fls'>
                        " . $row["FriendFirstName"] . $mn . $row["FriendLastName"] . "
                        </span>
                        <br />
                        ";
						sourceicons($row["friendId"]);
						echo "
                </div>
                    <div class='friendsel_minf' ><b>Activity:</b><br />News posts: ";
                                                 integrationactivity("news", $row["friendId"]);	 
                                                 echo "<br />Social posts: ";	 
                                                 integrationactivity("posts", $row["friendId"]);	 
                                                 echo "</div><div class='friendsel_tags' >
                    <b>Categories: </b>
                <br/>
                ";
						display_user_categories($row["friendId"]);
						echo "
</div>
</div>
        </div>";
					} else {
						//Unverified user box
						/* " . DispFreProfilePic($raw["sourceName"], $raw["sourceUid"], $raw["sourceProfilePicture"]) . " */
						echo "<div style='opacity: 0.5;' ><div class=\"friendsel_l2\" ><div id=\"" . $row["friendId"] . "\" class=\"friendsel_pic\" style=\"background: url('" . DispFreProfilePic($row['friendId']) . "');background-size: 100% 100%;\" ></div><div class=\"friendsel_name\" ><div class=\"friendsel_namespan\" ><span onclick='mod_gr()' title=\"" . $row["FriendFirstName"] . $fmn . $row["FriendLastName"] . "\" class='fls' >" . $row["FriendFirstName"] . $mn . $row["FriendLastName"] . "</span><br />";
						sourceicons($row["friendId"]);
						echo "</div><div class='friendsel_minf' ><b>Activity:</b><br />News posts: ";
                                                 integrationactivity("news", $row["friendId"]);	 
                                                 echo "<br />Social posts: ";	 
                                                 integrationactivity("posts", $row["friendId"]);	 
                                                 echo "</div><div class='friendsel_tags' ><b>Categories: </b><br></div></div></div></div>";
					}
					$fid++;

				}
				//<label for=\"". $fid ."_friend_checkbox\" ><img src=\"images/friend_checkbox.png\" id=\"". $fid ."_friend_checkboximg\" onclick=\"toggle_friend_checkboximg(". $fid .")\" class=\"friend_checbox_img\" /></label>
			}
		}
	}
	
}
	//Ching temp
} else {
	die("Unexpected error.");
}
?>
