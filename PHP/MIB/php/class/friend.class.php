<?php
class friend {
	
	public function userExists($sourceId, $table = 'source_import'){
		global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$result = $mysqli->query('SELECT * FROM '.$table.' WHERE sourceUid = "'.$sourceId.'"');
		$num = $result->num_rows;
		if($num == 0){
			return false;
		}
		if($num >= 1){
			$data = $result->fetch_array();
			return $data['userId'];
		}
	$mysqli->close();
	}
	
		public function userExistsForEmail($emailAddress, $table = 'user_email'){
		global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$result = $mysqli->query('SELECT * FROM '.$table.' WHERE LOWER(emailAddr)= "'.strtolower($emailAddress).'"');
		$num = $result->num_rows;
		if($num == 0){
			return false;
		}
		if($num >= 1){
			$data = $result->fetch_array();
			return $data['userId'];
		}
	$mysqli->close();
	}
	
	public function userExistsForMobile($mobPhone, $table = 'user_detail_public'){
		global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$cell_no_special_chars = preg_replace('(\D+)', '', $mobPhone);
		$cellq = "SELECT * FROM user_detail_public WHERE userPhoneCell='". $cell_no_special_chars ."'";
		$num = $cellq->num_rows;
		if($num == 0){
			return false;
		}
		if($num >= 1){
			$data = $cellq->fetch_array();
			return $data['userId'];
		}
	$mysqli->close();
	}
	public function relationExists($userId, $friendId){
		global $config;
      //  echo 'test1';
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']) or die ($mysqli->error);
		$result = $mysqli->query('SELECT * FROM user_friend_detail WHERE userId = "'.$_SESSION["userId"].'" AND friendId = "'.$friendId.'"') or die ($mysqli->error);
		$_SESSION['query'] = 'SELECT * FROM user_friend_detail WHERE userId = "'.$_SESSION["userId"].'" AND friendId = "'.$friendId.'"';
		$num = $result->num_rows;
       // echo 'Num: '.$num;
		if($num == 0){
           // echo 'test2';
			return false;
		}
		if($num >= 1){
			$data = $result->fetch_array();
			return $data['id'];
		}
		$mysqli->close();
	}
	
