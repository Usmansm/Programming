<?php
session_start();
require_once('../../config/config.php');
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
function namematch($fname,$lname,$fid){
	global $mysql;
	$fname = strtolower($fname);
	$lname = strtolower($lname);
	$query = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId!='". $fid ."' AND ViewableRow!='0' AND FriendFirstName=LOWER('". $fname ."') AND FriendLastName=LOWER('". $lname ."')";
	$res = $mysql->query($query);
	$count = $res->num_rows;
	$_SESSION["dasess"] = $count;
	if($count > 0){
		return 1;
	}
	else{
		return 0;
	}
}
//asdasd
if ($_GET["a"] == "wu") {

    $credat = explode(",.,", $_GET["d"]);
	$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
	if (mysqli_connect_errno()) {
		die("Connect failed: \n" . mysqli_connect_error());
	}
	$_SESSION["lastcredat"] = $credat;
	
		
	if($credat[10] == 'Home'){
		$mail = 'FriendEmail1';
	} elseif($credat[10] == 'Office'){
		$mail = 'FriendEmail2';
	} elseif($credat[10] == 'Other'){
		$mail = 'FriendEmail3';
	}
 $qv = "UPDATE user_friend_detail SET 
	".$credat[11]." = '". $credat[1] . "', 
	FriendDOB='" . $credat[2] . "', 
	FriendTitle='" . $credat[3] . "', 
	FriendHighschool='" . $credat[4] . "', 
	FriendFirstName='" . $credat[5] . "', 
	FriendMiddleName='" . $credat[6] . "', 
	FriendLastName='" . $credat[7] . "', 
	FriendCompany='" . $credat[8] . "', 
	FriendCollege='" . $credat[9] . "',
	FriendComments='" . $credat[12] . "',
	".$mail." ='".$credat[0] ."',
	onlineLink1 = '".$credat[13]."',
	onlineLink2 = '".$credat[14]."'
	WHERE friendId ='" . $_GET["tpid"] . "' AND userId='". $_SESSION["userId"] ."'";
	
	$mailSql = "SELECT * FROM user_friend_email WHERE friendId = '{$_GET["tpid"]}' AND userId = '{$_SESSION["userId"]}' AND emailType = '{$credat[10]}'";
	$result = $mysql->query($mailSql);
	
	if($result->num_rows === 1 ){
		$MailSlq2 = "UPDATE user_friend_email SET emailAddr = '{$credat[0]}' WHERE friendId = '{$_GET["tpid"]}' AND userId = '{$_SESSION["userId"]}' AND emailType = '{$credat[10]}'";
		$Mailresult2 = $mysql->query($MailSlq2);
	}elseif($result->num_rows === 0 ){
		$MailSlq2 = "INSERT INTO user_friend_email (
		userId,
		friendId,
		emailAddr,
		emailType
		)VALUES(
		'{$_SESSION["userId"]}',
		'{$_GET["tpid"]}',
		'{$credat[0]}',
		'{$credat[10]}'
		)";
		$Mailresult2 = $mysql->query($MailSlq2);
	}
	
	
//echo $qv;
 mysqli_query($mysql, $qv);
	$que = "SELECT * FROM user_friend_detail WHERE userId='".  $_SESSION["userId"] ."' AND friendId='". $_GET["tpid"] ."'";
	$res = $mysql->query($que);
	$dat = $res->fetch_assoc();
	$_SESSION["matchdebug"] = $dat;
	$namematch = namematch($dat["FriendFirstName"],$dat["FriendLastName"],$dat["friendId"]);
	$_SESSION["namematch"] = $namematch;
	if($namematch == 1){
		$newq = "UPDATE user_friend_detail SET FriendStatusCode='unverified', ViewableRow='' WHERE userId='".  $_SESSION["userId"] ."' AND friendId='". $_GET["tpid"] ."'";
		$res = $mysql->query($newq);
	}
	else{
		$newq = "UPDATE user_friend_detail SET FriendStatusCode='verified', ViewableRow='' WHERE userId='".  $_SESSION["userId"] ."' AND friendId='". $_GET["tpid"] ."'";
		$res = $mysql->query($newq);
	}
}

