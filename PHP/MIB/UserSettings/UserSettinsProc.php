<?php
/*This file is used as background php for page UserSettings.php need to rename it properly later*/
SESSION_START();
require_once('../config/config.php');
require_once('../php/class/friend.class.php');
require_once('../php/class/fb.class.php');
require_once('../php/class/li.class.php');
require_once('../php/class/import.class.php');
require_once('../php/class/default.class.php');
require_once('../php/class/email.class.php');
require_once('../php/class/urlCreator.class.php');
require_once('../php/class/cookie.class.php');
 $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
  
  
  class base{
      public function __construct(){
          global $config,$mysql;
          $_SESSION["mysql"] = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);;
      }
      
      public function get_user_info($uid){
          $query = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
          $res = $_SESSION["mysql"]->query($query);
          $data = $res->fetch_assoc();
          
          return $data;
      }
      
      public function send_verify_mail($email){
          
      }
      
      public function __destruct(){
          mysqli_close($_SESSION["mysql"]);
      }
  }
/* Function For encryption */
 function simple_encrypt($text,$salt)
    {
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }
  
  
	
/*function which is user to displaying emial information mklmlk*/
function DispEmailInfo($UserId){
global $config ;
  $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
    $query = "SELECT * FROM user_email WHERE userId = '".$UserId."' ORDER BY createdOn"; 
    $result = $mysqli->query($query);

    $i = 0;
    
    $EmailHolder = '';
   
    while ($row = $result->fetch_assoc()) {
    
         $i = $i + 1;
      
      $emailType = $row['emailType'];
      
      if ($emailType == '1' ){
        $emailType = 'Primary Email';
      }else{
        $emailType = 'Email ' . $emailType;
      }
	  
      if(($row['NotificationEmail'] !=  null) AND ($row['NotificationEmail'] !=  '0')){
		$checked = 'checked="checked"';
	  }else{
		$checked = '';
	  }
      $EmailHolder = $EmailHolder . '<div class="part_infotext">
            <div class="Upart_infotextb"><span class="UserInfoEmial">'.$emailType.':</span></span><span class="UserEmail"  id="Eholder'.$i.'">'.$row['emailAddr'].'</span><span class="UEmailStatus">'.$row['EmailStatus'].'</span>
			<span class="EmailCheckbox"> 
				<input type="radio" class="EmailRadiobutton" id="NotificationEmail'.$i.'" onclick="NotificationEmail('.$i.','.$UserId.')" '.$checked.' />
			</span><a href="#" onclick="DeleteEmail('.$UserId.', '.$i.')" class="Email_edit_button" id="EditEmailB'.$i.'">Delete Email</a></div>
        </div>';
    }
    $EmailHolder = $EmailHolder . '<div class="part_infotext">
<div class="Upart_infotextb"><span class="UserAddEmiail">Additional Emails <input type="text" id="inputEmail"></input><button id="AddEmialButton" onclick="emailButton('.$UserId.')">Add Email</button></div>
</div>
</div>

<div id="Buttons" > 
<div id="CancelAndSaveButton">
<button class="CancelAndSaveButton" value="Cancel">Cancel</button>
<button class="CancelAndSaveButton" value="Save">Save</button> 
</div>
</div>'; 

	echo $EmailHolder;
}