	public function addFacebook($data){
		global $config;
		$import = new import;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$data = $import->escape($data, $mysqli);
		if(!$data['userId']){
			$mysqli->query('INSERT INTO users (userStatus, accountType) VALUES ("'.$data["userStatus"].'",  "'.$data["accountType"].'")');
			$data['userId'] = $mysqli->insert_id;
			$mysqli->query('INSERT INTO source_import (userId, sourceUid, sourceName) VALUES ("'.$data["userId"].'",  "'.$data["sourceUid"].'",
			"'.$data["sourceName"].'")') or die ($mysqli->error);
			$data['sourceId'] = $mysqli->insert_id;
			$email = $data['email'];
				//$mysqli->query('INSERT INTO test (testemail) VALUES ("'.$email.'")');
			
				$result = $mysqli->query('SELECT * FROM user_email WHERE emailAddr !="" AND LOWER(emailAddr) = LOWER("'.$email.'")');
				if($result->num_rows == 0){
					//$mysqli->query('INSERT INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("'.$userId.'", "'.$email.'", "Primary", "verified")');
				}
			$mysqli->query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus) VALUES ("'.$_SESSION["userId"].'",  "'.$data["userId"].'",
			"'.$data["sourceId"].'", "'.$data["verified"].'")') or die ($mysqli->error);
			$mysqli->query('INSERT INTO user_detail_private (userId, firstName, middleName, lastName) VALUES ("'.$data["userId"].'",  "'.$data["firstName"].'",
			"'.$data["middleName"].'", "'.$data["lastName"].'")') or die ($mysqli->error);
			$mysqli->query('INSERT INTO user_detail_public (userId, firstName, middleName, lastName) VALUES ("'.$data["userId"].'",  "'.$data["firstName"].'",
			"'.$data["middleName"].'", "'.$data["lastName"].'")') or die ($mysqli->error);
			$mysqli->query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, FriendFirstName, FriendMiddleName, FriendLastName) VALUES ("'.$_SESSION["userId"].'", "'.$data["userId"].'", 
			"'.$data["verified"].'", "'.$data["firstName"].'", "'.$data["middleName"].'", "'.$data["lastName"].'")') or die ($mysqli->error);
		}
		else {
		
			if($data['relationExists'] == false){
				$mysqli->query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, FriendFirstName, FriendMiddleName, FriendLastName) VALUES ("'.$_SESSION["userId"].'", "'.$data["userId"].'", 
				"'.$data["verified"].'", "'.$data["firstName"].'", "'.$data["middleName"].'", "'.$data["lastName"].'")') or die ($mysqli->error) ;
			}
			
		}
		$result = $mysqli->query('SELECT * FROM source_import WHERE sourceUid="'.$data["sourceUid"].'"') or die($mysqli->error);
		if($result->num_rows > 0)
		{
		$sourceImport = $result->fetch_array();
		// search from UFD
		$resultView = $mysqli->query('SELECT * FROM user_friend_detail WHERE userId="'.$_SESSION["userId"].'" AND friendId="'.$sourceImport["userId"].' AND ViewableRow !=0"') or die($mysqli->error);
		
		
		if($resultView->num_rows > 0){
		$sourceImportView = $resultView->fetch_array();
		$result_check_user_friend_source = $mysqli->query('SELECT * FROM userfrnd_source WHERE userId="'.$_SESSION["userId"].'" AND 
		friendId = "'.$data["userId"].'" AND source_import_Id = "'.$sourceImport["sourceId"].'"');
		if($result_check_user_friend_source->num_rows == 0){
		$mysqli->query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus) 
		VALUES ("'.$_SESSION["userId"].'",  "'.$data["userId"].'","'.$sourceImport["sourceId"].'", "'.$data["verified"].'")')
		or die ($mysqli->error);
		}
		}
		}
        return $data['userId'];
	}
	
	public function addLinkedin($data){
		global $config;
		$import = new import;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$data = $import->escape($data, $mysqli);
		if(!$data['userId']){
			$mysqli->query('INSERT INTO users (userStatus, accountType) VALUES ("'.$data["userStatus"].'",  "'.$data["accountType"].'")');
			$data['userId'] = $mysqli->insert_id;
			$mysqli->query('INSERT INTO source_import (userId, sourceUid, sourceName, sourceProfilePicture, sourceProfileLink) VALUES ("'.$data["userId"].'",  "'.$data["sourceUid"].'",
			"'.$data["sourceName"].'", "'.$data["sourceProfilePicture"].'", "'.$data["sourceProfileLink"].'")') or die ($mysqli->error);
			$data['sourceId'] = $mysqli->insert_id;
			$email = $email['email'];
				$result = $mysqli->query('SELECT * FROM user_email WHERE emailAddr = "'.$email.'"');
				if($result->num_rows == 0){
					//$mysqli->query('INSERT INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("'.$userId.'", "'.$email.'", "Primary", "verified")');
				}
			$mysqli->query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus) VALUES ("'.$_SESSION["userId"].'",  "'.$data["userId"].'",
			"'.$data["sourceId"].'", "'.$data["verified"].'")') or die ($mysqli->error);
			$mysqli->query('INSERT INTO user_detail_private (userId, firstName, middleName, lastName) VALUES ("'.$data["userId"].'",  
			"'.$mysqli->real_escape_string($data["firstName"]).'",
			"'.$mysqli->real_escape_string($data["middleName"]).'", "'.$mysqli->real_escape_string($data["lastName"]).'")') or die ($mysqli->error);
			$mysqli->query('INSERT INTO user_detail_public (userId, firstName, middleName, lastName) VALUES ("'.$data["userId"].'",  "'.$mysqli->real_escape_string($data["firstName"]).'",
			"'.$mysqli->real_escape_string($data["middleName"]).'", "'.$mysqli->real_escape_string($data["lastName"]).'")') or die ($mysqli->error);
			$mysqli->query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, FriendFirstName, FriendMiddleName, FriendLastName) VALUES ("'.$_SESSION["userId"].'", "'.$data["userId"].'", 
			"'.$data["verified"].'", "'.$mysqli->real_escape_string(stripslashes($data["firstName"])).'", "'.$mysqli->real_escape_string(stripslashes($data["middleName"])).'", "'.$mysqli->real_escape_string(stripslashes($data["lastName"])).'")') or die ($mysqli->error);
		}
		else {
			if($data['relationExists'] == false){
				$mysqli->query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, FriendFirstName, FriendMiddleName, FriendLastName) VALUES ("'.$_SESSION["userId"].'", "'.$data["userId"].'", 
				"'.$data["verified"].'", "'.$mysqli->real_escape_string(stripslashes($data["firstName"])).'", "'.$mysqli->real_escape_string(stripslashes($data["middleName"])).'", "'.$mysqli->real_escape_string(stripslashes($data["lastName"])).'")');
			}
		}
		$result = $mysqli->query('SELECT * FROM source_import WHERE sourceUid="'.$data["sourceUid"].'"') or die($mysqli->error);
		if($result->num_rows > 0)
		{
		$sourceImport = $result->fetch_array();
		// search from UFD
		$resultView = $mysqli->query('SELECT * FROM user_friend_detail WHERE userId="'.$_SESSION["userId"].'" AND friendId="'.$sourceImport["userId"].' AND ViewableRow !=0"') or die($mysqli->error);
		
		
		if($resultView->num_rows > 0){
		$sourceImportView = $resultView->fetch_array();
		$result_check_user_friend_source = $mysqli->query('SELECT * FROM userfrnd_source WHERE userId="'.$_SESSION["userId"].'" AND 
		friendId = "'.$data["userId"].'" AND source_import_Id = "'.$sourceImport["sourceId"].'"');
		if($result_check_user_friend_source->num_rows == 0){
		$mysqli->query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus) 
		VALUES ("'.$_SESSION["userId"].'",  "'.$data["userId"].'","'.$sourceImport["sourceId"].'", "'.$data["verified"].'")')
		or die ($mysqli->error);
		}
		}
		}
        return $data['userId'];
	}
	public function addSalesforce($data){
		//var_dump($data);
        global $config;
		$import = new import;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		//$data = $import->escape($data, $mysqli);
		if(!$data['userId']){
			$mysqli->query('INSERT INTO users (userStatus, accountType) VALUES ("'.$data["userStatus"].'",  "'.$data["accountType"].'")');
			$data['userId'] = $mysqli->insert_id;
			$mysqli->query('INSERT INTO source_import_sf (userId, sourceUid, sourceName, sourceOrgId, sourceContactId, sourceProfileLink) VALUES ("'.$data["userId"].'",  "'.$data["sourceUid"].'",
			"'.$data["sourceName"].'", "'.$data["orgId"].'", "'.$data["Id"].'", "'.$data['sfUrl'].'")') or die ("ErrorK".$mysqli->error);
			$data['sourceId'] = $mysqli->insert_id;
			$email = $data['Email'];
			$result = $mysqli->query('SELECT * FROM user_email WHERE emailAddr = "'.$email.'"');
			if($result->num_rows == 0){
				$mysqli->query('INSERT INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("'.$data["userId"].'", "'.$email.'", "Primary", "unverified")');
			}
			$mysqli->query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, sourceType) VALUES ("'.$_SESSION["userId"].'",  "'.$data["userId"].'",
			"'.$data["sourceId"].'", "source_import_sf")') or die ("ErrorM".$mysqli->error);
			$mysqli->query('INSERT INTO user_detail_private (userId, firstName, lastName) VALUES ("'.$data["userId"].'",  
			"'.$mysqli->real_escape_string($data["FirstName"]).'", "'.$mysqli->real_escape_string($data["LastName"]).'")') or die ("ErrorL".$mysqli->error);
			$mysqli->query('INSERT INTO user_detail_public (userId, firstName, lastName) VALUES ("'.$data["userId"].'",  "'.$mysqli->real_escape_string($data["FirstName"]).'", "'.$mysqli->real_escape_string($data["LastName"]).'")') or die ("ErrorA".$mysqli->error);
			$mysqli->query('INSERT INTO user_friend_detail (
				userId, 
				friendId, 
				FriendStatusCode, 
				FriendFirstName, 
				FriendLastName, 
				FriendPhoneCell, 
				FriendPhoneHome, 
				FriendPhoneOffice, 
				FriendState1, 
				FriendCity1, 
				FriendZip1, 
				FriendAddress1, 
				FriendCountry1, 
				FriendState2, 
				FriendCity2, 
				FriendZip2, 
				FriendAddress2, 
				FriendEmail1,
				FriendTitle
				) VALUES (
				"'.$_SESSION["userId"].'", 
				"'.$data["userId"].'", 
				"'.$data["verified"].'", 
				"'.$mysqli->real_escape_string($data["FirstName"]).'", 
				"'.$mysqli->real_escape_string($data["LastName"]).'", 
				"'.$data["MobilePhone"].'", 
				"'.$data["Phone"].'",
				"'.$data["OtherPhone"].'",
				"'.$data["MailingState"].'", 
				"'.$data["MailingCity"].'", 
				"'.$data["MailingPostalCode"].'", 
				"'.$data["MailingStreet"].'",
				"'.$data["MailingCountry"].'",
				"'.$data["OtherState"].'", 
				"'.$data["OtherCity"].'", 
				"'.$data["OtherPostalCode"].'", 
				"'.$data["OtherStreet"].'",
				"'.$data["Email"].'",
				"'.$data["Title"].'"
				
				)') or die ("ErrorB".$mysqli->error);
			
		if($data["MobilePhone"] != ""){
																$cell_no_special_chars = preg_replace('(\D+)', '', $data["MobilePhone"]);
									$mysqli->query("UPDATE user_detail_public SET userPhoneCell='". $cell_no_special_chars ."' WHERE userId='". $data["userId"] ."'");
								    
								}
							
			
		}
		else {
			if($data['relationExists'] == false){
				$mysqli->query('INSERT INTO user_friend_detail (
				userId, 
				friendId, 
				FriendStatusCode, 
				FriendFirstName, 
				FriendLastName, 
				FriendPhoneCell, 
				FriendPhoneHome, 
				FriendPhoneOffice, 
				FriendState1, 
				FriendCity1, 
				FriendZip1, 
				FriendAddress1, 
				FriendCountry1, 
				FriendState2, 
				FriendCity2, 
				FriendZip2, 
				FriendAddress2, 
				FriendTitle
				) VALUES (
				"'.$_SESSION["userId"].'", 
				"'.$data["userId"].'", 
				"'.$data["verified"].'", 
				"'.$mysqli->real_escape_string($data["FirstName"]).'", 
				"'.$mysqli->real_escape_string($data["LastName"]).'", 
				"'.$data["MobilePhone"].'", 
				"'.$data["Phone"].'",
				"'.$data["OtherPhone"].'",
				"'.$data["MailingState"].'", 
				"'.$data["MailingCity"].'", 
				"'.$data["MailingPostalCode"].'", 
				"'.$data["MailingStreet"].'",
				"'.$data["MailingCountry"].'",
				"'.$data["OtherState"].'", 
				"'.$data["OtherCity"].'", 
				"'.$data["OtherPostalCode"].'", 
				"'.$data["OtherStreet"].'",
				"'.$data["Title"].'"
				
				)') or die ("ErrorC".$mysqli->error);
			}
		}
		
		//END
		//May be right now we are not checking either the email exist or not in UFE and just populating it regardless of it will duplicates.
		//insert in UFE
		$userfemail = "INSERT INTO user_friend_email(userId,friendId,emailAddr,emailType) VALUES('". $_SESSION["userId"] ."','". $data['userId']."','".$data['Email'] ."','home')";
								$mysqli->query($userfemail);
		
			//insert in U-F-Address
			
			if($data["OtherStreet"]!= ""){
									$mysqli->query("INSERT INTO user_friend_address(userId,friendId,friendAddrType,friendStreet,friendCity,friendState,friendZip,friendCountry) VALUES('".$_SESSION['userId']."','".$data['userId']."','other','". $data['OtherStreet']."','".$data['OtherCity'] ."','". $data['OtherState'] ."','". $data['OtherPostalCode']."','". $data['MailingCountry'] ."')");
							}
			if($data["MailingStreet"]!= ""){
									$mysqli->query("INSERT INTO user_friend_address(userId,friendId,friendAddrType,friendStreet,friendCity,friendState,friendZip,friendCountry) VALUES('".$_SESSION['userId']."','".$data['userId']."','home','". $data['MailingStreet'] ."','". $data['MailingCity'] ."','". $data['MailingState'] ."','". $data['MailingPostalCode'] ."','". $data['MailingCountry'] ."')");
							
																																																		
							}
							//END
		
		$result = $mysqli->query('SELECT * FROM source_import_sf WHERE sourceUid="'.$data["sourceUid"].'"') or die("ErrorD".$mysqli->error);
		if($result->num_rows > 0)
		{
		$sourceImport = $result->fetch_array();
		
		// search from UFD
		$resultView = $mysqli->query('SELECT * FROM user_friend_detail WHERE userId="'.$_SESSION["userId"].'" AND friendId="'.$sourceImport["userId"].' AND ViewableRow!=0"') or die($mysqli->error);
	
		
		if($resultView->num_rows > 0){
			$sourceImportView = $resultView->fetch_array();
		$result_check_user_friend_source = $mysqli->query('SELECT * FROM userfrnd_source WHERE userId="'.$_SESSION["userId"].'" AND 
		friendId = "'.$data["userId"].'" AND source_import_Id = "'.$sourceImport["sourceId"].'"');
		if($result_check_user_friend_source->num_rows == 0){
		$mysqli->query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus) 
		VALUES ("'.$_SESSION["userId"].'",  "'.$data["userId"].'","'.$sourceImport["sourceId"].'", "'.$data["verified"].'")')
		or die ("ErrorE".$mysqli->error);
		}
		}
		}
		else
		{
		$mysqli->query('INSERT INTO source_import_sf (userId, sourceUid, sourceName, sourceOrgId, sourceContactId, sourceProfileLink) VALUES ("'.$data["userId"].'",  "'.$data["sourceUid"].'",
			"'.$data["sourceName"].'", "'.$data["orgId"].'", "'.$data["Id"].'", "'.$data['sfUrl'].'")') or die ("ErrorK".$mysqli->error);
		$data['sourceId'] = $mysqli->insert_id;
$mysqli->query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, sourceType) VALUES ("'.$_SESSION["userId"].'",  "'.$data["userId"].'",
			"'.$data["sourceId"].'", "source_import_sf")') or die ("ErrorM".$mysqli->error);		
		}
        return $data['userId'];
	}
    
	
	public function addCloudSponge($data){
		var_dump($data);
        global $config;
		$import = new import;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		//$data = $import->escape($data, $mysqli);
		if(!$data['userId']){
			$mysqli->query('INSERT INTO users (userStatus, accountType) VALUES ("'.$data["userStatus"].'",  "'.$data["accountType"].'")');
			$data['userId'] = $mysqli->insert_id;
			$mysqli->query('INSERT INTO source_import_cs (userId ,sourceName) VALUES ("'.$data["userId"].'","'.$data['type'].'")') or die ("ErrorK".$mysqli->error);
			$data['sourceId'] = $mysqli->insert_id;
			$email = $data['Email'];
			$result = $mysqli->query('SELECT * FROM user_email WHERE emailAddr = "'.$email.'"');
			if($result->num_rows == 0){
				$mysqli->query('INSERT INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("'.$data["userId"].'", "'.$email.'", "Primary", "unverified")');
			}
			$mysqli->query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, sourceType) VALUES ("'.$_SESSION["userId"].'",  "'.$data["userId"].'",
			"'.$data["sourceId"].'","source_import_cs")') or die ("ErrorM".$mysqli->error);
			$mysqli->query('INSERT INTO user_detail_private (userId, firstName, lastName) VALUES ("'.$data["userId"].'",  
			"'.$mysqli->real_escape_string($data["FirstName"]).'", "'.$mysqli->real_escape_string($data["LastName"]).'")') or die ("ErrorL".$mysqli->error);
			$mysqli->query('INSERT INTO user_detail_public (userId, firstName, lastName) VALUES ("'.$data["userId"].'",  "'.$mysqli->real_escape_string($data["FirstName"]).'", "'.$mysqli->real_escape_string($data["LastName"]).'")') or die ("ErrorA".$mysqli->error);
			$mysqli->query('INSERT INTO user_friend_detail (
				userId, 
				friendId, 
				FriendStatusCode, 
				FriendFirstName, 
				FriendLastName, 
				FriendPhoneCell, 
				FriendPhoneHome, 
				FriendPhoneOffice, 
				FriendAddress1,
FriendState1,
FriendCity1,
FriendCountry1,
FriendZip1,	
	FriendAddress2,
FriendState2,
FriendCity2,
FriendCountry2,
FriendZip2,			
				FriendEmail1
				) VALUES (
				"'.$_SESSION["userId"].'", 
				"'.$data["userId"].'", 
				"'.$data["verified"].'", 
				"'.$mysqli->real_escape_string($data["FirstName"]).'", 
				"'.$mysqli->real_escape_string($data["LastName"]).'", 
				"'.$data["MobilePhone"].'", 
				"'.$data["Phone"].'",
				"'.$data["OtherPhone"].'",
				"'.$data["MailingState"].'", 
				"'.$data["region"].'", 
				"'.$data["city"].'", 
				"'.$data["country"].'", 
				"'.$data["postal_code"].'", 
				"'.$data["MailingState1"].'", 
				"'.$data["region1"].'", 
				"'.$data["city1"].'", 
				"'.$data["country1"].'", 
				"'.$data["postal_code1"].'",
				"'.$data["Email"].'"				
				)') or die ("ErrorB".$mysqli->error);
		}
		else {
			if($data['relationExists'] == false){
				$mysqli->query('INSERT INTO user_friend_detail (
				userId, 
				friendId, 
				FriendStatusCode, 
				FriendFirstName, 
				FriendLastName, 
				FriendPhoneCell, 
				FriendPhoneHome, 
				FriendPhoneOffice, 
				FriendAddress1,
FriendState1,
FriendCity1,
FriendCountry1,
FriendZip1,	
	FriendAddress2,
FriendState2,
FriendCity2,
FriendCountry2,
FriendZip2,			
				FriendEmail1
				) VALUES (
				"'.$_SESSION["userId"].'", 
				"'.$data["userId"].'", 
				"'.$data["verified"].'", 
				"'.$mysqli->real_escape_string($data["FirstName"]).'", 
				"'.$mysqli->real_escape_string($data["LastName"]).'", 
				"'.$data["MobilePhone"].'", 
				"'.$data["Phone"].'",
				"'.$data["OtherPhone"].'",
				"'.$data["MailingState"].'", 
				"'.$data["region"].'", 
				"'.$data["city"].'", 
				"'.$data["country"].'", 
				"'.$data["postal_code"].'", 
				"'.$data["MailingState1"].'", 
				"'.$data["region1"].'", 
				"'.$data["city1"].'", 
				"'.$data["country1"].'", 
				"'.$data["postal_code1"].'",
				"'.$data["Email"].'"				
				)') or die ("ErrorC".$mysqli->error);
			}
		}
		$result = $mysqli->query('SELECT * FROM source_import_cs WHERE userId="'.$data['userId'].'"') or die("ErrorD".$mysqli->error);
		echo 'Cloud';
		if($result->num_rows > 0)
		{
		$sourceImport = $result->fetch_array();
		// search from UFD for viewable
		$resultView = $mysqli->query('SELECT * FROM user_friend_detail WHERE userId="'.$_SESSION["userId"].'" AND friendId="'.$sourceImport["userId"].'" AND ViewableRow !=0"') or die($mysqli->error);
		
		
		if($resultView->num_rows > 0){
		$sourceImportView = $resultView->fetch_array();
		$result_check_user_friend_source = $mysqli->query('SELECT * FROM userfrnd_source WHERE userId="'.$_SESSION["userId"].'" AND 
		friendId = "'.$data["userId"].'" AND sourceType = "source_import_cs"');  // check the MBUID abd FUID and source_import_cs
		if($result_check_user_friend_source->num_rows > 0){
		$data_cs=$result_check_user_friend_source->fetch_array();
		//check for sourcename in _cs
		$result_cs = $mysqli->query('SELECT * FROM source_import_cs WHERE sourceId="'.$data_cs['source_import_Id'].'"') or die("ErrorD".$mysqli->error);
		if($result_cs->num_rows > 0)
		{
		$sname_res=$result_cs->fetch_array();
		$sname=$sname_res['sourceName'];
		$a_array = explode(",",trim($sname));
		if(in_array($data['type'],$a_array))
		{
		}
		else
		{
		$sname=$sname.','.$data['type'];
		$result_cs = $mysqli->query('Update source_import_cs set sourceName="'.$sname.'" WHERE sourceId="'.$data_cs['source_import_Id'].'"') or die("ErrorD".$mysqli->error);
		
		
		}
		
		}
		
		$mysqli->query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus) 
		VALUES ("'.$_SESSION["userId"].'",  "'.$data["userId"].'","'.$sourceImport["sourceId"].'", "'.$data["verified"].'")')
		or die ("ErrorE".$mysqli->error);
		}
		}
		
		//Search in UFD for non viewable
			// search from UFD
		$resultView = $mysqli->query('SELECT * FROM user_friend_detail WHERE userId="'.$_SESSION["userId"].'" AND friendId="'.$sourceImport["userId"].'" AND ViewableRow =0"') or die($mysqli->error);
		
		
		if($resultView->num_rows > 0){
		$sourceImportView = $resultView->fetch_array();
		$result_check_user_friend_source = $mysqli->query('SELECT * FROM userfrnd_source WHERE userId="'.$_SESSION["userId"].'" AND 
		friendId = "'.$sourceImportView["combinedTo"].'" AND source_import_Id = "'.$sourceImport["sourceId"].'"');
		if($result_check_user_friend_source->num_rows == 0){
		$mysqli->query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus) 
		VALUES ("'.$_SESSION["userId"].'",  "'.$data["userId"].'","'.$sourceImport["sourceId"].'", "'.$data["verified"].'")')
		or die ("ErrorE".$mysqli->error);
		}
		}
		
		
	}
        return $data['userId'];
	}
    
    public function addEmail($data){
    	global $config;
		$data['accountType'] = 'personal';
		$import = new import;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$data = $import->escape($data, $mysqli);
        $result = $mysqli->query('SELECT * FROM user_email WHERE emailAddr="'.$data["email"].'"');
        if($result->num_rows > 0){
            $row = $result->fetch_array();
            $data['userId'] = $row['userId'];
        }
        else{
            $data['userId'] = false;
        }
		if(!$data['userId']){
			$mysqli->query('INSERT INTO users (userStatus, accountType) VALUES ("'.$data["verified"].'",  "'.$data["accountType"].'")');
			$data['userId'] = $mysqli->insert_id;
			$mysqli->query('INSERT INTO source_import_cs (userId, sourceName) VALUES ("'.$data["userId"].'",  "'.$data["sourceName"].'")') or die ("ErrorF".$mysqli->error);
			$data['sourceId'] = $mysqli->insert_id;
			if($data['email'] != NULL){
				$mysqli->query('INSERT INTO user_email (userId, emailAddr, emailStatus, emailType) VALUES ("'.$data["userId"].'",  "'.$data["email"].'", "unverified", "Primary")') or die ("Error J".$mysqli->error);
			}
			$mysqli->query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus, sourceType) VALUES ("'.$_SESSION["userId"].'",  "'.$data["userId"].'",
			"'.$data["sourceId"].'", "'.$data["verified"].'", "source_import_cs")') or die ("ErrorG".$mysqli->error);
			$mysqli->query('INSERT INTO user_detail_private (userId, firstName, middleName, lastName) VALUES ("'.$data["userId"].'",  
			"'.$mysqli->real_escape_string($data["firstName"]).'",
			"'.$mysqli->real_escape_string($data["middleName"]).'", "'.$mysqli->real_escape_string($data["lastName"]).'")') or die ("ErrorH".$mysqli->error);
			$mysqli->query('INSERT INTO user_detail_public (userId, firstName, middleName, lastName) VALUES ("'.$data["userId"].'",  "'.$mysqli->real_escape_string($data["firstName"]).'",
			"'.$mysqli->real_escape_string($data["middleName"]).'", "'.$mysqli->real_escape_string($data["lastName"]).'")') or die ("ErrorI".$mysqli->error);
			$mysqli->query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, FriendFirstName, FriendMiddleName, FriendLastName, FriendPhoneCell, FriendState1, FriendCity1, FriendZip1, FriendAddress1) VALUES ("'.$_SESSION["userId"].'", "'.$data["userId"].'", 
			"'.$data["verified"].'", "'.$mysqli->real_escape_string($data["firstName"]).'", "'.$mysqli->real_escape_string($data["middleName"]).'", "'.$mysqli->real_escape_string($data["lastName"]).'", "'.$data["phone"].'", "'.$data["region"].'", "'.$data["city"].'", "'.$data["postal_code"].'", "'.$data["street"].'")') or die ("ErrorJ".$mysqli->error);
		}
		else {
			if($data['relationExists'] == false){
				$mysqli->query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, firstName, middleName, lastName) VALUES "'.$_SESSION["userId"].'", "'.$data["userId"].'", 
				"'.$data["verified"].'", "'.addslashes($mysqli->real_escape_string($data["firstName"])).'", "'.addslashes($mysqli->real_escape_string($data["middleName"])).'", "'.addslashes($mysqli->real_escape_string($data["lastName"])).'"');
			}
		}
        
        return $data['userId'];
	}
   public function mutualFriendsPage($friendId){
   global $config;

   $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
   require_once("../../lib/facebooksdk/src/facebook.php");
	if(!isset($facebook)){
	$facebook = new Facebook(array(
	  'appId'  => $config['facebook_appId'],
	  'secret' => $config['facebook_secret'],
	  'cookie' => true
	));
	}

  function fetch($resource, $params, $body = ''){ 
	$url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
	$context = stream_context_create(
		array('http' => 
		 array('method' => 'GET',
		 )
		)
	   );
	$response = file_get_contents($url, false, $context);
	return json_decode($response);
  }
  $users = array();
  $query = 'SELECT * FROM source_import WHERE userId = "'.$_SESSION["userId"].'"';
  $result = $mysqli->query($query);

  while($row = $result->fetch_array()){
  $accesstoken = $row['sourceAccessToken'];
  $sourceName = $row['sourceName'];
 
	
  if($sourceName === 'facebook' && $accesstoken != ''){
  $query = 'SELECT * FROM source_import WHERE userId = "'.$friendId.'" AND sourceName = "facebook" ';
  $result = $mysqli->query($query);
  if($result->num_rows >0){
  $row = $result->fetch_array();
  $friendUid = $row['sourceUid'];
  $mutualFriendsFB = $facebook->api('/'.$_SESSION["userId"].'/mutualfriends/'.$friendUid, 'GET' ,array ('access_token' => $accesstoken));
 
  foreach($mutualFriendsFB['data'] as $record){
	  
	  $query = 'SELECT * FROM source_import WHERE sourceUid = "'.$record["id"].'"';
	  $result = $mysqli->query($query);
	  $user = $result->fetch_array();
	  if(!in_array($user['userId'], $users)){
		  array_push($users, $user['userId']);
	  }
  }
  }
  }
  if($sourceName === 'linkedin' && $accesstoken != ''){
  $params = array('oauth2_access_token' => $accesstoken, 'format' => 'json');
  $query = 'SELECT * FROM source_import WHERE userId = "'.$friendId.'" AND sourceName = "linkedin" ';
  $result = $mysqli->query($query);
  if($result->num_rows >0){
  $row = $result->fetch_array();
  $friendUid = $row['sourceUid'];

  $data = fetch('/v1/people/'.$friendUid.':(relation-to-viewer:(connections))', $params);

  foreach($data->relationToViewer->connections->values as $temp){
  foreach($temp as $record){

    echo $record->person->{'id'};
	  $query = 'SELECT * FROM source_import WHERE sourceUid = "'.$record->{"id"}.'"';
	  $result = $mysqli->query($query);
	  $user = $result->fetch_array();
	  if(!in_array($user['userId'], $users)){
		  array_push($users, $user['userId']);
	  }
  }
  }
  }
  }
  return $users;

  }
   }
   public function mutualFriendsColumn($friendId){
   global $config;
    $key = 0;
   $users = array();
   $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
   require_once("../../lib/facebooksdk/src/facebook.php");
	if(!isset($facebook)){
	$facebook = new Facebook(array(
		
	  'appId'  => $config['facebook_appId'],
	  'secret' => $config['facebook_secret'],
	  'cookie' => true
	));
	}

  function fetch($resource, $params, $body = ''){ 
	$url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
	$context = stream_context_create(
		array('http' => 
		 array('method' => 'GET',
		 )
		)
	   );
	$response = file_get_contents($url, false, $context);
	return json_decode($response);
  }


	  $querysel = "SELECT * FROM userfrnd_source WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $friendId ."' AND sourceType=''";
	  $qres = $mysqli->query($querysel);
	  while($frnddata = $qres->fetch_assoc()){
	$checkq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fid ."'";
	$cres = $mysqli->query($checkq);
	$cdat = $cres->fetch_assoc();
	$cv = $cdat["ViewableRow"];		  
	if($cv != "0"){
	  	$selq = "SELECT * FROM source_import WHERE sourceId='". $frnddata["source_import_Id"] ."'";
		$selr = $mysqli->query($selq);
		$seldat = $selr->fetch_assoc();
		if($seldat["sourceName"] == "facebook"){
//FB START

  $query = 'SELECT * FROM user_external_accnt WHERE userId = "'.$_SESSION["userId"].'" AND authProvider = "facebook" ';
  $result = $mysqli->query($query);
  if($result->num_rows > 0){
  $row = $result->fetch_array();
  $accesstoken = $row['authAccesstoken'];
  $sourceName = $row['authProvider'];
  if($accesstoken != ''){	
  $query = 'SELECT * FROM source_import WHERE sourceId = "'.$frnddata["source_import_Id"].'"';
  $result = $mysqli->query($query);
  if($result->num_rows > 0){
  $row = $result->fetch_array();
  $friendUid = $row['sourceUid'];

	  $mutualFriendsFB = $facebook->api('/'.$_SESSION["userId"].'/mutualfriends/'.$friendUid, 'GET' ,array ('access_token' => $accesstoken));
	 
    foreach($mutualFriendsFB['data'] as $record){
	  $query = 'SELECT * FROM source_import WHERE sourceUid = "'.$record["id"].'" AND sourceName= "facebook" ';
	  $result = $mysqli->query($query);
	  $user = $result->fetch_array();
	  if(!in_array($user['userId'], $users)){
		  array_push($users, $user['userId']);
		  $key ++;
	  }
	  
  }
  
  
  
  //echo $users;
  }
  }
  }
  }
if($seldat["sourceName"] == "linkedin"){
  //LI START
  $query = 'SELECT * FROM user_external_accnt WHERE userId = "'.$_SESSION["userId"].'" AND authProvider = "linkedin" ';
  $result = $mysqli->query($query);
  if($result->num_rows > 0){
  $row = $result->fetch_array();
  $accesstoken = $row['authAccesstoken'];
  $sourceName = $row['authProvider'];
  if($accesstoken != ''){
  $query = 'SELECT * FROM source_import WHERE sourceId = "'.$frnddata["source_import_Id"].'"';
  $result = $mysqli->query($query);
  if($result->num_rows >0){
  $row = $result->fetch_array();
  $friendUid = $row['sourceUid'];
  $params = array('oauth2_access_token' => $accesstoken, 'format' => 'json');
  $data = fetch('/v1/people/'.$friendUid.':(relation-to-viewer:(connections))', $params);
  foreach($data->relationToViewer->connections->values as $temp){
  foreach($temp as $record){
	  $query = 'SELECT * FROM source_import WHERE sourceUid = "'.$record->{"id"}.'"';
	  $result = $mysqli->query($query);
	  $user = $result->fetch_array();
	  $checkq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $user["userId"] ."'";
	$cres = $mysqli->query($checkq);
	$cdat = $cres->fetch_assoc();
	if($cdat["ViewableRow"] != "0"){
	  if(!in_array($user['userId'], $users)){
	  	if($record->id != "private"){
		  array_push($users, $user['userId']);
		  $key++;
  }
	  }
	  
  }
  }

  }
  //echo $users;
  }
  }
  }
  
  }
	$_SESSION["last_users"] = $users;
	}}//end loop frnd src
  return $users;
  }


