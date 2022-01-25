<?php
session_start();
include('../config/config.php');
// Change these
define('API_KEY',      '82rbu6tsvmus'                                          );
define('API_SECRET',   '6R7hIoANfG2H0a8m'                                       );
$nope = true;
if(isset($_SESSION['userId'])){
define('REDIRECT_URI', $config['root'].'php/linkedinLogin.php?userId='.$_SESSION['userId']);
$nope = false;
}
if(isset($_GET['userId'])){
define('REDIRECT_URI', $config['root'].'php/linkedinLogin.php?userId='.$_GET['userId']);
$nope = false;
}
if($nope){
	define('REDIRECT_URI', $config['root'].'php/linkedinLogin.php');
}
define('SCOPE',        'r_fullprofile r_emailaddress rw_nus r_network'                        );
 
// You'll probably use a database


 
// OAuth 2 Control Flow
if (isset($_GET['error'])) {
    // LinkedIn returned an error
    print $_GET['error'] . ': ' . $_GET['error_description'];
    exit;
} elseif (isset($_GET['code'])) {
    // User authorized your application
    if ($_SESSION['state'] == $_GET['state']) {
        // Get token so you can make API calls
        getAccessToken();
    } else {
        // CSRF attack? Or did you mix up your states?
        exit;
    }
} else { 
    if ((empty($_SESSION['expires_at'])) || (time() > $_SESSION['expires_at'])) {
        // Token has expired, clear the state
        $_SESSION = array();
    }
    if (empty($_SESSION['access_token'])) {
        // Start authorization process
        getAuthorizationCode();
    }
}
 
// Congratulations! You have a valid token. Now fetch your profile 
$user = fetch('GET', '/v1/people/~:(firstName,lastName,id)');
 
function getAuthorizationCode() {
    $params = array('response_type' => 'code',
                    'client_id' => API_KEY,
                    'scope' => SCOPE,
                    'state' => uniqid('', true), // unique long string
                    'redirect_uri' => REDIRECT_URI,
              );
 
    // Authentication request
    $url = 'https://www.linkedin.com/uas/oauth2/authorization?' . http_build_query($params);
     
    // Needed to identify request when it returns to us
    $_SESSION['state'] = $params['state'];
 
    // Redirect user to authenticate
    header("Location: $url");
    exit;
}
     
function getAccessToken() {
    $params = array('grant_type' => 'authorization_code',
                    'client_id' => API_KEY,
                    'client_secret' => API_SECRET,
                    'code' => $_GET['code'],
                    'redirect_uri' => REDIRECT_URI,
              );
     
    // Access Token request
    $url = 'https://www.linkedin.com/uas/oauth2/accessToken?' . http_build_query($params);
     
    // Tell streams to make a POST request
    $context = stream_context_create(
                    array('http' => 
                        array('method' => 'POST',
                        )
                    )
                );
 
    // Retrieve access token information
    $response = file_get_contents($url, false, $context);
 
    // Native PHP object, please
    $token = json_decode($response);
 
    // Store access token and expiration time
    $_SESSION['access_token'] = $token->access_token; // guard this! 
   // $_SESSION['expires_in']   = $token->expires_in; // relative time (in seconds)
   // $_SESSION['expires_at']   = time() + $_SESSION['expires_in']; // absolute time
     
    return true;
}
 
function fetch($method, $resource, $body = '') {
    $params = array('oauth2_access_token' => $_SESSION['access_token'],
                    'format' => 'json',
              );
     
    // Need to use HTTPS
    $url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
    // Tell streams to make a (GET, POST, PUT, or DELETE) request
    $context = stream_context_create(
                    array('http' => 
                        array('method' => $method,
                        )
                    )
                );
 
 
    // Hocus Pocus
    $response = file_get_contents($url, false, $context);
 
    // Native PHP object, please
    return json_decode($response);
}
$accesstoken = $_SESSION['access_token'];
if(isset($_GET['userId'])){
$_SESSION['userId'] = $_GET['userId'];
}
$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
$query2 = "SELECT * FROM source_import WHERE sourceUid = '".$user->{'id'}."'";
$results = mysqli_query($conn, $query2);
	
	if(!isset($_GET['userId'])){

	$query = "INSERT INTO users (userStatus, accountType, externalIdentifier) VALUES ('verified', 1, '".md5($accesstoken)."')";
	mysqli_query($conn, $query);
	
	$query = "SELECT * FROM users WHERE externalIdentifier = '".md5($accesstoken)."'";
	$results = mysqli_query($conn, $query);
	$userData = mysqli_fetch_row($results);
	
	$query = "INSERT INTO source_import (userId, sourceUid, sourceAccessToken, sourceName, sourceProfileLink, sourceProfilePicture) VALUES ('".$userData[0]."', '".$user->{'id'}."', '".$accesstoken."', 'linkedin', '".$user->{'pictureUrl'}."',  '".$user->siteStandardProfileRequest->{'url'}."')";
	mysqli_query($conn, $query);
	$_SESSION['userId'] = $userData[0];
	$_SESSION['linkedinId'] = $user->{'id'};
	}
	else {

	$query = "INSERT INTO source_import (userId, sourceUid, sourceAccessToken, sourceName, sourceProfileLink, sourceProfilePicture) VALUES ('".$_SESSION['userId']."', '".$user->{'id'}."', '".$accesstoken."', 'linkedin', '".$user->{'pictureUrl'}."',  '".$user->siteStandardProfileRequest->{'url'}."')";
	mysqli_query($conn, $query);
	$_SESSION['linkedinId'] = $user->{'id'};	
	}
if($_GET['importLi'] == true){
	echo '<script type="text/javascript"> window.location = "'.$config['root'].'php/login.php?type=linkedin&importLi=true" </script>';
}
else {
echo '<script type="text/javascript"> window.location = "'.$config['root'].'php/login.php?type=linkedin" </script>';
}




?>