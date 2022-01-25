<?php
/*
//FACEBOOK IMPORT
session_start();
if(isset($_SESSION['facebookId'])){
	require_once("../lib/facebooksdk/src/facebook.php");
	include("../config/config.php");
	
	$facebook = new Facebook(array(
		  'appId'  => $config['facebook_appId'],
		  'secret' => $config['facebook_secret'],
	));
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM source_import WHERE userId = '".$_SESSION['userid']."' AND sourceUid = '".$_SESSION['facebookId']."'";
	$results = mysqli_query($conn, $query);
	$data = mysqli_fetch_row($results);
	$accesstoken = $data[1];
	$fbFriends = $facebook->api('/'.$_SESSION["facebookId"].'/friends','GET', array('accesstoken' => $accesstoken));
	//var_dump($fbFriends);
	$ids = array();
	$key3=0;
	$key4 = 0;
	$total = count($fbFriends['data']);
	//var_dump($total);
	foreach($fbFriends['data'] as $fbFriend){
		
		$query = "SELECT * FROM source_import WHERE sourceUid = '".$fbFriend['id']."'";
		$result = mysqli_query($conn, $query);
		$data = mysqli_fetch_row($result);
		$state = 1;
		$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' AND friendId = '".$data[0]."'";
		$results = mysqli_query($conn, $query);
		
		if(mysqli_num_rows($results) >= 1){
			$state = 0;
			$key4++;
		}
		if(mysqli_num_rows($result) == 1 && $state == 1){
			//echo '++ Key = '.$key3;
			$key3++;
			$fbFriendData = $facebook->api('/'.$fbFriend["id"],'GET', array('accesstoken' => $accesstoken));
			$query = "INSERT INTO user_friend_detail (userId, friendId, FriendFirstName, FriendLastName, importSources) VALUES ('".$_SESSION['userId']."', '".$data[0]."', 
			'".addslashes($fbFriendData['first_name'])."', '".addslashes($fbFriendData['last_name'])."', 'facebook')";
			mysqli_query($conn, $query);
			//var_dump($fbFriendData);
			array_push($ids, $data[0]);
			
			
		}
		
		elseif($state == 1){
			//echo '++ Key = '.$key3; 
		$key3++;
			$fbFriendData = $facebook->api('/'.$fbFriend["id"],'GET', array('accesstoken' => $accesstoken));
			//var_dump($fbFriendData);
			$query = "SELECT * FROM user_detail_private WHERE firstName = '".addslashes($fbFriendData['first_name'])."' AND lastName = '".addslashes($fbFriendData['last_name'])."'";
			$result = mysqli_query($conn, $query);
			if(mysqli_num_rows($result) >= 1){
				while($data2 = mysqli_fetch_row($result)){
					if($data2[3] == $fbFriendData['location']){
						$same = true;	
					}
					if($data2[4] == $fbFriendData['hometown']){
						$same = true;	
					}
					if($data2[5] == $fbFriendData['birthday']){
						$same = true;	
					}		
				}					
			}
			if($same){	
				$query = "INSERT INTO user_friend_detail (userId, friendId, FriendFirstName, FriendLastName, importSources) VALUES ('".$_SESSION['userId']."', '".$data2[0]."', 
				'".addslashes($fbFriendData['first_name'])."', '".addslashes($fbFriendData['last_name'])."', 'facebook')";
				mysqli_query($conn, $query);
				array_push($ids, $data2[0]);
								
			}
			else{
				$query = "INSERT INTO users (userStatus, accountType, externalIdentifier) VALUES ('verified', 2, '".md5($fbFriendData['id'])."')";
				mysqli_query($conn, $query);
				
				$query = "SELECT * FROM users WHERE externalIdentifier = '".md5($fbFriendData['id'])."'";
				$results = mysqli_query($conn, $query);
				$userData = mysqli_fetch_row($results);
				
				$query = "INSERT INTO source_import (userId, sourceUid, sourceName) VALUES ('".$userData[0]."', '".$fbFriendData['id']."', 'facebook')";
				mysqli_query($conn, $query);
				$query = "SELECT * FROM source_import WHERE userId = '".$userData[0]."' AND sourceUid ='".$fbFriendData['id']."'";
				$results = mysqli_query($conn, $query);
				$sourceImport = mysqli_fetch_row($results);
				$query = "INSERT INTO userfrnd_source (userId, friendId, source_import_Id) VALUES ('".$_SESSION['userId']."', '".$userData[0]."', '".$sourceImport[0]."')";
				mysqli_query($conn, $query);
				
				if(isset($fbFriendData['email'])){
					$email = $fbFriendData['email'];	
				}
				else {
					$email = NULL;
				}
				if(isset($fbFriendData['location'])){
					$location = $fbFriendData['location'];	
				}
				else {
					$location = NULL;
				}
				$query = "INSERT INTO user_friend_detail (userId, friendId, FriendFirstName, FriendLastName, FriendEmail1, FriendCity1, importSources) VALUES ('".$_SESSION['userId']."', 
				'".$userData[0]."', 
			'".addslashes($fbFriendData['first_name'])."', '".addslashes($fbFriendData['last_name'])."', '".$email."', '".$location."', 'facebook')";
				mysqli_query($conn, $query);
				array_push($ids, $userData[0]);
			
			}
			
		}
	}
	echo 'Out of the total of '.$total.' friends, Myiceberg has updated '.$key4.' friends and added '.$key3.' friends.<br> The total amount of users might differ from Facebook, because of privacy settings.<br><br> <input type="button" class="cat_button" value="Close" onClick="window.location(\''.$config['root'].'friends/\')"/><div class="cat_right" ></div></center>';
		
}
//echo '<script type="text/javascript"> window.location = "'.$config['root'].'" </script>';
 * */
 
?>