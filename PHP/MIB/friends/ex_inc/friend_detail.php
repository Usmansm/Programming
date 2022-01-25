
<?php

session_start();
require_once ('../../config/config.php');
include ('../../includes/DisplayAvatar.php');
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
?>

<?php
$_GET["fid"] = strip_tags($_GET["fid"]);
$friendIds = $_GET["fid"];
$_SESSION["lfid"] = $_GET["fid"];

$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
	if(mysqli_connect_errno()) {
		die("Connect failed: \n".mysqli_connect_error());
	}
	$query = "SELECT * FROM user_friend_detail WHERE friendId = '".$_GET["fid"]."' AND userId='". $_SESSION["userId"] ."'";
	$result = $mysql->query($query);
	$row = $result->fetch_assoc();
	$query2 =  "SELECT * FROM source_import WHERE userId = '".$row['friendId']."'";
			$res2 = $mysql->query($query2);
			$raw = $res2->fetch_assoc();
			
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

function listemaildrop(){
	global $mysql;
	$emailquery = "SELECT * FROM user_friend_email WHERE friendId = '". $_GET["fid"] ."' AND userId = '". $_SESSION["userId"] ."' AND emailType='Home'";
    $emailquery1 = "SELECT * FROM user_friend_email WHERE friendId = '". $_GET["fidlistemaildrop"] ."' AND userId = '". $_SESSION["userId"] ."' AND emailType='Office'";
    $emailquery2 = "SELECT * FROM user_friend_email WHERE friendId = '". $_GET["fid"] ."' AND userId = '". $_SESSION["userId"] ."' AND emailType='Other'";

	$emailresult = $mysql->query($emailquery);
    $emailresult1 = $mysql->query($emailquery1);
    $emailresult2 = $mysql->query($emailquery2);

    $emailrow = $emailresult->fetch_assoc();
    $emailrow1 = $emailresult1->fetch_assoc();
    $emailrow2 = $emailresult2->fetch_assoc();

	echo "<select id='email_sel' class='detail_drop' onchange='emailview(this.value)' >\n";
    
    if($emailrow["emailAddr"] != ""){
        echo "<option value='". $emailrow["emailAddr"] ."' >Home</option>\n";
    }
    else{
        echo "<option value='None' id='hmail' >Home</option>\n";
    }
    if($emailrow1["emailAddr"] != ""){
        echo "<option value='". $emailrow1["emailAddr"] ."' >Office</option>\n";
    }
    else{
        echo "<option value='None' >Office</option>\n";
    }
    if($emailrow2["emailAddr"] != ""){
        echo "<option value='". $emailrow2["emailAddr"] ."' >Other</option>\n";
    }
    else{
        echo "<option value='None'  >Other</option>\n";
    }

	echo "</select>";
 
}

function listphonedrop(){
    global $mysql;
    // need to recheck this function
    
}
// This function lemial() we are not useing any more ... 
/*function lemail(){
    global $mysql;
    $emailquery = "SELECT * FROM user_friend_email WHERE friendId = '". $_GET["fid"] ."' AND userId = '". $_SESSION["userId"] ."' AND emailType='Home'";
    $emailresult = $mysql->query($emailquery);
    $emailrow = $emailresult->fetch_assoc();
    
    if($emailrow["emailAddr"] == ""){
        echo "None";
    }
    else{
        echo $emailrow["emailAddr"];
    }
}*/

