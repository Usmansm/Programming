<?php

class user {
		

	public function newUser($userId = false, $sourceData = false, $userDetail = false, $verified = 'unverified', $accountType = 'personal', $email = NULL,$emailStat= 'unverified', $pass = NULL){
		global $config;
		//echo 'user initiated';
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']) or die($mysqli->connect_error);
		//echo 'user step 1';
		if($userId != false){
			//echo 'user step 2';
			//$result = $mysqli->query('REPLACE INTO users (userId,userStatus, accountType, email) VALUES ("'.$userId.'","'.$verified.'", "personal","'.$email.'")') or die($mysqli->error);
   
			//if($result->num_rows > 0){
			//	$mysqli->query('INSERT INTO source_import (userId, sourceUid, sourceAccessToken, sourceName, sourceProfilePicture, sourceProfileLink) VALUES ("'.$userId.'", "'.$sourceData["sourceUid"].'", "'.$sourceData["sourceAccessToken"].'", "'.$sourceData["sourceName"].'", "'.$sourceData["sourceProfilePicture"].'", "'.$sourceData["sourceProfileLink"].'")') or die ($mysqli->error);
			//}
		}
		if($sourceData != false){
			//echo 'user step 3';
			$source = array();
			$result = $mysqli->query('SELECT * FROM source_import WHERE sourceUid = "'.$sourceData["sourceUid"].'"') or die ($mysqli->error);
			if($result->num_rows > 0){
				//echo 'user step 6';
				$sourceData['exists'] == true;
			}
		}
		if($userDetail != false){
		//	echo 'user step 4';
			$result = $mysqli->query('SELECT * FROM user_detail_private WHERE userId = "'.$userId.'"') or die ($mysqli->error);
			$data = $result->fetch_assoc();
			if($result->num_rows != 0){
				//$userDetail['exists'] == true; // code commented because we always wanted to get updated data from source
			}
		}
        $userDetailCheck = false;
		if($userId == false){
			//echo 'user step 5';
			$result = $mysqli->query('INSERT INTO users (userStatus, accountType, email, userPassword) VALUES ("'.$verified.'", "personal", "'.$email.'", "'.$pass.'")') or die($mysqli->error);
     // echo 'test';
			$userId = $mysqli->insert_id;
    //  echo 'test2';
      $userDetailCheck = true;
		}
		if($sourceData != false && $sourceData['exists'] != true){
			//echo 'user step 7';
		//	var_dump($sourceData);
			$mysqli->query('INSERT INTO source_import (userId, sourceUid, sourceAccessToken, sourceName, sourceProfilePicture, sourceProfileLink) VALUES ("'.$userId.'", "'.$sourceData["sourceUid"].'", "'.$sourceData["sourceAccessToken"].'", "'.$sourceData["sourceName"].'", "'.$sourceData["sourceProfilePicture"].'", "'.$sourceData["sourceProfileLink"].'")') or die ($mysqli->error);
   		echo 'INSERT INTO source_import (userId, sourceUid, sourceAccessToken, sourceName, sourceProfilePicture, sourceProfileLink) VALUES ("'.$userId.'", "'.$sourceData["sourceUid"].'", "'.$sourceData["sourceAccessToken"].'", "'.$sourceData["sourceName"].'", "'.$sourceData["sourceProfilePicture"].'", "'.$sourceData["sourceProfileLink"].'")';
   		
		}
		
		if($sourceData != false){
		$mysqli->query('INSERT INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$userId.'", "'.$sourceData["sourceUid"].'", "'.$sourceData["sourceAccessToken"].'", "'.$sourceData["sourceName"].'")') or die ($mysqli->error);
		}
		
		if($userDetail != false && $userDetail['exists'] != true){
		$userDetailCheck = true; // always want to insert data
            if($userDetailCheck){
    		//echo 'user step 8';
    			$mysqli->query('REPLACE INTO user_detail_private (userId, firstName, middleName, lastName) VALUES ("'.$userId.'", "'.$userDetail["firstName"].'", "'.$userDetail["middleName"].'", "'.$userDetail["lastName"].'")');	
                $mysqli->query('REPLACE INTO user_detail_public (userId, firstName, middleName, lastName,userPhoneCell) VALUES ("'.$userId.'", "'.$userDetail["firstName"].'", "'.$userDetail["middleName"].'", "'.$userDetail["lastName"].'","'.$userDetail["phoneCell"].'")') or die ($mysqli->error);
            }
		}
        if($email != NULL){
			 //$result = $mysqli->query('SELECT * FROM user_email WHERE emailAddr = "'.$email.'"');
			 //if($result->num_rows == 0) // check that email exist update its status
			 
			 //{
            $mysqli->query('Insert INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("'.$userId.'", "'.$email.'", "Primary", "'.$emailStat.'")');
			
			// }
			// else {
			//	 $_SESSION['error2'] == 'Email found : '.$email;
			 //}
        }
        return $userId;
							
	}
	
	

    public function manualUser($firstName, $lastName, $email, $pass1, $pass2,$passcode){
	$sendemail="yes";
	$userId='';
        $data = array();
        $data['firstName'] = $firstName;
        $data['lastName'] = $lastName;
        $data['middleName'] = '';
        $data['email'] = $email;
		$data['pass'];
        if($email == $pass2){
            $data['pass'] = hash('sha256', $pass1);
        }
        $email = new email;
        $sanitize = new sanitize;
        echo 1;
		if($passcode!="betamib_2013")
		{
		return 'beta';
		}
        if($data == false){
            return 'There was an error creating your account';
        }
        if($data == 'Emailadress is already known in the system'){
            return 'Emailadress is already known in the system';
        }
        echo 2;
		global $config;
		//echo 'user initiated';
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']) or die($mysqli->connect_error);
		// To check if the user Email exist Already
	$result = $mysqli->query('SELECT * FROM user_email WHERE LOWER(emailAddr) = "'.strtolower($data['email']).'"') or die ($mysqli->error);
			if($result->num_rows > 0){
			$dataUser = $result->fetch_assoc();
			
			$emailst=$mysqli->query("select * from users where userId=".$dataUser['userId']) or die($mysqli->error);
			if($emailst->num_rows > 0){
			//We have a User with this Email
			$emailsst=$emailst->fetch_assoc();
			if($emailsst['userStatus']=="active"){
			$sendemail="no";
			echo "emailExist";
			}
			else {	
				
			$sendemail="yes";
			
			//$userId = $this->newUser($emailsst['userId'], false, $data, 'pending', '3', $data['email'], $data['pass']);
			$userId =$emailsst['userId'];
			$mysqli->query('UPDATE users SET userPassword = "'.$data['pass'].'" WHERE userId = "'.$userId.'"') or die ($mysqli->error);
	
			echo "notVerified";
			}
			}
			/* $data = $result->fetch_assoc();
			$s="pending1";
				$mysqli->query('UPDATE users SET userStatus = "'.$s.'" WHERE userId = "'.$data['userId'].'"') or die ($mysqli->error);
			$userId=$data['userId']; */
			}
		else
		{
        $userId = $this->newUser(false, false, $data, 'pending', '3', $data['email'],'pending',$data['pass']);
		$sendemail="yes";
		}
		if($sendemail=="yes")
		{
		
         $urlCreator = new urlCreator;
         $cookie = new cookie;
         $hash = $cookie->hashString(15);
		
         $link = $urlCreator->verifyEmail($userId, $hash);
         echo 3;
         $mail_body = array(
            'text/plain' => 
            'Myiceberg Registration Confirmation Email

            Welcome to Myiceberg '.$firstName.',
            
            Congratulations on your recent registration with Myiceberg.
            
            To complete the registration, please click this link:
            '.$link.'  
            Thank you joining Myiceberg,
            
            The MIB Team',
            
            'text/html' => 
            '<div id="backgorund">
                <div id="title"> Myiceberg Registration Confirmation Email </div>
                 
                    <div id="messsge">
                         <img alt="Logoupperright-original" src="http://assets.postageapp.com/000/002/002/logoUpperRight-original.jpg" id="logo"/>
                      <br>
                      <br>
                        <div id="subject"> Welcome to Myiceberg '.$firstName.', </div>
                        
                        <div id="messsge2">Congratulations on your recent registration with Myiceberg.<br>
                            The final step in completing your registration is activating your email by clicking the button below.</div>
                      <a href="'.$link.'"><img alt="Buttonforregistrationemail-original" src="http://assets.postageapp.com/000/002/003/buttonForRegistrationEmail-original.png" id="RegistrationButton"/></a>
                        <div id="messsge3">You can also copy and paste this link to your browser instead:<br> '.$link.'  </div>
                        
                        
                        <br />
                      <br>
                        <div id="footer">Thank you joining Myiceberg,<br />The MIB Team</div>
                    </div>
                </div>'
         );
        echo 4;
        $data = $email->send($data['email'], 'Myiceberg Confirmation Email', $mail_body, $config["headerMail"]);
        print_r($data);
        echo 5;
		$_SESSION['notify'] = 3;
        return true;
		}
         
    }
    
	public function merge($master, $child) {
        $_SESSION['error'] = 'MERGE TRIGGERED';
		//echo 'Merge Triggered';
		global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$_SESSION["cerror"][1] = $mysql->error;
		$mysqli->query('UPDATE different_users SET	userId = "'.$master.'" WHERE userId = "'.$child.'"');
		$_SESSION["cerror"][2] = $mysql->error;
		$mysqli->query('UPDATE source_import SET userId = "'.$master.'" WHERE userId = "'.$child.'"');
		$_SESSION["cerror"][3] = $mysql->error;
		$mysqli->query('UPDATE userfrnd_source SET	userId = "'.$master.'" WHERE userId = "'.$child.'"');
		$_SESSION["cerror"][4] = $mysql->error;
		$mysqli->query('UPDATE userfrnd_source SET	friendId = "'.$master.'" WHERE friendId = "'.$child.'"');
		$_SESSION["cerror"][5] = $mysql->error;
		//$mysqli->query('UPDATE users SET userId = "'.$master.'" WHERE userId = "'.$child.'"');
		$_SESSION["cerror"][6] = $mysql->error;
		$mysqli->query('UPDATE user_categories SET userId = "'.$master.'" WHERE userId = "'.$child.'"');
		$_SESSION["cerror"][7] = $mysql->error;
        $mysqli->query('UPDATE userfrnd_source SET userId = "'.$master.'" WHERE userId = "'.$child.'"');
		$_SESSION["cerror"][8] = $mysql->error;
        $mysqli->query('UPDATE user_friend_detail SET userId = "'.$master.'" WHERE userId = "'.$child.'"');
		$_SESSION["cerror"][9] = $mysql->error;
		$mysqli->query('UPDATE user_default_monitor_terms SET userId = "'.$master.'" WHERE userId = "'.$child.'"');
		$_SESSION["cerror"][10] = $mysql->error;
		$mysqli->query('UPDATE user_detail_private SET userId = "'.$master.'" WHERE userId = "'.$child.'"');
		$_SESSION["cerror"][11] = $mysql->error;
		$mysqli->query('UPDATE user_detail_public SET userId = "'.$master.'" WHERE userId = "'.$child.'"');
		$_SESSION["cerror"][12] = $mysql->error;
		$mysqli->query('UPDATE user_friend_family_details SET userId = "'.$master.'" WHERE userId = "'.$child.'"');
		$_SESSION["cerror"][13] = $mysql->error;
		$mysqli->query('UPDATE user_friend_family_details SET friendId = "'.$master.'" WHERE friendId = "'.$child.'"');
		$_SESSION["cerror"][14] = $mysql->error;
		$mysqli->query('UPDATE user_hash SET userId = "'.$master.'" WHERE userId = "'.$child.'"');
		$_SESSION["cerror"][15] = $mysql->error;
		$mysqli->query('UPDATE user_identity_count SET	userId_1 = "'.$master.'" WHERE userId_1= "'.$child.'"');
		$_SESSION["cerror"][16] = $mysql->error;
		$mysqli->query('UPDATE user_identity_count SET	userId_2 = "'.$master.'" WHERE userId_2= "'.$child.'"');
		$_SESSION["cerror"][17] = $mysql->error;
		$mysqli->query('UPDATE user_external_accnt SET	userId = "'.$master.'" WHERE userId= "'.$child.'"');
		$_SESSION["cerror"][18] = $mysql->error;
		$updatequery = "UPDATE user_friend_detail SET friendId='". $master ."' WHERE friendId='". $child ."'";  
        $execquery = $mysqli->query($updatequery);
		$updatequery1 = "UPDATE user_email SET userId='". $master ."' WHERE userId='". $child ."'";  
        $execquery1 = $mysqli->query($updatequery1);
	}
	public function updateLastLogin($id) {
       
		global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$updatequery = "UPDATE user_detail_private SET lastLoginDate=DEFAULT WHERE userId='". $id ."'";
		$execquery = $mysqli->query($updatequery) or die(mysql_error());
			
		echo 'Login time updated called '.$updatequery ;
		
	}
}

?>