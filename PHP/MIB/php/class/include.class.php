<?php

//Include all the php classes
include('check.class.php');
$check = new check;
if($check->check()){
	$check->prepare();
	//$check->includeAll();	
}

?>