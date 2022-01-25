<?php
session_start();
require_once "../../config/config.php";


$mysql =  new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);

use EDAM\Types\Data, EDAM\Types\Note, EDAM\NoteStore\NoteFilter, EDAM\Types\Tag, EDAM\NoteStore\NotesMetadataResultSpec, EDAM\Types\Resource, EDAM\Types\ResourceAttributes;
use EDAM\Error\EDAMUserException, EDAM\Error\EDAMErrorCode;
use Evernote\Client;

ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . "evn//lib" . PATH_SEPARATOR);
require_once 'autoload.php';

require_once 'Evernote/Client.php';


function checkevntechid(){
  global $mysql;
  $cq = "SELECT * FROM tech_partners WHERE ";
}

require_once 'packages/Errors/Errors_types.php';
require_once 'packages/Types/Types_types.php';
require_once 'packages/Limits/Limits_constants.php';

$evn_key = "tcleveland67";
$evn_secret = "23f24752543845e7";

$client = new Client(array(
                'consumerKey' => $evn_key,
                'consumerSecret' => $evn_secret,
                'sandbox' => TRUE
));

if(! isset($_GET['oauth_verifier'])){
$requestTokenInfo = $client->getRequestToken($config["root"]."friends/ex_inc/evn_authw.php");
$_SESSION['requestToken'] = $requestTokenInfo['oauth_token'];
$_SESSION['requestTokenSecret'] = $requestTokenInfo['oauth_token_secret'];


$authurl = $client->getAuthorizeUrl($_SESSION['requestToken']);
header("Location: ". $authurl);
die("Redirect.");
}
else{
  echo "You were sent back with the following attrs:<br />\n";
  print_r($_GET);
  echo "<br /><br />\nUsing GET's to attempt to get accesstoken";
  $accessTokenInfo = $client->getAccessToken($_SESSION['requestToken'], $_SESSION['requestTokenSecret'],$_GET['oauth_verifier']);
  echo "<br /><br />Access token info:<br />\n";
  print_r($accessTokenInfo);
  $_SESSION['accessToken'] = $accessTokenInfo['oauth_token'];
  
  $qu = "SELECT * FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."'";
  $result = $mysql->query($qu);
  $row = $result->fetch_assoc();
  if($row["id"] == ""){
      //No records for the user in this table
      $aquery = "INSERT INTO user_external_accnt(userId, externalAcctuid, authProvider, authAccesstoken, authExtendedproperties) VALUES('". $_SESSION["userId"] ."', '". $accessTokenInfo["edam_userId"] ."', 'evernote', '". $accessTokenInfo["oauth_token"] ."', 'edam_shard:". $accessTokenInfo["edam_shard"] .",edam_expires:". $accessTokenInfo["edam_expires"] .",edam_noteStoreUrl:". $accessTokenInfo["edam_noteStoreUrl"] .",edam_webApiUrlPrefix:". $accessTokenInfo["edam_webApiUrlPrefix"] ."')";
      $mysql->query($aquery);
  }
  else{
      //Record(s) found, check if any are for evernote
      $yes = "no";
      while($nrow = $result->fetch_assoc()){
            if($nrow["authProvider"] == "evernote"){
                $yes = "yes";
            }
        }
        if($yes == "yes"){
                $aquery = "UPDATE user_external_accnt SET externalAcctuid='". $accessTokenInfo["edam_userId"] ."', authAccesstoken='". $accessTokenInfo["oauth_token"] ."', authExtendedproperties='edam_shard:". $accessTokenInfo["edam_shard"] .",edam_expires:". $accessTokenInfo["edam_expires"] .",edam_noteStoreUrl:". $accessTokenInfo["edam_noteStoreUrl"] .",edam_webApiUrlPrefix:". $accessTokenInfo["edam_webApiUrlPrefix"] ."' WHERE authProvier='evernote' AND userId='". $_SESSION["userId"] ."'";
                $mysql->query($aquery);
                echo "<br /><br />Already found an entry for evernote, Updating with current access token";
            }
            else{
            $aquery = "INSERT INTO user_external_accnt(userId, externalAcctuid, authProvider, authAccesstoken, authExtendedproperties) VALUES('". $_SESSION["userId"] ."', '". $accessTokenInfo["edam_userId"] ."', 'evernote', '". $accessTokenInfo["oauth_token"] ."', 'edam_shard:". $accessTokenInfo["edam_shard"] .",edam_expires:". $accessTokenInfo["edam_expires"] .",edam_noteStoreUrl:". $accessTokenInfo["edam_noteStoreUrl"] .",edam_webApiUrlPrefix:". $accessTokenInfo["edam_webApiUrlPrefix"] ."')";
            $mysql->query($aquery);
            echo "<br /><br />Adding your account to the DB";
            }
            
    }
}

header("Location: evn_checkmibnb.php");
?>
