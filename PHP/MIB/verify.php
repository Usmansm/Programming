<?php
    require_once('config/config.php');
	$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
	//echo 'SELECT * FROM user_verify_email WHERE userId = "'.$_GET["userId"].'" AND hash = "'.$_GET["hash"].'"';
	
	$result = $mysqli->query('SELECT * FROM user_verify_email WHERE userId = "'.$_GET["userId"].'" AND code = "'.$_GET["hash"].'"');
	if($result->num_rows > 0){
	$data= $result->fetch_assoc();
	if($data['verifyEmail']=='')
	{
	$s="active";
	$t="verified";
		
		$mysqli->query('UPDATE users SET userStatus = "'.$s.'" WHERE userId = "'.$_GET["userId"].'"') or die ($mysqli->error);
		$mysqli->query('UPDATE user_email SET EmailStatus = "'.$t.'" WHERE userId = "'.$_GET["userId"].'"') or die ($mysqli->error);
		 $mysqli->query('DELETE FROM user_verify_email WHERE userId = "'.$_GET["userId"].'" AND code = "'.$_GET["hash"].'"');
	
			header('Location: index.php?verify=true') ;
	}
	else if ($data['verifyEmail']!='')
	{
	$mysqli->query('UPDATE user_email SET EmailStatus = "verified" ,userId="'.$_GET["userId"].'"   WHERE emailAddr = "'.$data['verifyEmail'].'"') or die ($mysqli->error);
		//echo 'UPDATE user_email SET EmailStatus = "verified" ,userId="'.$_GET["userId"].'"   WHERE emailAddr = "'.$data['verifyEmail'].'"' ;
		$mysqli->query('DELETE FROM user_verify_email WHERE userId = "'.$_GET["userId"].'" AND code = "'.$_GET["hash"].'"');
	
		
		echo ' Account Added Successfully';	
	
	}
	else {
			header('Location: index.php?verify=false') ;
	}
	}
	
?>