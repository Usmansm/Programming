<?php
  session_start();
require_once('../../config/config.php');


	$mysqli = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
  
  $Uid = $_GET['Uid'];
  $NameArray = explode(",.,", $_GET['NameArray']);
  $EmailArray = explode(",.,", $_GET["EmailArray"]);
  $HomeAddArray = explode(",.,", $_GET["HomeAddArray"]);
  $OfficeAddArray = explode(",.,", $_GET['OfficeAddArray']);
  $_SESSION["deni_debug"] = "made it to line 13";
  
  $result = mysqli_query($mysqli,"SELECT * FROM user_email WHERE emailAddr = '".$EmailArray[0]."'");
  
  $row = $result->fetch_assoc();
  if ($row['emailAddr'] != NULL){ 
  
   /*b. Add record to source_import with userId and add sourceName = ‘manual’; no other data elements need to be added.*/
   
    $sourceResult = mysqli_query($mysqli,"INSERT INTO source_import(userId,sourceName )VALUES('".$row['userId']."', 'manual')");
    
    $result = mysqli_query($mysqli,"INSERT INTO user_friend_detail
    (userId, 
    friendId, 
    FriendStatusCode,
  
    FriendFirstName,
    FriendMiddleName,
    FriendLastName,
    
    FriendAddress1,
    FriendCity1,
    FriendState1,
    FriendZip1,
    
    FriendAddress2,
    FriendCity2,
    FriendState2,
    FriendZip2,
    
    FriendPhone".$HomeAddArray[4].",
    FriendDOB,
    FriendTitle,
    FriendCompany,
    FriendCollege,
    FriendHighschool,
	FriendEmail1

    ) 
    VALUES(
    '".$_SESSION["userId"]."',
    '".$row['userId']."', 
    'unverified',
    
    '".$NameArray[0]."',
    '".$NameArray[1]."',
    '".$NameArray[2]."',
    
    '".$HomeAddArray[0]."',
    '".$HomeAddArray[1]."',
    '".$HomeAddArray[2]."',
    '".$HomeAddArray[3]."',
    
    '".$OfficeAddArray[0]."',
    '".$OfficeAddArray[1]."',
    '".$OfficeAddArray[2]."',
    '".$OfficeAddArray[3]."',
    
    '".$EmailArray[1]."',
    
    '".$EmailArray[2]."',
    '".$EmailArray[3]."',
    '".$NameArray[3]."',
    '".$NameArray[4]."',
    '".$EmailArray[4]."',
	'".$EmailArray[0]."'
    )");
    //Coreys note: Default phone
    
    $UFE = mysqli_query($mysqli,"INSERT INTO  user_friend_email( userId, friendId, emailAddr)VALUES( '".$_SESSION["userId"]."', '".$row['userId']."', '".$EmailArray[0]."')");
     

   /*e. Create new row in userfrnd_source with userId and friendId and source_import_id with the rowId from source_import 
   (the row from when you inserted in source_import in earlier step).  
   Don’t worry about status in this table for now (may change later).*/
   
   //$UFS = mysqli_query($mysqli ,"INSERT INTO  userfrnd_source( userId, friendId, source_import_id)VALUES( '".$_SESSION["userId"]."', '".$row_cnt."', '".NEED TO GET SOURCE IMPORT ID."')");
   
  } else { // Deni: if row['emailadd'] == NULL
  
  
/*a. Create new user in user table; set userStatus to ‘Temp’. 
 Add all data provided from input modal to user_detail_public, 
 user_detail_private and user_email 
 (all with new userId created when user was created).  
 User_email should be set to unverified on emailStatus.*/
 $UserQuery = mysqli_query($mysqli,"INSERT INTO users( userId, userStatus, accountType)VALUES('', 'temp', 'Personal')");

$UserQuery = mysqli_query($mysqli,"SELECT * FROM users ORDER BY userId DESC LIMIT 1");
$UId =  $UserQuery->fetch_assoc();

   // THIS IS 2 LARGE QUERY
   $resultUDPublic = mysqli_query($mysqli,"INSERT INTO user_detail_public
    (userId, 
    firstName, 
    middleName,
    lastName,
    
    userPhone".$HomeAddArray[4].",
    
    addressOne,
    cityOne,
    stateOne,
    zipOne,
    
    addressTwo,
    cityTwo,
    stateTwo,
    zipTwo,
    
    bornOn,
    companyName,
    companyTitle

    ) 
    VALUES(
    '".$UId['userId']."',
    '".$NameArray[0]."',
    '".$NameArray[1]."',
    '".$NameArray[2]."',
    
    '".$EmailArray[1]."',
    
    '".$HomeAddArray[0]."',
    '".$HomeAddArray[1]."',
    '".$HomeAddArray[2]."',
    '".$HomeAddArray[3]."',
    
    '".$OfficeAddArray[0]."',
    '".$OfficeAddArray[1]."',
    '".$OfficeAddArray[2]."',
    '".$OfficeAddArray[3]."',
    
    
    '".$EmailArray[2]."',
    '".$NameArray[3]."',
    '".$EmailArray[3]."'
    )");
     
   $resultUDPrivate = mysqli_query($mysqli,"INSERT INTO user_detail_private
    (userId, 
    firstName, 
    middleName,
    lastName
    )VALUES(
    '".$UId['userId']."',
    '".$NameArray[0]."',
    '".$NameArray[1]."',
    '".$NameArray[2]."'
    )");
    
    $resultUDPrivate = mysqli_query($mysqli,"INSERT INTO user_email
    (userId, 
    emailAddr, 
    emailType,
    EmailStatus

    ) 
    VALUES(
    '".$UId['userId']."',
    '".$EmailArray[0]."',
    'Primary',
    'unverified'
    )");
    
    //b. Add record to source_import with userId and add sourceName = ‘manual’; no other data elements need to be added.
    $sourceResult = mysqli_query($mysqli,"INSERT INTO source_import(userId,sourceName )VALUES('".$UId['userId']."', 'manual')");
    
    /*c. Create new user/friend relationship with existing userId as friendId and session id as the userId in user_friend_detail table; add all data that was provided in input screen (email goes in a separate table for friend).*/
    $result = mysqli_query($mysqli,"INSERT INTO user_friend_detail
    (userId, 
    friendId, 
    FriendStatusCode,
  
    FriendFirstName,
    FriendMiddleName,
    FriendLastName,
    
    FriendAddress1,
    FriendCity1,
    FriendState1,
    FriendZip1,
    
    FriendAddress2,
    FriendCity2,
    FriendState2,
    FriendZip2,
    
    FriendPhoneCell,
    FriendDOB,
    FriendTitle,
    FriendCompany,
    FriendCollege,
    FriendHighschool,
	FriendEmail1

    ) 
    VALUES(
    '".$_SESSION["userId"]."',
    '".$UId['userId']."', 
    'verified',
    
    '".$NameArray[0]."',
    '".$NameArray[1]."',
    '".$NameArray[2]."',
    
    '".$HomeAddArray[0]."',
    '".$HomeAddArray[1]."',
    '".$HomeAddArray[2]."',
    '".$HomeAddArray[3]."',
    
    '".$OfficeAddArray[0]."',
    '".$OfficeAddArray[1]."',
    '".$OfficeAddArray[2]."',
    '".$OfficeAddArray[3]."',
    
    '".$EmailArray[1]."',
    
    '".$EmailArray[2]."',
    '".$EmailArray[3]."',
    '".$NameArray[3]."',
    '".$NameArray[4]."',
    '".$EmailArray[4]."',
	'".$EmailArray[0]."'
    )");
    
    //d. Create new row in user_friend_email table with userId and friendId and email that was matched
    $UFE = mysqli_query($mysqli,"INSERT INTO  user_friend_email( userId, friendId, emailAddr)VALUES( '".$_SESSION["userId"]."', '".$UId['userId']."', '".$EmailArray[0]."')");
   
    //e. Create new row in userfrnd_source with userId and friendId and source_import_id with the rowId from source_import (the row from when you inserted in source_import in earlier step).  Don’t worry about status in this table for now (may change later).
     $UFS = mysqli_query($mysqli,"INSERT INTO  userfrnd_source( userId, friendId, source_import_id, userfrndsourceStatus)VALUES( '".$_SESSION["userId"]."', '".$UId['userId']."', '".$UId['userId']."','verified')");
    
  }
  	/*
	array (size=19)
		0 => string 'First Name' (length=10)
		1 => string 'Middle name' (length=11)
		2 => string 'Last Name' (length=9)
		3 => string 'Company' (length=7)
		4 => string 'College' (length=7)
		5 => string 'Email' (length=5)
		6 => string 'Phone' (length=5)
		7 => string 'Birthday' (length=8)
		8 => string 'Title' (length=5)
		9 => string 'High school' (length=11)
		10 => string 'Home Add' (length=8)
		11 => string 'Home City' (length=9)
		12 => string 'Home State' (length=10)
		13 => string 'Home Zip' (length=8)
		14 => string 'Cell' (length=4)
		15 => string 'Office Add' (length=10)
		16 => string ' Office City' (length=12)
		17 => string 'Office State' (length=12)
		18 => string 'Office Zip' (length=10)
	*/

  
?>
