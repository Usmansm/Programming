<?php

class login {

	public function login($userId){
		//echo 'USerId1:';
		//var_dump($userId);
		global $config;
		//$logout = new logout;
		//$logout->logout();
		//echo 'SESSION!!!';
        $cookie = new cookie;
        $cookie->userCookie($userId);
		//var_dump($_SESSION);
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$result = $mysqli->query('SELECT * FROM users WHERE userId = "'.$userId.'"');
		if($result->num_rows > 0){
			$data = $result->fetch_array();	
			$_SESSION['logged_in'] = true;
			$_SESSION['userId'] = $userId;
		}
		else {
			$_SESSION['logged_in'] = false;
			//$notification = new notification;
			//$notification->fatal('loginUser', 'userId was not found', 'userId:'.$userId);
		}
		$result = $mysqli->query('SELECT * FROM user_detail_private WHERE userId = "'.$userId.'"');
		if($result->num_rows > 0){
			$data = $result->fetch_array();
			$_SESSION['firstName'] = $data['firstName'];
			$_SESSION['middleName'] = $data['middleName'];
			$_SESSION['lastName'] = $data['lastName'];
		}
		$result = $mysqli->query('SELECT * FROM source_import WHERE userId = "'.$userId.'"');
		if($result->num_rows > 0){
			while($row = $result->fetch_array()){
				if($row['sourceName'] == 'facebook'){
					$_SESSION['facebook'] = true;
					$_SESSION['facebook_id'] = $row['sourceUid'];
				}
				if($row['sourceName'] == 'linkedin'){
					$_SESSION['linkedin'] = true;
					$_SESSION['linkedin_id'] = $row['sourceUid'];
					$_SESSION['linkedin_profile_picture'] = $row['sourceProfilePicture'];
					$_SESSION['linkedin_profile_link'] = $row['sourceProfileLink'];
				}
			}
		}	
	}
}

?>