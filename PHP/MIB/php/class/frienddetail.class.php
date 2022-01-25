<?php
class friend {

	public function userExists($sourceId, $table = 'source_import') {
		global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$result = $mysqli -> query('SELECT * FROM ' . $table . ' WHERE sourceUid = "' . $sourceId . '"');
		$num = $result -> num_rows;
		if ($num == 0) {
			return false;
		}
		if ($num >= 1) {
			$data = $result -> fetch_array();
			return $data['userId'];
		}
		$mysqli -> close();
	}

	public function relationExists($userId, $friendId) {
		global $config;
		//  echo 'test1';
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']) or die($mysqli -> error);
		$result = $mysqli -> query('SELECT * FROM user_friend_detail WHERE userId = "' . $_SESSION["userId"] . '" AND friendId = "' . $friendId . '"') or die($mysqli -> error);
		$_SESSION['query'] = 'SELECT * FROM user_friend_detail WHERE userId = "' . $_SESSION["userId"] . '" AND friendId = "' . $friendId . '"';
		$num = $result -> num_rows;
		// echo 'Num: '.$num;
		if ($num == 0) {
			// echo 'test2';
			return false;
		}
		if ($num >= 1) {
			$data = $result -> fetch_array();
			return $data['id'];
		}
		$mysqli -> close();
	}

