<?php

	include('../config/config.php');
	session_start();
	
		/*FUNCTION FOR DISPLAYING AVATAR IN TOP RIGHT SIDE*/
	function getAvatarLink(){
        global $config;
            $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
            $result = $mysqli->query('SELECT * FROM source_import WHERE userId = "'.$_SESSION["userId"].'"');
            
            
            $sources = array();
             while($row = $result->fetch_array()){
                if($row['sourceProfilePicture'] == '' && $row['sourceName'] == 'facebook'){
                   
                    $link = 'https://graph.facebook.com/'.$row["sourceUid"].'/picture?type=large';
                   
                }
                else {
                    $link = $row['sourceProfilePicture'];
                }
                $sources[$row["sourceName"]] = $link;    
            }
            $sourcesPriority = array('facebook', 'linkedin', 'salesforce');
            $done =  false;
            foreach($sourcesPriority as $sourcePriority){
                if(!$done){
                    if(isset($sources[$sourcePriority])){
                        echo  $sources[$sourcePriority];
                        $done = true;
                }
            }
        }
    }
	/*FUNCTION FOR DISPLAYING PROFILE PISTURE*/
	function DispFreProfilePic($fid) {
		return "test";
	    $profilepicurl = "images/noImagenoImage.jpg";
		global $config;
		$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
		$query22 = "SELECT * FROM userfrnd_source WHERE userId='". $_SESSION["userId"] ."' AND friendId = '" . $fid . "'";
		//echo $query22 ."<br/>";
		$res22 = $mysql -> query($query22);
		//$raww = $res22 -> fetch_assoc();
		$SoucrecsID = '';
		$SourcesTypeArray = array();

		
		while($raww = $res22 -> fetch_assoc()){
			if ($raww["sourceType"] != 'source_import_sf'){
				$SoucrecsID = $SoucrecsID . $raww["source_import_Id"] . "','";
				$SourcesTypeArray[] = $raww["sourceType"];
			}
		}
		//print_r($SourcesTypeArray);
		$SoucrecsID = substr($SoucrecsID, 0 , -3);
		$arrayLenght = count($SourcesTypeArray);
		//echo $arrayLenght. "<br/>";
		
		if ((in_array("source_import_sf", $SourcesTypeArray)) and ($arrayLenght == 1)){
				$profilepicurl = "images/noImagenoImage.jpg";
				return $profilepicurl;
		}else{
		
			$query2 = "SELECT * FROM source_import WHERE sourceId IN('". $SoucrecsID ."')";	
			//echo $query2."<br/>";
			$res2 = $mysql->query($query2);
			
			$SorucesArray = array();
			while( $row = $res2->fetch_assoc()){ 
				$SorucesArray[] = $row['sourceName'];
			}
//print_r($SorucesArray);
			if($st == ""){
				if (in_array("facebook", $SorucesArray)) {															
						$query3 = "SELECT * FROM source_import WHERE sourceId IN('". $SoucrecsID ."') AND sourceName = 'facebook'";
						$result = $mysql->query($query3);
						$raw = $result->fetch_assoc();
						
						$sourceName = $raw["sourceName"];
						$sourceUid = $raw["sourceUid"];
						$sourceProfilePicture = $raw["sourceProfilePicture"];
						
						if($raw["sourceProfilePicture"] == ""){
						$profilepicurl = "https://graph.facebook.com/" . $sourceUid . "/picture?type=large";
						return $profilepicurl;
					}else{
						$profilepicurl = $raw["sourceProfilePicture"];
						return $profilepicurl;
					}
				}elseif(in_array("linkedin", $SorucesArray)){
						$query3 = "SELECT * FROM source_import WHERE sourceId IN('". $SoucrecsID ."') AND sourceName = 'linkedin'";
					
						$result = $mysql->query($query3);
						$raw = $result->fetch_assoc();
						
						$sourceName = $raw["sourceName"];
						$sourceUid = $raw["sourceUid"];
						$sourceProfilePicture = $raw["sourceProfilePicture"];
			
					if($sourceProfilePicture == ''){
						$profilepicurl = "images/noImagenoImage.jpg";
						return $profilepicurl;
					}
					$profilepicurl = $sourceProfilePicture;

					return $profilepicurl;
				}else{
					$profilepicurl = "images/noImagenoImage.jpg";
					return $profilepicurl;
				}
			}else{
				return "images/noImagenoImage.jpg";
		}
	
		}
	}
	/*FUNCTION FOR GETING SOCIAL LINK*/
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
		if($src == "sf"){
			$cc = "SELECT * FROM source_import_sf WHERE sourceId='". $fid ."'";
			$cr = $mysql->query($cc) OR die($mysql->error());
			$shniggy = $cr->fetch_assoc();
			//print_r($expression)
			return $shniggy["sourceProfileLink"];
		}
	}
	
	/*FUNCTION FOR DISPLAYING SOURCE ICONS */
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
						$Sociallink = getsociallink($dat["userId"],"facebook");
						echo "<a href='". $Sociallink."' target='_blank' class='a_noshow' ><img src='images/facebook.png' style='width: 15px; height: 15px;' /></a> ";
					}
					if ($dat["sourceName"] == "linkedin") {
						$Sociallink = getsociallink($data["source_import_Id"],"linkedin");
						echo "<a href='". $Sociallink ."' target='_blank' class='a_noshow' ><img src='images/linkedin.png' style='width: 15px; height: 15px;' /></a> ";
					}
						if ($dat["sourceName"] == "mail_client") {
						//$Sociallink = getsociallink($data["source_import_Id"],"linkedin");
						echo "<a href='javascript:void(0)' target='_blank' class='a_noshow' ><img src='images/CSV.png' style='width: 15px; height: 15px;' /></a> ";
					}
				}
			} else if ($data["sourceType"] == "source_import_cs") {
				$aq = "SELECT * FROM source_import_cs WHERE sourceId='" . $data["source_import_Id"] . "'";
				$ar = $mysql -> query($aq);
				while ($dat = $ar -> fetch_assoc()) {
					echo "<a href='' target='_blank' class='a_noshow' ><img src='images/" . $dat["sourceName"] . ".png' style='width: 15px; height: 15px;' /></a> ";
				}
			} 
			
			else if ($data["sourceType"] == "mail_client") {
				$aq = "SELECT * FROM source_import WHERE sourceName='mail_client' AND 'userId='" . $fid. "'";
				$ar = $mysql -> query($aq);
				while ($dat = $ar -> fetch_assoc()) {
					echo "<a href='' target='_blank' class='a_noshow' ><img src='http://ec2-54-243-154-131.compute-1.amazonaws.com/MIBWORKING/dev/img/logos/CSV.png' style='width: 15px; height: 15px;' /></a> ";
				}
			}
			
			else if ($data["sourceType"] == "source_import_sf") {
				$asf = "SELECT * FROM source_import_sf WHERE userId='" . $fid . "'";
				$ar = $mysql -> query($asf);
				$dat = $ar -> fetch_assoc();
				$Sociallink = getsociallink($data["source_import_Id"], "sf");
				echo "<a href='". $Sociallink ."' target='_blank' class='a_noshow' ><img src='images/salesforce.png' style='width: 15px; height: 15px;' /></a> ";
			}
		}
	}
?>