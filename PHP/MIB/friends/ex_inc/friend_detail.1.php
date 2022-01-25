<?php
session_start();
$_GET["fid"] = strip_tags($_GET["fid"]);
$friendIds = $_GET["fid"];
$_SESSION["lfid"] = $_GET["fid"];
include "../../config/config.php";

$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
	if(mysqli_connect_errno()) {
		die("Connect failed: \n".mysqli_connect_error());
	}
	$query = "SELECT * FROM user_friend_detail WHERE friendId = '".$_GET["fid"]."'";
	$result = $mysql->query($query);
	$row = $result->fetch_assoc();
	$query2 =  "SELECT * FROM source_import WHERE userId = '".$row['friendId']."'";
			$res2 = $mysql->query($query2);
			$raw = $res2->fetch_assoc();
		/*	echo <<<LEFT
			<div class='frr' >
				<div class="part_title" ><span class="part_titletext" >Personal Information</span><div class="part_titleedit" ><a href="#" >Edit</a></div></div><div class="part_infohold" ><div class="part_infol" ><div class="part_infotext" ><span class="part_infotextb" >Email: </span>johndoe@johns-doe.net</div><div class="part_infotext" ><span class="part_infotextb" >Phone: </span>6969-6969-6969</div><div class="part_infotext" ><span class="part_infotextb" >Birthday: </span>Beggining-of-time</div><div class="part_infotext" ><span class="part_infotextb" >Title: </span>Financial advisor</div><div class="part_infotext" ><span class="part_infotextb" >Highschool: </span>John Doe's school'</div></div><div class="part_infor" ><div class="part_infotext" ><span class="part_infotextb" >First name: </span>Lexy</div><div class="part_infotext" ><span class="part_infotextb" >Middle name: </span>Rodney</div><div class="part_infotext" ><span class="part_infotextb" >Last name: </span>Breckenridge</div><div class="part_infotext" ><span class="part_infotextb" >Company: </span>C.N.G.H.</div><div class="part_infotext" ><span class="part_infotextb" >College: </span>University of C.N.G.H.</div></div></div>
				<div style="margin-top: 10px;" ><div class="part_title" ><span class="part_titletext" >Address Information</span><div class="part_titleedit" ><a href="#" >Edit</a></div></div><div class="part_infohold" ><div class="part_infol" ><div class="part_infotext" ><span class="part_infotextb" >Home: </span>123 somewheres street</div></div><div class="part_infor" ><div class="part_infotext" ><span class="part_infotextb" >Office: </span>123 somehwere elses street</div></div></div></div>
				<div style="margin-top: 10px;" ><div class="part_title" ><span class="part_titletext" >Family Information</span><div class="part_titleedit" ><a href="#" >Edit</a></div></div><div class="part_infohold" ><div class="part_infol" ><div class="part_infotext" ><span class="part_infotextb" >Spouse: </span>Susan Doe</div><div class="part_infotext" ><span class="part_infotextb" >Phone: </span>555-555-555</div></div><div class="part_infor" ><div class="part_infotext" ><span class="part_infotextb" >Email: </span>nowhere@domain.com</div><div class="part_infotext" ><span class="part_infotextb" >Birthday: </span>June 20, 1967</div></div></div></div>
				<img src="images/fback.png" style="cursor: pointer;" onclick="location.reload()" />
			</div>
LEFT;*/


function getfamily(){
	global $mysql;
	$query = "SELECT * FROM user_friend_family_details WHERE userfrnd_detail_user_UserId = '". $_SESSION["userId"] ."' AND userfrnd_detail_user_FriendUserId = '".$_GET["fid"]."'";
	$result = $mysql->query($query);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$ftype = $row["FamilyMember_Type"];
			$ffname = $row["FamilyMember_FirstName"];
			$flname = $row["FamilyMember_LastName"];
			$fphone = $row["FamilyMember_PhoneCell"];
			$fdob = $row["FamilyMember_BornOn"];
			$femail = $row["FamilyMember_Email"];
			
			echo <<<HTML
			
			<div class="part_infol" ><div class="part_infotext" ><span class="part_infotextb" >{$ftype}: </span>{$ffname} {$flname}</div>
					<div class="part_infotext" ><span class="part_infotextb" >Phone: </span>{$fphone} <select class="detail_drop"><option>Cell</option><option>Office</option><option>Home</option></select></div></div>
					<div class="part_infor" ><div class="part_infotext" ><span class="part_infotextb" >Email: </span>{$femail}</div>
					<div class="part_infotext" ><span class="part_infotextb" >Birthday: </span>{$fdob}</div>
					</div>
HTML;
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
}

function profilepicurl($src,$uid,$exinf){
	if($src == "facebook"){
		$profilepicurl = "<img src='https://graph.facebook.com/". $uid ."/picture?type=large' style='width: 100px; height: 100px;' />";
		return $profilepicurl;
	}
	elseif($src == "linkedin"){
		//When Sams end complete:
		$profilepicurl = "<img src='".$exinf."' style='width: 100px; height: 100px;' />";
		//
		return $profilepicurl;
	}
	else{
		$profilepicurl = "images/noimage.png";
		return $profilepicurl;
	}
}
function listFriendDetail($dr){
	global $db,$mysql,$query,$result,$row,$query2,$res2,$raw;
	$friendId = $_GET["fid"];
	$litem = $row[$dr];
	if($litem != ""){
		echo $litem;
	}
	else{
		echo "None";
	}
}
?>
<div class='frr' >

