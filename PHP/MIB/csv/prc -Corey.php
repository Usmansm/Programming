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
require_once "../php/class/user.class.php";
require_once "../php/class/verify.class.php";
$log = true;
$base = new mib_contacts;
$csv_parse = new parseCSV($_FILES["csv_import"]["tmp_name"]);
$user = new user;
function rlog($t){
    global $log;
    if($log == true){
        echo $t."<br />";
    }
}
$verify = new verify;

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
			$namelessdrop = 0;
			foreach($csv_parse->data as $key => $val){
				if($csv_parse->data[$key]["First Name"] == "" && $csv_parse->data[$key]["Last Name"] == ""){
					unset($csv_parse->data[$key]);
					$namelessdrop++;
				}
				//print_r($csv_parse->data[$key]);
			}
			echo "<br /><br /> Dropped ". $namelessdrop ." nameless people</br><br />";

			foreach($csv_parse->data as $key => $val){
				foreach($val as $key2 => $dat){
					$dat2 = str_replace("\"", "", $dat);
					$csv_parse->data[$key][$key2] = $dat2;
				}
			}
			//END
			
			/*
			 * Foreach contact, put all their emails into a seperate array but keep integer keys intact for future referrence
			 */
			 echo "4";
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
			echo "1";	
				/*
				 * Do the same as email with cell phone number
				 */
				$contact_cell = array();
				foreach($csv_parse->data as $key => $val){
					echo $key ."=>". $val;
					$contact_cell[$key] = $csv_parse->data[$key]["Mobile Phone"];
				}
				//print_r($contact_mail);
				echo "5";
				foreach($csv_parse->data as $key => $val){
					$stoopemail = "go";
					$mailmatch = "no";
					//Check if there are any emails for this user
					
					foreach($contact_mail[$key] as $mval){
							echo "-= ". $mval ." =-";
						if($mailmatch == "no"){
						$checkq = "SELECT * FROM user_email WHERE emailAddr='". $mval ."'";
						echo $checkq;
						$checkr = $_SESSION["mysql"]->query($checkq);
						$num = $checkr->num_rows;
						if($num > 0){
							$mailmatch = "yes";
							echo "matched";
							$maildata = $checkr->fetch_assoc();
						}}}
						echo "6";
						
						if($mailmatch == "yes"){
							//email was matched, update user friend details
							$uid = $maildata["userId"];
							
							$qq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
							$qres = $_SESSION["mysql"]->query($qq);
							$fdata = $qres->fetch_assoc();
							
							if($fdata["FriendPhoneCell"] == ""){
							$ufdupdate = "UPDATE user_friend_detail SET FriendPhoneCell='". $csv_parse->data[$key]["Mobile Phone"] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
							$_SESSION["mysql"]->query($ufdupdate);
							}
							if($fdata["FriendPhoneHome"] == ""){
							$ufdupdate = "UPDATE user_friend_detail SET FriendPhoneHome='". $csv_parse->data[$key]["Home Phone"] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
							$_SESSION["mysql"]->query($ufdupdate);
							}
							if($fdata["FriendAnniversary"] == ""){
							$ufdupdate = "UPDATE user_friend_detail SET FriendAnniversary='". $csv_parse->data[$key]["Anniversay"] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
							$_SESSION["mysql"]->query($ufdupdate);
							}
							if($fdata["FriendTitle"] == ""){
							$ufdupdate = "UPDATE user_friend_detail SET FriendTitle='". $csv_parse->data[$key]["Job Title"] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
							$_SESSION["mysql"]->query($ufdupdate);
							}
							if($fdata["FriendPhoneCompany"] == ""){
							$ufdupdate = "UPDATE user_friend_detail SET FriendPhoneCompany='". $csv_parse->data[$key]["Company"] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
							$_SESSION["mysql"]->query($ufdupdate);
							}
							
							if($csv_parse->data[$key]["Business Street"] != ""){
									$_SESSION["mysql"]->query("INSERT INTO user_friend_address(friendAddrType,friendStreet,friendCity,friendState,friendZip,friendCountry) VALUES('office','". $csv_parse->data[$key]["Business Street"] ."','". $csv_parse->data[$key]["Business City"] ."','". $csv_parse->data[$key]["Business State"] ."','". $csv_parse->data[$key]["Business Postal Code"] ."','". $csv_parse->data[$key]["Business Country/Region"] ."')");
							}
							if($csv_parse->data[$key]["Home Street"] != ""){
									$_SESSION["mysql"]->query("INSERT INTO user_friend_address(friendAddrType,friendStreet,friendCity,friendState,friendZip,friendCountry) VALUES('home','". $csv_parse->data[$key]["Home Street"] ."','". $csv_parse->data[$key]["Home City"] ."','". $csv_parse->data[$key]["Home State"] ."','". $csv_parse->data[$key]["Home Postal Code"] ."','". $csv_parse->data[$key]["Home Country/Region"] ."')");
							}}
							else if(!empty($contact_mail[$key]) && $stopemail != "stop"){
								$userDetail=array();
								$userDetail["firstName"]=$csv_parse->data[$key]["First Name"];
								$userDetail["middleName"]=$csv_parse->data[$key]["Middle Name"] ;
								$userDetail["lastName"]=$csv_parse->data[$key]["Last Name"] ;
								//print_r($userDetail);
								$new_user = $user->newUser(false,false,$userDetail,'temp');
								$insertq = "INSERT INTO user_email(userId,emailAddr,emailType,EmailStatus) VALUES('". $new_user ."','". $contact_mail[$key][0] ."','primary','unverified')";
								$_SESSION["mysql"]->query($insertq);
								$match = $verify->checkImport($csv_parse->data[$key]["First Name"],$csv_parse->data[$key]["Middle Name"],$csv_parse->data[$key]["Last Name"]);
								if($match == true){
									$mterm = "unverified";
								}
								else{
									$mterm = "verified";
								}
								$ufd_insert = "INSERT INTO user_friend_detail(userId,friendId,FriendFirstName,FriendStatusCode,FriendMiddleName,FriendLastName,FriendPhoneCell,FriendPhoneHome,FriendPhoneOffice,FriendAnniversary,FriendTitle,FriendCompany) 
								VALUES('". $_SESSION["userId"] ."','". $new_user ."','". $csv_parse->data[$key]["First Name"] ."','". $mterm ."','". $csv_parse->data[$key]["Middle Name"] ."','". $csv_parse->data[$key]["Last Name"] ."','". $csv_parse->data[$key]["Mobile Phone"] ."','". $csv_parse->data[$key]["Home Phone"] ."','". $csv_parse->data[$key]["Business Phone"] ."','". $csv_parse->data[$key]["Anniversary"] ."','". $csv_parse->data[$key]["Job Title"] ."','". $csv_parse->data[$key]["Company"] ."')";
								$_SESSION["mysql"]->query($ufd_insert);
									//echo $maildataq;
									if($csv_parse->data[$key]["Business Street"] != ""){
									$_SESSION["mysql"]->query("INSERT INTO user_friend_address(userId,friendId,friendAddrType,friendStreet,friendCity,friendState,friendZip,friendCountry) VALUES('". $_SESSION["userId"] ."','". $new_user ."','office','". $csv_parse->data[$key]["Business Street"] ."','". $csv_parse->data[$key]["Business City"] ."','". $csv_parse->data[$key]["Business State"] ."','". $csv_parse->data[$key]["Business Postal Code"] ."','". $csv_parse->data[$key]["Business Country/Region"] ."')");
							}
							foreach($contact_mail[$key] as $mail){
								$userfemail = "INSERT INTO user_friend_email(userId,friendId,emailAddr,emailType) VALUES('". $_SESSION["userId"] ."','". $new_user ."','". $mail ."','home')";
								$_SESSION["mysql"]->query($userfemail);
							}
							if($csv_parse->data[$key]["Home Street"] != ""){
									$_SESSION["mysql"]->query("INSERT INTO user_friend_address(userId,friendId,friendAddrType,friendStreet,friendCity,friendState,friendZip,friendCountry) VALUES('". $_SESSION["userId"] ."','". $new_user ."','home','". $csv_parse->data[$key]["Home Street"] ."','". $csv_parse->data[$key]["Home City"] ."','". $csv_parse->data[$key]["Home State"] ."','". $csv_parse->data[$key]["Home Postal Code"] ."','". $csv_parse->data[$key]["Home Country/Region"] ."')");
							
								}
								$si = "INSERT INTO source_import(userId,sourceName) VALUES('". $new_user ."','mail_client')";
								$_SESSION["mysql"]->query($si);
								$stopemail = "stop";
							}
						else{
							echo "7";
							//no match continue checking phone number
							$cell_no_special_chars = preg_replace('(\D+)', '', $csv_parse->data[$key]["Mobile Phone"]);
							$cellq = "SELECT * FROM user_detail_public WHERE userPhoneCell='". $cell_no_special_chars ."'";
							$cellr = $_SESSION["mysql"]->query($cellq);
							$cellnum = $cellr->num_rows;
							if($cellnum > 0){
								//cell match found
								$udata = $cellr->fetch_assoc();
								/*
								foreach($contact_mail[$key] as $mail){
									$maildataq = "INSERT INTO user_email(userId,emailAddr,emailType,emailStatus) VALUES('". $udata["userId"] ."','". $mail ."','Primary','unverified')";
									$_SESSION["mysql"]->query($maildataq);
								}*/
								$qq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
							$qres = $_SESSION["mysql"]->query($qq);
							$fdata = $qres->fetch_assoc();
							
							if($fdata["FriendPhoneCell"] == ""){
							$ufdupdate = "UPDATE user_friend_detail SET FriendPhoneCell='". $csv_parse->data[$key]["Mobile Phone"] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
							$_SESSION["mysql"]->query($ufdupdate);
							}
							if($fdata["FriendPhoneHome"] == ""){
							$ufdupdate = "UPDATE user_friend_detail SET FriendPhoneHome='". $csv_parse->data[$key]["Home Phone"] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
							$_SESSION["mysql"]->query($ufdupdate);
							}
							if($fdata["FriendAnniversary"] == ""){
							$ufdupdate = "UPDATE user_friend_detail SET FriendAnniversary='". $csv_parse->data[$key]["Anniversay"] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
							$_SESSION["mysql"]->query($ufdupdate);
							}
							if($fdata["FriendTitle"] == ""){
							$ufdupdate = "UPDATE user_friend_detail SET FriendTitle='". $csv_parse->data[$key]["Job Title"] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
							$_SESSION["mysql"]->query($ufdupdate);
							}
							if($fdata["FriendPhoneCompany"] == ""){
							$ufdupdate = "UPDATE user_friend_detail SET FriendPhoneCompany='". $csv_parse->data[$key]["Company"] ."' WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
							$_SESSION["mysql"]->query($ufdupdate);
							}
							
									if($csv_parse->data[$key]["Business Street"] != ""){
									$_SESSION["mysql"]->query("INSERT INTO user_friend_address(userId,friendId,friendAddrType,friendStreet,friendCity,friendState,friendZip,friendCountry) VALUES('". $_SESSION["userId"] ."','". $uid ."','office','". $csv_parse->data[$key]["Business Street"] ."','". $csv_parse->data[$key]["Business City"] ."','". $csv_parse->data[$key]["Business State"] ."','". $csv_parse->data[$key]["Business Postal Code"] ."','". $csv_parse->data[$key]["Business Country/Region"] ."')");
							}
							if($csv_parse->data[$key]["Home Street"] != ""){
									$_SESSION["mysql"]->query("INSERT INTO user_friend_address(userId,friendId,friendAddrType,friendStreet,friendCity,friendState,friendZip,friendCountry) VALUES('". $_SESSION["userId"] ."','". $uid ."','home','". $csv_parse->data[$key]["Home Street"] ."','". $csv_parse->data[$key]["Home City"] ."','". $csv_parse->data[$key]["Home State"] ."','". $csv_parse->data[$key]["Home Postal Code"] ."','". $csv_parse->data[$key]["Home Country/Region"] ."')");
							}
							
							}
							else{
							//echo '\n The fname is '.empty($csv_parse->data[$key]["First Name"]).' and lname is '.empty($csv_parse->data[$key]["Last Name"]);
							if( (!empty($csv_parse->data[$key]["First Name"])) AND (!empty($csv_parse->data[$key]["Last Name"])) ) {
								echo "8";
								//no cell matches, make new user
								//Create Array for UserDetail
								$userDetail=array();
								$userDetail["firstName"]=$csv_parse->data[$key]["First Name"];
								$userDetail["middleName"]=$csv_parse->data[$key]["Middle Name"] ;
								$userDetail["lastName"]=$csv_parse->data[$key]["Last Name"] ;
								//print_r($userDetail);
								$new_user = $user->newUser(false,false,$userDetail,'temp');
								$match = $verify->checkImport($csv_parse->data[$key]["First Name"],$csv_parse->data[$key]["Middle Name"],$csv_parse->data[$key]["Last Name"]);
								if($match == true){
									$mterm = "unverified";
								}
								else{
									$mterm = "verified";
								}
								$ufd_insert = "INSERT INTO user_friend_detail(userId,friendId,FriendFirstName,FriendStatusCode,FriendMiddleName,FriendLastName,FriendPhoneCell,FriendPhoneHome,FriendPhoneOffice,FriendAnniversary,FriendTitle,FriendCompany) 
								VALUES('". $_SESSION["userId"] ."','". $new_user ."','". $csv_parse->data[$key]["First Name"] ."','". $mterm ."','". $csv_parse->data[$key]["Middle Name"] ."','". $csv_parse->data[$key]["Last Name"] ."','". $csv_parse->data[$key]["Mobile Phone"] ."','". $csv_parse->data[$key]["Home Phone"] ."','". $csv_parse->data[$key]["Business Phone"] ."','". $csv_parse->data[$key]["Anniversary"] ."','". $csv_parse->data[$key]["Job Title"] ."','". $csv_parse->data[$key]["Company"] ."')";
								$_SESSION["mysql"]->query($ufd_insert);
								foreach($contact_mail[$key] as $mail){
									$maildataq = "INSERT INTO user_email(userId,emailAddr,emailType,emailStatus) VALUES('". $new_user ."','". $mail ."','Primary','unverified')";
									//echo $maildataq;
									$_SESSION["mysql"]->query($maildataq);
									if($csv_parse->data[$key]["Business Street"] != ""){
									$_SESSION["mysql"]->query("INSERT INTO user_friend_address(userId,friendId,friendAddrType,friendStreet,friendCity,friendState,friendZip,friendCountry) VALUES('". $_SESSION["userId"] ."','". $new_user ."','office','". $csv_parse->data[$key]["Business Street"] ."','". $csv_parse->data[$key]["Business City"] ."','". $csv_parse->data[$key]["Business State"] ."','". $csv_parse->data[$key]["Business Postal Code"] ."','". $csv_parse->data[$key]["Business Country/Region"] ."')");
							}
							foreach($contact_mail[$key] as $mail){
								$userfemail = "INSERT INTO user_friend_email(userId,friendId,emailAddr,emailType) VALUES('". $_SESSION["userId"] ."','". $new_user ."','". $mail ."','home')";
								$_SESSION["mysql"]->query($userfemail);
							}
							if($csv_parse->data[$key]["Home Street"] != ""){
									$_SESSION["mysql"]->query("INSERT INTO user_friend_address(userId,friendId,friendAddrType,friendStreet,friendCity,friendState,friendZip,friendCountry) VALUES('". $_SESSION["userId"] ."','". $new_user ."','home','". $csv_parse->data[$key]["Home Street"] ."','". $csv_parse->data[$key]["Home City"] ."','". $csv_parse->data[$key]["Home State"] ."','". $csv_parse->data[$key]["Home Postal Code"] ."','". $csv_parse->data[$key]["Home Country/Region"] ."')");
							}
								}
								$si = "INSERT INTO source_import(userId,sourceName) VALUES('". $new_user ."','mail_client')";
								$_SESSION["mysql"]->query($si);
								
								if($csv_parse->data[$key]["Mobile Phone"] != ""){
																$cell_no_special_chars = preg_replace('(\D+)', '', $csv_parse->data[$key]["Mobile Phone"]);
									$_SESSION["mysql"]->query("UPDATE user_detail_public SET userPhoneCell='". $cell_no_special_chars ."' WHERE userId='". $new_user ."'");
								}
							  } // FNAME,LNAME	
							}
							if(empty($contact_mail[$key]) && $csv_parse->data[$key]["Mobile Phone"] == ""){
								//no email no phone
								$userDetail=array();
								$userDetail["firstName"]=$csv_parse->data[$key]["First Name"];
								$userDetail["middleName"]=$csv_parse->data[$key]["Middle Name"] ;
								$userDetail["lastName"]=$csv_parse->data[$key]["Last Name"] ;
								//print_r($userDetail);
								$new_user = $user->newUser(false,false,$userDetail,'temp');
								$match = $verify->checkImport($csv_parse->data[$key]["First Name"],$csv_parse->data[$key]["Middle Name"],$csv_parse->data[$key]["Last Name"]);
								if($match == true){
									$mterm = "unverified";
								}
								else{
									$mterm = "verified";
								}
								$ufd_insert = "INSERT INTO user_friend_detail(userId,friendId,FriendFirstName,FriendStatusCode,FriendMiddleName,FriendLastName,FriendPhoneCell,FriendPhoneHome,FriendPhoneOffice,FriendAnniversary,FriendTitle,FriendCompany) 
								VALUES('". $_SESSION["userId"] ."','". $new_user ."','". $csv_parse->data[$key]["First Name"] ."','". $mterm ."','". $csv_parse->data[$key]["Middle Name"] ."','". $csv_parse->data[$key]["Last Name"] ."','". $csv_parse->data[$key]["Mobile Phone"] ."','". $csv_parse->data[$key]["Home Phone"] ."','". $csv_parse->data[$key]["Business Phone"] ."','". $csv_parse->data[$key]["Anniversary"] ."','". $csv_parse->data[$key]["Job Title"] ."','". $csv_parse->data[$key]["Company"] ."')";
								$_SESSION["mysql"]->query($ufd_insert);
									//echo $maildataq;
									if($csv_parse->data[$key]["Business Street"] != ""){
									$_SESSION["mysql"]->query("INSERT INTO user_friend_address(userId,friendId,friendAddrType,friendStreet,friendCity,friendState,friendZip,friendCountry) VALUES('". $_SESSION["userId"] ."','". $new_user ."','office','". $csv_parse->data[$key]["Business Street"] ."','". $csv_parse->data[$key]["Business City"] ."','". $csv_parse->data[$key]["Business State"] ."','". $csv_parse->data[$key]["Business Postal Code"] ."','". $csv_parse->data[$key]["Business Country/Region"] ."')");
							}
							if($csv_parse->data[$key]["Home Street"] != ""){
									$_SESSION["mysql"]->query("INSERT INTO user_friend_address(userId,friendId,friendAddrType,friendStreet,friendCity,friendState,friendZip,friendCountry) VALUES('". $_SESSION["userId"] ."','". $new_user ."','home','". $csv_parse->data[$key]["Home Street"] ."','". $csv_parse->data[$key]["Home City"] ."','". $csv_parse->data[$key]["Home State"] ."','". $csv_parse->data[$key]["Home Postal Code"] ."','". $csv_parse->data[$key]["Home Country/Region"] ."')");
							
								}
								$si = "INSERT INTO source_import(userId,sourceName) VALUES('". $new_user ."','mail_client')";
								$_SESSION["mysql"]->query($si);
							}
						}
					 
					}
				}
				
				
				
				}
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
        }
        else{
            $errors = array(1 => "File too large", 2 => "File not text/csv");
            die("Error code ".$integ.": ".$errors[$integ]);
        }
    


@mysqli_close($_SESSION["mysql"]);
?>