/*code for editiong email*/
if (isset($_GET['DeleteEmail'])){
	$EAddr = $_GET['DeleteEmail'];
	$Userid = $_GET['id'];
	$Eordinal = $_GET['Eordinal'];

	$sql = "SELECT * FROM user_email WHERE userId = '".$Userid."' AND EmailStatus = 'verified' AND emailAddr != '".$EAddr."'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
 
	if ($row === null){
		echo "false";
	}else{
		$sql2 = "DELETE FROM user_email WHERE userId = '".$Userid."' AND emailAddr = '".$EAddr."'";
		$result2 = $mysqli->query($sql2);
		
		//var_dump($sql2);
		//var_dump($result2);
		
		DispEmailInfo($Userid);
	}
 
}
/* Code for adding email www*/
    if (isset ($_GET['addEmail'])){
        $Id = $_GET['id'];
        $emialAdd = $_GET['addEmail'];
        /*  plan
        set that emial is unverified
        insert e-mail into datablase 
        call function DispEmailInfo
        */
        //$query = "SELECTuser_email FROM user_email WHERE userId = '". $Id ." ORDER BY emailType'"; 

		if(filter_var($emialAdd, FILTER_VALIDATE_EMAIL)){
       
		$sendmail="";
		$newMail="";
		// select all frim user_email
        $result = mysqli_query($mysqli,'SELECT * FROM user_email WHERE userId = "'.$Id.'" ORDER BY emailType');
        $row = $result->fetch_assoc();
        $emailnum = mysqli_affected_rows($mysqli) + 1;
		
        $checkStatus = mysqli_query($mysqli,'SELECT * FROM user_email WHERE emailAddr = "'.$emialAdd.'"');
		$status = $checkStatus->fetch_assoc();
		
			if(isset($status['EmailStatus']) && $status['EmailStatus'] == "verified"){
			
					
					if($_SESSION['userId']!=$status['userId']){
					
						
						echo "<h5>This email is verified by another user. If you believe this is your email address please contact Support@
myiceberg.com.</h5>";
						
					}else{
						if($status['EmailStatus']!="verified"){
							$sendmail="yes";
						}
						
					}
				
			
			}else if(! isset($status["EmailStatus"])){
				$sendmail="yes";
				$newMail="yes";
				
			}
              else if($status["EmailStatus"] == "pending" OR $status["EmailStatus"] == "unverified" ){
			 // echo 'condition hit';
                  $sendmail = "yes";
                  $newMail ="no";
                  $query = "UPDATE user_email SET EmailStatus='pending' WHERE userId='". $status["userId"] ."' AND emailAddr='". $status["emailAddr"] ."'";
                  $res = $mysqli->query($query);
              }
			if($sendmail=="yes"){
					
				$email = new email;
                $query = "SELECT * FROM  user_detail_private WHERE userId='". $_SESSION["userId"] ."'";
                $res = $mysqli->query($query);
                $dat = $res->fetch_assoc();
                $firstName = ucfirst($dat["firstName"]);
				//$urlCreator = new urlCreator;
				// $cookie = new cookie;
				// $hash = $cookie->hashString(15);
				// $key_value = "MIB"; 
				$plain_text = $emialAdd; 
				
				
				$encrypted_text=base64_encode($plain_text);
				//echo $encrypted_text;
				
				// $link = $config['root']."verify_email.php?userEmail=".$encrypted_text;
				 //echo $link;
				  $urlCreator = new urlCreator;
         $cookie = new cookie;
         $hash = $cookie->hashString(15);
		 $userId=$_SESSION['userId'];
         $link = $urlCreator->verifyEmail($userId, $hash,$plain_text);
				 $mail_body = array(
					  'text/plain' => 
            'Myiceberg Registration Confirmation Email

           Thank you for adding an additional email to your Myiceberg account. Please verify the email provided by clicking on the 
button.

        ',
            
            'text/html' => 
            '<div id="backgorund">
                <div id="title"> Myiceberg Registration Confirmation Email </div>
                 
                    <div id="messsge">
                         <img alt="Logoupperright-original" src="http://assets.postageapp.com/000/002/002/logoUpperRight-original.jpg" id="logo"/>
                      <br>
                      <br>
                        <div id="subject"> New Email Account Added'.$firstName.', </div>
                        
                        <div id="messsge2">Thank you for adding an additional email to your Myiceberg account.<br>
                            The final step in completing your registration is activating your email by clicking the button below.</div>
                      <a href="'.$link.'"><img alt="Buttonforregistrationemail-original" src="http://assets.postageapp.com/000/002/003/buttonForRegistrationEmail-original.png" id="RegistrationButton"/></a>
                        <div id="messsge3">You can also copy and paste this link to your browser instead:<br> '.$link.'  </div>
                        
                        
                        <br />
                      <br>
                        <div id="footer">Thank you joining Myiceberg,<br />The MIB Team</div>
                    </div>
                </div>'
         );
				 
				
				$data = $email->send($emialAdd, 'Myiceberg Confirmation Email', $mail_body,$config["headerMail"]);
			}
			if($newMail=="yes"){
				$result = mysqli_query($mysqli,'SELECT * FROM user_email WHERE userId = "'.$Id.'" ORDER BY emailType');
				$row = $result->fetch_assoc();
				$emailnum = mysqli_affected_rows($mysqli) + 1;
				
				//$sql = "SELECT * user_email "
				mysqli_query($mysqli,'INSERT INTO user_email (userId, emailAddr, emailType, EmailStatus) VALUES("'.$Id.'","'.$emialAdd.'","'.$emailnum.'","pending")');
			}
        DispEmailInfo($Id);
	}else{
		echo "false";
	
	}
    }

	
	/* Code for adding notifications www*/
    if (isset ($_GET['NotificationId'])){
        $Id = $_GET['UserId'];
        $nid = $_GET['NotificationId'];
        

		$query="REPLACE INTO user_notify_detail SET userId =".$Id." , notify_detail= '".$nid." '";
        
		$result = mysqli_query($mysqli,$query) or die(mysql_error());
		$_SESSION["notify"]=$nid;
		//mysqli_query($mysqli,"INSERT INTO 'test' ('id') VALUES ('1')") or die(mysql_error());
		//$result=$mysqli->query("INSERT INTO test (id) VALUES ('1')") or die(mysql_error());
		
		
		//var_dump($result);
        
		
            
    }
	
	
	