<div class="part_title" ><span class="part_titletext" >Personal Information</span><div class="part_titleedit" ><a href="#" onclick="makeEditable('pi','<?php echo $row["friendId"];  ?>')" class="detail_edit_button" id="pi_edit_click" >Edit</a> <img id="a11" src="images/-.png" onclick="fdcollapse('a11', 'fd1')" /></div></div>
	<div id="fd1" style="display: inline;" >
    <div id="pi_holder" class="part_infohold" >
		<div class="part_infol" >
    		<div class="part_infotext" ><span class="part_infotextb" >First name: </span><span id="ffname" ><?php listFriendDetail("FriendFirstName"); ?></span></div>
			<div class="part_infotext" ><span class="part_infotextb" >Middle name: </span><span id="fmname" ><?php listFriendDetail("FriendMiddleName"); ?></span></div>
            <div class="part_infotext" ><span class="part_infotextb" >Last name: </span><span id="flname" ><?php listFriendDetail("FriendLastName"); ?></span></div>
			<div class="part_infotext" ><span class="part_infotextb" >Birthday: </span><span id="fdob" ><?php listFriendDetail("FriendDOB"); ?></span></div>
        	<div class="part_infotext" ><span class="part_infotextb" >Email: </span><span id="femail" ><?php listFriendDetail("FriendEmail1"); ?> </span><select class="detail_drop"><option>Home</option><option>Office</option><option>Home2</option></select></div>
        </div>
		<div class="part_infor" >
			<div class="part_infotext" ><span class="part_infotextb" >Company: </span><span id="fcompany" ><?php listFriendDetail("FriendCompany"); ?></span></div>
    		<div class="part_infotext" ><span class="part_infotextb" >Title: </span><span id="ftitle" ><?php listFriendDetail("FriendTitle"); ?></span></div>
			<div class="part_infotext" ><span class="part_infotextb" >Highschool: </span><span id="fhighschool" ><?php listFriendDetail("FriendHighschool"); ?></span></div>
			<div class="part_infotext" ><span class="part_infotextb" >College: </span><span id="fcollege" ><?php listFriendDetail("FriendCollege"); ?></span></div>
    		<div class="part_infotext" ><span class="part_infotextb" >Phone: </span><span id="fphone1" ><?php listFriendDetail("FriendPhoneHome"); ?> </span><select class="detail_drop"><option>Cell</option><option>Office</option><option>Home</option></select></div>
			</div>
            </div>
			</div>
				<div style="margin-top: 20px;" ><div class="part_title" ><span class="part_titletext" >Address Information</span><div class="part_titleedit" ><a onclick="makeEditable('ai','<?php echo $row["friendId"]; ?>')" id="ai_edit_click" class="detail_edit_button" href="#" >Edit</a> <img id="a22" src="images/-.png" onclick="fdcollapse('a22', 'fd2')" /></div></div>
					<div id="fd2" ><div class="part_infohold" id="ai_holder" ><div class="part_infol" ><div class="part_infotext" ><span class="part_infotextb" >Home: </span><span id="homeaddr" ><?php listFriendDetail("FriendAddress1");  ?></span></div></div>
					<div class="part_infor" ><div class="part_infotext" ><span class="part_infotextb" >Office: </span><span id="officeaddr" ><?php listFriendDetail("FriendAddress2");  ?></span></div>
					</div></div></div></div>
				<div class="part_title" ><span class="part_titletext" >Family Information</span><div class="part_titleedit" ><input type="button" value="Add new family member" onclick="promptAddFamily()" class="family_button" />&nbsp;&nbsp;&nbsp;<a href="#" class="detail_edit_button" >Edit</a> <img id="a33" src="images/-.png" onclick="fdcollapse('a33', 'fd3')" /></div></div>
						<div id="fd3" ><div class="part_infohold" ><?php getfamily(); ?></div>
					</div>
				</div>
            <div class="tab_holder"
<?php
			//echo "<div class='frl' ><div class='fr' ><span class=\"detail_name\" >". $row["FriendFirstName"] ." ". $row["FriendLastName"] ."</span><br /><div class='detail_unname' >Text here</div><div><img src='images/facebook.png' style='width: 15px;height: 15px;' /> <img src='images/linkedin.png' style='width: 15px;height: 15px;' /></div><img src=\"https://graph.facebook.com/". $raw['sourceUid'] ."/picture?type=large\" style='width: 100px;height: 100px;' /></div><div class='opts' ><div class='opts_title' >Mutual Friends</div><div class='opts_mut' ><div class='opts_mutbox' ></div><div class='opts_mutbox' ></div><div class='opts_mutbox' ></div><div class='opts_mutbox' ></div></div><div class='opts_mut' ><div class='opts_mutbox' ></div><div class='opts_mutbox' ></div></div></div><div class='opts_title' >Shared albums</div></div></div>";
?>
<div class="frl" >
	<div class="fr" >
		<span class="detail_name" ><?php echo $row["FriendFirstName"] ." ". $row["FriendLastName"];  ?></span><br />
		<div class="detail_unname" >---</div>
		<?php
		echo profilepicurl($raw["sourceName"],$raw["sourceUid"],$row["sourceProfilePicture"]);
		?>
		<br />
		<img src='images/facebook.png' style='width: 15px;height: 15px;' /> <img src='images/linkedin.png' style='width: 15px;height: 15px;' /><br />
		<div>
			<?php
			include "detailCats.php";
			?>
		</div>
	</div>
</div>