<?php
//session_start();
echo $friendIds."_";
include('../../config/config.php');
$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
$query = 'SELECT * FROM user_friend_detail WHERE userId = "'.$_SESSION["userId"].'" AND friendId = "'.$friendIds.'"';
$results = mysqli_query($conn, $query) or die (mysqli_error($conn));
$data = mysqli_fetch_assoc($results);
if($data['FriendCategory'] != ''){
	$cats = explode(',', $data['FriendCategory']);
	foreach($cats as $cat){
		$query = "SELECT * FROM user_categories WHERE catId='".$cat."' AND userId = '".$_SESSION['userId']."'";
  		$results = mysqli_query($conn, $query);
  		$data = mysqli_fetch_assoc($results);
		echo <<<CATSTUFF
		<span class="cat" >{$data["catName"]}</span><br />
		
CATSTUFF;
	}
}
else {
	echo "CATS_EMPTY";
}