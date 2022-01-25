<?php

class import {
    
    public function escape($data, $mysqli){
		if(is_array($data)){
			foreach($data as $key=>$value) {
			  	if(is_array($value)) {
				   	escape($value, $mysqli); 
			 	 }
			  	else { 
			  		$data[$key] = $mysqli->real_escape_string($value); 
				}
			}
		}
		else {
			$data = $mysqli->real_escape_string($data);
		}
		return $data;
	}
	public function facebook($userId){
		global $config;
		$fb = new fb;
		$facebook = $fb->link2();
		$friend = new friend;
		//$sse = new sse;
		$verify = new verify;
		/*if($sse->checkBrowser()){
			$sseSend = true;
		}*/
		//var_dump($userId);
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$result = $mysqli->query('SELECT * FROM source_import WHERE userId = "'.$userId.'" AND sourceName = "facebook"');
		$data = $result->fetch_array();
		$accesstoken = $data['sourceAccessToken'];
		$uid = $data['sourceUid'];
		$fbFriends = $fb->friends($uid, $accesstoken);
		$friendsCount = 1;
        $friendsCat = array();
		foreach($fbFriends['data'] as $fbFriend){
			$userExists = $friend->userExists($fbFriend['id']);
			if($userExists != false){
				$friendId = $userExists;
				$relationExists = $friend->relationExists($userId, $friendId);
			}
            else {
                $relationExists = false;
            }
			
			$friendData = $facebook->api('/'.$fbFriend["id"], 'GET', array('accesstoken' => $accesstoken));
			$firstName = $friendData['first_name'];
			if(isset($friendData['middle_name'])){
				$middleName = $friendData['middle_name'];
			}
			else{
				$middleName = false;
			}
			if($verify->checkImport($friendData['first_name'], $middleName, $friendData['last_name'])){
				$verified = 'unverified';
			}
			else {
				$verified = 'verified';
			}
			$_SESSION['relation'][][$firstName] = array('User' => $userExists, 'Relation' => $relationExists);					
			
           //  echo 'test2';
			$data = array();
			$data['relationExists'] = $relationExists;
			$data['userId'] = $userExists;
			$data['verified'] = $verified;
			$data['sourceUid'] = $fbFriend['id'];
			$data['sourceName'] = 'facebook'; 
			$data['userStatus'] = 'temp';
			$data['accountType'] = 'personal'; 
			if(isset($friendData['email'])){
				$email = $friendData['email'];	
			}
			else {
				$email = NULL;	
			}
			$data['email'] = $email;
			$data['firstName'] = $friendData['first_name']; 
			if(isset($friendData['middle_name'])){
				$middleName = $friendData['middle_name'];	
			}
			else {
				$middleName = NULL;	
			}
            // echo 'test3';
			$data['middleName'] = $middleName;
			$data['lastName'] = $friendData['last_name'];
			$result = $mysqli->query("SELECT * FROM source_import WHERE sourceUid = '".$fbFriend['id']."'");
			   $id = $friend->addFacebook($data);
               array_push($friendsCat, $id);
		$friendsCount ++;
		//$params = array('method' => 'fql.query', 'query' => "SELECT uid, pic, pic_square, name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = 111111111)",);	
   		// $fqlResult = $facebook->api($params);
		// $_SESSION['resultFacebook'] = $fqlResult;

		
		}		
	}
	
