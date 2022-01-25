<?php
session_start();
require('../../config/config.php');
//useless commit

$mysql =  new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
define("EVERNOTE_LIBS", dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib");
ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . EVERNOTE_LIBS);
//ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . "evn//lib" . PATH_SEPARATOR);
require_once 'autoload.php';

require_once 'Evernote/Client.php';

require_once 'packages/Errors/Errors_types.php';
require_once 'packages/Types/Types_types.php';
require_once 'packages/Limits/Limits_constants.php';

use EDAM\Error\EDAMSystemException,
    EDAM\Error\EDAMUserException,
    EDAM\Error\EDAMErrorCode,
    EDAM\Error\EDAMNotFoundException;
use Evernote\Client;

function checkevncat($catid,$mysql){
    $cquery = "SELECT * FROM evn_notes_cat WHERE userId='". $_SESSION["userId"] ."' AND catId='". $catid ."'";
    $result = $mysql->query($cquery);
    $row = $result->fetch_assoc();
    if($row["id"] == ""){
        return true;
    }
    else{
        $dquery = "DELETE FROM evn_notes_cat WHERE id='". $row["id"] ."'";
        $mysql->query($dquery);
        return true;
    }
}



$evn_key = "tcleveland67";
$evn_secret = "23f24752543845e7";

$client = new Client(array(
                'consumerKey' => $evn_key,
                'consumerSecret' => $evn_secret,
                'sandbox' => TRUE
            ));

if(! isset($_GET['oauth_verifier'])){
$RequestTokenURL = $confing['friends_root'] ." ex_inc/evn_auth.php";
$requestTokenInfo = $client->getRequestToken($RequestTokenURL);
$_SESSION['requestToken'] = $requestTokenInfo['oauth_token'];
$_SESSION['requestTokenSecret'] = $requestTokenInfo['oauth_token_secret'];


$authurl = $client->getAuthorizeUrl($_SESSION['requestToken']);
header("Location: ". $authurl);
}
else{
    $_SESSION["oauthVerifier"] = $_GET['oauth_verifier'];
    print_r($_SESSION);
    $accessTokenInfo = $client->getAccessToken($_SESSION['requestToken'], $_SESSION['requestTokenSecret'], $_SESSION['oauthVerifier']);
    $_SESSION['accessToken'] = $accessTokenInfo['oauth_token'];
   
    $qu = "SELECT * FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."'";
    $result = $mysql->query($qu);
    $row = $result->fetch_assoc();
    if($result->num_rows == 0){
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
                $aquery = "UPDATE user_external_accnt SET externalAcctuid='". $accessTokenInfo["edam_userId"] ."', authAccesstoken='". $accessTokenInfo["oauth_token"] ."', authExtendedproperties='edam_shard:". $accessTokenInfo["edam_shard"] .",edam_expires:". $accessTokenInfo["edam_expires"] .",edam_noteStoreUrl:". $accessTokenInfo["edam_noteStoreUrl"] .",edam_webApiUrlPrefix:". $accessTokenInfo["edam_webApiUrlPrefix"] ."'";
                $mysql->query($aquery);
            }
            else{
            $aquery = "INSERT INTO user_external_accnt(userId, externalAcctuid, authProvider, authAccesstoken, authExtendedproperties) VALUES('". $_SESSION["userId"] ."', '". $accessTokenInfo["edam_userId"] ."', 'evernote', '". $accessTokenInfo["oauth_token"] ."', 'edam_shard:". $accessTokenInfo["edam_shard"] .",edam_expires:". $accessTokenInfo["edam_expires"] .",edam_noteStoreUrl:". $accessTokenInfo["edam_noteStoreUrl"] .",edam_webApiUrlPrefix:". $accessTokenInfo["edam_webApiUrlPrefix"] ."')";
            $mysql->query($aquery);
            }
            
            
    }
    
}

?>