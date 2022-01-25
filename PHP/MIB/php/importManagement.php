<?php
session_start();
include('../config/config.php');
if($_POST['type'] == 'facebook'){
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM source_import WHERE userId = '".$_SESSION['userId']."' AND sourceName = '".$_POST['type']."'";
	$results = mysqli_query($conn, $query);
	//echo mysqli_num_rows($results);
	if(mysqli_num_rows($results) != 1){
		echo 'false';
	}
	else {
		echo 'true';
	}
}

if($_POST['type'] == 'linkedin'){
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM source_import WHERE userId = '".$_SESSION['userId']."' AND sourceName = '".$_POST['type']."'";
	$results = mysqli_query($conn, $query);
	if(mysqli_num_rows($results) != 1){
		echo 'false';
	}
	else {
		echo 'import';
	}
}
?>