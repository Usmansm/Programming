<?php
session_start();
include "../../config/config.php";
$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);


function liconnected(){
	global $mysql;
	$query = "SELECT * FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."' AND authProvider='linkedin'";
	$result = $mysql->query($query);
	$data = $result->fetch_assoc();
	if($data["id"] != ""){
		return TRUE;
	}
	else{
		return FALSE;
	}
}

function getAccessToken($redirectUrl) {
		global $config;
		$params = array('grant_type' => 'authorization_code',
						'client_id' => $config['linkedin_key'],
						'client_secret' => $config['linkedin_secret'],
						'code' => $_GET['code'],
						'redirect_uri' => $redirectUrl
				  );
				  
				  var_dump($params);
		$url = 'https://www.linkedin.com/uas/oauth2/accessToken?' . http_build_query($params);
		$context = stream_context_create(
						array('http' => 
							array('method' => 'POST',
							)
						)
					);
		$response = file_get_contents($url, false, $context);
		$token = json_decode($response);
		return $token->access_token;
	}
	
	function getAuthorizationCode($redirectUrl) {
			global $config;
			$params = array('response_type' => 'code',
							'client_id' => $config['linkedin_key'],
							'scope' => $config['linkedin_scope'],
							'state' => uniqid('', true),
							'redirect_uri' => $redirectUrl,
					  );
			$url = 'https://www.linkedin.com/uas/oauth2/authorization?' . http_build_query($params);
			$_SESSION['state'] = $params['state'];
			header("Location: $url");
			exit;
	}

function updatetoken($tok){
	global $mysql;
	$query1 = "UPDATE user_external_accnt SET authAccesstoken='". $tok ."' WHERE userId='". $_SESSION["userId"] ."' AND authProvider='linkedin'";
	$query2 = "UPDATE source_import SET sourceAccessToken='". $tok ."' WHERE userId='". $_SESSION["userId"] ."' AND sourceName='linkedin'";
	$res1 = $mysql->query($query1);
	$res2 = $mysql->query($query2);
	header("Location: ". $config["root"] ."friends?manual=yes");
}

$liconnected = liconnected();
if($_GET["code"] == ""){
if($liconnected != FALSE){
	$uu = $config["root"]."friends/ex_inc/litokenrefresh.php";
	getAuthorizationCode($uu);
}
else{
	header("Location: ". $config["root"] ."friends/?manual=yes");
}
}
else{
	$uu = $config["root"]."friends/ex_inc/litokenrefresh.php";
	$at = getAccessToken($uu);
	updatetoken($at);
}
?>