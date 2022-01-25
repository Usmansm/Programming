<?php
session_start();
require_once "../config/config.php";
include "parsecsv.lib.php";
echo <<<STYLE
<style>
body{
    padding: 0px;
}
.chold{
    background: #e2e2e2; /* Old browsers */
background: -moz-linear-gradient(top,  #e2e2e2 0%, #ffffff 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#e2e2e2), color-stop(100%,#ffffff)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #e2e2e2 0%,#ffffff 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #e2e2e2 0%,#ffffff 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #e2e2e2 0%,#ffffff 100%); /* IE10+ */
background: linear-gradient(to bottom,  #e2e2e2 0%,#ffffff 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e2e2e2', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */

}
.contact_box{
    padding: 4px;
    background: #E3E3E3;
    border-bottom: 3px solid #BABABA;
    margin: 10px;
}
.contact_box_match{
    padding: 4px;
    background: #E3E3E3;
    border-bottom: 3px solid #BABABA;
    margin: 10px;
	box-shadow: 0px 0px 4px red;
}
.contact_box:hover{
    background: #E3E3E3;
    border-left: 2px solid #D4D4D4;
    cursor: pointer; 
}
.contact_title{
	font-weight: bold;
	font-size: 14pt;
}
.href_span{
	color: blue;
	font-size: 12pt;
	text-decoration: underline;
	float: right;
}
</style>
<script>
function togel(elid){
    cdis = document.getElementById(elid).style.display
    if(cdis == "none"){
        document.getElementById(elid).style.display="inline"
    }
    else{
        document.getElementById(elid).style.display="none"
    }
}
</script>
STYLE;
require_once "../contacts/contacts.class.php";
$log = true;
$base = new mib_contacts;
$csv_parse = new parseCSV($_FILES["csv_import"]["tmp_name"]);

echo "NOTE: 48 has no email - 174 has multiple emails - most other have 1 email<br />";

function rlog($t){
    global $log;
    if($log == true){
        echo $t."<br />";
    }
}

if(isset($_POST["act"])){
    if($_POST["act"] == "import_csv"){
        rlog("Detected CSV file upload/import.");
        rlog("Checking file validity.");
        $integ = $base->csv_check_integrity();
        if($integ == "good"){
        	$contact_data = array();
            rlog("File is good.");
			
			/*
			 * Remove all known not-import fields
			 */
			foreach($csv_parse->data as &$contact){
				unset($contact["Title"]);
				unset($contact["Suffix"]);
				unset($contact["Department"]);
				unset($contact["Assistant's Phone"]);
				unset($contact["Fax"]);
				unset($contact["Business Fax"]);
				unset($contact["Callback"]);
				unset($contact["Car Phone"]);
				unset($contact["Company Main Phone"]);
				unset($contact["Home Fax"]);
				unset($contact["Home Phone 2"]);
				unset($contact["ISDN"]);
				unset($contact["Other Fax"]);
				unset($contact["Other Phone"]);
				unset($contact["Pager"]);
				unset($contact["Primary Phone"]);
				unset($contact["Radio Phone"]);
				unset($contact["TTY/TDD Phone"]);
				unset($contact["Telex"]);
				unset($contact["Account"]);
				unset($contact["Assistant's Name"]);
				unset($contact["Billing Information"]);
				unset($contact["Business Address PO Box"]);
				unset($contact["Categories"]);
				unset($contact["Children"]);
				unset($contact["Directory Server"]);
				unset($contact["Gender"]);
				unset($contact["Government ID Number"]);
				unset($contact["Hobby"]);
				unset($contact["Home Address PO Box"]);
				unset($contact["Initials"]);
				unset($contact["Internet Free Busy"]);
				unset($contact["Keywords"]);
				unset($contact["Language"]);
				unset($contact["Location"]);
				unset($contact["Manager's Name"]);
				unset($contact["Mileage"]);
				unset($contact["Office Location"]);
				unset($contact["Organizational ID Number"]);
				unset($contact["Other Address PO Box"]);
				unset($contact["Priority"]);
				unset($contact["Private"]);
				unset($contact["Profession"]);
				unset($contact["Referred By"]);
				unset($contact["Sensitivity"]);
				unset($contact["Web Page"]);
			}
			
			//END
			
			/*
			 * Foreach contact, put all their emails into a seperate array but keep integer keys intact for future referrence
			 */
			 $verif = "false";
			$contact_mail = array();
			foreach($csv_parse->data as $int_key => $val){
				foreach($val as $key => $data){
					$key_split = explode(" ", $key);
					if($key_split[0] == "E-mail"){
						if(is_numeric($key_split[1])){ //If there is a number in the email, insert it with that key
						if($key_split[2] == "Address"){
							if($data != ""){
								$contact_mail[$int_key][$key_split[1]] = $data;
						}}}
						else{ //Else there is no number insert it as key 0 (first)
						if($key_split[1] == "Address"){
								if($data != ""){
									$contact_mail[$int_key][0] = $data;
						}}}
					}
				}
			}
			
			
			
				$usemail = "";
			foreach($csv_parse->data as $cont_num => &$contact){ 
				//Whole contact
				echo "<div class='contact_box' id='contact_box_". $cont_num ."' >\n";
				echo "<span class='contact_title' >". $cont_num .". ". $contact["First Name"] ." ". $contact["Middle Name"] ." ". $contact["Last Name"] ." </span> ";
				echo "<span class='href_span' onclick='togel(\"contact_data_". $cont_num ."\")' >View/Hide</span>";
				echo "<div id='contact_data_". $cont_num ."' style='display: none;' ><br />";
				$num_mails = count($contact_mail[$cont_num]);
				if($num_mails > 0){
					echo "This contact has ". $num_mails ." emails associated with him. Loopin thru and doing the checks.<br />";
					foreach($contact_mail[$cont_num] as $mail){
						if($verif == "false"){
						echo "Checkin for ".$mail."<br />";
						$mailres = $base->csv_check_mail($mail);
						if($mailres == "none"){
							echo "Nothing found. Moving on.<br /><br />";
						}
						else{
							echo "We got a match on ".$mail.". Belongs to user ".$mailres["userId"].". Checking account state.<br />";
							echo "<script>alert('Found a match on ". $cont_num ."');togel(\"contact_data_". $cont_num ."\");document.getElementById('contact_box_". $cont_num ."').className='contact_box_match'</script>";
							$query = "SELECT * FROM users WHERE userId='". $mailres["userId"] ."'";
							$res = $_SESSION["mysql"]->query($query);
							$nums = $res->num_rows;
							$data = $res->fetch_assoc();
							if($data["userStatus"] == "active" AND $mailres["userId"] != $_SESSION["userId"] ){
								echo "User is verified. Using this id.<br />";
								$verif = "true";
							}
							else{
								echo "User is not verified but we will keep him around to sue as a userid incase no verifides are found.<br >";
								if($usemail == ""){
									$usemail = $mail;
								}
							}
						}}
					}
						echo "Done going through all mails.<br />";
						if($verif == "false"){
							echo "We didnt find any verified matches.<br />";
						}
						if($usemail == ""){
							echo "We didnt find any unverified matches... Treating contact as new user_friend<br />";
						}
						else{
							echo "First unverified email match was ".$usemail." we will use this as the userid<br />";
						}
						
						if($verif == "false" && $usemail == ""){
							echo "NOW Treating contact as new user<br />";
							$imail = $contact_mail[$cont_num][0];
								$fid = $base->csv_create_user($imail);
								$ufd_insert = "INSERT INTO user_friend_detail(userId,friendId,FriendFirstName,FriendMiddleName,FriendLastName,FriendPhoneCell,FriendPhoneHome,FriendPhoneOffice,FriendAnniversary,FriendTitle,FriendCompany) 
								VALUES('". $_SESSION["userId"] ."','". $fid ."','". $csv_parse->data[$cont_num]["First Name"] ."','". $csv_parse->data[$cont_num]["Middle Name"] ."','". $csv_parse->data[$cont_num]["Last Name"] ."','". $csv_parse->data[$cont_num]["Mobile Phone"] ."','". $csv_parse->data[$cont_num]["Home Phone"] ."','". $csv_parse->data[$cont_num]["Business Phone"] ."','". $csv_parse->data[$cont_num]["Anniversary"] ."','". $csv_parse->data[$cont_num]["Job Title"] ."','". $csv_parse->data[$cont_num]["Company"] ."')";
								$_SESSION["mysql"]->query($ufd_insert) OR die($_SESSION["mysql"]->error);
								echo $ufd_insert;
								$useremailq = "ISNERT INTO user_email(userId,emailAddr,emailType,EmailStatus) VALUES('". $fid ."','". $contact_mail[$cont_num][0] ."','Primary','pending')";
						}
						
						if($verif == "true"){
							echo "NOW using verified user as userid<br />";
							//////DB WRITE
							$ufdq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $data["userId"] ."'";
							$ufdr = $_SESSION["mysql"]->query($ufdq);
							$ufd_num = $ufdr->num_rows;
							if($ufd_num > 0){
								
							}
							else{
								//This user_friend_detail relationship doesnt exist, create it and populate it
								$imail = $contact_mail[$cont_num][0];
								$fid = $base->csv_create_user($imail);
								$ufd_insert = "INSERT INTO user_friend_detail(userId,friendId,FriendFirstName,FriendMiddleName,FriendLastName,FriendPhoneCell,FriendPhoneHome,FriendPhoneOffice,FriendAnniversary,FriendTitle,FriendCompany) 
								VALUES('". $_SESSION["userId"] ."','". $fid ."','". $csv_parse->data[$cont_num]["First Name"] ."','". $csv_parse->data[$cont_num]["Middle Name"] ."','". $csv_parse->data[$cont_num]["Last Name"] ."','". $csv_parse->data[$cont_num]["Mobile Phone"] ."','". $csv_parse->data[$cont_num]["Home Phone"] ."','". $csv_parse->data[$cont_num]["Business Phone"] ."','". $csv_parse->data[$cont_num]["Anniversary"] ."','". $csv_parse->data[$cont_num]["Job Title"] ."','". $csv_parse->data[$cont_num]["Company"] ."')";
								$_SESSION["mysql"]->query($ufd_insert) OR die($_SESSION["mysql"]->error);
								echo $ufd_insert;
							}
							
							
							//////END DB WRITE
						}
						
						if($usemaill != ""){
							echo "NOW useing first unverified email match userid<Br />";
							
						}
						
				}else{
					echo "This contact has no email associated with him, processing as a non-existent user_friend<br />";
					$imail = $contact_mail[$cont_num][0];
								$fid = $base->csv_create_user($imail);
								$ufd_insert = "INSERT INTO user_friend_detail(userId,friendId,FriendFirstName,FriendMiddleName,FriendLastName,FriendPhoneCell,FriendPhoneHome,FriendPhoneOffice,FriendAnniversary,FriendTitle,FriendCompany) 
								VALUES('". $_SESSION["userId"] ."','". $fid ."','". $csv_parse->data[$cont_num]["First Name"] ."','". $csv_parse->data[$cont_num]["Middle Name"] ."','". $csv_parse->data[$cont_num]["Last Name"] ."','". $csv_parse->data[$cont_num]["Mobile Phone"] ."','". $csv_parse->data[$cont_num]["Home Phone"] ."','". $csv_parse->data[$cont_num]["Business Phone"] ."','". $csv_parse->data[$cont_num]["Anniversary"] ."','". $csv_parse->data[$cont_num]["Job Title"] ."','". $csv_parse->data[$cont_num]["Company"] ."')";
								$_SESSION["mysql"]->query($ufd_insert) OR die($_SESSION["mysql"]->error);
								echo $ufd_insert;
				}
			foreach($contact as $key => $val){
					//Each data field in the contact
			}
				echo "</div>";
				echo "</div>";
				$usemail = "";
				$verif = "false";
			}
			
            echo "<pre>";
			print_r($csv_parse->data);
			echo "</pre>";
        }
        else{
            $errors = array(1 => "File too large", 2 => "File not text/csv");
            die("Error code ".$integ.": ".$errors[$integ]);
        }
    }
}

@mysqli_close($_SESSION["mysql"]);
?>