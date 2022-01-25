<?php
if(@! $_SESSION){
  session_start();
}

include "../../config/config.php";

$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);

function checkevntoken(){
    global $mysql;
    $evnq = "SELECT * FROM user_externalaccount WHERE userId='". $_SESSION["userId"] ."' AND authProvider='evernote'";
    $result = $mysql->query($evnq);
    $row = $result->fetch_assoc();
    if($row["id"] != ""){
        $_SESSION["entoken"] = $row["authAccesstoken"];
    }
    else{
    //something useless
        die("Err - No evn token found");
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

function getalltags(){
    global $noteStore, $userStore;
    $tags = $noteStore->listTags($_SESSION["entoken"]);
    return $tags;
}

$qu = "SELECT * FROM user_categories WHERE userId='". $_SESSION["userId"] ."'";
$result = $mysql->query($qu);
$row = $result->fetch_assoc();

$tags = getalltags();


//print_r($row);

foreach($tags as $tag){
    $tqu = "SELECT * FROM user_categories WHERE userId='". $_SESSION["userId"] ."' AND catName='". $tag->name ."'";
    $tresult = $mysql->query($tqu);
    $trow = $tresult->fetch_assoc();
    if($trow["catId"] == ""){
        echo "Found tag '". $tag->name ."' but did not find a category with the same name. Creating it now.";
        $nqu = "INSERT INTO user_categories(userId, catName, catDescription) VALUES('". $_SESSION["userId"] ."', '". $tag->name ."', 'Imported from evernote')";
        $nresult = $mysql->query($nqu);
        
        $nqu = "SELECT * FROM evn_notes_cat WHERE evnTagGuid='". $tag->guid ."'";
        $nnresult = $mysql->query($nqu);
        $nrow = $nnresult->fetch_assoc();
        echo "1";
        
        if($nrow["id"] == ""){
            
            $rq = "SELECT * FROM user_categories WHERE userId='". $_SESSION["userId"] ."'";
            $rres = $mysql->query($rq);
            $lgid = 0;
            while($grow = $rres->fetch_assoc()){
                $lgid = $grow["catId"];
            }
            echo "2";
            
            
            $nwu = "INSERT INTO evn_notes_cat(evnTagGuid, catId) VALUES('". $tag->guid ."', '". $lgid ."')";
            $mysql->query($nwu);
            
            echo " - <font color=green >Success</font><br /><br />"; 
        }
        else{
          echo " - <font color=red >Failed, a record with that tag guid already exists</font><br /><br />"; 
        }
    }
    else{
        echo "Found tag '". $tag->name ."' and found a category with the same name. Merging now.<br /><br />";
        }
}

echo "<br /><br />---<br /><br />";

$qu = "SELECT * FROM user_categories WHERE userId='". $_SESSION["userId"] ."'";
$result = $mysql->query($qu);
//$row = $result->fetch_assoc();

while($row = $result->fetch_assoc()){
    $isf = 0;
    foreach($tags as $tag){
        if($isf == 0){
            if($row["catName"] == $tag->name){
                $isf = 1;
            }
        }
    }
    if($isf == 0){
        $newtaginfo = new Tag();
        $newtaginfo->name = $row["catName"];
        $noteStore->createTag($newtaginfo);
    }
    $isf = 0;
}

?>