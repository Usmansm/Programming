<?php
@ session_start();
class li {
	
	public function fetch($resource, $params, $body = ''){ 
		$url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
		$context = stream_context_create(
						array('http' => 
							array('method' => 'GET',
							)
						)
					);
		$response = file_get_contents($url, false, $context);
		return json_decode($response);
	}
	
	public function getAccessToken($redirectUrl) {
		global $config;
		$params = array('grant_type' => 'authorization_code',
						'client_id' => $config['linkedin_key'],
						'client_secret' => $config['linkedin_secret'],
						'code' => $_GET['code'],
						'redirect_uri' => $redirectUrl
				  );
				  
				 // var_dump($params);
		$url = 'https://www.linkedin.com/uas/oauth2/accessToken?' . http_build_query($params);
		$context = stream_context_create(
						array('http' => 
							array('method' => 'POST',
							)
						)
					);
		$response = file_get_contents($url, false, $context);
		$token = json_decode($response);
		return $token->access_token;
	}
	
	public function getAuthorizationCode($redirectUrl) {
			global $config;
			$params = array('response_type' => 'code',
							'client_id' => $config['linkedin_key'],
							'scope' => $config['linkedin_scope'],
							'state' => uniqid('', true),
							'redirect_uri' => $redirectUrl,
					  );
			$url = 'https://www.linkedin.com/uas/oauth2/authorization?' . http_build_query($params);
			$_SESSION['state'] = $params['state'];
			header("Location: $url");
			exit;
	}
	public function login(){
	$idlitemp='';
		$liUser='';
		$liArrayUser=array();
		$accesstoken='';
		$userData=array();
		$idfromSimport='';
		$emailAdd='';
		global $_SESSION;
		$code = true;
		if(isset($_SESSION['userId'])){
			$userId = $_SESSION['userId'];
		}
		else {
			$userId = false;
		}
		if(isset($_SESSION['login_source'])){
			$source = $_SESSION['login_source'];
		}
		else {
			$source = false;
		}
			
		global $config;
		$url = new urlCreator;
		$redirectUrl = $url->linkedinRedirect();		 
		if($_GET['type'] != 'linkedinlogin'){ 
			$_SESSION['redirect_linkedin'] = true;
			$this->getAuthorizationCode($redirectUrl);
		}
		if (isset($_GET['code']) && $code == true) {
			$_SESSION['redirect_linkedin'] = true;
			$accesstoken = $this->getAccessToken($redirectUrl);
			$code = false;
		}
		$params = array('oauth2_access_token' => $accesstoken,
						'format' => 'json');
		$liArrayUser = $this->fetch('/v1/people/~:(first-name,last-name,headline,picture-url,id,email-address,public-profile-url)', $params);
		
		/// My Updated Linkedin Code
		//print_r($liArrayUser );
		$liUser=$liArrayUser->{'id'};
		$emailAdd=$liArrayUser->{'emailAddress'};
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$result = $mysqli->query("SELECT * FROM source_import WHERE sourceUid = '".$liUser."'");
		
		if($result->num_rows > 0){
		$data1 = $result->fetch_array();
		$idfromSimport=$data1['userId'];
		 $resultEmail = $mysqli->query('SELECT * FROM user_email WHERE LOWER(emailAddr) = "'.strtolower($emailAdd).'"');
		if($resultEmail->num_rows > 0){
			$dataEmail = $resultEmail->fetch_array();
			//Email Exists
			$idlitemp=$dataEmail['userId'];
			
	
			//echo 'login';
			//Old User
			//FBID exists
			
 
			
			$result2 = $mysqli->query('SELECT * FROM users WHERE (userStatus="active" or userStatus="pending")  AND (userId != "'.$_SESSION['userId'].'"  AND  userId= "'.$idlitemp.')"');
		//	$data = $result2->fetch_array();
			//echo 'Step 3:'.'SELECT * FROM users WHERE userStatus="active" OR userStatus="pending"  AND userId= "'.$idFbtemp.'"';
			if($result2->num_rows > 0){
			
			//UserID exist and status is active too
			//echo ' Two existing accounts for you ';		
$_SESSION['multipleli']=TRUE;	
$_SESSION['import'] = 'linkedin';		
			}
			else
			{
			//echo 'Hit Scenerio 3:';
			// when we have fbID ,email
			//Must check that
			//$mysqli->query('Insert INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("'.$_SESSION['userId'].'", "'.$emailAdd.'", "Primary", "verified")');
			//$idFbtemp
	 $extacess = $mysqli->query('SELECT * FROM user_external_accnt WHERE userId = "'.$_SESSION['userId'].'" and authProvider="linkedin"');
			 if($extacess->num_rows == 0) 
			 
			 {
			$mysqli->query('Insert INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$_SESSION['userId'].'", "'.$liUser.'", "'.$accesstoken.'", "linkedin")') or die ($mysqli->error);
	}  
		else
		{
			
	    $mysqli->query('update user_external_accnt set userId="'.$_SESSION["userId"].'", authAccesstoken="'.$accesstoken.'"  where authProvider="linkedin" AND externalAcctuid="'.$liUser.'"') or die ($mysqli->error);
}
			$mysqli->query('UPDATE source_import SET userId = "'.$_SESSION['userId'].'", sourceAccessToken="'.$accesstoken.'"  WHERE sourceName="linkedin" AND userId = "'.$idfromSimport.'"');
			//echo 'UPDATE source_import SET userId = "'.$_SESSION['userId'].'", sourceAccessToken="'.$accesstoken.'", sourceName="linkedin"  WHERE userId = "'.$idfromSimport.'"';
						$user = new user;
			$user->merge($_SESSION['userId'],$idfromSimport);
					$_SESSION['linkedin_id']=TRUE;
			$_SESSION['linkedin'] = true;
			$_SESSION['import'] = 'linkedin';
			$login = new login($_SESSION['userId']);

			}
			}
			else
			{
			//echo 'Scenerio 4:';
			// condition if FB ID exists but no Email for it
			// Update source import table and add email in user_email table
				 $extacess = $mysqli->query('SELECT * FROM user_external_accnt WHERE userId = "'.$_SESSION['userId'].'" and authProvider="linkedin"');
			 if($extacess->num_rows == 0) 
			 
			 {
			$mysqli->query('Insert INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$_SESSION['userId'].'", "'.$liUser.'", "'.$accesstoken.'", "linkedin")') or die ($mysqli->error);
	} 
	else
	{
		    $mysqli->query('update user_external_accnt set userId="'.$_SESSION["userId"].'", authAccesstoken="'.$accesstoken.'"  where authProvider="linkedin" AND externalAcctuid="'.$liUser.'"') or die ($mysqli->error);
	}
			$mysqli->query('Insert INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("'.$_SESSION['userId'].'", "'.$emailAdd.'", "Primary", "verified")');
			//$idFbtemp
			$mysqli->query('UPDATE source_import SET userId = "'.$_SESSION['userId'].'", sourceAccessToken="'.$accesstoken.'"  WHERE sourceName="linkedin" AND userId = "'.$idfromSimport.'"');
					$_SESSION['linkedin_id']=TRUE;
			$_SESSION['linkedin'] = true;
			$_SESSION['import'] = 'linkedin';
			$login = new login($_SESSION['userId']);
			
			}
			
			
			
           
			//$user->updateLastLogin($data['userId']);
			//$login = new login($userId);
			
			
		}
		else
		{
		// FB ID doesn't exist
		    $result4 = $mysqli->query('SELECT * FROM user_email WHERE LOWER(emailAddr) = "'.strtolower($emailAdd).'"');
			
			
			if($result4->num_rows > 0){
			$mysqli->query('update user_email SET emailStatus="verified" WHERE LOWER(emailAddr) = "'.strtolower($emailAdd).'"');
			
			$data2 = $result4->fetch_array();
			//Email Exists
			$idlitemp=$data2['userId'];
			if($idlitemp==$_SESSION['userId'])
			{
			//echo ' in condition 1'.$idFbtemp;
			//when the MB UID matches the Session ID
			//Just do importing
			        //  $mysqli->query('REPLACE INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$_SESSION['userId'].'", "'.$liUser.'", "'.$accesstoken.'", "linkedin")') or die ($mysqli->error);
						 $extacess = $mysqli->query('SELECT * FROM user_external_accnt WHERE userId = "'.$_SESSION['userId'].'" and authProvider="linkedin"');
			 if($extacess->num_rows == 0) 
			 
			 {
			$mysqli->query('Insert INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$_SESSION['userId'].'", "'.$liUser.'", "'.$accesstoken.'", "linkedin")') or die ($mysqli->error);
	}
					$mysqli->query('REPLACE INTO source_import (userId, sourceUid, sourceAccessToken, sourceName,sourceProfilePicture, sourceProfileLink) VALUES ("'.$_SESSION['userId'].'", "'.$liUser.'", "'.$accesstoken.'", "linkedin","'.$liArrayUser->{'pictureUrl'}.'", "'.$liArrayUser->{'publicProfileUrl'}.'")') or die ($mysqli->error);
					$_SESSION['linkedin_id']=TRUE;
			$_SESSION['linkedin'] = true;
			$_SESSION['import'] = 'linkedin';
			$login = new login($_SESSION['userId']);
		
			}
			else 
			{
			//echo ' in condition 2'.$idFbtemp;
			$result5 = $mysqli->query('SELECT * FROM users WHERE userStatus="active" or userStatus="pending" AND userId= "'.$idlitemp.'"');
			if($result5->num_rows > 0){
			//echo 'SELECT * FROM users WHERE userStatus="active" or userStatus="pending" AND userId= "'.$idFbtemp.'"';
			//echo ' side condition 2'.$idFbtemp;
			//UserID exist and status is active too
						       //  $mysqli->query('REPLACE INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$_SESSION['userId'].'","'.$liUser.'", "'.$accesstoken.'", "linkedin")') or die ($mysqli->error);
									 $extacess = $mysqli->query('SELECT * FROM user_external_accnt WHERE userId = "'.$_SESSION['userId'].'" and authProvider="linkedin"');
			 if($extacess->num_rows == 0) 
			 
			 {
			$mysqli->query('Insert INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$_SESSION['userId'].'", "'.$liUser.'", "'.$accesstoken.'", "linkedin")') or die ($mysqli->error);
	}
								$mysqli->query('REPLACE INTO source_import (userId, sourceUid, sourceAccessToken, sourceName,sourceProfilePicture, sourceProfileLink) VALUES ("'.$_SESSION['userId'].'", "'.$liUser.'", "'.$accesstoken.'", "linkedin","'.$liArrayUser->{'pictureUrl'}.'", "'.$liArrayUser->{'publicProfileUrl'}.'")') or die ($mysqli->error);
					
			$user = new user;
			$user->merge($_SESSION['userId'],$idlitemp);
			$_SESSION['linkedin_id']=TRUE;
			$_SESSION['linkedin'] = true;
			$_SESSION['import'] = 'linkedin';
			$login = new login($_SESSION['userId']);		
			}
					
			}
			}
			else
			{
			//echo 'No where';
			// condition when the manual user email is diff than the facebook one and we dont find that in user_email
			$user = new user;
			//$user->merge($_SESSION['userId'],$idFbtemp);
			
			$login = new login($_SESSION['userId']);
			$sourceData = array();
			$userDetail = array();
			$sourceData['sourceUid'] = $liUser;
			$sourceData['sourceAccessToken'] = $accesstoken; 
			$sourceData['sourceName'] = 'linkedin';
			//print_r($liArrayUser);
			$sourceData['sourceProfilePicture'] = $liArrayUser->{'pictureUrl'};
			//echo 'the pic url is'.$liArrayUser->{'pictureUrl'};
			$sourceData['sourceProfileLink'] = $liArrayUser->{'publicProfileUrl'};
//echo 'the pic url is'.$liArrayUser->{'publicProfileUrl'};
			$userDetail['firstName'] = $liArrayUser->{'firstName'};
			$userDetail['lastName'] = $liArrayUser->{'lastName'};
			
			$userDetail['middleName'] = NULL;
			
			$userDetail['userId'] = $_SESSION['userId'];
			$userId = $user->newUser($_SESSION['userId'], $sourceData, $userDetail, 'active', 'personal', $emailAdd,'verified');
			$_SESSION['linkedin_id']=TRUE;
			$_SESSION['linkedin'] = true;
			$_SESSION['import'] = 'linkedin';
			}
			
			
		
		}
		
		////MY updated Linkedin Code
		
		// LI Sam Code
		/*$user = new user;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$result = $mysqli->query("SELECT * FROM source_import WHERE sourceUid = '".$liUser->{'id'}."'");
		if($result->num_rows > 0){
            $result2 = $mysqli->query('SELECT * FROM source_import WHERE sourceUid = "'.$liUser->{"id"}.'"');
            $data = $result2->fetch_array();
			
           
		if($sourceData != false){
			
		}
		
			$mysqli->query('UPDATE source_import SET sourceAccessToken = "'.$accesstoken.'" WHERE sourceUid = "'.$liUser->{"id"}.'"');
			$gg = "UPDATE user_external_accnt SET authAccesstoken='". $accesstoken ."' WHERE externalAcctuid='". $liUser->{"id"} ."' AND authProvider='linkedin'";
			$mysqli->query($gg);
			$_SESSION["litokendebug"] = $gg."<br />".$mysqli->error;
			$data = $result->fetch_array();
			if($data['userId'] != $userId && $userId != false){
        			$user->merge($userId, $data['userId']);
				
			}
            if($userId == '' && $userId == false){
                $userId = $data['userId'];
            }
			 if(empty($data['sourceAccessToken'])){
			 	//$qqq = "SELECT * FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."' AND authProvider='linkedin'";
			 	//$ress = $mysqli->query($qqq);
				//$qdat = $ress->fetch_assoc();
				//if($qdat["authAccessToken"] == ""){
            	$mysqli->query('INSERT INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$userId.'", "'.$liUser->{"id"}.'", "'.$accesstoken.'", "linkedin")') or die ($mysqli->error);
				//}
				$mysqli->query('UPDATE users SET userStatus = "active" WHERE userId = "'.$userId.'"');
				$email = $liUser->{'emailAddress'};
				$_SESSION['email'] = $email;
				$result = $mysqli->query('SELECT * FROM user_email WHERE emailAddr = "'.$email.'"');
				if($result->num_rows == 0){
					$mysqli->query('INSERT INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("'.$userId.'", "'.$email.'", "Primary", "verified")');
				}
                $_SESSION['import'] = 'linkedin';
				
            }
            $friend_detail_query = "SELECT * FROM userfrnd_source WHERE userId='". $_SESSION["userId"] ."'";
            $result3 = $mysqli->query('SELECT * FROM users WHERE userId = "'.$userId.'"');
			
			$_SESSION['userId'] = $userId;
			$login = new login($userId);
           $user->updateLastLogin($userId);
			
			//$login->login();
			
		}
		else {
            $cookie = new cookie;
            $result = $cookie->check();
            if($result != false){
                $userId = $result;
            }         
			$sourceData = array();
			$userDetail = array();
			$sourceData['sourceUid'] = $liUser->{'id'};
			$sourceData['sourceAccessToken'] = $accesstoken; 
			$sourceData['sourceName'] = 'linkedin';
			$sourceData['sourceProfilePicture'] = $liUser->{'pictureUrl'};
			$sourceData['sourceProfileLink'] = $liUser->{'publicProfileUrl'};
			$userDetail['firstName'] = $liUser->{'firstName'};
			$userDetail['lastName'] = $liUser->{'lastName'};
			$userDetail['middleName'] = NULL;
			$userDetail['userId'] = $userId;
			$userdetail['email'] = $liUser->{'emailAdress'};
			$_SESSION['email'] = $liUser->{'emailAdress'};
            $_SESSION['userData'] = $liUser;
            //var_dump($sourceData);
            //var_dump($userDetail);
			$user->newUser($userId, $sourceData, $userDetail, 'active', 'personal', $liUser->{'emailAddress'},'verified');
				$user->updateLastLogin($userId);
            $_SESSION['import'] = 'linkedin';
			$this->login();
		}*/
	}
}



?>