function getfamily(){
 global $mysql;
	$query = "SELECT * FROM user_friend_family_details WHERE userId = '". $_SESSION["userId"] ."' AND friendId = '".$_GET["fid"]."'";
	$result = $mysql->query($query);
    echo "<div id='fhold' >";
	if($result->num_rows > 0){
      $i = 0;
		while($row = $result->fetch_assoc()){
      $fid = $row['id'];
			$ftype = ucfirst($row["FamilyMember_Type"]);
			$ffname = $row["FamilyMember_FirstName"];
			$flname = $row["FamilyMember_LastName"];
			$fphone = $row["FamilyMember_PhoneCell"];
			$fdob = $row["FamilyMember_BornOn"];
			$femail = $row["FamilyMember_Email"];
      $OnlineLink1 = $row["OnlineLink1"];
	  $templink1=$OnlineLink1;
      $OnlineLink2 = $row["OnlineLink2"];
	  $templink2=$OnlineLink2;
	  $parsed = parse_url($OnlineLink1);
	  $parsed2 = parse_url($OnlineLink2);
	  if(strtolower(empty($parsed['scheme'])) && $OnlineLink1!=''){
	  	 $OnlineLink1 = 'http://' . ltrim($OnlineLink1, '/');
	  }
	  if(strtolower(empty($parsed2['scheme'])) && $OnlineLink2!=''){
	  	$OnlineLink2 = "http://".$OnlineLink2 . "";
	  }
      $fDescription= $row["FamilyMember_Notes"];
   $i++;
   echo '<div class="FamilyHold" > 
    			<div class="part_infol" id="part_infol" style="margin-top: 4px; margin-bottom: 6px;" >
				         <div id="FamilyMemberid'.$i.'" style="display:none">'.$fid.'</div>
                 <div class="part_infotext" ><span class="part_infotextb" ><span id="part_infoFamilyType'.$i.'">'.$ftype.'</span>:</span><span id="part_infoName'.$i.'" style="margin-right: 6px;" >'.$ffname.' '.$flname.'</span></div>
    					   <div class="part_infotext" ><span class="part_infotextb" id="part_infotext" >Phone:</span><span id="part_infoPhone'.$i.'" style="margin-right: 6px;" >'.$fphone.'</span></div>
                 <div class="part_infotext" ><span class="part_infotextb" id="part_infotext" >OnlineLink1:</span><span id="OnlineLink1'.$i.'" style="margin-right: 6px;" ><a href="'. $OnlineLink1 .'" target="_blank" id="aOnlineLink1'.$i.'" style="word-wrap: break-word;">'.$templink1.'</a></span></div>
            </div>
    					 
          <div class="part_infor" style="margin-top: 4px; margin-bottom: 4px;" >
                 <div class="part_infotext" ><span class="part_infotextb" id="part_infotext" >Email:</span><span id="part_infoMail'.$i.'" style="margin-right: 6px;" >'.$femail.'</span></div>
    					   <div class="part_infotext" ><span class="part_infotextb" id="part_infotext" >Birthday1:</span><span id="part_infoDoB'.$i.'" style="margin-right: 6px;" >'.$fdob.'</span></div>
                 <div class="part_infotext" ><span class="part_infotextb" id="part_infotext" >OnlineLink2:</span><span id="OnlineLink2'.$i.'" style="margin-right: 6px;" ><a href="'. $OnlineLink2 .'" target="_blank" id="aOnlineLink2'.$i.'" style="word-wrap: break-word;">'.$templink2.'</a></span></div>
    			</div>
        <div class="NoteFreindDetail"><span class="part_infotextb">Note: </span><span id="FamilyNoteDetail'.$i.'"> '.$fDescription.'</span></div>
     </div>

      <hr style="height: 1px; width: 100%; background: black;border: none;" />';
		}
	}
	else{
echo <<<HTML
			<div class="part_infol" ><div class="part_infotext" style="border: none;" ><span class="part_infotextb" style="border: none;" ></span></div>
					<div class="part_infotext" style="border: none;" ><span style="border: none;" class="part_infotextb" ></span>You haven't added any family members for this person yet!</div></div>
					<div class="part_infor" ><div class="part_infotext" style="border: none;" ><span class="part_infotextb" style="border: none;" ></span></div>
					<div class="part_infotext" style="border: none;" ><span class="part_infotextb" style="border: none;" ></span></div>
					</div></div>
HTML;
	}
    echo "</div>";
}

/*function profilepicurl($sourceName, $sourceUid, $sourceProfilePicture) {
	//echo $sourceName."/-/".$sourceUid  .'/-/'. $sourceProfilePicture;
	if ($sourceName == "facebook") {
		$profilepicurl = "https://graph.facebook.com/" . $sourceUid . "/picture?type=large";
		echo $profilepicurl;
	} elseif ($sourceName == "linkedin") {
		if ($sourceProfilePicture == '') {
			$profilepicurl = "images/noimage.png";
			echo $profilepicurl;
		}//When Sams end complete:
		$profilepicurl = $sourceProfilePicture;

		echo $profilepicurl;
	} else {
		$profilepicurl = "images/noimage.png";
		echo $profilepicurl;
	}

}*/
function listFriendDetail($dr){
	global $db,$mysql,$query,$result,$row,$query2,$res2,$raw;
	$friendId = $_GET["fid"];
	$litem = $row[$dr];
	if($litem != ""){
		echo $litem;
	}
	else{
		echo "";
	}
}
/*function for displaying Sales Force icon in top meny bar */
function SalesForceIcon(){
	global $config;

	$result = mysqli_query($mysqli,"SELECT * FROM tech_partners WHERE partnerName =  'salesforce'");
      
	$row = $result->fetch_assoc(); 
	  
	//echo '<img id="SalesFreindIcon"  src="../img/logos/'.$row["partnerName"].'.png"  width="35px" height="35px"/></a> ';
}

