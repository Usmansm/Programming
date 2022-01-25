<?php
session_start();
require('../../config/config.php');

$friends = explode('|',$_GET['friends']);
//print(print_r($friends,true).'<br/>');


$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
  
if(mysqli_connect_errno()) {
   die("Connect failed: \n".mysqli_connect_error());
}

if(count($friends) > 1) {
  print('The following users have been deleted:<br/>');
} else print('The following user is deleted:<br/>');

foreach($friends as $friend)
{
  //Sql stuff to delete friend.
  $query = "SELECT FriendFirstName,FriendMiddleName,FriendLastName FROM user_friend_detail WHERE userId='".$_SESSION['userId']."' AND friendId='".$friend."'";
  //print $query.'<br/>';
          //SELECT FriendFirstName, FriendMiddleName, FriendLastName FROM `user_friend_detail` WHERE userId =13 AND friendId =14 
          
  $result = $mysql->query($query) or die($mysql->error);
  
  while(list($firstname, $middlename, $lastname) = $result->fetch_row()) {
    print("&emsp; $firstname $middlename $lastname<br/>");
  }
  
  $query = "DELETE FROM user_friend_detail WHERE userId='".$_SESSION['userId']."' AND friendId='".$friend."'";
  //print $query.'<br/>';
          //DELETE FROM `user_friend_detail` WHERE userId=13 AND friendId=14
          
  $result = $mysql->query($query) or die($mysql->error);
}
?>