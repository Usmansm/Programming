<?php
	require_once("../../lib/facebooksdk/src/facebook.php");
	include('../../config/config.php');
	/*if(!isset($facebook)){
		$facebook = new Facebook(array(
		  'appId'  => '163575353787526',
		  'secret' => '0bf87c797d468745e3f40a0eee2e763d',
		  'cookie' => true
		));
	}*/
	$userFriends = array();
	$userFriendsData = array();

	$friendId = $_SESSION['currentFid'];
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$userId = $_SESSION['userId'];
	$queryResults = mysqli_query($conn, 'SELECT * FROM userfrnd_source WHERE userId = '.$userId);
	while($row = mysqli_fetch_assoc($queryResults)){
		array_push($userFriends, $row['friendId']);
		array_push($userFriendsData, $row);
	}
	//var_dump($userFriends);
	$friendFriends = array();
	$queryResults = mysqli_query($conn, 'SELECT * FROM userfrnd_source WHERE userId = '.$friendId);
	while($row = mysqli_fetch_assoc($queryResults)){
		array_push($friendFriends, $row['friendId']);
	}
	//var_dump($friendFriends);
	$mutualFriendsMIBRaw = array_intersect($userFriends, $friendFriends);
	$mutualFriendsMIBData = array();
	$mutualFriendsMIB = array();
	foreach($mutualFriendsMIBRaw as $mutualFriendMIBRaw){
		$key = in_array($mutualFriendMIBRaw, $mutualFriendsMIB);
		if($key == false){
			array_push($mutualFriendsMIB, $mutualFriendMIBRaw);
		}
	}
	$key = 0;
	//var_dump($mutualFriendsMIB);
	//var_dump($friendId);
	//echo '---';
	if(!empty($mutualFriendsMIB)){
		//echo '---';
	foreach($mutualFriendsMIB as $mutualFriendMIB){
		$queryResults = mysqli_query($conn, 'SELECT * FROM source_import WHERE userId = '.$mutualFriendMIB) or die(mysqli_error($conn));
		while($row = mysqli_fetch_row($queryResults)){
			//var_dump($row);
			if($row[4] == 'facebook'){ 
				foreach($userFriendsData as $userFriendData){
					//var_dump($userFriendData);
					if($userFriendData['friendId'] == $row[1] && $userFriendData['source_import_Id'] == $row[0]){
						//var_dump($userFriendData);
						$queryResult = mysqli_query($conn, 'SELECT * FROM user_friend_detail WHERE userId ="'.$_SESSION["userId"].'" AND friendId="'.$userFriendData["friendId"].'"') or die(mysqli_error($conn));
						//echo 'SELECT * FROM user_friend_detail WHERE userId ="'.$_SESSION["userId"].'" AND friendId="'.$userFriendData["userId"].'"';
						$mutualFriendInfo = mysqli_fetch_row($queryResult);
						//var_dump($mutualFriendInfo);
						$mutualFriendsMIBData[$key]['type1'] = 'facebook';
						$mutualFriendsMIBData[$key]['UID'] = $row[2];
						$mutualFriendsMIBData[$key]['NameFB'] = $mutualFriendInfo[5].' '.$mutualFriendInfo[7];
					
				}	
			}
			if($row[4] == 'linkedin'){
				foreach($userFriendsData as $userFriendData){
					if($userFriendData['friendId'] == $row[1] && $userFriendData['source_import_Id'] == $row[0]){
						$queryResult = mysqli_query('SELECT * FROM user_friend_detail WHERE userId ="'.$_SESSION["userId"].'" AND friendId="'.$userFriendData["friendId"].'"', $conn) or die(mysqli_error($conn));
						$mutualFriendInfo = mysqli_fetch_row($queryResult);
						$mutualFriendsMIBData[$key]['type2'] = 'linkedin';
						$mutualFriendsMIBData[$key]['profilePicture'] = '';
						if($row[6] != NULL){
						$mutualFriendsMIBData[$key]['profilePicture'] = $row[6];
						$mutualFriendsMIBData[$key]['NameLI'] = $mutualFriendInfo[5].' '.$mutualFriendInfo[7];
						}
					}
				}
			}
		}
		$key++;
	}
	}
	}
	//echo '---';
	//var_dump($mutualFriendsMIBData);
	//var_dump($mutualFriendsMIBData);
	$results = mysqli_query($conn, 'SELECT * FROM source_import WHERE userId = "'.$_SESSION["userId"].'"') or die(mysqli_error($conn));
	$userSource = mysqli_fetch_row($results);
	//var_dump($userSource);
		if($userSource[4] == 'facebook'){
			//echo '...';
			$userId = $_SESSION["userId"];
			$friendId = $_SESSION['currentFid'];
			$queryResults = mysqli_query($conn, 'SELECT * FROM source_import WHERE userId ="'.$userId.'" AND SourceName = "facebook"') or die(mysqli_error($conn));
			$userSourceImport = mysqli_fetch_row($queryResults);
			$queryResults = mysqli_query($conn, 'SELECT * FROM source_import WHERE userId ="'.$friendId.'" AND SourceName = "facebook"') or die(mysqli_error($conn));
			$friendSourceImport = mysqli_fetch_row($queryResults);
			//var_dump($friendId);
			if($friendSourceImport != NULL){
				$mutualFriendsFB = $facebook->api('/'.$userSourceImport[2].'/mutualfriends/'.$friendSourceImport[2],'GET',array ('access_token' => $userSource[3]));
				//echo  '---';
				//var_dump($mutualFriendsFB);
				$mutualFriendsFBUID = array();
				foreach($mutualFriendsFB['data'] as $mutualFriendFB){
					array_push($mutualFriendsFBUID, $mutualFriendFB['id']);	
				}
				foreach($mutualFriendsMIBData as $mutualFriendMIBData){
					if(isset($mutualFriendMIBData['type1'])){
						$key = array_search($mutualFriendMIBData['UID'], $mutualFriendsFBUID);
						if(!is_numeric($key)){
							$push = array('type1'=>'facebook', 'UID'=>$mutualFriendsFB['data'][$key]['id'], 'NameFB'=>$mutualFriendsFB['data'][$key]['name']);
							array_push($mutualFriendsMIBData, $push);
						}
						$key++;
					}
				}
				if(empty($mutualFriendsMIBData)){
					foreach($mutualFriendsFB['data'] as $mutualFriendFB){
						$push = array('type1'=>'facebook', 'UID'=>$mutualFriendFB['id'], 'NameFB'=>$mutualFriendFB['name']);
						array_push($mutualFriendsMIBData, $push);
					}
				}
			}
		}
	
	$key = 1;
	foreach($mutualFriendsMIBData as $mutualFriendMIBData){
		if($_POST['type'] == 'side' && $key <= 8){
			
			if($key == 4){
				echo '<br>';
			}
			if(isset($mutualFriendMIBData['type1'])){
				echo '<div class="opts_mut" ><img src="https://graph.facebook.com/'.$mutualFriendMIBData["UID"].'/picture?width=50&height=50"></div>';
			}
			if(isset($mutualFriendMIBData['type2'])){
				echo '<div class="opts_mut" ><img src="'.$mutualFriendMIBData['profilePicture'].'" width="40" height="40"></div>';
			}
		
		}
		$key++;
		if($_POST['type'] == 'page'){
			
		}
	
		
		
		/*$entry = $xml->addChild('entry');
		if(isset($mutualFriendMIBData['type1'])){
			$entry->addChild('type1', $mutualFriendMIBData['type1']);
			$entry->addChild('UID', $mutualFriendMIBData['UID']);
			$entry->addChild('NameFB', $mutualFriendMIBData['NameFB']);
		}
		if(isset($mutualFriendMIBData['type2'])){
			$entry->addChild('type2', $mutualFriendMIBData['type2']);
			$entry->addChild('profileLink', $mutualFriendMIBData['profileLink']);
			$entry->addChild('profilePicture', $mutualFriendMIBData['profilePicture']);
			$entry->addChild('NameLI', $mutualFriendMIBData['NameLI']);
		}*/
	}
	mysqli_close($conn);
	
	

?>