/*function for displaying Insurance and Finance part*/
function Insurance(){
	global $mysql;

	$query = "SELECT * FROM source_import WHERE	userId = '".$_SESSION['userId']."' AND 	sourceUid = '1770365817'";
	$result = $mysql->query($query);
	
	if ($result->num_rows != 0 ){
		echo '<div class="frl" >
		<div id="InsuranceTitle"> <span onclick="InsuranceFinanceDisplay(1)" >Insurance </span> || <span onclick="InsuranceFinanceDisplay(2)" > Finance</span></div> <br />	
		<div id="InsuranceContent">
			<table id="InsuranceTable">
				<tr><td>L-Cindy:</td><td>$1M</td><td>$2,150</td></tr>

				<tr><td>L-Al</td><td>$M</td><td>$2,150</td></tr>

				<tr><td>H-2928 Nor..</td><td>$1.35</td><td>$1,175</td></tr>

				<tr>
					<td style="color:red">A-AL  (4)</td>
					<td style="color:red">$125K</td>
					<td style="color:red">$1,125</td>
				</tr>
			</table>

		</div>
	
		<div id="FinanceContent">
			<table id="InsuranceTable">
				<tr >
					<td>Q-al:</td>
					<td>$675,039</td>
				</tr>
				
				<tr>
					<td>Al:</td>
					<td>$675,039</td>
				</tr>
				<tr>
					<td>Cindy:</td>
					<td>$276,401</td>
				</tr>
				
				<tr>
					<td>529-Alex:</td>
					<td>$376,451</td>
				</tr>
				
				<tr>
					<td>529-Emily:</td>
					<td>$46,232</td>
				</tr>
				
				<tr>
					<td></td>
					<td style="color:red">$1,450,523</td>
				</tr>
				
			</table>	
		</div>
	</div>';
	
	}
} 

function DisplayFreindEmail($userId, $freindID ){
	global $mysql;
	$sql = "SELECT * FROM user_friend_email  WHERE userId = '{$userId}' AND friendId = '{$freindID}' and emailType = 'home'";
	$result = $mysql->query($sql);
	
	$result_row = $result->fetch_assoc();
	echo $result_row['emailAddr'];
}

function DisplayFriendArdess($userId, $freindID, $friendAddrType, $friendaddr){
	global $mysql;
	$sql = "SELECT `{$friendaddr}` FROM user_friend_address WHERE userId = '{$userId}' AND friendId = '{$freindID}' AND friendAddrType='{$friendAddrType}'";

	$result = $mysql->query($sql);
	$result_row = $result->fetch_assoc();

	echo $result_row[$friendaddr];
}
?>
<script>
	//alert("asdasd");
</script>
 <link type="text/css" href="jquery.datepick.css" rel="stylesheet">
 <script type="text/javascript" src="jquery.datepick.js"></script>

<div class='frr' id="FrIinfo" >

    <div id="mainmenu">
		<div class="selectedTab" id="FriendDetail"><a href="#" onclick="get_friend_detail(<?php echo $_GET["fid"];?>)">Friend Detail</a></div>
		<div class="normalTab"><a href="#" onclick="get_Social_News_Stream(<?php echo $_GET["fid"];?>)">Social/News Stream</a></div>
		<div class="normalTab" ><a href="#" onclick="get_Mutual_Friends(<?php echo $_GET["fid"];?>)">Mutual Friends</a></div>
		
	</div>

<div class="part_title" >
<span class="part_titletext" >Personal Information</span> 
<?php
//Code to show Saleforce Icon or noT -U
global $mysql;
$fqsf = "SELECT * FROM userfrnd_source as u ,user_external_accnt as s WHERE sourceType='source_import_sf' AND u.userId='" . $_SESSION["userId"] . "' AND u.friendId='" . $row["friendId"] . "'";
	$trsf = $mysql -> query($fqsf);
	 $querysf = "Select authAccesstoken From user_external_accnt where userid = '".$_SESSION["userId"]."' AND authProvider = 'salesforce'";
	            $resultsf = $mysql -> query($querysf) or die('Error 1: ' . mysqli_error($con));
	//echo $trsf->num_rows;
	//echo $fqsf ;
if ((!($trsf->num_rows > 0)) AND (($resultsf->num_rows > 0)  ))
{
?>
 <img id="SalesFreindIcon"  src="../img/logos/salesforce.png"  onclick='promptNewUserSF(<?php echo $_SESSION["userId"] .",".$row["friendId"]; ?>)' width="35px" height="35px"/></a> 
<?php
}
?>