/* Code for showing phone and address when dropdown is seleted"'.$id.'"'*/
if (isset ($_GET['PhoneorAdd'])){
  $PorAdd = $_GET['PhoneorAdd']; // is it request for Phone or address
  $id = $_GET['id'];// user ID 
 // $ordinal = "userPhone".$_GET['ordinal']; // is it Phone 1 or Phone 2 
  
  if ($PorAdd == 'Phone'){
    $ordinal = "userPhone".$_GET['ordinal'];
    $result = $mysqli->query('SELECT * FROM user_detail_public WHERE userId = "'.$id.'"'); 
    $row = $result->fetch_assoc(); 
    echo $row[$ordinal];
  }
  
  if ($PorAdd == 'Address'){
    $result = $mysqli->query('SELECT * FROM user_detail_public WHERE userId = "'.$id.'"'); 
    $row = $result->fetch_assoc(); 
	 $ordinal = $_GET['ordinal'];
    if ($ordinal == '1' ){
		
		// $ordinal = 'addressOne,cityOne,stateOne,zipOne,countryOne';
		$address = $row['addressOne'];
		$city = $row['cityOne'];
		$state = $row['stateOne'];
		$zip = $row['zipOne'];
		$country = $row['countryOne'];
    }
    
    if ($ordinal == '2' ){
		//$ordinal = 'addressTwo,cityTwo,stateTwo,zipTwo,countryTwo';
		$address = $row['addressTwo'];
		$city = $row['cityTwo'];
		$state = $row['stateTwo'];
		$zip = $row['zipTwo'];
		$country = $row['countryTwo'];
      
    }
    
echo 'Address: <span class="ffname" id="UserAddress">'. $address . '</span><br />
<span class="UTitle">City:</span><span class="ffname" id="UserCity">'. $city . '</span>
<span class="UTitle">State:</span> <span class="ffname" id="UserState">'. $state . '</span><br />
<span class="UTitle">Zip:</span> <span class="ffname" id="UserZip">'. $zip . '</span> 
<span class="UTitle">Country:</span><span class="ffname" id="UserCountry">'. $country . '</span>';
    
  }
  
}

