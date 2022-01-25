<?php
include_once('email.class.php');

class fb {
	
	public function link2(){
		global $config;
		$facebook = new Facebook(array(
			  'appId'  => $config['facebook_appId'],
			  'secret' => $config['facebook_secret'],
			  'cookie' => true
		));
		return $facebook;
	}
	
	public function getUser(){
		$facebook = $this->link2();
		$user = $facebook->getUser();
		return $user;	
	}
	
	public function loginUrl($redirectUrl){
		global $config;
		$facebook = $this->link2();
		$params = array(
			'scope' => $config['facebook_scope'],
			'redirect_uri' => $redirectUrl,
		);
		$link = $facebook->getLoginUrl($params);
		return $link;
	}
	
	public function friends($uid, $accesstoken){
		$facebook = $this->link2();
		$friends = $facebook->api('/'.$uid.'/friends','GET', array('accesstoken' => $accesstoken));
		return $friends;
	}
	
	public function mutualFriends($uid, $friendUid, $accesstoken){
		$facebook = $this->link2();
		$mutualFriends = $facebook->api('/'.$uid.'/mutualfriends/'.$friendUid,'GET',array ('access_token' => $accesstoken));
		return $mutualFriends;	
	}
	
	public function mail_registration($to)
{
 
 $email = new email;
        $sanitize = new sanitize;
        
        
       
         $header = array(
            'From'      => 'tcleveland@myiceberg.com',
            'Reply-to'  => 'tcleveland@myiceberg.com'
         );
         
         $mail_body = array(
            'text/plain' => 
            'Myiceberg Registration Confirmation Email

            Welcome to Myiceberg ,
            
            Congratulations on your recent registration with Myiceberg.
            
            To complete the registration, please click this link:
            
            Thank you joining Myiceberg,
            
            The MIB Team',
            
            'text/html' => 
            '<div id="backgorund">
                <div id="title"> Myiceberg Registration Confirmation Email </div>
                 
                    <div id="messsge">
                         <img alt="Logoupperright-original" src="http://assets.postageapp.com/000/002/002/logoUpperRight-original.jpg" id="logo"/>
                      <br>
                      <br>
                        <div id="subject"> Welcome to Myiceberg , </div>
                        
                        <div id="messsge2">Congratulations on your recent registration with Myiceberg.<br>
                            The final step in completing your registration is activating your email by clicking the button below.</div>
                      <a href=""><img alt="Buttonforregistrationemail-original" src="http://assets.postageapp.com/000/002/003/buttonForRegistrationEmail-original.png" id="RegistrationButton"/></a>
                        <div id="messsge3">You can also copy and paste this link to your browser instead:<br>   </div>
                        
                        
                        <br />
                      <br>
                        <div id="footer">Thank you joining Myiceberg,<br />The MIB Team</div>
                    </div>
                </div>'
         );
        //echo 4;
        $data = $email->send($to, 'Myiceberg Confirmation Email', $mail_body, $header);
        //print_r($data);
        //echo 5;
}
	public function login($red){
		global $config;
		$idFbtemp='';
		$fbUser='';
		$accesstoken='';
		$userData=array();
		$idfromSimport='';
        //Test
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
		$facebook = self::link2();
		$user = $facebook->getUser();
		//var_dump($user);
		$emailAdd='';
		if($user) {
			// Proceed knowing you have a logged in user who's authenticated.
			$varForSession = 'fb_'.$config['facebook_appId'].'_access_token';
			$accesstoken = $_SESSION[$varForSession];
			$userData= $facebook->api('/me', 'GET' ,array ('access_token' => $accesstoken));
			$email= $facebook->api('/me?fields=email', 'GET' ,array ('access_token' => $accesstoken));
			$emailAdd = $email['email'];
			$fbUser = $userData['id'];
			//var_dump($userData);
		}			
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$result = $mysqli->query("SELECT * FROM source_import WHERE sourceUid = '".$fbUser."'");
		
		if($result->num_rows > 0){
		$data1 = $result->fetch_array();
		$idfromSimport=$data1['userId'];
		 $resultEmail = $mysqli->query('SELECT * FROM user_email WHERE LOWER(emailAddr) = "'.strtolower($emailAdd).'"');
		if($resultEmail->num_rows > 0){
			$dataEmail = $resultEmail->fetch_array();
			//Email Exists
			$idFbtemp=$dataEmail['userId'];
			
	
			//echo 'login';
			//Old User
			//FBID exists
			
 
			
			$result2 = $mysqli->query('SELECT * FROM users WHERE (userStatus="active" or userStatus="pending")  AND (userId != "'.$_SESSION['userId'].'"  AND  userId= "'.$idFbtemp.')"');
		//	$data = $result2->fetch_array();
			//echo 'Step 3:'.'SELECT * FROM users WHERE userStatus="active" OR userStatus="pending"  AND userId= "'.$idFbtemp.'"';
			if($result2->num_rows > 0){
			
			//UserID exist and status is active too
			//echo ' Two existing accounts for you ';		
$_SESSION['multiplefb']=TRUE;	
$_SESSION['import'] = 'facebook';		
			}
			else
			{
			//echo 'Hit Scenerio 3:';
			// when we have fbID ,email
			//Must check that
			//$mysqli->query('Insert INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("'.$_SESSION['userId'].'", "'.$emailAdd.'", "Primary", "verified")');
			//$idFbtemp
			 $extacess = $mysqli->query('SELECT * FROM user_external_accnt WHERE userId = "'.$_SESSION['userId'].'" and authProvider="facebook"');
			 if($extacess->num_rows == 0) 
			 
			 {
			$mysqli->query('Insert INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$_SESSION['userId'].'", "'.$fbUser.'", "'.$accesstoken.'", "facebook")') or die ($mysqli->error);
	}
	else
	{
	 $mysqli->query('update user_external_accnt set userId="'.$_SESSION["userId"].'", authAccesstoken="'.$accesstoken.'"  where authProvider="facebook" AND externalAcctuid="'.$fbUser.'"') or die ($mysqli->error);
}
			$mysqli->query('UPDATE source_import SET userId = "'.$_SESSION['userId'].'", sourceAccessToken="'.$accesstoken.'"   WHERE sourceName="facebook" AND userId = "'.$idfromSimport.'"');
			//echo 'UPDATE source_import SET userId = "'.$_SESSION['userId'].'", sourceAccessToken="'.$accesstoken.'", sourceName="facebook"  WHERE userId = "'.$idfromSimport.'"';
						$user = new user;
			$user->merge($_SESSION['userId'],$idfromSimport);
					$_SESSION['facebook_id']=TRUE;
			$_SESSION['facebook'] = true;
			$_SESSION['import'] = 'facebook';
			$login = new login($_SESSION['userId']);

			}
			}
			else
			{
			//echo 'Scenerio 4:';
			// condition if FB ID exists but no Email for it
			// Update source import table and add email in user_email table
				 $extacess = $mysqli->query('SELECT * FROM user_external_accnt WHERE userId = "'.$_SESSION['userId'].'" and authProvider="facebook"');
			 if($extacess->num_rows == 0) 
			 
			 {
			$mysqli->query('Insert INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$_SESSION['userId'].'", "'.$fbUser.'", "'.$accesstoken.'", "facebook")') or die ($mysqli->error);
	} 
	else
	{
	 $mysqli->query('update user_external_accnt set userId="'.$_SESSION["userId"].'", authAccesstoken="'.$accesstoken.'"  where authProvider="facebook" AND externalAcctuid="'.$fbUser.'"') or die ($mysqli->error);
}
			$mysqli->query('Insert INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("'.$_SESSION['userId'].'", "'.$emailAdd.'", "Primary", "verified")');
			//$idFbtemp
			$mysqli->query('UPDATE source_import SET userId = "'.$_SESSION['userId'].'", sourceAccessToken="'.$accesstoken.'"   WHERE sourceName="facebook" AND userId = "'.$idfromSimport.'"');
					$user = new user;
					$user->merge($_SESSION['userId'],$idfromSimport);
					$_SESSION['facebook_id']=TRUE;
			$_SESSION['facebook'] = true;
			$_SESSION['import'] = 'facebook';
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
			$idFbtemp=$data2['userId'];
			if($idFbtemp==$_SESSION['userId'])
			{
			//echo ' in condition 1'.$idFbtemp;
			//when the MB UID matches the Session ID
			//Just do importing
			          //$mysqli->query('REPLACE INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$_SESSION['userId'].'", "'.$fbUser.'", "'.$accesstoken.'", "facebook")') or die ($mysqli->error);
				 
				 
				 $extacess = $mysqli->query('SELECT * FROM user_external_accnt WHERE userId = "'.$_SESSION['userId'].'" and authProvider="facebook"');
			 if($extacess->num_rows == 0) 
			 
			 {
			$mysqli->query('Insert INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$_SESSION['userId'].'", "'.$fbUser.'", "'.$accesstoken.'", "facebook")') or die ($mysqli->error);
	}		
	$mysqli->query('REPLACE INTO source_import (userId, sourceUid, sourceAccessToken, sourceName) VALUES ("'.$_SESSION['userId'].'", "'.$fbUser.'", "'.$accesstoken.'", "facebook")') or die ($mysqli->error);
					$_SESSION['facebook_id']=TRUE;
			$_SESSION['facebook'] = true;
			$_SESSION['import'] = 'facebook';
			$login = new login($_SESSION['userId']);
		
			}
			else 
			{
			//echo ' in condition 2'.$idFbtemp;
			$result5 = $mysqli->query('SELECT * FROM users WHERE userStatus="active" or userStatus="pending" AND userId= "'.$idFbtemp.'"');
			if($result5->num_rows > 0){
			//echo 'SELECT * FROM users WHERE userStatus="active" or userStatus="pending" AND userId= "'.$idFbtemp.'"';
			//echo ' side condition 2'.$idFbtemp;
			//UserID exist and status is active too
						        // $mysqli->query('REPLACE INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$_SESSION['userId'].'","'.$fbUser.'", "'.$accesstoken.'", "facebook")') or die ($mysqli->error);
				 $extacess = $mysqli->query('SELECT * FROM user_external_accnt WHERE userId = "'.$_SESSION['userId'].'" and authProvider="facebook"');
			 if($extacess->num_rows == 0) 
			 
			 {
			$mysqli->query('Insert INTO user_external_accnt (userId, externalAcctuid, authAccesstoken, authProvider) VALUES ("'.$_SESSION['userId'].'", "'.$fbUser.'", "'.$accesstoken.'", "facebook")') or die ($mysqli->error);
	}						$mysqli->query('REPLACE INTO source_import (userId, sourceUid, sourceAccessToken, sourceName) VALUES ("'.$_SESSION['userId'].'", "'.$fbUser.'", "'.$accesstoken.'", "facebook")') or die ($mysqli->error);
			
			$user = new user;
			$user->merge($_SESSION['userId'],$idFbtemp);
			$_SESSION['facebook_id']=TRUE;
			$_SESSION['facebook'] = true;
			$_SESSION['import'] = 'facebook';
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
			$sourceData['sourceUid'] = $fbUser;
			$sourceData['sourceAccessToken'] = $accesstoken; 
			$sourceData['sourceName'] = 'facebook';
			$sourceData['sourceProfilePicture'] = NULL;
			$sourceData['sourceProfileLink'] = NULL;
			$userDetail['firstName'] = $userData['first_name'];
			$userDetail['lastName'] = $userData['last_name'];
			if(isset($userData['middle_name'])){
				$userDetail['middleName'] = $userData['middle_name'];
			}
			else {
			$userDetail['middleName'] = NULL;
			}
			$userDetail['userId'] = $_SESSION['userId'];
			$userId = $user->newUser($_SESSION['userId'], $sourceData, $userDetail, 'active', 'personal', $emailAdd,'verified');
			$_SESSION['facebook_id']=TRUE;
			$_SESSION['facebook'] = true;
			$_SESSION['import'] = 'facebook';
			}
			
			
		
		}
		
			
	}


	
}