<div class="part_titleedit" ><a href="#" onclick="makeEditable('pi','<?php echo $row["friendId"];  ?>','<?php echo $_SESSION["userId"]; ?>' )" class="detail_edit_button" id="pi_edit_click" >Edit</a> <img id="a11" src="images/-.png" onclick="fdcollapse('a11', 'fd1')" /></div></div>
	<div id="fd1" style="display: inline;" >
    <div id="pi_holder" class="part_infohold" >
		<div class="part_infol" >
    		<div class="part_infotext" ><span class="part_infotextb" >First name: </span><span id="ffname" ><?php listFriendDetail("FriendFirstName"); ?></span></div>
			<div class="part_infotext" ><span class="part_infotextb" >Middle name: </span><span id="fmname" ><?php listFriendDetail("FriendMiddleName"); ?></span></div>
            <div class="part_infotext" ><span class="part_infotextb" >Last name: </span><span id="flname" ><?php listFriendDetail("FriendLastName"); ?></span></div>
<!--Usman Code is thi-->
 <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		
			<script>
	/*$( "#fdobd" ).datepicker({
      changeMonth: true,
      changeYear: true,
	  yearRange: "-113:+0" 
    });*/
  </script>
 
			<div class="part_infotext" ><span class="part_infotextb" >Birthday: </span><span id="fdob" ><?php listFriendDetail("FriendDOB"); ?></span></div>
        	<div class="part_infotext" ><span class="part_infotextb" >Email: </span><span id="femail" title="<?php DisplayFreindEmail($_SESSION["userId"], $_GET["fid"]); ?>" ><?php DisplayFreindEmail($_SESSION["userId"], $_GET["fid"]); ?></span>
         
         <select id='email_sel' class='detail_drop' onchange="EditFriendEmail(this.value,<?php echo $friendIds ?>,<?php echo $_SESSION["userId"] ?> )">
          <option value='Home' >Home</option>          
          <option value='Office' >Office</option>          
          <option value='Other' >Other</option>
        </select>
         
         
         </div>
		 <div class="part_infotext" ><span class="part_infotextb" >Online Link1:</span><span id="DetailOnlineLink1" ><a id="aDetailOnlineLink1" href="http://<?php listFriendDetail("onlineLink1"); ?>" target="_blank"><?php listFriendDetail("onlineLink1"); ?></a></span></div>
        </div>
		<div class="part_infor" >
			<div class="part_infotext" ><span class="part_infotextb" >Company: </span><span id="fcompany" ><?php listFriendDetail("FriendCompany"); ?></span></div>
    		<div class="part_infotext" ><span class="part_infotextb" >Title: </span><span id="ftitle" ><?php listFriendDetail("FriendTitle"); ?></span></div>
			<div class="part_infotext" ><span class="part_infotextb" >Highschool: </span><span id="fhighschool" ><?php listFriendDetail("FriendHighschool"); ?></span></div>
			<div class="part_infotext" ><span class="part_infotextb" >College: </span><span id="fcollege" ><?php listFriendDetail("FriendCollege"); ?></span></div>
    		<div class="part_infotext" ><span class="part_infotextb" >Phone: </span><span id="fphone1" ><?php listFriendDetail("FriendPhoneHome"); ?> </span>
      
      <select class="detail_drop" id="FrDetailPhoneDropDown" onchange="EditFriendPhone(this.value,<?php echo $friendIds ?> ,<?php echo $_SESSION["userId"] ?>)">
	  
        <option value='FriendPhoneHome'>Home</option> 
        <option value='FriendPhoneOffice'>Office</option>
        <option  value='FriendPhoneCell'>Cell</option>

      </select></div>
			<div class="part_infotext" ><span class="part_infotextb" >Online Link2:</span><span id="DetailOnlineLink2" ><a id="aDetailOnlineLink2" href="http://<?php listFriendDetail("onlineLink2"); ?>" target="_blank"><?php listFriendDetail("onlineLink2"); ?></a></span></div>
		</div>
            <div class="NoteFreindDetail"><span class="part_infotextb">Notes: </span><span id="NoteFrDetail"><?php listFriendDetail("FriendComments"); ?> </span></div>
            </div>
			

       </div>
   
   
				<div style="margin-top: 20px;" >
          <div class="part_title" ><span class="part_titletext" >Address Information</span><div class="part_titleedit" ><a onclick="makeEditable('ai','<?php echo $row["friendId"]; ?>', '<?php echo $_SESSION["userId"]; ?>' )" id="ai_edit_click" class="detail_edit_button" href="#" >Edit</a> <img id="a22" src="images/-.png" onclick="fdcollapse('a22', 'fd2')" /></div></div>
					<div id="fd2" ><div class="part_infohold" id="ai_holder" >
             <div class="part_infol" >
               <div class="part_infotext" ><span class="part_infotextb" >Home Address: </span><span id="homeaddr" ><?php DisplayFriendArdess($_SESSION["userId"], $_GET["fid"], 'home', 'friendStreet');  ?></span></div>
               <div class="part_infotext" ><span class="part_infotextb" >Home City: </span><span id="homeCity" ><?php  DisplayFriendArdess($_SESSION["userId"], $_GET["fid"], 'home', 'friendCity'); ?></span></div>
               <div class="part_infotext" ><span class="part_infotextb" >Home State: </span><span id="homeState" ><?php  DisplayFriendArdess($_SESSION["userId"], $_GET["fid"], 'home', 'friendState'); ?></span></div>
               <div class="part_infotext" ><span class="part_infotextb" >Home Zip: </span><span id="homeZip" ><?php  DisplayFriendArdess($_SESSION["userId"], $_GET["fid"], 'home', 'friendZip');  ?></span></div>

             </div>
  					
           <div class="part_infor" >
               <div class="part_infotext" ><span class="part_infotextb" >Office Address: </span><span id="officeaddr" ><?php  DisplayFriendArdess($_SESSION["userId"], $_GET["fid"], 'office', 'friendStreet');  ?></span></div>
               <div class="part_infotext" ><span class="part_infotextb" >Office City: </span><span id="officeCity" ><?php DisplayFriendArdess($_SESSION["userId"], $_GET["fid"], 'office', 'friendCity'); ?></span></div>
               <div class="part_infotext" ><span class="part_infotextb" >Office State: </span><span id="officeState" ><?php DisplayFriendArdess($_SESSION["userId"], $_GET["fid"], 'office', 'friendState');  ?></span></div>
               <div class="part_infotext" ><span class="part_infotextb" >Office Zip: </span><span id="officeZip" ><?php  DisplayFriendArdess($_SESSION["userId"], $_GET["fid"], 'office', 'friendZip');  ?></span></div>
					</div></div></div></div>
					
				<div class="part_title" ><span class="part_titletext" >Family Information</span><div class="part_titleedit" ><input type="button" value="Add new family member" onclick="promptAddFamily('<?php echo $_SESSION['lfid'];  ?>')" class="family_button" />&nbsp;&nbsp;&nbsp;<a href="#" id="fmedit" class="detail_edit_button" onclick="makeFamilyEditable(<?php echo $row["friendId"]; ?>)" >Edit</a> <img id="a33" src="images/-.png" onclick="fdcollapse('a33', 'fd3')" /></div></div>
						<div id="fd3" ><div class="part_infohold" ><?php   echo getfamily(); ?>
      
      </div>
					</div>
				</div>
                
	
