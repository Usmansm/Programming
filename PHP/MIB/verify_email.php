<?php
session_start();
    require_once('config/config.php');
	$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
	 function simple_decrypt($text,$salt)
    {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }
	$s="active";
	$t="verified";
	$key_value ="MIB"; 
				$plain_text = $_GET["userEmail"]; 
				
		//$decrypted_text = simple_decrypt($plain_text,$key_value);
		$decrypted_text =base64_decode($plain_text);

		
		$mysqli->query('UPDATE user_email SET EmailStatus = "'.$t.'" ,userId="'.$_SESSION['userId'].'"   WHERE emailAddr = "'.$decrypted_text.'"') or die ($mysqli->error);
		//echo 'UPDATE user_email SET EmailStatus = "'.$t.'" ,userId="'.$_SESSION['userId'].'"   WHERE emailAddr = "'.$decrypted_text.'"';
		
		
		echo ' Account Added Successfully';	
	
?>