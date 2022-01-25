<?php
session_start();
include('../config/config.php');
//var_dump($config);
//$_SESSION['CALLING'] = $_GET;
//print_r($_GET);
//unset($_SESSION['test']);
echo "testin if this showsins uppins";
$verifyFriends = $_GET['val'];
if($_GET['type'] == 'multiple'){
    if($_GET['val'] == 'different'){
        reset($_SESSION['exists']);
        $key = key($_SESSION['exists']);
        $_SESSION['key'] = $key;
        foreach($_SESSION['new'][$key] as $new){
            single('dif'.$_SESSION['exists'][$key].'-'.$new);
            print_r('dif'.$_SESSION['exists'][$key].'-'.$new);
        }
        unset($_SESSION['exists'][$key]);
        unset($_SESSION['new'][$key]);
    }
    else {
        reset($_SESSION['exists']);
        $key = key($_SESSION['exists']);
        $_SESSION['key'] = $key;
        foreach($_SESSION['new'][$key] as $new){
            if($_GET['val'] == $new){
                single($_SESSION['exists'][$key].'-'.$new); 
                print_r($_SESSION['exists'][$key].'-'.$new);
            }
            else {
                single('dif'.$_SESSION['exists'][$key].'-'.$new); 
                print_r('dif'.$_SESSION['exists'][$key].'-'.$new);
            }
        }
        unset($_SESSION['exists'][$key]);
        unset($_SESSION['new'][$key]);
    }
}
if($_GET['type'] == 'multiple2'){
    if($_GET['val'] == 'different'){
        reset($_SESSION['rightFriend']);
        $key = key($_SESSION['rightFriend']);
        $_SESSION['key'] = $key;
        foreach($_SESSION['leftFriends'][$key] as $new){
            single('dif'.$new.'-'.$_SESSION['rightFriend'][$key]);
            print_r('dif'.$new.'-'.$_SESSION['rightFriend'][$key]);
        }
        unset($_SESSION['rightFriend'][$key]);
        unset($_SESSION['leftFriends'][$key]);
    }
    else {
        reset($_SESSION['rightFriend']);
        $key = key($_SESSION['rightFriend']);
        $_SESSION['key'] = $key;
        foreach($_SESSION['leftFriends'][$key] as $new){
            if($_GET['val'] == $new){
                single($new.'-'.$_SESSION['rightFriend'][$key]); 
                print_r($new.'-'.$_SESSION['rightFriend'][$key]);
            }
            else {
                single('dif'.$new.'-'.$_SESSION['rightFriend'][$key]); 
                print_r('dif'.$new.'-'.$_SESSION['rightFriend'][$key]);
            }
        }
        unset($_SESSION['rightFriend'][$key]);
        unset($_SESSION['leftFriends'][$key]);
    }
}
if($_GET['type'] == 'single') {
    single($verifyFriends);
}
function single($verifyFriends){
	$_SESSION['singleHelp'][] = $verifyFriends;
	//var_dump($verifyFriends);
    //$_SESSION['verify'][] = $verifyFriends;
    global $config;
$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
if(substr($verifyFriends, 0, 3) == 'dif'){
$verifyId = substr($verifyFriends, 3);
$query = "INSERT INTO different_users (userId, selectorId) VALUES ('".$_SESSION['userId']."', '".$verifyId."')";
mysqli_query($conn, $query) or die (mysqli_error($conn));
$friends = explode('-', $verifyFriends);
$query = "UPDATE  user_friend_detail SET FriendStatusCode= 'verified' WHERE id = '".$friends[1]."'";
mysqli_query($conn, $query) or die (mysqli_error($conn));	
$query = "UPDATE  user_friend_detail SET FriendStatusCode= 'verified' WHERE id = '".$friends[0]."'";
mysqli_query($conn, $query) or die (mysqli_error($conn));		
}

else {
	$friends = explode('-', $verifyFriends);
	
	$num = count($friends);
	$query = "SELECT * FROM different_users WHERE userId = '".$_SESSION['userId']."' AND selectorId = '".$verifyFriends."' ";
	$results = mysqli_query($conn, $query) or die(mysqli_error($conn));
	$num2 = mysqli_num_rows($results);
	//var_dump($friends);
	///echo 'Num: '.$num.'    Num2:'.$num2;
	if($num == 2 && $num2 == 0){
		//$_SESSION['test'] = $verifyFriends;
		$query = "SELECT id, userId, friendId, `FriendFirstName`, `FriendMiddleName`, `FriendLastName`, `FriendAddress1`, `FriendCity1`, `FriendState1`, `FriendCountry1`, `FriendZip1`, `FriendAddress2`, `FriendCity2`, `FriendState2`, `FriendCountry2`, `FriendZip2`, `FriendPhoneCell`, `FriendPhoneHome`, `FriendPhoneOffice`, `FriendDOB`, `FriendTitle`, `FriendCompany`, `FriendCollege`, `FriendHighschool`, `FriendComments`, `FriendAvatarOverride`, `FriendEmail1`, `FriendEmail2`, `FriendEmail3`, `FriendCategory`, `importSources` FROM user_friend_detail WHERE id = '".$friends[0]."' AND userId = '".$_SESSION['userId']."'";                             
		$results = mysqli_query($conn, $query) or die(mysqli_error($conn));
		//var_dump($query);
		$masterFriend = mysqli_fetch_row($results);
		$query = "SELECT id, userId, friendId, `FriendFirstName`, `FriendMiddleName`, `FriendLastName`, `FriendAddress1`, `FriendCity1`, `FriendState1`, `FriendCountry1`, `FriendZip1`, `FriendAddress2`, `FriendCity2`, `FriendState2`, `FriendCountry2`, `FriendZip2`, `FriendPhoneCell`, `FriendPhoneHome`, `FriendPhoneOffice`, `FriendDOB`, `FriendTitle`, `FriendCompany`, `FriendCollege`, `FriendHighschool`, `FriendComments`, `FriendAvatarOverride`, `FriendEmail1`, `FriendEmail2`, `FriendEmail3`, `FriendCategory`, `importSources` FROM user_friend_detail WHERE id = '".$friends[1]."' AND userId = '".$_SESSION['userId']."'";
		$results = mysqli_query($conn, $query);
		//var_dump($query);
		$childFriend = mysqli_fetch_row($results);
		print_r($masterFriend);
		print_r($childFriend);
		$key = 0;
		$newMasterFriend = $masterFriend;
		$updateCats = false;
		//echo 'ChildFriend[30] == '.$childFriend[30].'<br><br><br>';
		$query = "UPDATE userfrnd_source SET friendId = '".$masterFriend[2]."' WHERE userId = '".$_SESSION['userId']."' AND friendId = '".$childFriend[2]."'";
		mysqli_query($conn, $query);
		
		foreach($masterFriend as $master){
			echo 'Starting with masterRecord nmr: '.$key.'<br>';
			echo 'Record Value = '.$master.'<br>'; 
			$updateCats = false;
			if($updateCats){
				//echo 'Starting updating the categories! <br>' ;
				$cats = explode(',', $newMasterFriend[29]);
				//var_dump($cats);
				foreach($cats as $catId){
					//echo 'Current CatId:' .$catId.'  <br>' ;
					$query = "SELECT * FROM user_categories WHERE catId = '".$catId."'";
					$result = mysqli_query($conn, $query);
					$data = mysqli_fetch_row($result);
					//var_dump($data);
					$friends = explode(',', $data[4]);
					if(!in_array($childFriend[2], $friends)){
						$friends[] = $childFriend[2];
					}
					//var_dump($friends);
					$friendsDone = implode(',', $friends);
					//var_dump($friendsDone);
					$query = "UPDATE  user_categories SET catFriends= '".$friendsDone."' WHERE catId = '".$catId."'";
					mysqli_query($conn, $query);	
				}
				$updateCats = false;
			}
			if($key != 29 && $key != 30 && $key != 0 && $key != 1 && $key != 2){
				if($master == ''){
					echo 'Overridable Master Field Found! <br>';
					echo 'Child Value = '.$childFriend[$key].'<br>';
					$newMasterFriend[$key] = $childFriend[$key];
				}
			}
			
			$key++;
			//echo '<br>'.$key;
			
		}
		//var_dump($updateCats);
		$query = "UPDATE  user_friend_detail SET 
		`FriendFirstName` = '".addslashes($newMasterFriend[3])."', 
		`FriendMiddleName` = '".addslashes($newMasterFriend[4])."', 
		`FriendLastName` = '".addslashes($newMasterFriend[5])."', 
		`FriendAddress1` = '".$newMasterFriend[6]."', 
		`FriendCity1` = '".$newMasterFriend[7]."', 
		`FriendState1` = '".$newMasterFriend[8]."', 
		`FriendCountry1` = '".$newMasterFriend[9]."', 
		`FriendZip1` = '".$newMasterFriend[10]."', 
		`FriendAddress2` = '".$newMasterFriend[11]."', 
		`FriendCity2` = '".$newMasterFriend[12]."', 
		`FriendState2` = '".$newMasterFriend[13]."', 
		`FriendCountry2` = '".$newMasterFriend[14]."', 
		`FriendZip2` = '".$newMasterFriend[15]."', 
		`FriendPhoneCell` = '".$newMasterFriend[16]."', 
		`FriendPhoneHome` = '".$newMasterFriend[17]."', 
		`FriendPhoneOffice` = '".$newMasterFriend[18]."', 
		`FriendDOB` = '".$newMasterFriend[19]."', 
		`FriendTitle` = '".$newMasterFriend[20]."', 
		`FriendCompany` = '".$newMasterFriend[21]."', 
		`FriendCollege` = '".$newMasterFriend[22]."', 
		`FriendHighschool` = '".$newMasterFriend[23]."', 
		`FriendComments` = '".$newMasterFriend[24]."', 
		`FriendAvatarOverride` = '".$newMasterFriend[25]."', 
		`FriendEmail1` = '".$newMasterFriend[26]."', 
		`FriendEmail2` = '".$newMasterFriend[27]."', 
		`FriendEmail3` = '".$newMasterFriend[28]."', 
		`FriendCategory` = '".$newMasterFriend[29]."', 
		`importSources` = '".$newMasterFriend[30]."' 
		WHERE id = '".$masterFriend[0]."'";
		//var_dump($query);
		mysqli_query($conn, $query) or die(mysqli_error($conn));
		$query = "UPDATE  user_friend_detail SET ViewableRow= '0', FriendStatusCode = 'verified' WHERE id = '".$childFriend[0]."'";
		mysqli_query($conn, $query) or die(mysqli_error($conn));
			
	}
}
}
//echo $key;
?>