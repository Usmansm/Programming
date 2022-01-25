<?php
include("../../config/config.php");
class urlCreator {
	   
    public function verifyEmail($userId, $hash,$email=''){
        global $config;
        $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
        $mysqli->query('REPLACE INTO user_verify_email VALUES ("'.$userId.'", "'.$hash.'", "'.$email.'")') or die ($mysqli->error);
        $link = $config['root']."verify.php?userId=".$userId."&hash=".$hash;
        return $link;
	}
	
	public function forgotPassword($userId, $hash){
        global $config;
        $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
         $mysqli->query('REPLACE INTO user_verify_email VALUES ("'.$userId.'", "'.$hash.'", "")') or die ($mysqli->error);
        $link = $config['root']."index.php?userId=".$userId."&hash=".$hash;
        return $link;
	}
      
	public function linkedinRedirect(){
		global $config;
		$url = $config['root'].'php/class/get.php?type=linkedinlogin';
		return $url;
	}
	
	public function facebookRedirect(){
		global $config;
		$url = $config['root'].'php/class/fblogin.php';
		return $url;
	}
	
}

?>