/* Code for inserting USer Info in DB and dispaying it back*/   
if (isset($_GET['PhNum'])){
	$UserId = $_GET['UserId'];
	$FirstName = $_GET['FN'];
	$MddileName = $_GET['MN'];
	$LastName = $_GET['LN'];
	$DisplayName = $_GET['DN'];
	$UserPhone = $_GET['UP'];
	$UserTitle = $_GET['UTI'];
	$UserCompany =  $_GET['UCI'];
	$UserAddress =  $_GET['UA'];
	$UserCity =  $_GET['UC'];
	$UserState =  $_GET['US'];
	$UserZip =  $_GET['UZip'];
	$PhoneOrdinal =  $_GET['PhNum'];
	$AddOrdinal =  $_GET['AddNum'];
	$udob= $_GET['udob'];
	$Country = $_GET['CountryDrop'];
  
  
	$query_private = "UPDATE user_detail_private SET firstName='". $FirstName ."', middleName='". $MddileName . "', lastName='". $LastName . "', displayName='". $DisplayName . "' WHERE userId='". $UserId ."'";
	$result = $mysqli->query($query_private);

	$query_public1 = "UPDATE user_detail_public SET firstName='". $FirstName ."', middleName='". $MddileName . "', lastName='". $LastName . "', companyName='". $UserCompany . "', bornOn='". $udob . "' , companyTitle='". $UserTitle . "' WHERE userId='". $UserId ."'";
	$result = $mysqli->query($query_public1);
  

    $query_public2 = "UPDATE user_detail_public SET userPhone".$PhoneOrdinal."='". $UserPhone ."' WHERE userId='". $UserId ."'";
    $result = $mysqli->query($query_public2);
    //echo $query_public1;
    $Pselected1 = '';
    $Pselected2 = '';
    $Pselected3 = '';
    
        if ($PhoneOrdinal == 'Cell'){
            $Pselected1 = 'selected';
        } else if ($PhoneOrdinal == 'Home'){
            $Pselected2 = 'selected';
        }else if ($PhoneOrdinal == 'Office'){
            $Pselected3 = 'selected';
        }

  
  
  if($AddOrdinal == '1'){
    $query_public3 = "UPDATE user_detail_public SET addressOne='". $UserAddress ."', cityOne='". $UserCity ."', stateOne='". $UserState ."', zipOne='". $UserZip ."', countryOne='".$Country."' WHERE userId='". $UserId ."'";  
    $result = $mysqli->query($query_public3);
    $Aselected1 = 'selected';
    $Aselected2 = '';
  }
  
  if ($AddOrdinal == '2'){
    $query_public3 = "UPDATE user_detail_public SET addressTwo='". $UserAddress ."', cityTwo='". $UserCity ."', stateTwo='". $UserState ."', zipTwo='". $UserZip ."', countryTwo='".$Country."' WHERE userId='". $UserId ."'";
    $result = $mysqli->query($query_public3);
    $Aselected1 = '';
    $Aselected2 = 'selected';
  }
   /*CODE FOR RETURNING COUNTRY DROP DOWN MENY*/
	$queryForCountry = "SELECT * FROM country_name";
	$resultForCountry = $mysqli->query($queryForCountry);
  $countryDD = '<select id="Country_drop">';
while ($row = $resultForCountry->fetch_assoc()){
	$countryDD = $countryDD . "<option value=".$row['countryName'].">" . $row['countryName'] . "</option>";
} 
$countryDD = $countryDD . "</select>";
  
  
  $ResponseText = '
	<div class="part_title">
		<span class="UserSettingsPartTitletext">User Information </span>
		<span class="aboutInfo">(<a href="www.aboutUserInfo.com"><u>About User Info</u></a>) </span>
		<span id="smallicon" ><a href="#" class="detail_edit_button"  onclick="EditUsettings('.$UserId.')" id="UinfoEditbutton">Edit</a></span>
		<span id="minus" ><img id="UInfominus" onclick="hideUserInfo()" src="images/-.png"></img></span>
	</div>	

	<div class="UserPart_infol">
		<div class="part_infotext"> 
			<div class="Upart_infotextb">First Name: <span class="ffname" id="FirsteName">'.$FirstName.'</span></div>
		</div>	
		<div class="part_infotext"> 
			<div class="Upart_infotextb">Middle Name: <span class="ffname" id="MiddleName">'.$MddileName.'</span></div>
		</div>
		<div class="part_infotext"> 
			<div class="Upart_infotextb">Last Name: <span class="ffname" id="lastname">'.$LastName.'</span></div>
		</div>	
		<div class="part_infotext"> 
			<div class="Upart_infotextb">Display Name: <span class="ffname" id="DisplayName">'.$DisplayName.'</span></div>
		</div>  

		<div class="part_infotext"> 
			<div class="Upart_infotextb"> Date of Birth: <span id="_birthday" class="ffname"></span>'.$udob.'</span></div>
		</div>	
	</div>

<div class="UserPart_infor">
	<div class="part_infotext">
	<span class="Upart_infotextb">Phone: <span class="ffname" id="UserPhone">'.$UserPhone.'</span>
	<select class="detail_drop" id="PhoneSelect" onchange=\'AddressAndPhoneDropDown("Phone", '.$UserId.' )\'>
		<option value="Cell" '.$Pselected1.'  value="Cell">Cell</option>
		<option value="Home" '.$Pselected2.' value="Home">Home</option>
		<option value="Office" '.$Pselected3.' value="Office">Office</option>
	</select>
	</div>

	<div class="part_infotext"> 
		<div class="Upart_infotextb">Company: <span class="ffname" id="UserCompany">'.$UserCompany.'</span></div>
	</div>
	<div class="part_infotext">
		<div class="Upart_infotextb">Title: <span class="ffname" id="UserTitle">'.$UserTitle.'</span></div>
	</div>
	<div class="part_infotext">                         
		  <select class="detail_drop" id="AddressSelet" onchange=\'AddressAndPhoneDropDown("Address", '.$UserId.' )\'>
			<option  value="1" '.$Aselected1.'>Address 1</option>
			<option  value="2" '.$Aselected2.'>Address 2</option>
		  </select>
		<div class="Upart_infotextb" id="AddressHolder">Address: <span class="ffname" id="UserAddress">'.$UserAddress.'</span><br />
			<span class="UTitle">City:</span><span class="ffname" id="UserCity">'.$UserCity.'</span>
			<span class="UTitle">State:</span> <span class="ffname" id="UserState">'.$UserState.'</span><br />
			<span class="UTitle">Zip:</span> <span class="ffname" id="UserZip">'.$UserZip.'</span> 
			<span class="UTitle">Country:</span><span class="ffname" id="UserCountry">'.$Country.'</span>   
		</div>
		'.$countryDD.'
	</div>
</div>';
  
  echo $ResponseText;
  
  
  
}

