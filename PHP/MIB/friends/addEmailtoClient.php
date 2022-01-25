<?php
session_start();
    require_once('../config/config.php');
	$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
	
	$fid=$_POST['fidc'];
	$guid=$_POST['guidc'];
	
		
		
		$mysqli->query('UPDATE user_frnd_evernote SET noteProcessed=1 where userId="'.$_SESSION['userId'].'"  AND friendId="'.$fid.'"  AND   evnNoteGuid="'.$guid.'"') or die ($mysqli->error);
		echo 'UPDATE user_frnd_evernote SET noteProcessed=1 where  userId="'.$_SESSION['userId'].'" AND friendId="'.$fid.'" AND    evnNoteGuid="'.$guid.'"';
		
		
		echo ' Account Added Successfully';	
	
?>