public function getsociallink($fid,$src){
	global $config;
		$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
        if($src == "facebook"){
            $cc = "SELECT * FROM source_import WHERE userId='". $fid ."' AND sourceName='facebook'";
            $cr = $mysql->query($cc);
            $shniggy = $cr->fetch_assoc();
            return "http://facebook.com/".$shniggy["sourceUid"];
        }
        if($src == "linkedin"){
            $cc = "SELECT * FROM source_import WHERE sourceId='". $fid ."' AND sourceName='linkedin'";
            $cr = $mysql->query($cc) OR die($mysql->error());
            $shniggy = $cr->fetch_assoc();
            //print_r($expression)
            return $shniggy["sourceProfileLink"];
        }
        if($src == "sf"){
            $cc = "SELECT * FROM source_import_sf WHERE sourceId='". $fid ."'";
            $cr = $mysql->query($cc) OR die($mysql->error());
            $shniggy = $cr->fetch_assoc();
            //print_r($expression)
            return $shniggy["sourceProfileLink"];
        }
}

public function sourceicons($fid) {
	global $config;
		$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);

	$tq = "SELECT * FROM userfrnd_source WHERE userId='" . $_SESSION["userId"] . "' AND friendId='" . $fid . "'";
	$tr = $mysql -> query($tq);
	while ($data = $tr -> fetch_assoc()) {
		if ($data["sourceType"] == "") {
			$aq = "SELECT * FROM source_import WHERE sourceId='" . $data["source_import_Id"] . "'";
			$ar = $mysql -> query($aq);
			while ($dat = $ar -> fetch_assoc()) {
				if ($dat["sourceName"] == "facebook") {
					echo "<a href='". $this->getsociallink($data["userId"],"facebook") ."' target='_blank' class='a_noshow' ><img src='images/facebook.png' style='width: 15px; height: 15px;' /></a> ";
				}
				if ($dat["sourceName"] == "linkedin") {
					echo "<a href='". $this->getsociallink($data["source_import_Id"],"linkedin") ."' target='_blank' class='a_noshow' ><img src='images/linkedin.png' style='width: 15px; height: 15px;' /></a> ";
				}
				if ($dat["sourceName"] == "mail_client") {
					echo "<img src='images/CSV.png' style='width: 15px; height: 15px;' />";
				}
			}
		} else if ($data["sourceType"] == "source_import_cs") {
			$aq = "SELECT * FROM source_import_cs WHERE sourceId='" . $data["source_import_Id"] . "'";
			$ar = $mysql -> query($aq);
			while ($dat = $ar -> fetch_assoc()) {
				echo "<a href='' target='_blank' class='a_noshow' ><img src='images/" . $dat["sourceName"] . ".png' style='width: 15px; height: 15px;' /></a> ";
			}
		} else if ($data["sourceType"] == "source_import_sf") {
			$asf = "SELECT * FROM source_import_sf WHERE userId='" . $fid . "'";
			$ar = $mysql -> query($asf);
			$dat = $ar -> fetch_assoc();
			echo "<a href='". $this->getsociallink($data["source_import_Id"], "sf") ."' target='_blank' class='a_noshow' ><img src='images/salesforce.png' style='width: 15px; height: 15px;' /></a> ";
		}
	}

}

