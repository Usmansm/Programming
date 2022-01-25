<?php
session_start();
include("../config/config.php");
$total = 0;
$key3 = 0;
$key4 = 0;

if(isset($_SESSION['linkedinId'])){
	$key3 = 0;
	$key4 = 0;
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM source_import WHERE userId = '".$_SESSION['userId']."' AND sourceUid = '".$_SESSION['linkedinId']."'";
	$results = mysqli_query($conn, $query);
	$data = mysqli_fetch_row($results);
	$accessToken = $data[3];
	$params = array('oauth2_access_token' => $accessToken,
                    'format' => 'json',
              );
	$liFriends = fetch('GET', '/v1/people/~/connections', $params);
	//print_r($liFriends);
	//var_dump($liFriends);
$nr = 1;

	//var_dump($liFriends);
	foreach($liFriends as $temp){
		$total = count($temp);
		foreach($temp as $liFriend){
			//echo '<br>New Friend. Nr:'.$nr.' <br><br>';
			$nr++;
			//var_dump($liFriend);
				//echo '<br><br><br><br><BR><BR>';
	//echo 'Info: <br>';
	//print_r($liFriend->siteStandardProfileRequest->{'url'});
	////echo '<br><br><br><br><BR><BR>';
	//		var_dump($liFriend);
		$query = "SELECT * FROM source_import WHERE sourceUid = '".$liFriend->{'id'}."'";
		$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
		$data = mysqli_fetch_row($result);		
		$state = 1;
		$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' AND friendId = '".$data[1]."'";
		$results = mysqli_query($conn, $query) or die(mysqli_error($conn));
		
		if(mysqli_num_rows($results) >= 1){
			$key4 ++;
			//echo 'Why u keep importing :P! Id: '.$liFriend->{'id'}.'<br>';
			$state = 0;
		}
		//echo $state.' --- ';
		//echo mysqli_num_rows($result).'<br>';
		if(mysqli_num_rows($result) == 1 && $state == 1){
			$key3++;
			//echo 'Found existing! Id: '.$liFriend->{'id'}.'<br>';
			$liFriendFname = mysqli_real_escape_string($conn, addslashes($liFriend->{'firstName'}));
			$liFriendLname = mysqli_real_escape_string($conn, addslashes($liFriend->{'lastName'}));
			//echo $liFriendFname.'<br>';
			//echo $liFriendLname.'<br>';
			$query = "INSERT INTO user_friend_detail (userId, friendId, FriendFirstName, FriendLastName, importSources) VALUES ('".$_SESSION['userId']."', '".$data[1]."', 
			'".$liFriendFname."', '".$liFriendLname."', 'linkedin')";
			mysqli_query($conn, $query);
		}
		elseif($state == 1){ 
			$key3++;
		//echo 'Not existing! Id: '.$liFriend->{'id'}.'<br>';
				$query = "INSERT INTO users (userStatus, accountType, externalIdentifier) VALUES ('verified', 2, '".md5($liFriend->{'id'})."')";
				mysqli_query($conn, $query);
				
				$query = "SELECT * FROM users WHERE externalIdentifier = '".md5($liFriend->{'id'})."'";
				$results = mysqli_query($conn, $query);
				$userData = mysqli_fetch_row($results);
				
				$query = "INSERT INTO source_import (userId, sourceUid, sourceName, sourceProfileLink, sourceProfilePicture) VALUES ('".$userData[0]."', '".$liFriend->{'id'}."', 'linkedin',  '".$liFriend->siteStandardProfileRequest->{'url'}."','".$liFriend->{'pictureUrl'}."'  )";
				mysqli_query($conn, $query);
				$query = "SELECT * FROM source_import WHERE userId = '".$userData[0]."' AND sourceUid ='".$liFriend->{'id'}."'";
				$results = mysqli_query($conn, $query);
				$sourceImport = mysqli_fetch_row($results);
				$query = "INSERT INTO userfrnd_source (userId, friendId, source_import_Id) VALUES ('".$_SESSION['userId']."', '".$userData[0]."', '".$sourceImport[0]."')";
				mysqli_query($conn, $query);
				$query = "INSERT INTO user_friend_detail (userId, friendId, FriendFirstName, FriendLastName, importSources) VALUES ('".$_SESSION['userId']."', '".$userData[0]."', 
			'".addslashes($liFriend->{'firstName'})."', '".addslashes($liFriend->{'lastName'})."', 'linkedin')";
				mysqli_query($conn, $query);
			
			}}
		}
	
}
function fetch($method, $resource, $params, $body = '') {
     
    // Need to use HTTPS
    $url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
    // Tell streams to make a (GET, POST, PUT, or DELETE) request
    $context = stream_context_create(
                    array('http' => 
                        array('method' => $method,
                        )
                    )
                );
 
 
    // Hocus Pocus
    $response = file_get_contents($url, false, $context);
 
    // Native PHP object, please
    return json_decode($response);
}
echo 'Of your '.$total.' connections, Myiceberg has imported '.$key3.'.<br> The '.$key4.' remaining connections were not imported due to their Linkedin privacy settings.<br><br> <input type="button" class="cat_button" value="Close" 
onClick="window.location = \''.$config['root'].'/friends\'"/><div class="cat_right" ></div></center>';
?>