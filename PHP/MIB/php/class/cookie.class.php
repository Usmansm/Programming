<?php

class cookie {
 
    public function hashString ($length){
        $chars = "abcde7fgh5i4jkl43mna213sdasopqHGJK32GrstuvwxygjlklklzABCDEFGHIJKLMNOPQRJHTJHGRFSTUVWXYZ01235345345456789";    
    	$size = strlen( $chars );
    	for( $i = 0; $i < $length; $i++ ) {
    		$str .= $chars[ rand( 0, $size - 1 ) ];
    	}
        $hash = md5($str);
    	return $hash;
    }
    
    public function store ($hash1, $hash2, $hash3, $userId, $update = false){
        //var_dump($hash1);
        global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
        if($update){
            $mysqli->query('UPDATE user_hash SET hash_1 = "'.$hash1.'", hash_2 = "'.$hash2.'", hash_3 = "'.$hash3.'" WHERE userId = "'.$userId.'"') or die ($mysqli->error);
        }
        else {
            $mysqli->query('INSERT INTO user_hash (userId, hash_1, hash_2, hash_3) VALUES ("'.$userId.'", "'.$hash1.'", "'.$hash2.'", "'.$hash3.'")')or die ($mysqli->error);
        }
    }
    
    public function userCookie ($userId) {
        $hash1 = $this->hashString(28);
        $hash2 = $this->hashString(36);
        $hash3 = $this->hashString(20);
        if(isset($_COOKIE['userId'])){
           setcookie("hash1", $hash1, time()+2628000, '/');
           setcookie("hash2", $hash2, time()+2628000, '/');
           setcookie("hash3", $hash3, time()+2628000, '/');
           $this->store($hash1, $hash2, $hash3, $userId, true);
        }
        else {
           
           setcookie("userId", $userId, time()+2628000, '/');
           setcookie("hash1", $hash1, time()+2628000, '/');
           setcookie("hash2", $hash2, time()+2628000, '/');
           setcookie("hash3", $hash3, time()+2628000, '/'); 
           $this->store($hash1, $hash2, $hash3, $userId, false);
        }
    }  
    
    public function check () {
        global $config;
        if(!isset($_COOKIE['userId'])){
            return false;
        }
        $userId = $_COOKIE['userId'];
        $hash1 = $_COOKIE['hash1'];
        $hash2 = $_COOKIE['hash2'];
        $hash3 = $_COOKIE['hash3'];
    	$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
        $result = $mysqli->query('SELECT * FROM user_hash WHERE userId = "'.$userId.'" AND hash_1 = "'.$hash1.'" AND hash_2 = "'.$hash2.'" AND hash_3 = "'.$hash3.'"') or die ($mysqli->error);
        if($result->num_rows > 0){
            return $userId;
        }
        else {
            return false;
        }
    }
}

?>