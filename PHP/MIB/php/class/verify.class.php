<?php

class verify {

	public function checkImport($firstName, $middleName, $lastName){
		global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
			$result = $mysqli->query('SELECT * FROM user_friend_detail WHERE userId = "'.$_SESSION["userId"].'" AND FriendFirstName = "'.mysql_real_escape_string($firstName).'" AND FriendLastName = "'.mysql_real_escape_string($lastName).'" AND  FriendStatusCode="verified" AND ViewableRow <> "0" ')  or die($mysqli->error);
		//var_dump($result->num_rows);
		//var_dump($result->fetch_array());
			
		if($result->num_rows >= 1){
			//Add the  code for adding comma seperated values
		$matchedwith='';
			while($data=$result->fetch_assoc())
			{
			$matchedwith.=$data['friendId'].",";
			}
			return $matchedwith;
			//return true;
		}
		else {
			return false;
		}
	}
	
		public function checkImportFriend($firstName, $middleName, $lastName,$fid){
		global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$result = $mysqli->query('SELECT * FROM user_friend_detail WHERE userId = "'.$_SESSION["userId"].'" AND FriendFirstName = "'.mysql_real_escape_string($firstName).'" AND FriendLastName = "'.mysql_real_escape_string($lastName).'"   AND   FriendStatusCode="verified" AND friendId!='.$fid.' AND ViewableRow <> "0" ')  or die($mysqli->error);
		//var_dump($result->num_rows);
		//var_dump($result->fetch_array());
			
		if($result->num_rows >= 1){
			//Add the  code for adding comma seperated values
		$matchedwith='';
			while($data=$result->fetch_assoc())
			{
			$matchedwith.=$data['friendId'].",";
			}
			return $matchedwith;
			//return true;
		}
		else {
			return false;
		}
		
	}
	
}

?>