elseif($_GET["a"] == "wu2"){

	$credat = explode(",.,", $_GET["d"]);

    $mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
	if (mysqli_connect_errno()) {
		die("Connect failed: \n" . mysqli_connect_error());
	}

	$_SESSION["lastcredat"] = $credat;
	$userId = $_GET["UsedId"];
	$varFromget = $_GET["d"];
	$freindId = $_GET["tpid"];
	//var_dump($varFromget);
	//var_dump($userId);
	
	
	$sql1 = "SELECT `id` FROM user_friend_address WHERE userId = '{$userId}' AND friendId = '{$freindId}' AND friendAddrType = 'home'";
	$sql2 = "SELECT `id` FROM user_friend_address WHERE userId = '{$userId}' AND friendId = '{$freindId}' AND friendAddrType = 'office'";
	
	echo $sql1. "<br />";
	echo $sql2. "<br />";
	
	$result1 = $mysql->query($sql1);
	$num_rows1 =  $result1->num_rows;
	var_dump($num_rows1);
	
	$result2 = $mysql->query($sql2);
	$num_rows2 =  $result2->num_rows;
	//var_dump($num_rows2);
	
	if ($num_rows1 === 1){
		$sql = "UPDATE user_friend_address SET 
				friendStreet= '{$credat[0]}',
				friendCity 	= '{$credat[1]}',
				friendState = '{$credat[2]}',
				friendZip 	= '{$credat[3]}'
				WHERE userId = '{$userId}' AND friendId = '{$freindId}' AND friendAddrType = 'home'";
				$result = $mysql->query($sql);
	}else if ($num_rows1 === 0){
		$sql = "INSERT INTO user_friend_address (
				userId, 
				friendId, 
				friendAddrType, 
				friendStreet, 
				friendCity, 
				friendState , 
				friendZip
				)VALUES(
				'{$userId}',
				'{$freindId}',
				'home',
				'{$credat[0]}',
				'{$credat[1]}',
				'{$credat[2]}',
				'{$credat[3]}'
				)" ;
				$result = $mysql->query($sql);
	}
	
	if($num_rows2 === 1){
		$sql = "UPDATE user_friend_address SET 
				friendStreet= '{$credat[4]}',
				friendCity 	= '{$credat[5]}',
				friendState = '{$credat[6]}',
				friendZip 	= '{$credat[7]}'
				WHERE userId = '{$userId}' AND friendId = '{$freindId}' AND friendAddrType = 'office'";
				$result = $mysql->query($sql);

	}elseif($num_rows2 === 0){
		$sql = "INSERT INTO user_friend_address (
				userId, 
				friendId, 
				friendAddrType, 
				friendStreet, 
				friendCity, 
				friendState , 
				friendZip
				)VALUES(
				'{$userId}',
				'{$freindId}',
				'office',
				'{$credat[4]}',
				'{$credat[5]}',
				'{$credat[6]}',
				'{$credat[7]}'
				)" ;
				$result = $mysql->query($sql);	

	}
	
	/*$qv = "UPDATE user_friend_detail SET 
          FriendAddress1='" . $credat[0] . "',
          FriendCity1='" . $credat[1] . "', 
          FriendState1='" . $credat[2] . "',
          FriendZip1='" . $credat[3] . "',
          FriendAddress2='" . $credat[4] . "',
          FriendCity2='" . $credat[5] . "',
          FriendState2='" . $credat[6] . "',
          FriendZip2='" . $credat[7] . "'
     
     WHERE friendId = '" . $_GET["tpid"] . "'";
	 */
	//mysqli_query($mysql, $qv);
	}
 
  /*code for displaying Email tpye*/
if(isset($_GET['Emailtype'])){
	
	
  $Fid = $_GET['Fid'];
  $UserId = $_GET['UserId'];
  $Emailtype = $_GET['Emailtype'];

  $emailquery = "SELECT * FROM user_friend_email WHERE friendId = '". $Fid ."' AND userId = '". $UserId."' AND emailType = '{$Emailtype}'";
  $emailresult = $mysql->query($emailquery);
  $emailrow = $emailresult->fetch_assoc();
  

	echo $emailrow["emailAddr"];
	
  
 }
 
 /*code for displaying Phone ddasdastpye*/
if(isset($_GET['Phonetype'])){ 
	$PhoneType = $_GET['Phonetype'];
	$Fid = $_GET['Fid'];
	$UserId = $_GET['UserId'];

    $mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
	if (mysqli_connect_errno()) {
		die("Connect failed: \n" . mysqli_connect_error());
	}

  $Phonequery = "SELECT *  FROM user_friend_detail WHERE friendId = '". $Fid ."' AND userId = '". $UserId."'";
  $phoneResult = $mysql->query($Phonequery);
  $PhoneRow = $phoneResult->fetch_assoc();
  echo $PhoneRow[$PhoneType];
  

}


