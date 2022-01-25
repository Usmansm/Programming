<?php
session_start();
error_reporting(0);
$_SESSION["return_url"] = "http://ec2-54-243-154-131.compute-1.amazonaws.com/MIBWORKING/dev/yahoo/test/";
require_once "../../config/config.php";
require_once "../../yahoo-api/lib/Yahoo.inc";

define('OAUTH_CONSUMER_KEY', 'dj0yJmk9U244TXVGNFE1NlFZJmQ9WVdrOVVYQnhlVTlxTjJrbWNHbzlNVEE1TURBMk5UWXkmcz1jb25zdW1lcnNlY3JldCZ4PThj');
define('OAUTH_CONSUMER_SECRET', '0e3a5a6b7242361bec2f699d1161a544ab53efa1'); 
define('OAUTH_APP_ID', 'QpqyOj7i'); 
    $session = YahooSession::requireSession(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID);
print_r($session);
include "../../contacts/contacts.class.php";

function rlog($t){
    echo $t."<br />";
}

$base = new mib_contacts;

rlog("Checking if user has token in our DB.");
$token = $base->yahoo_check_internal_token();
if(is_object($session)){
    rlog("You got a session");
    rlog("Let's try to get your contacts...");
    echo "<pre>";
    $user = $session->getSessionedUser();
    $contacts = $user->getContacts();
    $contacts = (array) $contacts;
    print_r($contacts);
    
    echo "</pre>";
}


/*
 * https://api.login.yahoo.com/oauth/v2/  
  get_request_token?oauth_nonce=ce2130523f788f313f76314ed3965ea6  
  &oauth_timestamp=time()
  &oauth_consumer_key=123456891011121314151617181920  
  &oauth_signature_method=plaintext  
  &oauth_signature=secret  
  &oauth_version=1.0  
  &xoauth_lang_pref="en-us"  
  &oauth_callback="http://yoursite.com/callback"  
 */
?>