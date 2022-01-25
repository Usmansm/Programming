<?php
if(@! $_SESSION){
	session_start();
	include "../../config/config.php";
}
class categories {
 
	public function addCategory($catName, $catDescription){
		global $config;
        $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		if($catName != ''){
			$result = $mysqli->query('INSERT INTO user_categories (userId, catName, catDescription) VALUES ("'.$_SESSION["userId"].'", "'.$catName.'", "'.$catDescription.'")') or die ($mysqli->error);          
		}
	}
	// function for adding to category
	public function addToCat($catId, $friends){
		global $config; // DB confing
        $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']); // DB connection
		$friends = explode(',', $friends); // eexplide form Modal (Javascript)
		$AddedcatId = array(); // this arry is used for lated in everone section
		$AddedFrID = array();// this arry is used for lated in everone section
		if($catId != ''){ // if catefory id is selected
			foreach($friends as $friend){ // for each  freidn select and check if he apready exist in tah catefgory
				$result = $mysqli->query("SELECT * FROM user_friend_cat WHERE catId = '". $catId ."' AND  userId = '". $_SESSION["userId"] ."' AND friendId  =  '". $friend ."'");
				$check = $result->num_rows;
				if ($check == 0 ){  // if  there is not freind in that category add him to database
					$result = $mysqli->query('INSERT INTO user_friend_cat (catId, userId, friendId) VALUES ("'.$catId.'", "'.$_SESSION["userId"].'", "'.$friend.'")') or die ($mysqli->error);
						// add freindID  and CATegory ID  inot the arrays 
						array_push($AddedcatId, $catId);
						array_push($AddedFrID, $friend);
				}
				 
			}          
		}
		print_r($AddedFrID);
		// EVERNOTE SECTION  OF ADDTO CAT FUNCTION
		// check all evernote connec tions 
		$query = "SELECT * FROM user_external_accnt WHERE userId='" . $_SESSION["userId"] . "' AND authProvider='evernote'";
		$result = $mysqli -> query($query);
		$data = $result -> fetch_assoc();
		// if user is connected whit evernot 
		if ($data["id"] != "") {

			foreach ($AddedcatId as $cat) {
				// fore each freind wich is added to category add him to evernote (Corey wrote this part so I do not know what is going on here corey add coments when you see this if you see this ever DENI)
				
				$checkquery = "SELECT * FROM evn_notes_cat WHERE catId='". $cat ."'";
				$checkres = $mysqli->query($checkquery);
				
				while($data = $checkres->fetch_assoc()){
					$i = 0 ;
					foreach ($AddedFrID as $FRID[$i]){
						$noteq = "SELECT * FROM evn_note_detail WHERE evnNoteGuid='". $data["evnNoteGuid"] ."'";
						$rres = $mysqli->query($noteq);
						$dat = $rres->fetch_assoc();
						
						/*
							$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' AND friendId IN ($friends) ORDER BY FriendFirstName ASC, FriendLastName ASC";
							$result = $mysql->query($query) or die ($mysql->error);
							if($result->num_rows > 0
						*/
						
						$CheckQ = "SELECT * FROM user_frnd_evernote WHERE userId = '".$_SESSION['userId']."' AND friendId = '".$FRID[$i]."' AND evnNoteGuid = '".$data['evnNoteGuid']."' AND evnNoteCreatedate = '".$dat['evnNoteCreatedate']."'";
						$CheckQRres = $mysqli->query($CheckQ);
						if ($CheckQRres->num_rows == 0){
						
						
							
							$nn = "INSERT INTO user_frnd_evernote(userId,friendId,evnNoteGuid,evnNoteCreatedate) VALUES('". $_SESSION['userId'] ."','". $FRID[$i]."','". $data['evnNoteGuid'] ."','". $dat['evnNoteCreatedate'] ."')";
							$i++;
							$rres2 = $mysqli->query($nn);
						
						}
					}
				}
			}
			
		}
		
	}
	
	
	public function deleteFromCat ($catId, $friends){
		global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		//Echo 'initiated';
		//echo 'initiateEEEEEEEEEEEEEEEEd';
		//var_dump($friends);

			$friendsIDs = explode(',', $friends);
			
			foreach($friendsIDs as $friend){
				$result = $mysqli->query('SELECT * FROM user_friend_cat WHERE catId = "'.$catId.'" AND userId = "'.$_SESSION["userId"].'" AND friendId = "'.$friend.'"') or die ($mysqli->error);
					$check = $result->num_rows;
					if($check > 0){
						//echo "we are adsdASdasasdasdasd";
						$result2 = $mysqli->query('DELETE FROM user_friend_cat WHERE catId = "'.$catId.'" AND userId = "'.$_SESSION["userId"].'" AND friendId = "'.$friend.'"') or die ($mysqli->error);
					}
			}
			
		}
	

      
 
