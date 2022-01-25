<?php
include "../config/config.php";
include "../php/class/user.class.php";
$user = new user;

$newuserid = $user->newUser(false,false,array("firstName" => "testname","middleName" => "testmiddlename", "lastName" => "testlastname"));
echo $newuserid;
//331312

?>