function listemaildrop(){
	global $mysql;
	$emailquery = "SELECT * FROM user_friend_email WHERE friendId = '". $_GET["fid"] ."' AND userId = '". $_SESSION["userId"] ."' AND emailType='Home'";
  $emailquery1 = "SELECT * FROM user_friend_email WHERE friendId = '". $_GET["fidlistemaildrop"] ."' AND userId = '". $_SESSION["userId"] ."' AND emailType='Office'";
  $emailquery2 = "SELECT * FROM user_friend_email WHERE friendId = '". $_GET["fid"] ."' AND userId = '". $_SESSION["userId"] ."' AND emailType='Other'";

	$emailresult = $mysql->query($emailquery);
  $emailresult1 = $mysql->query($emailquery1);
  $emailresult2 = $mysql->query($emailquery2);

    $emailrow = $emailresult->fetch_assoc();
    $emailrow1 = $emailresult1->fetch_assoc();
    $emailrow2 = $emailresult2->fetch_assoc();

	echo "<select id='email_sel' class='detail_drop' onchange='emailview(this.value)' >\n";
    
    if($emailrow["emailAddr"] != ""){
        echo "<option value='". $emailrow["emailAddr"] ."' >Home</option>\n";
    }
    else{
        echo "<option value='None' id='hmail' >Home</option>\n";
    }
    if($emailrow1["emailAddr"] != ""){
        echo "<option value='". $emailrow1["emailAddr"] ."' >Office</option>\n";
    }
    else{
        echo "<option value='None' >Office</option>\n";
    }
    if($emailrow2["emailAddr"] != ""){
        echo "<option value='". $emailrow2["emailAddr"] ."' >Other</option>\n";
    }
    else{
        echo "<option value='None'  >Other</option>\n";
    }

	echo "</select>";
 
}

if (isset($_GET['FamilyName'])){
   
	   
	$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
		
	$FamilyType = $_GET['FamilyType'];
	$FamilyMemid = $_GET['FamilyMemid'];
	$FamilyName = explode(" ", $_GET["FamilyName"]);
	$FamilyPhone = $_GET['FamilyPhone'];
	$FamilyMail = $_GET['FamilyMail'];
	$FamilyDoB = $_GET['FamilyDoB'];
	$OnlineLink1 = $_GET['OnlineLink1'];
	$OnlineLink2 = $_GET['OnlineLink2'];
	$FamilyNote = $_GET['FamilyNote'];

  if (count($FamilyName) == 2 ){
    $query = "UPDATE user_friend_family_details SET 
	FamilyMember_Type = '" . $FamilyType. "',  
    FamilyMember_FirstName='" . $FamilyName[0]. "', 
    FamilyMember_LastName='" . $FamilyName[1] . "',
    FamilyMember_Email='" . $FamilyMail . "', 
    FamilyMember_PhoneCell='" . $FamilyPhone . "', 
    OnlineLink1='" . $OnlineLink1 . "', 
    OnlineLink2='" . $OnlineLink2 . "', 
	FamilyMember_BornOn='" . $FamilyDoB. "', 
    FamilyMember_Notes='" . $FamilyNote . "'
  
    WHERE id = '" . $FamilyMemid . "'" ;
  } else{
    $query = "UPDATE user_friend_family_details SET 
	FamilyMember_Type = '" . $FamilyType. "',  
    FamilyMember_FirstName='" . $FamilyName[0]. "',  
    FamilyMember_Email='" . $FamilyMail . "', 
    FamilyMember_PhoneCell='" . $FamilyPhone . "', 
    OnlineLink1='" . $OnlineLink1 . "', 
    OnlineLink2='" . $OnlineLink2 . "', 
	FamilyMember_BornOn='" . $FamilyDoB. "', 
    FamilyMember_Notes='" . $FamilyNote . "'
 
    WHERE id = '" . $FamilyMemid . "'" ;
    
    

  }
 // echo $query;
  mysqli_query($mysql, $query) or die(mysql_error());
  
  // user_friend_family_details
  /*FamilyMember_BornOn 	FamilyMember_Email  FamilyMember_PhoneCell FamilyMember_PhoneHome FamilyMember_Notes */
   
 // $qv = "UPDATE user_friend_family_details SET 	FamilyMember_FirstName='" . SOME VAR. "', FamilyMember_LastName='" . SOME VAR . "' WHERE friendId = '" . $_GET["tpid"] . "'";
	//mysqli_query($mysql, $qv);
}
  
  



 
?>