	public function addFacebook($data) {
		//echo 'DATA';
		// var_dump($data);
		global $config;
		$import = new import;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$data = $import -> escape($data, $mysqli);
		if (!$data['userId']) {
			$mysqli -> query('INSERT INTO users (userStatus, accountType) VALUES ("' . $data["userStatus"] . '",  "' . $data["accountType"] . '")');
			$data['userId'] = $mysqli -> insert_id;
			$mysqli -> query('INSERT INTO source_import (userId, sourceUid, sourceName) VALUES ("' . $data["userId"] . '",  "' . $data["sourceUid"] . '",
			"' . $data["sourceName"] . '")') or die($mysqli -> error);
			$data['sourceId'] = $mysqli -> insert_id;
			$email = $email['email'];
			$result = $mysqli -> query('SELECT * FROM user_email WHERE emailAddr = "' . $email . '"');
			if ($result -> num_rows == 0) {
				$mysqli -> query('INSERT INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("' . $userId . '", "' . $email . '", "Primary", "verified")');
				$mysqli -> query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus) VALUES ("' . $_SESSION["userId"] . '",  "' . $data["userId"] . '",
			"' . $data["sourceId"] . '", "' . $data["verified"] . '")') or die($mysqli -> error);
				$mysqli -> query('INSERT INTO user_detail_private (userId, firstName, middleName, lastName) VALUES ("' . $data["userId"] . '",  "' . $data["firstName"] . '",
			"' . $data["middleName"] . '", "' . $data["lastName"] . '")') or die($mysqli -> error);
				$mysqli -> query('INSERT INTO user_detail_public (userId, firstName, middleName, lastName) VALUES ("' . $data["userId"] . '",  "' . $data["firstName"] . '",
			"' . $data["middleName"] . '", "' . $data["lastName"] . '")') or die($mysqli -> error);
				$mysqli -> query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, FriendFirstName, FriendMiddleName, FriendLastName) VALUES ("' . $_SESSION["userId"] . '", "' . $data["userId"] . '", 
			"' . $data["verified"] . '", "' . $data["firstName"] . '", "' . $data["middleName"] . '", "' . $data["lastName"] . '")') or die($mysqli -> error);

			}else{
			$mysqli -> query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, FriendFirstName, FriendMiddleName, FriendLastName) VALUES ("' . $_SESSION["userId"] . '", "' . $data["userId"] . '", 
				"' . $data["verified"] . '", "' . $data["firstName"] . '", "' . $data["middleName"] . '", "' . $data["lastName"] . '")') or die($mysqli -> error);	
			}
		} else {
			if ($data['relationExists'] == false) {
				$mysqli -> query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, FriendFirstName, FriendMiddleName, FriendLastName) VALUES ("' . $_SESSION["userId"] . '", "' . $data["userId"] . '", 
				"' . $data["verified"] . '", "' . $data["firstName"] . '", "' . $data["middleName"] . '", "' . $data["lastName"] . '")') or die($mysqli -> error);

			} else {

			}
		}
		$result = $mysqli -> query('SELECT * FROM source_import WHERE sourceUid="' . $data["sourceUid"] . '"') or die($mysqli -> error);
		$sourceImport = $result -> fetch_array();
		$result_check_user_friend_source = $mysqli -> query('SELECT * FROM userfrnd_source WHERE userId="' . $_SESSION["userId"] . '" AND 
		friendId = "' . $data["userId"] . '" AND source_import_Id = "' . $sourceImport["sourceId"] . '"');
		if ($result_check_user_friend_source -> num_rows == 0) {
			$mysqli -> query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus) 
		VALUES ("' . $_SESSION["userId"] . '",  "' . $data["userId"] . '","' . $sourceImport["sourceId"] . '", "' . $data["verified"] . '")') or die($mysqli -> error);
		}
		return $data['userId'];
	}

	public function addLinkedin($data) {
		global $config;
		$import = new import;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$data = $import -> escape($data, $mysqli);
		if (!$data['userId']) {
			$mysqli -> query('INSERT INTO users (userStatus, accountType) VALUES ("' . $data["userStatus"] . '",  "' . $data["accountType"] . '")');
			$data['userId'] = $mysqli -> insert_id;
			$mysqli -> query('INSERT INTO source_import (userId, sourceUid, sourceName, sourceProfilePicture, sourceProfileLink) VALUES ("' . $data["userId"] . '",  "' . $data["sourceUid"] . '",
			"' . $data["sourceName"] . '", "' . $data["sourceProfilePicture"] . '", "' . $data["sourceProfileLink"] . '")') or die($mysqli -> error);
			$data['sourceId'] = $mysqli -> insert_id;
			$email = $email['email'];
			$result = $mysqli -> query('SELECT * FROM user_email WHERE emailAddr = "' . $email . '"');
			if ($result -> num_rows == 0) {
				$mysqli -> query('INSERT INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("' . $userId . '", "' . $email . '", "Primary", "verified")');
			}
			$mysqli -> query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus) VALUES ("' . $_SESSION["userId"] . '",  "' . $data["userId"] . '",
			"' . $data["sourceId"] . '", "' . $data["verified"] . '")') or die($mysqli -> error);
			$mysqli -> query('INSERT INTO user_detail_private (userId, firstName, middleName, lastName) VALUES ("' . $data["userId"] . '",  
			"' . $mysqli -> real_escape_string($data["firstName"]) . '",
			"' . $mysqli -> real_escape_string($data["middleName"]) . '", "' . $mysqli -> real_escape_string($data["lastName"]) . '")') or die($mysqli -> error);
			$mysqli -> query('INSERT INTO user_detail_public (userId, firstName, middleName, lastName) VALUES ("' . $data["userId"] . '",  "' . $mysqli -> real_escape_string($data["firstName"]) . '",
			"' . $mysqli -> real_escape_string($data["middleName"]) . '", "' . $mysqli -> real_escape_string($data["lastName"]) . '")') or die($mysqli -> error);
			$mysqli -> query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, FriendFirstName, FriendMiddleName, FriendLastName) VALUES ("' . $_SESSION["userId"] . '", "' . $data["userId"] . '", 
			"' . $data["verified"] . '", "' . $mysqli -> real_escape_string(stripslashes($data["firstName"])) . '", "' . $mysqli -> real_escape_string(stripslashes($data["middleName"])) . '", "' . $mysqli -> real_escape_string(stripslashes($data["lastName"])) . '")') or die($mysqli -> error);
		} else {
			if ($data['relationExists'] == false) {
				$mysqli -> query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, FriendFirstName, FriendMiddleName, FriendLastName) VALUES ("' . $_SESSION["userId"] . '", "' . $data["userId"] . '", 
				"' . $data["verified"] . '", "' . $mysqli -> real_escape_string(stripslashes($data["firstName"])) . '", "' . $mysqli -> real_escape_string(stripslashes($data["middleName"])) . '", "' . $mysqli -> real_escape_string(stripslashes($data["lastName"])) . '")');
			}
		}
		$result = $mysqli -> query('SELECT * FROM source_import WHERE sourceUid="' . $data["sourceUid"] . '"') or die($mysqli -> error);
		$sourceImport = $result -> fetch_array();
		$result_check_user_friend_source = $mysqli -> query('SELECT * FROM userfrnd_source WHERE userId="' . $_SESSION["userId"] . '" AND 
		friendId = "' . $data["userId"] . '" AND source_import_Id = "' . $sourceImport["sourceId"] . '"');
		if ($result_check_user_friend_source -> num_rows == 0) {
			$mysqli -> query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus) 
		VALUES ("' . $_SESSION["userId"] . '",  "' . $data["userId"] . '","' . $sourceImport["sourceId"] . '", "' . $data["verified"] . '")') or die($mysqli -> error);
		}
		return $data['userId'];
	}

	public function addSalesforce($data) {
		var_dump($data);
		global $config;
		$import = new import;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		//$data = $import->escape($data, $mysqli);
		if (!$data['userId']) {
			$mysqli -> query('INSERT INTO users (userStatus, accountType) VALUES ("' . $data["userStatus"] . '",  "' . $data["accountType"] . '")');
			$data['userId'] = $mysqli -> insert_id;
			$mysqli -> query('INSERT INTO source_import_sf (userId, sourceUid, sourceName, sourceOrgId, sourceContactId, sourceProfileLink) VALUES ("' . $data["userId"] . '",  "' . $data["sourceUid"] . '",
			"' . $data["sourceName"] . '", "' . $data["orgId"] . '", "' . $data["Id"] . '", "' . $data["sourceProfileLink"] . '")') or die($mysqli -> error);
			$data['sourceId'] = $mysqli -> insert_id;
			$email = $data['Email'];
			$result = $mysqli -> query('SELECT * FROM user_email WHERE emailAddr = "' . $email . '"');
			if ($result -> num_rows == 0) {
				$mysqli -> query('INSERT INTO user_email (userId, emailAddr, emailType, emailStatus) VALUES ("' . $data["userId"] . '", "' . $email . '", "Primary", "verified")');
			}
			$mysqli -> query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, sourceType) VALUES ("' . $_SESSION["userId"] . '",  "' . $data["userId"] . '",
			"' . $data["sourceId"] . '", "source_import_sf")') or die($mysqli -> error);
			$mysqli -> query('INSERT INTO user_detail_private (userId, firstName, middleName, lastName) VALUES ("' . $data["userId"] . '",  
			"' . $mysqli -> real_escape_string($data["FirstName"]) . '",
			"' . $mysqli -> real_escape_string($data["MiddleName"]) . '", "' . $mysqli -> real_escape_string($data["LastName"]) . '")') or die($mysqli -> error);
			$mysqli -> query('INSERT INTO user_detail_public (userId, firstName, middleName, lastName) VALUES ("' . $data["userId"] . '",  "' . $mysqli -> real_escape_string($data["FirstName"]) . '",
			"' . $mysqli -> real_escape_string($data["MiddleName"]) . '", "' . $mysqli -> real_escape_string($data["LastName"]) . '")') or die($mysqli -> error);
			$mysqli -> query('INSERT INTO user_friend_detail (
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
				"' . $_SESSION["userId"] . '", 
				"' . $data["userId"] . '", 
				"' . $data["verified"] . '", 
				"' . $mysqli -> real_escape_string($data["FirstName"]) . '", 
				"' . $mysqli -> real_escape_string($data["LastName"]) . '", 
				"' . $data["MobilePhone"] . '", 
				"' . $data["Phone"] . '",
				"' . $data["OtherPhone"] . '",
				"' . $data["MailingState"] . '", 
				"' . $data["MailingCity"] . '", 
				"' . $data["MailingPostalCode"] . '", 
				"' . $data["MailingStreet"] . '",
				"' . $data["MailingCountry"] . '",
				"' . $data["OtherState"] . '", 
				"' . $data["OtherCity"] . '", 
				"' . $data["OtherPostalCode"] . '", 
				"' . $data["OtherStreet"] . '",
				"' . $data["Title"] . '"
				
				)') or die($mysqli -> error);
		} else {
			if ($data['relationExists'] == false) {
				$mysqli -> query('INSERT INTO user_friend_detail (
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
				"' . $_SESSION["userId"] . '", 
				"' . $data["userId"] . '", 
				"' . $data["verified"] . '", 
				"' . $mysqli -> real_escape_string($data["FirstName"]) . '", 
				"' . $mysqli -> real_escape_string($data["LastName"]) . '", 
				"' . $data["MobilePhone"] . '", 
				"' . $data["Phone"] . '",
				"' . $data["OtherPhone"] . '",
				"' . $data["MailingState"] . '", 
				"' . $data["MailingCity"] . '", 
				"' . $data["MailingPostalCode"] . '", 
				"' . $data["MailingStreet"] . '",
				"' . $data["MailingCountry"] . '",
				"' . $data["OtherState"] . '", 
				"' . $data["OtherCity"] . '", 
				"' . $data["OtherPostalCode"] . '", 
				"' . $data["OtherStreet"] . '",
				"' . $data["Title"] . '"
				
				)') or die($mysqli -> error);
			}
		}
		$result = $mysqli -> query('SELECT * FROM source_import WHERE sourceUid="' . $data["sourceUid"] . '"') or die($mysqli -> error);
		$sourceImport = $result -> fetch_array();
		$result_check_user_friend_source = $mysqli -> query('SELECT * FROM userfrnd_source WHERE userId="' . $_SESSION["userId"] . '" AND 
		friendId = "' . $data["userId"] . '" AND source_import_Id = "' . $sourceImport["sourceId"] . '"');
		if ($result_check_user_friend_source -> num_rows == 0) {
			$mysqli -> query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus) 
		VALUES ("' . $_SESSION["userId"] . '",  "' . $data["userId"] . '","' . $sourceImport["sourceId"] . '", "' . $data["verified"] . '")') or die($mysqli -> error);
		}
		return $data['userId'];
	}

	public function addEmail($data) {
		global $config;
		$data['accountType'] = 'personal';
		$import = new import;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$data = $import -> escape($data, $mysqli);
		$result = $mysqli -> query('SELECT * FROM user_email WHERE emailAddr="' . $data["email"] . '"');
		if ($result -> num_rows > 0) {
			$row = $result -> fetch_array();
			$data['userId'] = $row['userId'];
		} else {
			$data['userId'] = false;
		}
		if (!$data['userId']) {
			$mysqli -> query('INSERT INTO users (userStatus, accountType) VALUES ("' . $data["verified"] . '",  "' . $data["accountType"] . '")');
			$data['userId'] = $mysqli -> insert_id;
			$mysqli -> query('INSERT INTO source_import_cs (userId, sourceName) VALUES ("' . $data["userId"] . '",  "' . $data["sourceName"] . '")') or die($mysqli -> error);
			$data['sourceId'] = $mysqli -> insert_id;
			if ($data['email'] != NULL) {
				$mysqli -> query('INSERT INTO user_email (userId, emailAddr, emailStatus, emailType) VALUES ("' . $data["userId"] . '",  "' . $data["email"] . '", "unverified", "Primary")') or die($mysqli -> error);
			}
			$mysqli -> query('INSERT INTO userfrnd_source (userId, friendId, source_import_Id, userfrndsourceStatus, sourceType) VALUES ("' . $_SESSION["userId"] . '",  "' . $data["userId"] . '",
			"' . $data["sourceId"] . '", "' . $data["verified"] . '", "source_import_cs")') or die($mysqli -> error);
			$mysqli -> query('INSERT INTO user_detail_private (userId, firstName, middleName, lastName) VALUES ("' . $data["userId"] . '",  
			"' . $mysqli -> real_escape_string($data["firstName"]) . '",
			"' . $mysqli -> real_escape_string($data["middleName"]) . '", "' . $mysqli -> real_escape_string($data["lastName"]) . '")') or die($mysqli -> error);
			$mysqli -> query('INSERT INTO user_detail_public (userId, firstName, middleName, lastName) VALUES ("' . $data["userId"] . '",  "' . $mysqli -> real_escape_string($data["firstName"]) . '",
			"' . $mysqli -> real_escape_string($data["middleName"]) . '", "' . $mysqli -> real_escape_string($data["lastName"]) . '")') or die($mysqli -> error);
			$mysqli -> query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, FriendFirstName, FriendMiddleName, FriendLastName, FriendPhoneCell, FriendState1, FriendCity1, FriendZip1, FriendAddress1) VALUES ("' . $_SESSION["userId"] . '", "' . $data["userId"] . '", 
			"' . $data["verified"] . '", "' . $mysqli -> real_escape_string($data["firstName"]) . '", "' . $mysqli -> real_escape_string($data["middleName"]) . '", "' . $mysqli -> real_escape_string($data["lastName"]) . '", "' . $data["phone"] . '", "' . $data["region"] . '", "' . $data["city"] . '", "' . $data["postal_code"] . '", "' . $data["street"] . '")') or die($mysqli -> error);
		} else {
			if ($data['relationExists'] == false) {
				$mysqli -> query('INSERT INTO user_friend_detail (userId, friendId, FriendStatusCode, firstName, middleName, lastName) VALUES "' . $_SESSION["userId"] . '", "' . $data["userId"] . '", 
				"' . $data["verified"] . '", "' . addslashes($mysqli -> real_escape_string($data["firstName"])) . '", "' . addslashes($mysqli -> real_escape_string($data["middleName"])) . '", "' . addslashes($mysqli -> real_escape_string($data["lastName"])) . '"');
			}
		}

		return $data['userId'];
	}

	public function mutualFriendsPage($friendId) {
		global $config;

		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		require_once ("../../lib/facebooksdk/src/facebook.php");
		require_once ('../../config/config.php');
		if (!isset($facebook)) {
			$facebook = new Facebook( array('appId' => $config['facebook_appId'], 'secret' => '0bf87c797d468745e3f40a0eee2e763d', 'cookie' => true));
		}

		function fetch($resource, $params, $body = '') {
			$url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
			$context = stream_context_create(array('http' => array('method' => 'GET', )));
			$response = file_get_contents($url, false, $context);
			return json_decode($response);
		}

		$users = array();
		$query = 'SELECT * FROM source_import WHERE userId = "' . $_SESSION["userId"] . '"';
		$result = $mysqli -> query($query);

		while ($row = $result -> fetch_array()) {
			$accesstoken = $row['sourceAccessToken'];
			$sourceName = $row['sourceName'];

			if ($sourceName === 'facebook' && $accesstoken != '') {
				$query = 'SELECT * FROM source_import WHERE userId = "' . $friendId . '" AND sourceName = "facebook" ';
				$result = $mysqli -> query($query);
				if ($result -> num_rows > 0) {
					$row = $result -> fetch_array();
					$friendUid = $row['sourceUid'];
					$mutualFriendsFB = $facebook -> api('/' . $_SESSION["userId"] . '/mutualfriends/' . $friendUid, 'GET', array('access_token' => $accesstoken));

					foreach ($mutualFriendsFB['data'] as $record) {

						$query = 'SELECT * FROM source_import WHERE sourceUid = "' . $record["id"] . '"';
						$result = $mysqli -> query($query);
						$user = $result -> fetch_array();
						if (!in_array($user['userId'], $users)) {
							array_push($users, $user['userId']);
						}
					}
				}
			}
			if ($sourceName === 'linkedin' && $accesstoken != '') {
				$params = array('oauth2_access_token' => $accesstoken, 'format' => 'json');
				$query = 'SELECT * FROM source_import WHERE userId = "' . $friendId . '" AND sourceName = "linkedin" ';
				$result = $mysqli -> query($query);
				if ($result -> num_rows > 0) {
					$row = $result -> fetch_array();
					$friendUid = $row['sourceUid'];

					$data = fetch('/v1/people/' . $friendUid . ':(relation-to-viewer:(connections))', $params);

					foreach ($data->relationToViewer->connections->values as $temp) {
						foreach ($temp as $record) {
							if ($record -> id != "private") {
								//echo $record->person->{'id'};
								$query = 'SELECT * FROM source_import WHERE sourceUid = "' . $record -> id . '"';
								$result = $mysqli -> query($query);
								$user = $result -> fetch_array();
								$viewq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $user["userId"] ."'";
								$viewr = $mysqli->query($viewq);
								$view = $viewr->fetch_assoc();
								if($view["ViewableRow"] == "0"){
									$user["userId"] = $view["combinedTo"];
								}
								if ($user["sourceProfileLink"] != "") {
									if (!in_array($user['userId'], $users)) {
										array_push($users, $user['userId']);
									}
								}
							}
						}
					}
				}
			}
			// return $users;

		}

	}

	public function mutualFriendsColumn($friendId) {
		global $config;
		$key = 0;
		$users = array();
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		require_once ("../lib/facebooksdk/src/facebook.php");
		require_once ("../config/config.php");

		if (!isset($facebook)) {
			$facebook = new Facebook( array('appId' => $config['facebook_appId'], 'secret' => '0bf87c797d468745e3f40a0eee2e763d', 'cookie' => true));
		}

		function fetch($resource, $params, $body = '') {
			$url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
			$context = stream_context_create(array('http' => array('method' => 'GET', )));
			$response = file_get_contents($url, false, $context);
			return json_decode($response);
		}

		$querysel = "SELECT * FROM userfrnd_source WHERE userId='" . $_SESSION["userId"] . "' AND friendId='" . $friendId . "' AND sourceType=''";
		$qres = $mysqli -> query($querysel);
		while ($frnddata = $qres -> fetch_assoc()) {
			$selq = "SELECT * FROM source_import WHERE sourceId='" . $frnddata["source_import_Id"] . "'";
			$selr = $mysqli -> query($selq);
			$seldat = $selr -> fetch_assoc();
			if ($seldat["sourceName"] == "facebook") {
				//FB START

				$query = 'SELECT * FROM user_external_accnt WHERE userId = "' . $_SESSION["userId"] . '" AND authProvider = "facebook" ';
				$result = $mysqli -> query($query);
				if ($result -> num_rows > 0) {
					$row = $result -> fetch_array();
					$accesstoken = $row['authAccesstoken'];
					$sourceName = $row['authProvider'];
					if ($accesstoken != '') {
						$realq = "SELECT * userfrnd_source WHERE userId='" . $_SESSION["userId"] . "' AND ";

						$query = 'SELECT * FROM source_import WHERE sourceId = "' . $frnddata["source_import_Id"] . '"';
						$result = $mysqli -> query($query);
						if ($result -> num_rows > 0) {
							$row = $result -> fetch_array();
							$friendUid = $row['sourceUid'];

							$mutualFriendsFB = $facebook -> api('/' . $_SESSION["userId"] . '/mutualfriends/' . $friendUid, 'GET', array('access_token' => $accesstoken));

							foreach ($mutualFriendsFB['data'] as $record) {
								$query = 'SELECT * FROM source_import WHERE sourceUid = "' . $record["id"] . '" AND sourceName= "facebook" ';
								$result = $mysqli -> query($query);
								$user = $result -> fetch_array();
								$checkq = "SELECT * FROM user_friend_detail WHERE userId='" . $_SESSION["userId"] . "' AND friendId='" . $user["userId"] . "'";
								$cres = $mysqli -> query($checkq);
								$cdat = $cres -> fetch_assoc();
								if ($cdat["ViewableRow"] != "0") {
									if (!in_array($user['userId'], $users)) {
										array_push($users, $user['userId']);
										$key++;
									}
								}
							}

							//echo $users;
						}
					}
				}
			}
			if ($seldat["sourceName"] == "linkedin") {
				$_SESSION["lifound"] = 0;
				//LI START
				$query = 'SELECT * FROM user_external_accnt WHERE userId = "' . $_SESSION["userId"] . '" AND authProvider = "linkedin" ';
				$result = $mysqli -> query($query);
				if ($result -> num_rows > 0) {
					$row = $result -> fetch_array();
					$accesstoken = $row['authAccesstoken'];
					$sourceName = $row['authProvider'];
					if ($accesstoken != '') {
						$query = 'SELECT * FROM source_import WHERE sourceId = "' . $frnddata["source_import_Id"] . '"';
						$result = $mysqli -> query($query);
						$start = 0;
						$stop = 20;
						$loop = TRUE;
						$_SESSION["cdebug"] = array();
						if ($result -> num_rows > 0) {
							array_push($_SESSION["cdebug"], "1");
							$row = $result -> fetch_array();
							$friendUid = $row['sourceUid'];
							while ($loop == TRUE) {
								array_push($_SESSION["cdebug"], "2");
								$params = array('oauth2_access_token' => $accesstoken, 'start' => $start, 'count' => $stop, 'format' => 'json');
								$_SESSION["cparams"] = $params;
								$data = fetch('/v1/people/' . $friendUid . ':(relation-to-viewer:(connections))', $params);
								$cc = count($data -> relationToViewer -> connections -> values);
								if ($cc != 0) {
									array_push($_SESSION["cdebug"], "3");
									foreach ($data->relationToViewer->connections->values as $temp) {
										array_push($_SESSION["cdebug"], "4");
										foreach ($temp as $record) {
											if ($record -> id != "private") {
												array_push($_SESSION["cdebug"], "5");
												$query = 'SELECT * FROM source_import WHERE sourceUid = "' . $record -> id . '"';
												$result = $mysqli -> query($query);
												$user = $result -> fetch_array();
												if ($user["sourceProfileLink"] != "") {
													$viewq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $user["userId"] ."'";
								$viewr = $mysqli->query($viewq);
								$view = $viewr->fetch_assoc();
								if($view["ViewableRow"] == "0"){
									$user["userId"] = $view["combinedTo"];
								}
													if (!in_array($user['userId'], $users)) {
														$_SESSION["lifound"]++;
														array_push($_SESSION["cdebug"], "6");
														array_push($_SESSION["cdebug"], "7");
														array_push($users, $user['userId']);
													}
												}
											}
										}
									}
								} else {
									$loop = FALSE;
								}
								$start = $stop;
								$stop = $stop + 20;
							}
							// echo $users;
						}
					}
				}

			}
			$_SESSION["last_users"] = $users;
		}
		//var_dump ($users);
		return $users;
	}

	public function getAvatarLink($id) {
		global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$result = $mysqli -> query('SELECT * FROM source_import WHERE userId = "' . $id . '"');

		$sources2 = array();
		while ($row = $result -> fetch_array()) {
			var_dump($row);
			if ($row['sourceProfilePicture'] == '' && $row['sourceName'] == 'facebook') {

				$link = 'https://graph.facebook.com/' . $row["sourceUid"] . '/picture?type=large';
				var_dump($link);

			} else {
				$link = $row['sourceProfilePicture'];
			}
			$sources2[$row["sourceName"]] = $link;
			var_dump($sources2);
		}
		$sourcesPriority = array('facebook', 'linkedin', 'salesforce');
		$done = false;
		if (!$done) {
			foreach ($sourcesPriority as $sourcePriority) {
				if (isset($sources2[$sourcePriority])) {
					echo $sources2[$sourcePriority];
					$done = true;
				}
			}
		}
	}

	function getsociallink($fid, $src) {
		global $config;
		$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		if ($src == "facebook") {
			$cc = "SELECT * FROM source_import WHERE userId='" . $fid . "' AND sourceName='facebook'";
			$cr = $mysql -> query($cc);
			$shniggy = $cr -> fetch_assoc();
			return "http://facebook.com/" . $shniggy["sourceUid"];
		}
		if ($src == "linkedin") {
			$cc = "SELECT * FROM source_import WHERE sourceId='" . $fid . "' AND sourceName='linkedin'";
			$cr = $mysql -> query($cc) or die($mysql -> error());
			$shniggy = $cr -> fetch_assoc();
			//print_r($expression)
			return $shniggy["sourceProfileLink"];
		}
	}

	function sourceicons($fid) {
		global $config;
		$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$tq = "SELECT * FROM userfrnd_source WHERE userId='" . $_SESSION["userId"] . "' AND friendId='" . $fid . "'";
		$tr = $mysql -> query($tq);
		while ($data = $tr -> fetch_assoc()) {
			if ($data["sourceType"] == "") {
				$aq = "SELECT * FROM source_import WHERE sourceId='" . $data["source_import_Id"] . "'";
				$ar = $mysql -> query($aq);
				while ($dat = $ar -> fetch_assoc()) {
					if ($dat["sourceName"] == "facebook") {
						echo "<a href='" . $this -> getsociallink($fid, "facebook") . "' target='_blank' class='a_noshow' ><img src='images/facebook.png' style='width: 15px; height: 15px;' /></a> ";
					}
					if ($dat["sourceName"] == "linkedin") {
						echo "<a href='" . $this -> getsociallink($data["source_import_Id"], "linkedin") . "' target='_blank' class='a_noshow' ><img src='images/linkedin.png' style='width: 15px; height: 15px;' /></a> ";
					}
				}
			} else if ($data["sourceType"] == "source_import_cs") {
				$aq = "SELECT * FROM source_import_cs WHERE sourceId='" . $data["source_import_Id"] . "'";
				$ar = $mysql -> query($aq);
				while ($dat = $ar -> fetch_assoc()) {
					echo "<a href='' target='_blank' class='a_noshow' ><img src='images/" . $dat["sourceName"] . ".png' style='width: 15px; height: 15px;' /></a> ";
				}
			} else if ($data["sourceType"] == "source_import_sf") {
				echo "<a href='' target='_blank' class='a_noshow' ><img src='images/salesforce.png' style='width: 15px; height: 15px;' /></a> ";
			}
		}

	}

	public function verificationList() {
		global $config;
		$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
		$query = "SELECT * FROM user_friend_detail WHERE userId = '" . $_SESSION['userId'] . "' AND ViewableRow != '0'";
		$results8 = mysqli_query($conn, $query);
		$fullNames = array();
		$multipleNames = array();
		while ($row = mysqli_fetch_assoc($results8)) {
			$fullName = $row['FriendFirstName'] . ' ' . $row['FriendLastName'];

			if (!in_array(strtolower($fullName), $fullNames)) {
				array_push($fullNames, strtolower($fullName));
			} else {
				$name = array($row['FriendFirstName'], $row['FriendLastName']);
				array_push($multipleNames, $name);
			}
		}
		$data = array();
		$first = true;
		//$key2 = 2;
		//$key3 = 0;
		$key2 = 2;
		$key3 = 0;
		foreach ($multipleNames as $name) {
			$query = "SELECT * FROM user_friend_detail WHERE FriendFirstName = '" . mysqli_real_escape_string($conn, $name[0]) . "' AND	FriendLastName = '" . mysqli_real_escape_string($conn, $name[1]) . "' AND userId = '" . $_SESSION['userId'] . "' AND ViewableRow != '0'";
			$results8 = mysqli_query($conn, $query);
			$num = mysqli_num_rows($results8);
			$key = 0;
			while ($row = mysqli_fetch_row($results8)) {
				//var_dump($row);
				$data[$key] = $row;
				$key++;
			}
			//var_dump($data);
			if ($num == 2) {
				$query = "SELECT * FROM source_import WHERE userId = '" . $data[0][2] . "'";
				//echo $query.'<br>';
				$results = mysqli_query($conn, $query);
				$raw = mysqli_fetch_assoc($results);
				//var_dump($raw);
				$query2 = "SELECT * FROM source_import WHERE userId = '" . $data[1][2] . "'";
				$results28 = mysqli_query($conn, $query2);
				$raw2 = mysqli_fetch_assoc($results28);
				if ($raw['sourceName'] == 'facebook') {
					$data[0]['image'] = 'https://graph.facebook.com/' . $raw["sourceUid"] . '/picture?type=large';
				}
				if ($raw2['sourceName'] == 'facebook') {
					$data[1]['image'] = 'https://graph.facebook.com/' . $raw2["sourceUid"] . '/picture?type=large';
				}
				if ($raw['sourceName'] == 'linkedin') {
					$data[0]['image'] = $raw["sourceProfilePicture"];
				}
				if ($raw2['sourceName'] == 'linkedin') {
					$data[1]['image'] = $raw2["sourceProfilePicture"];
				}
				$selectorId = $data[0][0] . '-' . $data[1][0];
				$query = "SELECT * FROM different_users WHERE userId='" . $_SESSION['userId'] . "' AND selectorId = '" . $selectorId . "'";
				$results8 = mysqli_query($conn, $query) or die(mysqli_error($conn));
				$numDif = mysqli_num_rows($results8);
				if ($numDif == 0) {
					$key7 = uniqid();
					//var_dump($data);
					if ($first) {
						if ($_POST['type'] != 'nmr') {
							echo '<div class="vv" style="height:79px;" id="v1">
			  <div class="verify_l" ><img img src="' . $data[0]["image"] . '" class="verify_avatarl" /><div class="verify_namel" >' . $data[0][5] . ' ' . $data[0][7] . '<br />';
							$this -> sourceicons($data[0][2]);
							echo '<br /><span class="v2" ></span></div></div>
			  <div class="verify_m" >
				<input type="radio" class="single" name="f' . $key7 . '" value="' . $data[0][0] . '-' . $data[1][0] . '" checked/>Same<br /><input type="radio" name="f' . $key7 . '" class="single" value="dif' . $data[0][0] . '-' . $data[1][0] . '"/>Different
			  </div>
			  <div class="verify_r" ><img img src="' . $data[1]["image"] . '" class="verify_avatarr" /><div class="verify_namer" >' . $data[0][5] . ' ' . $data[0][7] . '<br />';
							$this -> sourceicons($data[1][2]);
							echo '</div></div>
			  </div>';
						}
					} else {
						if ($_POST['type'] != 'nmr') {
							echo '<div class="vv" style="height:79px;">
			  <div class="verify_l" ><img img src="' . $data[0]["image"] . '" class="verify_avatarl" /><div class="verify_namel" >' . $data[0][5] . ' ' . $data[0][7] . '<br />';
							$this -> sourceicons($data[0][2]);
							echo '<br /><span class="v2" ></span></div></div>
			  <div class="verify_m" >
			  <input type="radio" class="single" name="f' . $key7 . '" value="' . $data[0][0] . '-' . $data[1][0] . '" checked/>Same<br /><input type="radio" name="f' . $key7 . '" class="single" value="dif' . $data[0][0] . '-' . $data[1][0] . '"/>Different
			  </div>
			  <div class="verify_r" ><img img src="' . $data[1]["image"] . '" class="verify_avatarr" /><div class="verify_namer" >' . $data[0][5] . ' ' . $data[0][7] . '<br />';
							$this -> sourceicons($data[1][2]);
							echo '</div></div>
			  </div>';
						}
						$key2++;
						$key3++;
					}
				}
				$first = false;
			}
		}
		$verId = 1289;
		$sources = array();
		$exists = array();
		global $config;
		$first = true;
		$heightDiv = array(2 => 212, 3 => 307, 4 => 402);
		$heightRight = array(2 => 183, 3 => 177, 4 => 224);
		$padding = array(2 => 29, 3 => 65, 4 => 140);
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$results999 = $mysqli -> query('SELECT * FROM user_friend_detail WHERE userId = "' . $_SESSION["userId"] . '" AND FriendStatusCode = "verified" AND ViewableRow != "0"');
		$key6 = 1;
		$key5 = 1;
		while ($row = $results999 -> fetch_array()) {
			//var_dump($row);
			$ban = array();
			$exit = false;

			$result = $mysqli -> query('SELECT * FROM user_friend_detail WHERE userId = "' . $_SESSION["userId"] . '" AND FriendFirstName = "' . $row["FriendFirstName"] . '" AND FriendLastName = "' . $row["FriendLastName"] . '" AND FriendStatusCode = "unverified"');
			$count = $result -> num_rows;
			//var_dump($count);
			if ($count < 5 && $count > 1) {
				$result = $mysqli -> query('SELECT * FROM user_friend_detail WHERE userId = "' . $_SESSION["userId"] . '" AND FriendFirstName = "' . $row["FriendFirstName"] . '" AND FriendLastName = "' . $row["FriendLastName"] . '" AND FriendStatusCode = "unverified"');
				// var_dump($result->num_rows);
				while ($data78 = $result -> fetch_array()) {
					$results = $mysqli -> query('SELECT * FROM different_users WHERE userId="' . $_SESSION["userId"] . '" AND selectorId = "' . $row["id"] . '-' . $data78["id"] . '"');
					//var_dump('SELECT * FROM different_users WHERE userId="'.$_SESSION["userId"].'" AND selectorId = "'.$row["id"].'-'.$data78["id"].'"');
					echo 'Count:';
					//var_dump($count);
					if ($results -> num_rows > 0) {
						//var_dump($results->fetch_array());
						$count--;
						echo '<br>COUNT--:';
						//var_dump($count);
						$ban[] = $data78["friendId"];
					}
					if ($count == 1) {
						$data[0] = $row;
						$data[1] = $data78;
						$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
						$query = "SELECT * FROM source_import WHERE userId = '" . $data[0][2] . "'";
						//echo $query.'<br>';
						$results = mysqli_query($conn, $query);
						$raw = mysqli_fetch_assoc($results);
						//var_dump($raw);
						$query2 = "SELECT * FROM source_import WHERE userId = '" . $data[1][2] . "'";
						$results28 = mysqli_query($conn, $query2);
						$raw2 = mysqli_fetch_assoc($results28);
						if ($raw['sourceName'] == 'facebook') {
							$data[0]['image'] = 'https://graph.facebook.com/' . $raw["sourceUid"] . '/picture?type=large';
						}
						if ($raw2['sourceName'] == 'facebook') {
							$data[1]['image'] = 'https://graph.facebook.com/' . $raw2["sourceUid"] . '/picture?type=large';
						}
						if ($raw['sourceName'] == 'linkedin') {
							$data[0]['image'] = $raw["sourceProfilePicture"];
						}
						if ($raw2['sourceName'] == 'linkedin') {
							$data[1]['image'] = $raw2["sourceProfilePicture"];
						}
						$selectorId = $data[0][0] . '-' . $data[1][0];
						//var_dump($selectorId);
						$query = "SELECT * FROM different_users WHERE userId='" . $_SESSION['userId'] . "' AND selectorId = '" . $selectorId . "'";
						$results8 = mysqli_query($conn, $query) or die(mysqli_error($conn));
						$numDif = mysqli_num_rows($results8);
						//var_dump($numDif);
						if ($numDif == 0) {
							$key7 = uniqid();
							//var_dump($data);
							if ($first) {
								if ($_POST['type'] != 'nmr') {
									echo '<div class="vv" style="height:79px;" id="v1">
			  <div class="verify_l" ><img img src="' . $data[0]["image"] . '" class="verify_avatarl" /><div class="verify_namel" >' . $data[0][5] . ' ' . $data[0][7] . '<br />';
									$this -> sourceicons($data[0][2]);
									echo '<br /><span class="v2" ></span></div></div>
			  <div class="verify_m" >
				<input type="radio" class="single" name="f' . $key7 . '" value="' . $data[0][2] . '-' . $data[1][2] . '" checked/>Same<br /><input type="radio" name="f' . $key7 . '" class="single" value="dif' . $data[0][2] . '-' . $data[1][2] . '"/>Different
			  </div>
			  <div class="verify_r" ><img img src="' . $data[1]["image"] . '" class="verify_avatarr" /><div class="verify_namer" >' . $data[0][5] . ' ' . $data[0][7] . '<br />';
									$this -> sourceicons($data[1][2]);
									echo '</div></div>
			  </div>';
								}
							} else {
								if ($_POST['type'] != 'nmr') {
									echo '<div class="vv" style="height:79px;">
			  <div class="verify_l" ><img img src="' . $data[0]["image"] . '" class="verify_avatarl" /><div class="verify_namel" >' . $data[0][5] . ' ' . $data[0][7] . '<br />';
									$this -> sourceicons($data[0][2]);
									echo '<br /><span class="v2" ></span></div></div>
			  <div class="verify_m" >
			  <input type="radio" class="single" name="f' . $key7 . '" value="' . $data[0][2] . '-' . $data[1][2] . '" checked/>Same<br /><input type="radio" name="f' . $key7 . '" class="single" value="dif' . $data[0][2] . '-' . $data[1][2] . '"/>Different
			  </div>
			  <div class="verify_r" ><img img src="' . $data[1]["image"] . '" class="verify_avatarr" /><div class="verify_namer" >' . $data[0][5] . ' ' . $data[0][7] . '<br />';
									$this -> sourceicons($data[1][2]);
									echo '</div></div>
			  </div>';
								}
								$key2++;
								$key3++;
							}
						}
						$first = false;

						$exit = true;
					}
					if ($count < 1) {
						$exit = true;
					}
				}
				if (!$exit) {
					//echo 'YYYYYYYYYYYYYYYYYYYY';
					//$heightRight[$count]
					// $padding[$count]
					if ($first) {
						echo '<div class="vv" style="height:' . $heightDiv[$count] . 'px;" id="v1">';
						$first = false;
					} else {
						echo '<div class="vv">';
					}
					echo '<div class="verify_l" style="width:40%; height:' . $heightDiv[$count] . 'px;"><br><br>';
					//echo 'SELECT * FROM user_friend_detail WHERE userId = "'.$_SESSION["userId"].'" AND FriendFirstName = "'.$row["FriendFirstName"].'" AND FriendMiddleName = "'.$row["FriendMiddleName"].'" AND FriendLastName = "'.$row["FriendLastName"].'" AND FriendStatusCode = "verified" AND ViewableRow != "0"';
					$results4 = $mysqli -> query('SELECT * FROM user_friend_detail WHERE userId = "' . $_SESSION["userId"] . '" AND FriendFirstName = "' . $row["FriendFirstName"] . '" AND FriendMiddleName = "' . $row["FriendMiddleName"] . '" AND FriendLastName = "' . $row["FriendLastName"] . '" AND FriendStatusCode = "verified" AND ViewableRow != "0"') or die($mysqli -> error);
					while ($data = $results4 -> fetch_array()) {
						//var_dump($data);
						$exists = $data['id'];
						$rest = $mysqli -> query('SELECT * FROM userfrnd_source WHERE userId ="' . $_SESSION["userId"] . '" AND friendId = "' . $data["friendId"] . '"') or die($mysqli -> error);
						while ($out = $rest -> fetch_array()) {
							$res = $mysqli -> query('SELECT * FROM source_import WHERE sourceId = "' . $out["source_import_Id"] . '"');
							while ($dat = $res -> fetch_array()) {
								$sources[] = $dat['sourceName'];
							}
						}

						$sources2 = array();
						$result5 = $mysqli -> query('SELECT * FROM source_import WHERE userId = "' . $data["Id"] . '"');
						while ($row2 = $result5 -> fetch_array()) {
							//var_dump($row);
							if ($row2['sourceProfilePicture'] == '' && $row2['sourceName'] == 'facebook') {

								$link = 'https://graph.facebook.com/' . $row2["sourceUid"] . '/picture?type=large';
								//var_dump($link);

							} else {
								$link = $row2['sourceProfilePicture'];
							}
							$sources2[$row2["sourceName"]] = $link;
							//var_dump($sources2);
						}
						$sourcesPriority = array('facebook', 'linkedin', 'salesforce');
						$done = false;

						//var_dump($sources2);
						foreach ($sourcesPriority as $sourcePriority) {
							if (!$done) {
								if (isset($sources2[$sourcePriority])) {
									$avatr = $sources2[$sourcePriority];
									$done = true;
								}
							}
						}
						//var_dump($sources2);
						//$avatar = $this->getAvatarLink($data["userId"]);
						//var_dump($avatar);
						$sources = array();

						echo '<img src="' . $avatar . '" class="verify_avatarl"/><div class="verify_namel" >' . $data["FriendFirstName"] . ' ' . $data["FriendMiddleName"] . ' ' . $data["FriendLastName"] . '<br />';
						foreach ($sources2 as $source) {
							$this -> sourceicons($data['friendId']);
						}
						if ($data['FriendEmail1'] != '') {
							echo '<span class="v2" >Email: <a href="#" >' . $data["FriendEmail1"] . '</a></span>';
						}
						echo '</div>';
						if ($key < $count) {
							echo '<br><br><br><br><br>';
							$key++;

						}
					}
					$_SESSION['exists'][$key5] = $exists;
					echo '</div>';
					$first67 = true;
					$results88 = $mysqli -> query('SELECT * FROM user_friend_detail WHERE userId = "' . $_SESSION["userId"] . '" AND FriendFirstName = "' . $row["FriendFirstName"] . '" AND FriendMiddleName = "' . $row["FriendMiddleName"] . '" AND FriendLastName = "' . $row["FriendLastName"] . '" AND FriendStatusCode = "unverified"') or die($mysqli -> error);
					$verId = uniqid();
					echo '<div class="verify_m" >';

					echo '<input type="radio" name="' . $verId . '" value="different" class="multiple" checked/>All Different<br><br><br>';
					while ($data4 = $results88 -> fetch_array()) {
						echo '<input type="radio" name="' . $verId . '" value="' . $data4["id"] . '" class="multiple" />Same<br><br><br><br><br>';
					}
					echo '</div>';
					$test = array();
					$results88 = $mysqli -> query('SELECT * FROM user_friend_detail WHERE userId = "' . $_SESSION["userId"] . '" AND FriendFirstName = "' . $row["FriendFirstName"] . '" AND FriendMiddleName = "' . $row["FriendMiddleName"] . '" AND FriendLastName = "' . $row["FriendLastName"] . '" AND FriendStatusCode = "unverified"') or die($mysqli -> error);
					echo '<div class="verify_r" style="padding-top:' . $padding[$count] . 'px; width:40%; height:' . $heightRight[$count] . 'px;">';

					while ($row = $results88 -> fetch_array()) {

						//echo $row["userId"];
						$sources2 = array();
						$result5 = $mysqli -> query('SELECT * FROM source_import WHERE userId = "' . $row["friendId"] . '"');
						while ($row2 = $result5 -> fetch_array()) {
							// var_dump($row2);
							if ($row2['sourceProfilePicture'] == '' && $row2['sourceName'] == 'facebook') {

								//echo '<input type="radio" name="'.$verId.'" value="different"/>All Different<br><br><br>';
								//var_dump($link);

							} else {
								$link = $row2['sourceProfilePicture'];
							}
							$sources2[$row2["sourceName"]] = $link;
							//var_dump($sources2);
						}
						$sourcesPriority = array('facebook', 'linkedin', 'salesforce');
						$done = false;

						//var_dump($sources2);
						foreach ($sourcesPriority as $sourcePriority) {
							if (!$done) {
								if (isset($sources2[$sourcePriority])) {
									//var_dump($sources2[$sourcePriority]);
									$avatar = $sources2[$sourcePriority];
									$done = true;
								}
							}
						}

						echo '<img src="' . $avatar . '" class="verify_avatarr" /><div class="verify_namer" >' . $row["FriendFirstName"] . ' ' . $row["FriendMiddleName"] . ' ' . $row["FriendLastName"] . '<br />';
						$rest = $mysqli -> query('SELECT * FROM userfrnd_source WHERE userId ="' . $_SESSION["userId"] . '" AND friendId = "' . $row["friendId"] . '"');
						while ($out = $rest -> fetch_array()) {
							$res = $mysqli -> query('SELECT * FROM source_import WHERE sourceId = "' . $out["source_import_Id"] . '"');
							while ($dat = $res -> fetch_array()) {
								$sources[] = $dat['sourceName'];
							}
						}
						foreach ($sources2 as $source) {
							echo $this -> sourceicons($row['friendId']);
						}
						if ($row['FriendEmail1'] != '') {
							echo '<span class="v2" >Email: <a href="#" >' . $row["FriendEmail1"] . '</a></span></div>';
						}
						array_push($test, $row['id']);
						echo '</div><br><br><br><br><br>';

					}
					$_SESSION['new'][$key5] = $test;
					$key5++;
					echo '</div>';
				}

			}
		}
		$results = $mysqli -> query('SELECT * FROM user_friend_detail WHERE userId = "' . $_SESSION["userId"] . '" AND FriendStatusCode = "unverified"');
		$key = 1;
		$key2 = 0;
		$avatar = '';
		$source = '';
		$heightDiv = array(2 => 250, 3 => 307, 4 => 402);
		$heightRight = array(2 => 161, 3 => 177, 4 => 224);
		$padding = array(2 => 89, 3 => 65, 4 => 140);
		$_SESSION['leftFriends'] = array();
		$_SESSION['rightFriend'] = array();
		while ($row = $results -> fetch_array()) {
			$_SESSION['error_code'] = true;
			$first = true;
			$result = $mysqli -> query('SELECT * FROM user_friend_detail WHERE userId = "' . $_SESSION["userId"] . '" AND FriendStatusCode = "verified" AND ViewableRow != "0" AND 
			FriendFirstName = "' . $row["FriendFirstName"] . '" AND FriendLastName = "' . $row["FriendLastName"] . '"');
			$count = $result -> num_rows;
			if ($count >= 2) {
				echo '<div class="vv">';
				echo '<div class="verify_l" style="width:40%; height:' . $heightDiv[$count] . 'px;"><br><br>';
				while ($data = $result -> fetch_array()) {
					echo '<img src="' . $avatar . '" class="verify_avatarl"/><div class="verify_namel" >' . $data["FriendFirstName"] . ' ' . $data["FriendMiddleName"] . ' 
					' . $data["FriendLastName"] . '</div><br />';
					if ($data['FriendEmail1'] != '') {
						echo '<span class="v2" >Email: <a href="#" >' . $data["FriendEmail1"] . '</a></span>';
					}
					echo '<br><br><br><br><br>';
					$_SESSION['leftFriends'][$key][] = $data['id'];
				}
				echo '</div>';
				$verId = uniqid();
				echo '<div class="verify_m" >';
				echo '<input type="radio" name="' . $verId . '" value="different" class="multiple2" checked/>All Different<br><br><br>';
				foreach ($_SESSION['leftFriends'][$key] as $val) {
					if (!$first) {
						echo '<br><br><br><br><br>';
					}
					$first = false;
					echo '<input type="radio" name="' . $verId . '" value="' . $val . '" class="multiple2" />Same<br>';
				}
				echo '<br><br><br>';
				echo '</div>';
				echo '<div class="verify_r" style="padding-top:' . $padding[$count] . 'px; width:40%; height:' . $heightRight[$count] . 'px;">';
				echo '<img src="' . $avatar . '" class="verify_avatarr" /><div class="verify_namer" >' . $row["FriendFirstName"] . ' ' . $row["FriendMiddleName"] . ' ' . $row["FriendLastName"] . '<br />';
				echo '<img src="images/' . $source . '.png" style="width: 30px;height: 30px;" /><br />';
				echo '</div></div>';
				$_SESSION['rightFriend'][$key] = $row['id'];
				$key++;
			}

		}
	}

}

//echo'</div>';
?>