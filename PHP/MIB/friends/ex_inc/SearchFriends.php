<?php


    //startup.
    error_reporting(E_ERROR | E_PARSE);
    if(@!$_SESSION){
      session_start();
    }
    require('../../config/config.php');
	$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]) or die('Connecting error');
	
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
	
	
	
    
    //error preventing.
    //if(!isset($_GET['key'])) die('no key');
   // if($_GET['key'] == "") die('Empty string'); 
    
    $mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]); 
    
    if(mysqli_connect_errno()) {
    die("Connect failed: \n".mysqli_connect_error());
    }
    function getsociallink($fid,$src){
	global $mysql;
	if($src == "facebook"){
		$cc = "SELECT * FROM source_import WHERE userId='". $fid ."' AND sourceName='facebook'";
		$cr = $mysql->query($cc);
		$shniggy = $cr->fetch_assoc();
		return "http://facebook.com/".$shniggy["sourceUid"];
	}
	if($src == "linkedin"){
		$cc = "SELECT * FROM source_import WHERE sourceId='". $fid ."' AND sourceName='linkedin'";
		$cr = $mysql->query($cc) OR die($mysql->error());
		$shniggy = $cr->fetch_assoc();
		//print_r($expression)
		return $shniggy["sourceProfileLink"];
	}
}
function DispFreProfilePic($sourceName, $sourceUid, $sourceProfilePicture) {

	if ($sourceName == "facebook") {
		$profilepicurl = "https://graph.facebook.com/" . $sourceUid . "/picture?type=large";
		return $profilepicurl;
	} elseif ($sourceName == "linkedin") {
		if ($sourceProfilePicture == '') {
			$profilepicurl = "images/noimage.jpg";
			return $profilepicurl;
		}//When Sams end complete:
		$profilepicurl = $sourceProfilePicture;

		return $profilepicurl;
	} else {
		$profilepicurl = "images/noimage.jpg";
		return $profilepicurl;
	}

}
function sourceicons($fid) {
	global $mysql;
	$tq = "SELECT * FROM userfrnd_source WHERE userId='" . $_SESSION["userId"] . "' AND friendId='" . $fid . "'";
	$tr = $mysql -> query($tq);
	while ($data = $tr -> fetch_assoc()) {
		if ($data["sourceType"] == "") {
			$aq = "SELECT * FROM source_import WHERE sourceId='" . $data["source_import_Id"] . "'";
			$ar = $mysql -> query($aq);
			while ($dat = $ar -> fetch_assoc()) {
				if ($dat["sourceName"] == "facebook") {
					echo "<a href='". getsociallink($dat["userId"],"facebook") ."' target='_blank' class='a_noshow' ><img src='images/facebook.png' style='width: 15px; height: 15px;' /></a> ";
				}
				if ($dat["sourceName"] == "linkedin") {
					echo "<a href='". getsociallink($data["source_import_Id"],"linkedin") ."' target='_blank' class='a_noshow' ><img src='images/linkedin.png' style='width: 15px; height: 15px;' /></a> ";
				}
			}
		} else if ($data["sourceType"] == "source_import_cs") {
			$aq = "SELECT * FROM source_import_cs WHERE sourceId='" . $data["source_import_Id"] . "'";
			$ar = $mysql -> query($aq);
			while ($dat = $ar -> fetch_assoc()) {
				echo "<a href='' target='_blank' class='a_noshow' ><img src='images/" . $dat["sourceName"] . ".png' style='width: 15px; height: 15px;' /></a> ";
			}
		} else if ($data["sourceType"] == "source_import_sf") {
			echo "<a href='' target='_blank' class='a_noshow' ><img src='images/salesforce.png' style='width: 15px; height: 15px;' /></a> ";
		}
	}

}
	if($_GET["key"] == ""){
	
       include 'frnd_list_l.php';
    }
	
	else if($_GET["key"] != ""){
	
     
    //splitting the input into words.
    $key = explode(" ", $_GET['key']); // CREATE ARRAY FORM GET METHOD
    
    $key = array_filter($key); // REMOVEING EMPTY PROPETIES OF ARRAY
    
    $key  = array_unique($key); // REMOVE SAME INSTANCES FORM ARRAY
    
    // first we need to bild a query 
    $query = "SELECT * FROM user_friend_detail WHERE ViewableRow='' AND userId ='".$_SESSION['userId']."' AND (FriendFirstName LIKE '".$key['0']."%' OR FriendLastName LIKE '".$key['0']."%' OR FriendMiddleName LIKE '".$key['0']."%')";
    // if there is more words in $key then we need to add more statements in array
    for($a=1; $a<=count($key); $a++){
        $query = $query .  " AND (FriendFirstName LIKE '".$key[$a]."%' OR FriendLastName LIKE '".$key[$a]."%' OR FriendMiddleName LIKE '".$key[$a]."%')"; 
    }
    // finishing tthe query whit order BY 
    $query = $query . " ORDER BY FriendFirstName ASC, FriendLastName ASC";
    
    //procesing the array
    $result = $mysql->query($query);
    // if there are result display them if there is no result create do the sam but now create arrate whit '%KEY%'
    if($result->num_rows > 0){
        while ($resultArray = $result->fetch_assoc()){
            //  we ned data form source impor for dispalying images incos ect 
            $query2 =  "SELECT * FROM source_import WHERE userId = '".$resultArray['friendId']."'";
            $result2 = $mysql->query($query2);
            $raw = $result2->fetch_assoc();
           
			foreach($userCats as $userCat){
				if(!empty($userCat)){
					$catsEcho .= $cats[$userCat].'<br>';
				}
			}
            // display the data
             $sourceicon =  sourceiconurlSecond($raw["sourceName"], $raw["sourceUid"],$raw["sourceProfileLink"]);
            //$replaceing = '<u>'.$key['0'].'</u>'
            //str_replace($key['0'],, $resultArray['FriendFirstName']);
            //str_replace($key['0'], '<u>'.$key['0'].'</u>', $resultArray['FriendMiddleName']);
            //tr_replace($key['0'], '<u>'.$key['0'].'</u>', $resultArray['FriendLastName']);
            ?>
           <div class="friendsel_l2" oncontextmenu="show_cmenu();return false;">
              		<div id="763ll" class="friendsel_pic" style="background: url('<?php echo DispFreProfilePic($raw["sourceName"], $raw["sourceUid"], $raw["sourceProfilePicture"]); ?>');background-size: 100% 100%;">
          				<input id="<?php echo $resultArray["friendId"]; ?>" type="checkbox" name="friend_checkbox"></input>
          			</div>
          			<div class="friendsel_name">
          				<div class="friendsel_namespan">
          					<span class="fls" title="<?php print($resultArray['FriendFirstName'] . ' ' . $resultArray['FriendMiddleName'] . ' ' . $resultArray['FriendLastName']); ?>" onclick="get_friend_detail('<?php echo $resultArray['friendId']; ?>');"><?php print($resultArray['FriendFirstName'] . ' ' . $resultArray['FriendMiddleName'] . ' ' . $resultArray['FriendLastName']); ?></span>
                              <br />
                               <?php sourceicons($resultArray["friendId"]); ?>
           				</div>
          				<div class="friendsel_minf">
          				<b>Activity:</b><br />
          				News stories: <?php integrationactivity("news", $resultArray["friendId"]); ?><br />
          				Social posts: <?php integrationactivity("posts", $resultsArray["friendId"]); ?>
          				</div>
          				<div class="friendsel_tags">
          					<b>Categories</b>
							<br/>
								<?php echo display_user_categories($resultArray["friendId"]); ?>
          					
          				</div>
          			</div>
          		</div>
            <?php
			}
			} else {
			// if there is no result try different array whit "%KEY% and do all the same like before
			$query = "SELECT * FROM user_friend_detail WHERE userId ='".$_SESSION['userId']."' AND (FriendFirstName LIKE '%".$key['0']."%' OR FriendLastName LIKE '%".$key['0']."%' OR FriendMiddleName LIKE '%".$key['0']."%')";

			for($a=1; $a<=count($key); $a++){
			$query = $query .  " AND (FriendFirstName LIKE '%".$key[$a]."%' OR FriendLastName LIKE '%".$key[$a]."%' OR FriendMiddleName LIKE '%".$key[$a]."%')";
			}
			$query = $query . " ORDER BY FriendFirstName ASC, FriendLastName ASC";
			$result = $mysql->query($query);

			while ($resultArray = $result->fetch_assoc()){
			$query2 =  "SELECT * FROM source_import WHERE userId = '".$resultArray['friendId']."'";
			$result2 = $mysql->query($query2);
			$raw = $result2->fetch_assoc();
			$sourceicon =  sourceiconurlSecond($raw["sourceName"], $raw["sourceUid"],$raw["sourceProfileLink"]);

			$firstLetterName = substr(ucfirst($resultArray["FriendFirstName"]), 0, 1);

			if($firstLetterName != $letter){
			$letter = $firstLetterName;
			echo '<a name="'.$letter.'"></a><span class="AlphabetLetter" style="color: #227eb8;" >'.$letter.'</span><hr class="alpha_hr" />';
			}
            ?> 
            
           <div class="friendsel_l2" oncontextmenu="show_cmenu();return false;">
                  	<div id="763lll" class="friendsel_pic" style="background: url('<?php DispFreProfilePic($raw["sourceName"], $raw["sourceUid"], $raw["sourceProfilePicture"]); ?>');background-size: 100% 100%;">
          				<input id="<?php echo $resultArray["friendId"]; ?>" type="checkbox" name="friend_checkbox"></input>
          			</div>
          			<div class="friendsel_name">
          				<div class="friendsel_namespan">
          					<span class="fls" title="<?php print($resultArray['FriendFirstName'] . ' ' . $resultArray['FriendMiddleName'] . ' ' . $resultArray['FriendLastName']); ?>" onclick="get_friend_detail('<?php echo $resultArray['friendId']; ?>');"><?php print($resultArray['FriendFirstName'] . ' ' . $resultArray['FriendMiddleName'] . ' ' . $resultArray['FriendLastName']); ?></span>
                              <br>
                             <?php sourceicons($resultArray["friendId"]); ?> 
          				</div>
          				<div class="friendsel_minf">
          				Mail
          				</div>
          				<div class="friendsel_tags">
          					<b>Categories</b>
							<br/>
							<?php echo display_user_categories($resultArray["friendId"]); ?>
          					
          				</div>
          			</div>
          		</div>
            <?php
			}
			}
}
			function profilepicurlSecond($src,$uid,$exinf){
			if($sourceName == "facebook"){
			$profilepicurl = "https://graph.facebook.com/". $sourceUid ."/picture?type=large";
			return $profilepicurl;
			}
			elseif($sourceName== "linkedin"){
			if ($sourceProfilePicture == ''){
			$profilepicurl = "images/noimage.png";
			return $profilepicurl;
			}//When Sams end complete:
			$profilepicurl = $sourceProfilePicture;

			return $profilepicurl;
			}else{
			$profilepicurl = "images/noimage.png";
			return $profilepicurl;
			}

			}

			function sourceiconurlSecond($src,$uid,$exinf){
			$sources = explode(",",$src);
			foreach($sources as $k => $v){
			if($v == "facebook"){
			$srci = $srci."<a href='". $config['FBlink'] . $uid ."' target='_blank' class='a_noshow' ><img src='images/facebook.png' style='width: 15px;height: 15px;' /></a>";
			}
			else if($v == "linkedin"){
			$srci = $srci."<a href='". $exinf ."' target='_blank' class='a_noshow' ><img src='images/linkedin.png' style='width: 15px;height: 15px;' /></a>";
			}
			}
			return $srci;
			}
		
		?>