/*code for editing Terms*/

if (isset($_GET['EditTerm'])){
	$term = $_GET['EditTerm'];
	$Tid = $_GET['Tid'];
	if($term != "Add new term"){
	$result = $mysqli->query('UPDATE user_monitor_terms SET termName="'.$term.'"  WHERE userId="'.$_SESSION["userId"].'" AND termId = "'.$Tid.'"');
	}
	echo $term;
}

/*Code for saveing and editing password*/
if (isset($_GET['ChangeCurrentPassword'])){

	$password1 = $_GET['PasswordInput1'];
	$password2 = $_GET['PasswordInput2'];
	
	if ($password1 === $password2 ){
		
		if (ctype_alnum($password1)){
			if ((strlen($password1) >= 5) and (strlen($password1) <= 30 )){
				//$password1 = mysql_real_escape_string($password1); // sicne we are useing hash we do not need real escape string ...
				$password1 = hash(sha256,$password1);
				$query = "UPDATE users SET userPassword = '".$password1."' WHERE userId= '".$_SESSION["userId"]."'";
				$result = $mysqli->query($query);				
				echo "********** <span style='font-size:11px;'> You changed your password </span>";
				
			}else {
				echo"<span style='font-size:11px; color:red;'>ERROR: Password not changed it, must be between 6-30 chatacters long</span>";
			}
		
		}else{
			echo"<span style='font-size:11px; color:red;'>ERROR: Password not changed it, can contain only letters and nubmers</span>"; 
		}
		// inser data into the DB and display info
	}else {
		echo"<span style='font-size:11px; color:red;'>ERROR: Password not changed it, ctwo passwords do not match</span>"; 
	}
}