    public function getAllCategories(){
        global $config;
        $source = array();
        $source['facebook'] = 0; 
        $source['linkedin'] = 0; 
        $source['salesforce'] = 0;
        $source['gmail'] = 0; 
        $source['outlook'] = 0; 
        $source['windowslive'] = 0; 
        $source['yahoo'] = 0; 
        $source['aol'] = 0; 
        $source['plaxo'] = 0; 
        $source['addressbook'] = 0;
        $nmrFriends = 0;
        $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db'])  or die ($mysqli->error);
        $results = $mysqli->query('SELECT * FROM userfrnd_source WHERE userId = "'.$_SESSION["userId"].'"') or die ($mysqli->error);
        
        echo '<div id="CatHold">';
        
        while($row = $results->fetch_array()){
            if($row["sourceType"] == ''){
                $result = $mysqli->query('SELECT * FROM source_import WHERE sourceId = "'.$row["source_import_Id"].'"') or die ($mysqli->error);
                $row = $result->fetch_array();
                if($row['sourceName'] == 'facebook'){
                    $source['facebook'] ++;
                }
                if($row['sourceName'] == 'linkedin'){
                    $source['linkedin'] ++;
                }
            }
            else {
                $result = $mysqli->query('SELECT * FROM '.$row["sourceType"].' WHERE sourceId = "'.$row["source_import_Id"].'"') or die ($mysqli->error);
                $row = $result->fetch_array();
                if($row['sourceName'] == 'gmail'){
                    $source['gmail'] ++;
                }
                if($row['sourceName'] == 'yahoo'){
                    $source['yahoo'] ++;
                }
                if($row['sourceName'] == 'windowslive'){
                    $source['windowslive'] ++;
                }
                if($row['sourceName'] == 'aol'){
                    $source['aol'] ++;
                }
                if($row['sourceName'] == 'plaxo'){
                    $source['plaxo'] ++;
                }
                if($row['sourceName'] == 'addressbook'){
                    $source['addressbook'] ++;
                }
                if($row['sourceName'] == 'outlook'){
                    $source['outlook'] ++;
                }
                if($row['sourceName'] == 'salesforce'){
                    $source['salesforce'] ++;
                }
            }
        }
        foreach($source as $val){
            if($val > 0){
                $catCount ++;
            }
        }
		
		$fq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND ViewableRow!='0'";
		$fres = $mysqli->query($fq);
		$tfriends = $fres->num_rows;
		echo "<h4 >Total friends: ".$tfriends."</h4>";
        echo '<h4 onclick="Disp_Freind_Sources()">Friend Sources: <span class="catCount">'.$catCount.'</span></h4>';
        echo '<div id="FrSourcesList"  style="display:none" > <p>';
        foreach ($source as $key => $value) {
            if($value > 0){
               echo "<div class='' ><span class='cat' >".ucfirst($key)."</span> - <span class='numfr' >". $value ." friends</span></div>"; 
            }
        }
        echo '</p> </div>';
        
        $results = $mysqli->query('SELECT * FROM user_categories WHERE userId="'.$_SESSION["userId"].'" ORDER BY catName ASC') or die ($mysqli->error);
        $catCount = $results->num_rows;
        
        if($catCount > 0){
        	if($_GET["act"] != "news"){
            echo '<h4 onclick="Disp_News_Categories() "><span id="CategTitle">Friend Categories:</span><span class="catCount">'.$catCount.'</span></h4>';
            echo '<div id=NewsCatList > <p>';
            
            while($row = $results->fetch_array()){
                $nmrFriends = 0;
                $result = $mysqli->query('SELECT * FROM user_friend_cat WHERE catId = "'.$row["catId"].'" ') or die ($mysqli->error);
                while($data = $result->fetch_array()){
                    $nmrFriends ++;
                }
                if($nmrFriends == "0"){
                	$onclick = "onclick=\"displaysmallnotification('This category is empty')\"";
                }
else{
	$onclick = "onclick=\"showCat('".$row['catId']."');makeblue('". $row["catId"] ."cat')\"";
}
                echo "<div class='' name='cspann' id='". $row["catId"] ."cat' ><img id='". $row["catId"] ."-img' onclick=\"togopts('".  $row["catId"]."')\" src='images/+.png'/> <span ". $onclick ." class='cat'>". $row["catName"] ."</span> - <span class='numfr' >". $nmrFriends ." friends</span></div>";
                echo "<span id='". $row["catId"] ."-opts' class='cat_options' style='display: none;'><span style='cursor: pointer;' onclick=\"promptEditCat('". $row["catId"] ."')\" >Edit</span> <span style='cursor: pointer;' onclick=\"promptCloneCat('". $row["catId"] ."')\" >Clone</span> <span style='cursor: pointer;' onclick=\"promptDeleteCat('". $row["catId"] ."','". $row["catName"] ."')\" >Delete</span></span>";
            }
            
            echo '</div>';
            }
else{
	//news cats
            echo '<h4 onclick="Disp_News_Categories() "><span id="CategTitle">News Categories:</span><span class="catCount">'.$catCount.'</span></h4>';
            echo '<div id=NewsCatList  style="display:none" > <p>';
            
            while($row = $results->fetch_array()){
                $nmrFriends = 0;
                $result = $mysqli->query('SELECT * FROM evn_notes_cat WHERE catId = "'.$row["catId"].'" AND userId="'.  $_SESSION["userId"] .'" ') or die ($mysqli->error);
                while($data = $result->fetch_array()){
                    $nmrFriends ++;
                }
				if($nmrFriends == "0"){
					$onclick = "onclick=\"displaysmallnotification('This category doesn\'t have any news stories to display')\"";
				}
				else{
					$onclick = "onclick=\"shownewscat('".$row['catId']."')\"";
				}
                echo "<div class='' ><img id='". $row["catId"] ."-img' onclick=\"togopts('".  $row["catId"]."')\" src='images/+.png'/> <span style=\"cursor: pointer;\" ". $onclick ." class='cat' style='cursor: pointer;'>". $row["catName"] ."</span> - <span class='numfr' >". $nmrFriends ." stories</span></div>";
                echo "<span id='". $row["catId"] ."-opts' class='cat_options' style='display: none;'><span >Edit</span> <span style='cursor: pointer;' >Clone</span> <span style='cursor: pointer;' >Delete</span></span>";
            }
            
            echo '</div>';
}
        }
        echo '</div>';
        if($_GET["act"] != "news"){
        echo '
        <center>
            <input type="button" class="cat_button" value="Add Category" onClick="promptAddCat()" /><div class="cat_right" ></div><br />
            <input type="button" class="cat_button" value="Delete From Category" onClick="promptDeleteFromCat()" /><div class="cat_right" ></div><br />
            <input type="button" class="cat_button" value="Add Friend To Category" onClick="promptAddToCat()" /><div class="cat_right" ></div>
        </center>
        ';
		}
        
    }
    
