<?php

class detail {

	public function dataElement($userId = false, $friendId = false, $element){
		global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		if(is_array($element)){
			$element = implode(', ', $element);	
		}
		if($userId != false && $friendId != false){
			$query = 'SELECT	'.$element.' FROM user_friend_detail WHERE userId = "'.$userId.'" AND friendId = "'.$friendId.'"';
			$result = $mysqli->query($query); 
		}
		if($userId == false && $friendId != false){
			$result = $mysqli->query('SELECT	'.$element.' FROM user_friend_detail WHERE userId = "'.$_SESSION["userId"].'" AND friendId = "'.$friendId.'"'); 
		}
		if($userId != false && $friendId == false){
			$result = $mysqli->query('SELECT	'.$element.' FROM user_detail_private WHERE userId = "'.$userId.'"'); 
		}
		if($userId == false && $friendId == false){
			$result = $mysqli->query('SELECT	'.$element.' FROM user_detail_private WHERE userId = "'.$_SESSION["userId"].'"'); 
		}
		$data = $result->fetch_array();
		return $data;
	}
		
}

?>