/*public function DispFreProfilePic($fid) {
	global $config;
	$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
	$query22 = "SELECT * FROM userfrnd_source WHERE userId='". $_SESSION["userId"] ."' AND friendId = '" . $fid . "'";
	$res22 = $mysql -> query($query22);
	$raww = $res22 -> fetch_assoc();
	$query2 = "SELECT * FROM source_import WHERE sourceId='". $raww["source_import_Id"] ."'";
	$res2 = $mysql -> query($query2);
	$raw = $res2 -> fetch_assoc();
	$sourceName = $raw["sourceName"];
	$sourceUid = $raw["sourceUid"];
	$sourceProfilePicture = $raw["sourceProfilePicture"];
	if ($sourceName == "facebook") {
		$profilepicurl = "https://graph.facebook.com/" . $sourceUid . "/picture?type=large";
		return $profilepicurl;
	} elseif ($sourceName == "linkedin") {
		if ($sourceProfilePicture == '') {
			$profilepicurl = "images/noimage.png";
			return $profilepicurl;
		}//When Sams end complete:
		$profilepicurl = $sourceProfilePicture;

		return $profilepicurl;
	} else {
		$profilepicurl = "images/noimage.png";
		return $profilepicurl;
	}

}*/
  public function getAvatarLink($id){
        global $config;
            $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
            $result = $mysqli->query('SELECT * FROM source_import WHERE userId = "'.$id.'"');
            
            
            $sources2 = array();
             while($row = $result->fetch_array()){
              //   var_dump($row);
                if($row['sourceProfilePicture'] == '' && $row['sourceName'] == 'facebook'){
                   
                    $link = 'https://graph.facebook.com/'.$row["sourceUid"].'/picture?type=large';
                  //  var_dump($link);
                   
                }
                else {
                    $link = $row['sourceProfilePicture'];
                }
                $sources2[$row["sourceName"]] = $link;
               // var_dump($sources2);
            }
            $sourcesPriority = array('facebook', 'linkedin', 'salesforce');
            $done = false;
            if(!$done){
            foreach($sourcesPriority as $sourcePriority){
                    if(isset($sources2[$sourcePriority])){
                        echo  $sources2[$sourcePriority];
                        $done = true;
                    }
            }
            }
    }

	public function getuserinfo($id){
		global $config;
		$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$getq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $id ."'";
		$getr = $mysql->query($getq);
		$FriendData = $getr->fetch_assoc();
		return $FriendData;
	}
 public function verificationList(){
      global $config;
       $mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
	   //Every person with a name match in the db should already bemarked as "unverified"
	   //so we are able to get all unverifieds and match them that way
	   $unverified_query = "SELECT * FROM user_friend_detail WHERE userId='". addslashes($_SESSION["userId"]) ."' AND ViewableRow!='0' AND FriendStatusCode='unverified'";
	   $res = $mysql->query($unverified_query);
	   while($data = $res->fetch_assoc()){
	   	$ffname = strtolower($data["FriendFirstName"]);
		$flname = strtolower($data["FriendLastName"]);
	    //Before doing anything with this data we need to make sure he does not have a unverified match
	    //or else he would be handled by n-way and thus we can skip them
	    $unmatch = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId!='". $data["friendId"] ."' AND ViewableRow!='0' AND FriendFirstName=LOWER('". addslashes($ffname) ."') AND FriendLastName=LOWER('". addslashes($flname) ."') AND FriendStatusCode='unverified'";
	    $unres = $mysql->query($unmatch);
		$numatches = $unres->num_rows;
		if($numatches < 1){
		//Create/execute the query to check for their verified name match
	   	$query = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND ViewableRow!='0' AND FriendFirstName=LOWER('". addslashes($ffname) ."') AND FriendLastName=LOWER('". addslashes($flname) ."') AND FriendStatusCode='verified'";
		
			
		$res2 = $mysql->query($query);
		$count = $res2->num_rows;
		if($count == 1){
		$fdata = $res2->fetch_assoc();
		$addressq = "SELECT * FROM user_friend_address WHERE friendAddrType='home' AND userId='". $_SESSION["userId"] ."' AND friendId='". $fdata["friendId"] ."'";
		$addres = $mysql->query($addressq);
		$addresses = $addres->fetch_assoc();
		
		$emailq = "SELECT * FROM user_friend_email WHERE userId='". $_SESSION["userId"] ."' AND friendId= '". $fdata["friendId"] ."' LIMIT 1";
		
		$emailres = $mysql->query($emailq);
		$email = $emailres->fetch_assoc();
			$rand = rand(1,86950);
			$rand = $rand + time();
			$rand2 = $rand - 5;
			
			//$url = DispFreProfilePic($fdata["friendId"]);
			//$srcIcons = $this->sourceicons($fdata["friendId"]);
			
			echo "<div class='vv' style='height:79px;'>";
			echo '<div class="verify_l" ><img img src="'.DispFreProfilePic($fdata["friendId"]).'" class="verify_avatarl" /><div class="verify_namel" >'.ucfirst($ffname).' '.ucfirst($flname);
			$thisguy = $this->getuserinfo($fdata["friendId"]);
			

			
			if($thisguy["FriendCity1"] != ""){
				$cityVerifyFriendDisplay =  $thisguy["FriendCity1"];
			} elseif(($cityVerifyFriendDisplay == '') and ($thisguy["FriendCity2"] != "")){
				$cityVerifyFriendDisplay = $thisguy["FriendCity2"];
			}
			
			if($thisguy["FriendState1"] != ""){
				$stateVerifyFriendDisplay =  $thisguy["FriendState1"];
			} elseif(($cityVerifyFriendDisplay == '') and ($thisguy["FriendState2"] != "")){
				$stateVerifyFriendDisplay = $thisguy["FriendState2"];
			}
			
			echo"<div class='leftVerifyModalSrcIcon'>";
			
			$this->sourceicons($fdata["friendId"]);
			
			
			echo '</div></div>			
				<span class="CityStateEmailLeft">';
					//<p class="StateCityEmailParagraph">' .$addresses["friendCity"]. '</p>
					echo '<p class="StateCityEmailParagraph">'.$addresses["friendState"].'</p>
					<p class="StateCityEmailParagraph"><a href="mailto:'. $email["emailAddr"] .'" >'.$email["emailAddr"].'</a></p>
				</span>
			</div>';
			$cityVerifyFriendDisplay = "";
			$stateVerifyFriendDisplay = "";
			$emailVerifyFreindDisplay = "";
			
			echo '<div class="verify_m" >
			  <input type="radio" class="1way" name="fcheck'. $rand .'" value="same-0-'.$fdata["friendId"].'-'.$data["friendId"].'" checked/>Same<br /><input type="radio" name="fcheck'. $rand .'" class="1way" value="dif-0-'.$fdata["friendId"].'-'.$data["friendId"].'"/>Different
			  </div>';
			  echo '<div class="verify_r" ><img img src="'.DispFreProfilePic($data["friendId"]).'" class="verify_avatarr" /><div class="verify_namer" >'.ucfirst($ffname).' '.ucfirst($flname);
			
			$thisguy = $this->getuserinfo($data["friendId"]);
			
			//$sourceicons = $this->sourceicons($data["friendId"]);

		
		
		$addressq = "SELECT * FROM user_friend_address WHERE friendAddrType='home' AND userId='". $_SESSION["userId"] ."' AND friendId='". $data["friendId"] ."'";
		$addres = $mysql->query($addressq);
		$addresses = $addres->fetch_assoc();
		$res2 = $mysql->query($query);
		
		$emailq = "SELECT * FROM user_friend_email WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $data["friendId"] ."' LIMIT 1";
		$emailres = $mysql->query($emailq);
		$email = $emailres->fetch_assoc();
		$count = $res2->num_rows;
		
			if($thisguy["FriendCity1"] != ""){
				$cityVerifyFriendDisplay =  $thisguy["FriendCity1"];
			} elseif(($cityVerifyFriendDisplay == '') and ($thisguy["FriendCity2"] != "")){
				//echo $thisguy["FriendCity2"];
			}
			
			if($thisguy["FriendState1"] != ""){
				$stateVerifyFriendDisplay =  $thisguy["FriendState1"];
			} elseif(($cityVerifyFriendDisplay == '') and ($thisguy["FriendState2"] != "")){
				$stateVerifyFriendDisplay = $thisguy["FriendState2"];
			}
			echo '<div class="rightVerifyModalSrcIcon" >';
				$this->sourceicons($data["friendId"]);
			  echo '
			  </div>
			  </div>
				  <span class="CityStateEmailRight">';
					//<p class="StateCityEmailParagraph">'.$addresses["friendCity"].'</p>
				 echo '	<p class="StateCityEmailParagraph">'.$addresses["friendState"].'</p>
					<p class="StateCityEmailParagraph"><a href="mailto:'. $email["emailAddr"] .'" >'.$email["emailAddr"].'</a></p>
				  </span>
			  </div>
			  </div>';
			$cityVerifyFriendDisplay = "";
			$stateVerifyFriendDisplay = "";
			$emailVerifyFreindDisplay = "";
			  		}
		}
   }
	   
	   
		/*N-WAY CODE*/
 
 			$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
 			//Proces for whiteboard scenario 2: if unverified matches 2 or more VERIFIED and does NOT any unverified ||||||| only 1 on right
 			$query = "SELECT * FROM user_friend_detail  WHERE userId='". $_SESSION["userId"] ."' AND FriendStatusCode='unverified' AND ViewableRow!='0'";
			//$query = $mysql->real_escape_string($query); 
			$query_res = $mysql->query($query);
			$leftid = array();
			while($data = $query_res->fetch_assoc()){
				
				//For each unverified friend do this
				//convert firstname and lastname of friend being compared to loweredy mccasedy
				$ffname = strtolower($data["FriendFirstName"]);
				$flname = strtolower($data["FriendLastName"]);
				//Query for checking if there are any name 
				$match_query_un = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND FriendStatusCode='verified' 
				AND ViewableRow!='0' AND FriendFirstName=LOWER('". $ffname ."') AND FriendLastName=LOWER('". $flname ."')";
				$match_res_un = $mysql->query($match_query_un);
				//Count the number of results
				$matches = $match_res_un->num_rows;
				if($matches > 1){
					//echo "There are multiple matches for unverified friend ".$ffname." ".$flname."<br />";
					//There are more then 2 verified matches
					//Now we check if there are any unveried matches, if there are another process will handle this
					$match_query = "SELECT * FROM user_friend_detail WHERE FriendStatusCode='unverified' AND userId='". $_SESSION["userId"] ."' AND friendId!='". $data["friendId"] ."' AND ViewableRow!='0' AND FriendFirstName=lower('". $ffname ."') AND FriendLastName=lower('". $flname ."')";
					$match_res = $mysql->query($match_query);
					$match_count = $match_res->num_rows;
					if($match_count > 0 ){
						//There are unverified matches, another process will handle this
						//echo "There are unverified matches<br />";
					}
					else{
					$match_res_unn = $mysql->query($match_query_un);
					while($dd = $match_res_unn->fetch_assoc()){
						array_push($leftid,$dd["friendId"]);
					}
						//There are no unverified matches
						$height = 90 * $matches;
						$height = $height - 1;
						//var_dump($height);
						$i=0;
						echo '<div class="verify2_lN" style="height:'.$height.'px;border:none;">';
						while($fdata = $match_res_un->fetch_assoc()){
							
							$i++;
							echo '<div class="VerifyLeftN" > <img class="verify_avatarl" src="'.DispFreProfilePic($fdata["friendId"]).'" />
						
							<div class="verify_namel" > '. ucfirst($fdata["FriendFirstName"]) .' '. ucfirst($fdata["FriendLastName"]);
			$thisguy = $this->getuserinfo($fdata["friendId"]);
			
			if($thisguy["FriendEmail1"] != ""){
				$emailVerifyFreindDisplay =  "<a href='mailto:". $thisguy["FriendEmail1"] ."' >". $thisguy["FriendEmail1"] ."</a>";
			} elseif (($emailVerifyFreindDisplay == '') and ($thisguy["FriendEmail2"] != '')){
				$emailVerifyFreindDisplay = "<a href='mailto:". $thisguy["FriendEmail2"] ."' >". $thisguy["FriendEmail2"] ."</a>";
			} elseif (($emailVerifyFreindDisplay == '')and ($thisguy["FriendEmail3"] != "")){
				$emailVerifyFreindDisplay =  "<a href='mailto:". $thisguy["FriendEmail3"] ."' >". $thisguy["FriendEmail3"] ."</a>";
			}
			
			if($thisguy["FriendCity1"] != ""){
				$cityVerifyFriendDisplay =  $thisguy["FriendCity1"];
			} elseif(($cityVerifyFriendDisplay == '') and ($thisguy["FriendCity2"] != "")){
				//echo $thisguy["FriendCity2"];
			}
			
			if($thisguy["FriendState1"] != ""){
				$stateVerifyFriendDisplay =  $thisguy["FriendState1"];
			} elseif(($cityVerifyFriendDisplay == '') and ($thisguy["FriendState2"] != "")){
				$stateVerifyFriendDisplay = $thisguy["FriendState2"];
			}
				$addressq = "SELECT * FROM user_friend_address WHERE friendAddrType='home' AND userId='". $_SESSION["userId"] ."' AND friendId='". $fdata["friendId"] ."'";
		$addres = $mysql->query($addressq);
		$addresses = $addres->fetch_assoc();
		$res2 = $mysql->query($query);
		$emailq = "SELECT * FROM user_friend_email WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fdata["friendId"] ."' LIMIT 1";
		$emailres = $mysql->query($emailq);
		$email = $emailres->fetch_assoc();
				echo "<div class='leftVerifyModalSrcIcon'>";
							$this->sourceicons($fdata["friendId"]);
				
				echo '</div></div>
					<span class="CityStateEmailLeft">';
					//<p class="StateCityEmailParagraph">'.$addresses["friendCity"].'</p>
					echo '<p class="StateCityEmailParagraph">'.$addresses["friendState"].'</p>
					<p class="StateCityEmailParagraph"><a href="mailto:'. $email["emailAddr"] .'" >'.$email["emailAddr"].'</a></p>
					</span>
					</div>';
				$cityVerifyFriendDisplay = "";
				$stateVerifyFriendDisplay = "";
				$emailVerifyFreindDisplay = "";
						}
						
						echo '</div>';
						$doing = rand(1,38567960);
						echo '<div class="verify_mN" id="'. $doing .'" style="height:'.$height.'px;">';
						$y=0;
					$right_query = "SELECT * FROM user_friend_detail WHERE FriendStatusCode='unverified' AND userId='". $_SESSION["userId"] ."' AND ViewableRow!='0' AND FriendFirstName=lower('". $ffname ."') AND FriendLastName=lower('". $flname ."')";
					$right_res = $mysql->query($right_query);
					$right = $right_res->fetch_assoc();
					$leftc = 0;
						while($i > $y){
							$uid = uniqid();
						$rand_id = rand(1, 1234567890);
						$r1 = $rand_id + 1;
						$r2 = $rand_id + 2;
						$r3 = $rand_id + 3;
						echo '<div class="verifyMidN">
							<input type="radio" id="sameradio'. $r2 .'" onchange="sameverifychecks(\''. $doing .'\',\'sameradio'. $r2 .'\',\'difradio'. $r3 .'\')" id="sameradio'. $r2.'"  value="same-0-'. $leftid[$leftc] .'-'. $data["friendId"] .'" name="f'. $uid .'" class="rb" />Same<br>
							<input type="radio"  id="difradio'. $r3 .'" onchange="difverifychecks(\''. $doing .'\',\'sameradio'. $r2 .'\',\'difradio'. $r3 .'\')" value="dif-0-'. $leftid[$leftc] .'-'. $data["friendId"] .'" name="f'. $uid .'" class="rb" />Different
							</div>';
							$y++;//wtf
							$leftc++;
						}
						echo '</div>';
					
					//Right side
					
					echo '<div class="verify_rN" style="height:'.$height.'px;">
							<img  style="margin-top:10%;" img="" src="'. DispFreProfilePic($right["friendId"]).'" class="verify_avatarr" />
							<div style="margin-top:10%;margin-bottom: 25px;" class="verify_namer">'. $right["FriendFirstName"]." ".$right["FriendLastName"];
									$thisguy = $this->getuserinfo($right["friendId"]);
			if($thisguy["FriendEmail1"] != ""){
				$emailVerifyFreindDisplay =  "<a href='mailto:". $thisguy["FriendEmail1"] ."' >". $thisguy["FriendEmail1"] ."</a>";
			} elseif (($emailVerifyFreindDisplay == '') and ($thisguy["FriendEmail2"] != '')){
				$emailVerifyFreindDisplay = "<a href='mailto:". $thisguy["FriendEmail2"] ."' >". $thisguy["FriendEmail2"] ."</a>";
			} elseif (($emailVerifyFreindDisplay == '')and ($thisguy["FriendEmail3"] != "")){
				$emailVerifyFreindDisplay =  "<a href='mailto:". $thisguy["FriendEmail3"] ."' >". $thisguy["FriendEmail3"] ."</a>";
			}
			
			if($thisguy["FriendCity1"] != ""){
				$cityVerifyFriendDisplay =  $thisguy["FriendCity1"];
			} elseif(($cityVerifyFriendDisplay == '') and ($thisguy["FriendCity2"] != "")){
				//echo $thisguy["FriendCity2"];
			}
			
			if($thisguy["FriendState1"] != ""){
				$stateVerifyFriendDisplay =  $thisguy["FriendState1"];
			} elseif(($cityVerifyFriendDisplay == '') and ($thisguy["FriendState2"] != "")){
				$stateVerifyFriendDisplay = $thisguy["FriendState2"];
			}
							$addressq = "SELECT * FROM user_friend_address WHERE friendAddrType='home' AND userId='". $_SESSION["userId"] ."' AND friendId='". $right["friendId"] ."'";
		$addres = $mysql->query($addressq);
		$addresses = $addres->fetch_assoc();
		$res2 = $mysql->query($query);
		$emailq = "SELECT * FROM user_friend_email WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $right["friendId"] ."' LIMIT 1";
		$emailres = $mysql->query($emailq);
		$email = $emailres->fetch_assoc();
						echo "<div class='rightVerifyModalSrcIcon'>";
							$this->sourceicons($right["friendId"]); 
						echo '</div>
						</div>
							<span class="CityStateEmailRight">';
					//<p class="StateCityEmailParagraph">'.$addresses["friendCity"].'</p>
					echo '<p class="StateCityEmailParagraph">'.$addresses["friendState"].'</p>
					<p class="StateCityEmailParagraph"><a href="mailto:'. $email["emailAddr"] .'" >'.$email["emailAddr"].'</a></p>
							</span>
							</div>';
						$cityVerifyFriendDisplay = "";
						$stateVerifyFriendDisplay = "";
						$emailVerifyFreindDisplay = "";
					//echo "Right side:<br />";
					//echo $right["FriendFirstName"]." ".$right["FriendLastName"]."<br />";
					
					}
				}
				else{
					//There are not more then 2 verified matches
					
				}
				unset($email);
			}
			//End process //
			
			
 			//INCORRECT IGNORE THIS STATEMENT (referrence)!! : Proces for whiteboard scenario 3: if unverified matches only 1 and mathces more then 1 unverfied ||||||| 1 on left 2 or more on right
 			$query = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND ViewableRow!='0' AND FriendStatusCode='verified'";
 			$query_res = $mysql->query($query);
			while($data = $query_res->fetch_assoc()){
				$rightid = array();
				$ffname = strtolower($data["FriendFirstName"]);
				$flname = strtolower($data["FriendLastName"]);
				$matchq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND ViewableRow!='0' AND FriendStatusCode='unverified' AND FriendFirstName=LOWER('". $ffname ."') AND 
				FriendLastName=LOWER('". $flname ."')";
				$matchr = $mysql->query($matchq);
				$matchrr = $mysql->query($matchq);
				$matches = $matchr->num_rows;
				if($matches > 1){
					//echo "There are at least 2 unverified matches for verified friend ". $ffname ." ".$flname."<br />";
					//echo "Left side: <br />".$ffname ." ". $flname." <br />";this is a change
					
					$somerandomvar = 1 ;
					$height = 90 * $matches;
					$height2 = $height - 3 ;
					echo '<div style="height:'.$height2.'px; display: inline-block; width:100%"> <div class="verify_lN" style="height:calc(100% - 3px);">
							<img  style="margin-top: 10%;"   class="verify_avatarl" src="'. DispFreProfilePic($data["friendId"]).'" />
						
							<div class="verify_namel" style="margin-top: 10%;">'. ucfirst($ffname) ." ".ucfirst($flname);
										$thisguy = $this->getuserinfo($data["friendId"]);
			if($thisguy["FriendEmail1"] != ""){
				$emailVerifyFreindDisplay =  "<a href='mailto:". $thisguy["FriendEmail1"] ."' >". $thisguy["FriendEmail1"] ."</a>";
			} elseif (($emailVerifyFreindDisplay == '') and ($thisguy["FriendEmail2"] != '')){
				$emailVerifyFreindDisplay = "<a href='mailto:". $thisguy["FriendEmail2"] ."' >". $thisguy["FriendEmail2"] ."</a>";
			} elseif (($emailVerifyFreindDisplay == '')and ($thisguy["FriendEmail3"] != "")){
				$emailVerifyFreindDisplay =  "<a href='mailto:". $thisguy["FriendEmail3"] ."' >". $thisguy["FriendEmail3"] ."</a>";
			}
			if($thisguy["FriendCity1"] != ""){
				$cityVerifyFriendDisplay =  $thisguy["FriendCity1"];
			} elseif(($cityVerifyFriendDisplay == '') and ($thisguy["FriendCity2"] != "")){
				echo $thisguy["FriendCity2"];
			}
			if($thisguy["FriendState1"] != ""){
				$stateVerifyFriendDisplay =  $thisguy["FriendState1"];
			} elseif(($cityVerifyFriendDisplay == '') and ($thisguy["FriendState2"] != "")){
				$stateVerifyFriendDisplay = $thisguy["FriendState2"];
			} 
			$addressq = "SELECT * FROM user_friend_address WHERE friendAddrType='home' AND userId='". $_SESSION["userId"] ."' AND friendId='". $data["friendId"] ."'";
		$addres = $mysql->query($addressq);
		$addresses = $addres->fetch_assoc();
		$res2 = $mysql->query($query);
		$emailq = "SELECT * FROM user_friend_email WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $data["friendId"] ."' LIMIT 1";
		$emailres = $mysql->query($emailq);
		$email = $emailres->fetch_assoc();
				echo '<div class="leftVerifyModalSrcIcon">';
				$this->sourceicons($data["friendId"]); 
				echo '</div>
						</div>
							<span class="CityStateEmailRight">';
					//<p class="StateCityEmailParagraph">'.$addresses["friendCity"].'</p>
			echo '	<p class="StateCityEmailParagraph">'.$addresses["friendState"].'</p>
					<p class="StateCityEmailParagraph"><a href="mailto:'. $email["emailAddr"] .'" >'.$email["emailAddr"].'</a></p>
							</span>
							</div>';
				
				$cityVerifyFriendDisplay = "";
				$stateVerifyFriendDisplay = "";
				$emailVerifyFreindDisplay = "";	
					
					$i=0;
					$doing = rand(1,456378);
					echo '<div class="verify_mN" id="'. $doing .'" style="height:'.$height.'px;">';
					while($ffdata = $matchrr->fetch_assoc()){
							array_push($rightid,$ffdata["friendId"]);
							//echo "soemthinf";jhbguytghtnbkgjnnghkgn
							//var_dump($rightid);
					}
					//var_dump($rightid);
					$totalids = count($rightid);
					$idc = 0;
					while($matches > $i ){
					$i++;
				    $rand_id = rand(1, 1234567890);
					$r1 = $rand_id + 1;
					$r2 = $rand_id + 2;
					$r3 = $rand_id + 3;
						$uid = uniqid();
						echo '<div class="verifyMidN" style="height:87px;">
							<input type="radio" id="sameradio'. $r2 .'" value="same-1-'. $data["friendId"] .'-'. $rightid[$idc] .'" onchange="sameverifychecks(\''. $doing .'\',\'sameradio'. $r2 .'\',\'difradio'. $r3 .'\')" id="sameradio'. $r2 .'" name="f'. $uid .'" class="rb" />Same<br>
							<input type="radio" id="difradio'. $r3 .'" value="dif-1-'. $data["friendId"] .'-'. $rightid[$idc].'" onchange="difverifychecks(\''. $doing .'\',\'sameradio'. $r2 .'\',\'difradio'. $r3 .'\')" name="f'. $uid .'" class="rb" />Different
							</div>';
							$idc++;
					}
					echo "</div>";
					$y=0;
					echo '<div class="verify2_rN" style="height:'.$height.'px;border:none;">';
					while($fdata = $matchr->fetch_assoc()){ 
						echo '<div class="VerifyRightN" style="height:87px;" > <img class="verify_avatarr" src="'. DispFreProfilePic($fdata["friendId"]).'" />
					
						<div class="verify_namer">'.ucfirst($fdata["FriendFirstName"]).' '. ucfirst($fdata["FriendLastName"]);
									$thisguy = $this->getuserinfo($fdata["friendId"]);
			if($thisguy["FriendEmail1"] != ""){
				$emailVerifyFreindDisplay =  "<a href='mailto:". $thisguy["FriendEmail1"] ."' >". $thisguy["FriendEmail1"] ."</a>";
			} elseif (($emailVerifyFreindDisplay == '') and ($thisguy["FriendEmail2"] != '')){
				$emailVerifyFreindDisplay = "<a href='mailto:". $thisguy["FriendEmail2"] ."' >". $thisguy["FriendEmail2"] ."</a>";
			} elseif (($emailVerifyFreindDisplay == '')and ($thisguy["FriendEmail3"] != "")){
				$emailVerifyFreindDisplay =  "<a href='mailto:". $thisguy["FriendEmail3"] ."' >". $thisguy["FriendEmail3"] ."</a>";
			}
			
			if($thisguy["FriendCity1"] != ""){
				$cityVerifyFriendDisplay =  $thisguy["FriendCity1"];
			} elseif(($cityVerifyFriendDisplay == '') and ($thisguy["FriendCity2"] != "")){
				//echo $thisguy["FriendCity2"];
			}
			
			if($thisguy["FriendState1"] != ""){
				$stateVerifyFriendDisplay =  $thisguy["FriendState1"];
			} elseif(($cityVerifyFriendDisplay == '') and ($thisguy["FriendState2"] != "")){
				$stateVerifyFriendDisplay = $thisguy["FriendState2"];
			}

			
			echo'<div class="rightVerifyModalSrcIcon">';
			$this->sourceicons($fdata["friendId"]); 
			$addressq = "SELECT * FROM user_friend_address WHERE friendAddrType='home' AND userId='". $_SESSION["userId"] ."' AND friendId='". $right["friendId"] ."'";
        $addres = $mysql->query($addressq);
        $addresses = $addres->fetch_assoc();
        $res2 = $mysql->query($query);
        $emailq = "SELECT * FROM user_friend_email WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $right["friendId"] ."' LIMIT 1";
        $emailres = $mysql->query($emailq);
        $email = $emailres->fetch_assoc();
			echo '</div>
						</div>
							<span class="CityStateEmailRight">';
					//<p class="StateCityEmailParagraph">'.$addresses["friendCity"].'</p>
				echo '<p class="StateCityEmailParagraph">'.$addresses["friendState"].'</p>
				<p class="StateCityEmailParagraph"><a href="mailto:'. $email["emailAddr"] .'" >'.$email["emailAddr"].'</a></p>
							</span>
							</div>';
			$cityVerifyFriendDisplay = "";
			$stateVerifyFriendDisplay = "";
			$emailVerifyFreindDisplay = "";
						//echo $fdata["FriendFirstName"]." ".$fdata["FriendLastName"]."<br />";
					}
					echo '</div></div>';
				}
			}
			
			unset($email);
			//End process //
			}
			}


?>