    public function showCat($catId){
        global $config;
        $mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
        $catFriends = $mysql->query('SELECT * FROM user_friend_cat WHERE userId = "'.$_SESSION["userId"].'" AND catId = "'.$catId.'"');
        $friends = array();
        while($friend = $catFriends->fetch_array()){
            array_push($friends, $friend['friendId']);
        }
        $friends = join(',', $friends);
            $query = "SELECT * FROM user_categories WHERE userId = '".$_SESSION['userId']. "'";
            $result = $mysql->query($query);
        	$cats = array();
        	while($row = $result->fetch_assoc()){
        		$catId = $row['catId'];
        		$cats[$catId] = $row['catName'];	
        	}
        	$catFb = array();
        	$catLi = array();
        	$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' AND friendId IN ($friends) ORDER BY FriendFirstName ASC, FriendLastName ASC";
        	$result = $mysql->query($query) or die ($mysql->error);
        	if($result->num_rows > 0){
        		$_SESSION["dir_com"]["show_get_started_widget"] = false;
        		$fid = 0;
        		while($row = $result->fetch_assoc()){
        			if($row['ViewableRow'] != '0'){
        			$query2 =  "SELECT * FROM source_import WHERE userId = '".$row['friendId']."'";
        			$res2 = $mysql->query($query2);
        			$raw = $res2->fetch_assoc();
        			$userCats = explode(',', $row['FriendCategory']);
        			$catsEcho = '';
        			$cats2 = explode (',', $row['importSources']);
        			foreach($cats2 as $cat2){
        				if(!empty($cat2)){
        					$catsEcho .= $cat2.'<br>';
        				}
        			}
        			foreach($userCats as $userCat){
        				if(!empty($userCat)){
        					$catsEcho .= $cats[$userCat].'<br>';
        				}
        			}
             /* $result5 = $mysqli->query('SELECT * FROM user_friend_cat  WHERE userId="'.$_SESSION["userId"].'" AND friendId="'.$row["friendId"].'"');
              while($row4 = $result5->fetch_array()){
                  //$catsEcho .= $row4['catName']."<br>";
              }*/
        			
        			
        			if($row["FriendMiddleName"] != NULL){
        				$fmn = " ".$row["FriendMiddleName"]." ";
        				$mn = " ".substr($row["FriendMiddleName"], 0, 1).". ";
        			}
        			else{
        				$mn = " ";
        				$fmn = " ";
        			}
              $firstLetterName = substr(ucfirst($row["FriendFirstName"]), 0, 1);
        			//var_dump($firstLetterName);
        			if($firstLetterName != $letter){
        				$letter = $firstLetterName;
            			echo '<a name="'.$letter.'"></a><span class="AlphabetLetter" style="color: #227eb8;" >'.$letter.'</span><hr class="alpha_hr" />';
        			}
        			
        			if(! in_array($row["friendId"], $_SESSION["incatusers"]) || $_SESSION["incatusers"] == ""){
        				if($row["FriendStatusCode"] != $friend_status_word){
        				//Normal user box
        			echo 
             "<div oncontextmenu=\"show_cmenu();return false;\" class=\"friendsel_l2\" >
               <div id=\"". $row["friendId"] ."\" class=\"friendsel_pic\" style=\"background: url('". profilepicurl($raw["sourceName"],$raw["sourceUid"],$raw["sourceProfilePicture"]) ."');background-size: 100% 100%;\" >
                 <input name='friend_checkbox' id='".$row['friendId']."' type='checkbox' />
                </div>
                <div class=\"friendsel_name\" >
                  <div class=\"friendsel_namespan\" >
                    <span onclick=\"get_friend_detail('". $row["friendId"] ."');\" title=\"". $row["FriendFirstName"] . $fmn . $row["FriendLastName"] ."\" class='fls'>
                      ". $row["FriendFirstName"] . $mn . $row["FriendLastName"] ."
                    </span>
                    <br />
                    ". sourceiconurl($raw["sourceName"], $raw["sourceUid"],$raw["sourceProfileLink"]) ."
                    </div>
                    <div class='friendsel_minf' >Mail</div><div class='friendsel_tags' >
                      <b>Categories: </b>
                      <br/>
                      ";
                      display_user_categories($row["friendId"]);
                      echo "
                    </div>
                  </div>
                </div>";
        			}
        			else{
           //Unverified user box
        			echo "<div style='opacity: 0.5;' ><div oncontextmenu=\"show_cmenu();return false;\" class=\"friendsel_l2\" ><div id=\"". $row["friendId"] ."\" class=\"friendsel_pic\" style=\"background: url('". profilepicurl($raw["sourceName"],$raw["sourceUid"],$raw["sourceProfileLink"]) ."');background-size: 100% 100%;\" ></div><div class=\"friendsel_name\" ><div class=\"friendsel_namespan\" ><span onclick=\"get_friend_detail('". $row["id"] ."');\" title=\"". $row["FriendFirstName"] . $fmn . $row["FriendLastName"] ."\" class='fls'>". $row["FriendFirstName"] . $mn . $row["FriendLastName"] ."</span><br />". sourceiconurl($row["importSources"], $raw["sourceUid"],$raw["sourceProfilePicture"]) ."</div><div class='friendsel_minf' >Mail</div><div class='friendsel_tags' ><b>Categories: </b><br>". display_user_categories($row["friendId"]) ."</div></div></div></div>";
        			}
        			$fid++;
        			
        			}
        			//<label for=\"". $fid ."_friend_checkbox\" ><img src=\"images/friend_checkbox.png\" id=\"". $fid ."_friend_checkboximg\" onclick=\"toggle_friend_checkboximg(". $fid .")\" class=\"friend_checbox_img\" /></label>
        			}
        		}
        	}
        }
    }
    
    function profilepicurl($src,$uid,$exinf){
        if($src == "facebook"){
    		$profilepicurl = "https://graph.facebook.com/". $uid ."/picture?type=large";
    		return $profilepicurl;
    	}
    	elseif($src == "linkedin"){
    		//When Sams end complete:
    		$profilepicurl = $exinf;
    		//
    		return $profilepicurl;
    	}
    	else{
    		$profilepicurl = "images/noimage.png";
    		return $profilepicurl;
    	}
    }
    
    function display_user_categories($fid){
      global $config;
      $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
      $lq = "SELECT * FROM user_friend_cat WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fid ."'";
      $res = $mysqli->query($lq);
      while($raw = $res->fetch_assoc()){
        $nq = "SELECT * FROM user_categories WHERE catId='". $raw["catId"] ."'";
        $nres = $mysqli->query($nq);
        $row = $nres->fetch_assoc();
        echo ucfirst($row["catName"])."<br />";
      }
    }
    
    function sourceiconurl($src,$uid,$exinf){
    	$sources = explode(",",$src);
    	foreach($sources as $k => $v){
    		if($v == "facebook"){
    			$srci = $srci."<a href='http://facebook.com/". $uid ."' target='_blank' class='a_noshow' ><img src='images/facebook.png' style='width: 15px;height: 15px;' /></a>";
    		}
    		else if($v == "linkedin"){
    			$srci = $srci."<a href='". $exinf ."' target='_blank' class='a_noshow' ><img src='images/linkedin.png' style='width: 15px;height: 15px;' /></a>";
    		}
    	}
    	return $srci;
    }
	
if($_GET["a"] == "reload"){
	$categories = new categories;
            echo $categories->getAllCategories();
}

?>