	public function linkedin($userId){
		global $config;
		$li = new li;
		$friend = new friend;
		//$sse = new sse;
		$verify = new verify;
		/*if($sse->checkBrowser()){
			$sseSend = true;
		}*/
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$result = $mysqli->query("SELECT * FROM source_import WHERE userId = '".$userId."' AND sourceName = 'linkedin'");
		$data = $result->fetch_array();
		$accessToken = $data["sourceAccessToken"];
 // echo ' 6';
		$params = array('oauth2_access_token' => $accessToken,
						'format' => 'json');
		$liFriends = $li->fetch('/v1/people/~/connections', $params);
		$friendsCount = 1;
        $friendsCat = array();
		$_SESSION['li'] = $liFriends;
		foreach($liFriends->{'values'} as $liFriend){
			if($liFriend->{"firstName"} != "private" && $liFriend->{"lastName"} != "private"){
				//echo ' 5';
				$userExists = $friend->userExists($liFriend->{'id'});
				if($userExists != false){
					$friendId = $userExists;
					$relationExists = $friend->relationExists($userId, $friendId);
				}
				else{
					$relationExists = false;
				}
				$_SESSION['relation'][][$firstName] = array('User' => $userExists, 'Relation' => $relationExists);	
				$firstName = $liFriend->{'firstName'};
				$middleName = false;
				$lastName = $liFriend->{'lastName'};
   // echo ' 3';
   				if($verify->checkImport($liFriend->{'firstName'}, false, $liFriend->{'lastName'})){
					$verified = 'unverified';
				}
				else {
					$verified = 'verified';
				}					
			
				$data = array();
				if(!isset($relationExists)){
					$relationExists = false;	
				}
				$data['relationExists'] = $relationExists;
				$data['userId'] = $userExists;
				$data['verified'] = $verified;
				$data['sourceUid'] = $liFriend->{'id'};
				$data['sourceName'] = 'linkedin'; 
				$data['userStatus'] = 'temp';
				$data['accountType'] = 'personal'; 
				if(isset($liFriend->{'email'})){
					$email = $liFriend->{'email'};	
				}
				else {
					$email = NULL;	
				}
				$data['email'] = $email;
				$data['firstName'] = $firstName; 
				$data['middleName'] = NULL;
				$data['lastName'] = $lastName;
				$data['sourceProfilePicture'] = $liFriend->{'pictureUrl'};
				$data['sourceProfileLink'] = $liFriend->siteStandardProfileRequest->{'url'};
   // echo ' 1';
					$id = $friend->addLinkedin($data);
    // echo ' 2';
                    //$friendsCat[] = $id;
				
				/*if($sseSend){
					$percent = round(($friendsCount/$total)*100);
					$msg = $total.':'.$friendsCount.':'.$percent;
					$sse->send($msg);
				}*/
				$friendsCount ++;
		
			}}			//END HERE - CPREY
	}
	public function addSF($data){
			$friend = new friend;
			$verify = new verify;		
			//$userExists = $friend->userExists($data['id'], 'source_import_sf');
			//$data["MobilePhone"] is for Mobile Phone Number
			if(!($data['Email']=="" && $data["MobilePhone"]==""))
			{
			$userExists = $friend->userExistsForEmail($data['Email'], 'user_email');
			if($userExists != false){
				$friendId = $userExists;
				$relationExists = $friend->relationExists($userId, $friendId);
			}
            else {
				//Now check for phone Number
				$userExists = $friend->userExistsForMobile($data["MobilePhone"], 'user_detail_public');
				if($userExists != false){
				$friendId = $userExists;
				$relationExists = $friend->relationExists($userId, $friendId);
			}
			else
			{
                $relationExists = false; 
			}				
            }
           	$_SESSION['relation'][] = array('User' => $userExists, 'Relation' => $relationExists);
			$firstName = $data['FirstName'];
			$middleName = false;
			if($verify->checkImport($data['FirstName'], $middleName, $data['LastName'])){
				$verified = 'unverified';
			} 
			else {
				$verified = 'verified';
			}
			$data['relationExists'] = $relationExists;
			$data['userId'] = $userExists;
			$data['verified'] = $verified;
			$data['sourceUid'] = $data['id'];
			$data['sourceName'] = 'salesforce'; 
			$data['userStatus'] = 'temp';
			$data['accountType'] = 'personal';
			$id = $friend->addSalesforce($data);
			$friendsCount ++;
			}
	}
	
	// Add cloud Sponge Data -U
	public function addCS($data){
			$friend = new friend;
			$verify = new verify;		
			//$userExists = $friend->userExists($data['id'], 'source_import_cs');
			$userExists = $friend->userExistsForEmail($data['Email'], 'user_email');
			
			if($userExists != false){
				$friendId = $userExists;
				$relationExists = $friend->relationExists($userId, $friendId);
			}
            else {
                $relationExists = false;            
            }
           	$_SESSION['relation'][] = array('User' => $userExists, 'Relation' => $relationExists);
			$firstName = $data['FirstName'];
			$middleName = false;
			if($verify->checkImport($data['FirstName'], $middleName, $data['LastName'])){
				$verified = 'unverified';
			}
			else {
				$verified = 'verified';
			}					
			
           //  echo 'test2';
			$data['relationExists'] = $relationExists;
			$data['userId'] = $userExists;
			$data['verified'] = $verified;
			$data['sourceUid'] = $data['id'];
			$data['userStatus'] = 'temp';
			$data['accountType'] = 'personal';
			$id = $friend->addCloudSponge($data);
			$friendsCount ++;
	}
}
