<?php

class check {
	
	public function check(){
		//session_start();
		if(isset($_SESSION['userId']) && isset($_SESSION['logged_in'])){
			if($_SESSION['logged_in'] == true){
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;	
		}
	}
	
	public function prepare(){
		//session_start();
		include('../config/config.php');
		include('friend.class.php');
		include('fb.class.php');
		include('import.class.php');
		include('default.class.php');
		include('sanitize.class.php');
		include('user.class.php');
		include('verify.class.php');
	}
}

?>
 