/*Code for editing User Account Type*/
if (isset($_GET['acctype'])){
	$acctype = $_GET['acctype'];
	$acctypelower = strtolower($acctype);
	
	$currentacctype = $_GET['currentacctype'];
	$currentacctype = strtolower($currentacctype);

		// IF user is switching  form eprsonal to higher lvl of account then it will be better
		if (($acctypelower != 'personal')  AND ($currentacctype == 'personal') ){
		$sql = "SELECT * FROM user_emai WHERE userId = '".$_SESSION["userId"]."' AND NotificationEmail = '1'";
		$resultmail = $mysqli->query($sql);
		
		// if there is no verification email search for primary email
		if ($resultmail->num_rows === null ){
			$sql2 = "SELECT `emailAddr` FROM user_email WHERE userId = '".$_SESSION["userId"]."' AND emailType = 'Primary' LIMIT 1";
			
			$result2 = $mysqli->query($sql2);
			$resilt2_row = $result2->fetch_assoc();
			//var_dump($result2->num_rows);
				// if there is no primary email chanhe first possible email to nitification email
				
				if($result2->num_rows == null){
					$sql3 = "SELECT `emailAddr` FROM user_email WHERE userId = '".$_SESSION["userId"]."' ORDER BY emailType LIMIT 1";
					$result3 = $mysqli->query($sql3);
					$resilt3_row = $result3->fetch_assoc();
					
					$sql4 = "UPDATE user_email SET NotificationEmail = '1' WHERE emailAddr = '".$resilt3_row['emailAddr']."' AND userId = '".$_SESSION["userId"]."'";
					$result4 = $mysqli->query($sql4);
					
				}else{
					// if there is primaery email update this mail to notification email.

					$sql4 = "UPDATE user_email SET NotificationEmail = '1' WHERE emailAddr = '".$resilt2_row['emailAddr']."' AND userId = '".$_SESSION["userId"]."'";
					$result4 = $mysqli->query($sql4);
				}

				$queryFinal = "UPDATE users SET accountType = '".$acctype."' WHERE userId= '".$_SESSION["userId"]."'";
				$resultFinal = $mysqli->query($queryFinal);
				if ($result4 === true and $resultFinal === true){
					echo $acctype;  
				}else{
					echo 'Account is not updataed we hace problem whi DB'; 
				}
				
				
			//$sql = "UPDATE user_emai set NotificationEmail";
			//var_dump($resultmail->num_rows);

		}else{
			$queryFinal = "UPDATE users SET accountType = '".$acctype."' WHERE userId= '".$_SESSION["userId"]."'";
			$resultFinal = $mysqli->query($query);
			echo $acctype; 
		}
	}else{
		$queryFinal = "UPDATE users SET accountType = '".$acctype."' WHERE userId= '".$_SESSION["userId"]."'";
		$resultFinal = $mysqli->query($queryFinal);
		echo $acctype;
	}
}

/* Changeing notification mail UserSettings Page */
if (isset($_GET['ChangeNotificationEmail'])){
	$EmailType = $_GET['ChangeNotificationEmail'];
	if ($EmailType == '1'){
		$EmailType  = 'Primary';
	}
	$query2 = "UPDATE user_email SET NotificationEmail = '1' WHERE userId = '".$_SESSION["userId"]."' AND  emailType = '".$EmailType."' AND EmailStatus = 'verified'";
	$result2 = $mysqli->query($query2);	
	$num_of_rows = $mysqli->affected_rows;
	
	if ($num_of_rows === 0 ){
		echo '2';
	}else{
		$query = "UPDATE user_email SET NotificationEmail = NULL WHERE userId = '".$_SESSION["userId"]."' AND  NotificationEmail = '1' AND emailType != '".$EmailType."'";
		$result = $mysqli->query($query);	
		echo '1';
	}
	
}



























?>