<?php
			//echo "<div class='frl' ><div class='fr' ><span class=\"detail_name\" >". $row["FriendFirstName"] ." ". $row["FriendLastName"] ."</span><br /><div class='detail_unname' >Text here</div><div><img src='images/facebook.png' style='width: 15px;height: 15px;' /> <img src='images/linkedin.png' style='width: 15px;height: 15px;' /></div><img src=\"https://graph.facebook.com/". $raw['sourceUid'] ."/picture?type=large\" style='width: 100px;height: 100px;' /></div><div class='opts' ><div class='opts_title' >Mutual Friends</div><div class='opts_mut' ><div class='opts_mutbox' ></div><div class='opts_mutbox' ></div><div class='opts_mutbox' ></div><div class='opts_mutbox' ></div></div><div class='opts_mut' ><div class='opts_mutbox' ></div><div class='opts_mutbox' ></div></div></div><div class='opts_title' >Shared albums</div></div></div>";
?>
<div class="frl" >
	<div class="fr" >
		<span class="detail_name" ><?php echo $row["FriendFirstName"] ." ". $row["FriendLastName"];  ?></span><br />
		<div class="detail_unname" style="background:url('<?php echo DispFreProfilePic($row['friendId']); ?>'); background-size: 100% 100%;" ></div>
		<?php
		//profilepicurl($raw["sourceName"],$raw["sourceUid"],$raw["sourceProfilePicture"]);
		?>
		<br />
		<?php
        sourceicons($_GET["fid"]);
        ?>
	</div>
	
	<div class="opts_title" >Categories</div>
 
	<div class="fr" ><?php include('friend_cat.php'); ?></div>
	
	<div class="opts_title" >Mutual Friends</div>
	<div class="fr" ><?php include('../../php/class/mutualFriends.php'); ?></div>
	</div>  	
	<?php Insurance(); ?>
  


</div>
