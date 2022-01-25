<?php
if(@! $_SESSION){
  session_start();
}
//I was here
include "../../config/config.php";
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);

function checkevntoken(){
    global $mysql;
    $evnq = "SELECT * FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."' AND authProvider='evernote'";
    $result = $mysql->query($evnq);
    $row = $result->fetch_assoc();
    if($row["id"] != ""){
        $_SESSION["entoken"] = $row["authAccesstoken"];
    }
    else{
    //something useless
        die("Err - No evn token found <a href='evn_auth.php' > </a>");
    }
}

checkevntoken();
$entoken = $_SESSION["entoken"];
$notestoreurl = "https://sandbox.evernote.com/shard/s1/notestore";

use EDAM\Types\Data, EDAM\Types\Note, EDAM\NoteStore\NoteFilter, EDAM\Types\Tag, EDAM\NoteStore\NotesMetadataResultSpec, EDAM\Types\Resource, EDAM\Types\ResourceAttributes;
use EDAM\Error\EDAMUserException, EDAM\Error\EDAMErrorCode;
use Evernote\Client;

ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . "evn//lib" . PATH_SEPARATOR);
require_once 'autoload.php';

require_once 'Evernote/Client.php';



require_once 'packages/Errors/Errors_types.php';
require_once 'packages/Types/Types_types.php';
require_once 'packages/Limits/Limits_constants.php';

function en_exception_handler($exception)
{
    echo "Uncaught " . get_class($exception) . ":\n";
    if ($exception instanceof EDAMUserException) {
        echo "Error code: " . EDAMErrorCode::$__names[$exception->errorCode] . "\n";
        echo "Parameter: " . $exception->parameter . "\n";
    } elseif ($exception instanceof EDAMSystemException) {
        echo "Error code: " . EDAMErrorCode::$__names[$exception->errorCode] . "\n";
        echo "Message: " . $exception->message . "\n";
    } else {
        echo $exception;
    }
}
set_exception_handler('en_exception_handler');

$client = new Client(array('token' => $entoken));

$userStore = $client->getUserStore();
$noteStore = $client->getNoteStore();

function findmibnotebook(){
  global $noteStore,$userStore,$client;
  $notebooks = $notStore->listNotebooks($_SESSION["entoken"]);
  $mibnbfound = FALSE;
  foreach($notebooks as $nb){
    if($nb->name == "Myiceberg"){
      $mibnbfound = TRUE;
    }
    return $mibnbfound;
  }
}

function createmibnotebook(){
   global $noteStore,$userStore,$client;
   $typenb = new Notebook();
   $typenb->name = "Myiceberg";
   $nbc = $noteStore->createNotebook($_SESSION["entoken"],$typenb);
   return $nbc->guid;
}

$mibnbexists = findmibnotebook();

if($mibnbexists == TRUE){
  
}
else{
  echo gotocreatemibnotebook();
}

?>