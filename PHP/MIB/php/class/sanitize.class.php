<?php

class sanitize{
	
	public function input($input, $fieldName, $maxLength = false, $type = false, $mysqli = false, $conn = false){
		$notification = new notification;
		if($input == '') {
			$notification->inputAlert('Please make sure you fill in all the required fields.', $fieldName);
			return false;
		}
		else {
			if($maxLength != false){
				if(strlen($input) > $maxLength){
					$notification->inputAlert('One of the fields has more than the maximum allowed charactors.', $fieldName);
					return false;
				}
			}
			if($type != false){
				$inputType = gettype($input);
				if($type != $inputType){
					$notification->inputAlert('One of the fields has an incorrect input type, please check the field requirements.', $fieldName);
					return false;
				}
			}
			if($mysqli && $conn != false){
				$input = mysqli_real_escape_string($conn, $input);
			}
			return $input;
		}		
		
	}
    public function manualUser($data){
        if(empty($data['email'])){
            return false;
        }
        if(empty($data['firstName'])){
            return false;
        }
        if(empty($data['lastName'])){
            return false;
        }
        global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
        $result = $mysqli->query('SELECT * FROM user_email WHERE emailAddr = "'.$data['email'].'"');
        if($result->num_rows > 0){
            return 'Emailadress is already known in the system';
        }
    }
	
}

?>