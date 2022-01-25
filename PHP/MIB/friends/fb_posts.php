<?php
session_start();
echo "<style>
.fb_term{
    text-decoration: underline;
}
</style>";
include "../config/config.php";
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
$query = "SELECT * FROM user_frnd_fbpost WHERE userId='". $_SESSION["userId"] ."' ORDER BY FbCreatedtime DESC";
$res = $mysql->query($query);

function user_lookup($uid){
    global $mysql;
    $query = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $uid ."'";
    $res = $mysql->query($query);
    $data = $res->fetch_assoc();
    return $data;
}

function fbpost_lookup($postid){
    global $mysql;
    $query = "SELECT * FROM fb_stream WHERE fbPostid='". $postid ."'";
    $res = $mysql->query($query);
    $data = $res->fetch_assoc();
    return $data;
}

while($data = $res->fetch_assoc()){
    $friend_data = user_lookup($data["friendId"]);
    $post_data = fbpost_lookup($data["fbPostid"]);
	if( $post_data['targetName']=='')
	{
    echo $friend_data["FriendFirstName"] ." ". $friend_data["FriendLastName"] ." posted on ". date('l jS \of F Y h:i:s A',$data["FbCreatedtime"])."<br />";
    $post_dec = $post_data["fbMessage"];
    echo $post_dec."...<a href='". $post_data["fbPermalink"] ."' target='_blank' >See full post</a><br /><br /><br />";
	}
	else
	{
	 echo $friend_data["FriendFirstName"] ." ". $friend_data["FriendLastName"] ." posted  to ".$post_data['targetName'] ." on ". date('l jS \of F Y h:i:s A',$data["FbCreatedtime"])."<br />";
    $post_dec = $post_data["fbMessage"];
    echo $post_dec."...<a href='". $post_data["fbPermalink"] ."' target='_blank' >See full post</a><br /><br /><br />";
	}
}
?>