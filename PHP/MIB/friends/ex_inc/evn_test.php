<?php
error_reporting(0);
if (@!$_SESSION) {
	session_start();
}
include "../../config/config.php";
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);

function checkevntoken() {
	global $mysql;
	$evnq = "SELECT * FROM user_external_accnt WHERE userId='" . $_SESSION["userId"] . "' AND authProvider='evernote'";
	$result = $mysql -> query($evnq);
	$row = $result -> fetch_assoc();
	if ($row["id"] != "") {
		$_SESSION["entoken"] = $row["authAccesstoken"];
	} else {
		die("Err - No evn token found <a href='evn_auth.php' > </a>");
	}
}

checkevntoken();
$entoken = $_SESSION["entoken"];
$notestoreurl = "https://sandbox.evernote.com/shard/s1/notestore";

use EDAM\Types\Data, EDAM\Types\Note, EDAM\Types\Notebook, EDAM\NoteStore\NoteFilter, EDAM\Types\Tag, EDAM\NoteStore\NotesMetadataResultSpec, EDAM\Types\Resource, EDAM\Types\ResourceAttributes;
use EDAM\Error\EDAMUserException, EDAM\Error\EDAMErrorCode;
use Evernote\Client;

ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . "evn//lib" . PATH_SEPARATOR);
require_once 'autoload.php';

require_once 'Evernote/Client.php';

require_once 'packages/Errors/Errors_types.php';
require_once 'packages/Types/Types_types.php';
require_once 'packages/Limits/Limits_constants.php';

function writetolog($dat) {
	$fp = "log/evn/log.html";
	$ching = file_put_contents($fp, $dat);
	if ($ching !== false) {
		return true;
	} else {
		return false;
	}
}

function addtologfile($dat) {
	$fp = "log/evn/log.html";
	$file_handle = fopen($fp, "a");
	fwrite($file_handle, $dat);
	fclose($file_handle);
}

function en_exception_handler($exception) {
	echo "Uncaught " . get_class($exception) . ":\n";
	if ($exception instanceof EDAMUserException) {
		echo "Error code: " . EDAMErrorCode::$__names[$exception -> errorCode] . "\n";
		echo "Parameter: " . $exception -> parameter . "\n";
	} elseif ($exception instanceof EDAMSystemException) {
		echo "Error code: " . EDAMErrorCode::$__names[$exception -> errorCode] . "\n";
		echo "Message: " . $exception -> message . "\n";
	} else {
		echo $exception;
	}
}

set_exception_handler('en_exception_handler');

$client = new Client( array('token' => $entoken));

$userStore = $client -> getUserStore();
$noteStore = $client -> getNoteStore();

function checkmibnbguid() {
	global $mysql;
	$query = "SELECT * FROM user_external_accnt WHERE userId='" . $_SESSION["userId"] . "' AND authProvider='evernote'";
	$res = $mysql -> query($query);
	$row = $res -> fetch_assoc();
	if ($row["authExtendedproperties2"] == "") {
		return FALSE;
	} else {
		return $row["authExtendedproperties2"];
	}
}

$maxnotes = 250;
$startindex = 0;
$nc = 0;
$mibnbid = checkmibnbguid();
if ($mibnbid != false) {
	$nf = new NoteFilter();
	$nf -> notebookGuid = $mibnbid;
	$gmdata = new NotesMetadataResultSpec();
	$gmdata -> includeTitle = TRUE;
	$gmdata -> includeContentLength = TRUE;
	$gmdata -> includeCreated = TRUE;
	$gmdata -> includeUpdated = TRUE;
	$gmdata -> includeDeleted = TRUE;
	$gmdata -> includeUpdateSequenceNum = TRUE;
	$gmdata -> includeNotebookGuid = TRUE;
	$gmdata -> includeTagGuids = TRUE;
	$gmdata -> includeAttributes = TRUE;
	$gmdata -> includeLargestResourceMime = TRUE;
	$gmdata -> includeLargestResourceSize = TRUE;
    $_SESSION["tnr"] = 0;
	$notes = $noteStore -> findNotesMetadata($_SESSION["entoken"], $nf, $_SESSION["tnr"], 2500, $gmdata);
	$nwt = 0;
	$insd = 0;
	foreach ($notes->notes as $note) {
	    echo "<div style='border: 1px solid gray; padding: 5px;' ><br /><br />================= NEW NOT0E STARTS HERE ===============<br />Note details:  ";
		var_dump($note);
        $notecont = $noteStore->getNoteContent($_SESSION["entoken"],$note->guid);
        echo "<br /><br />Note content: <br />";
        var_dump($notecont);
        
        echo "</div>";
	}
	$_SESSION["tnr"] = $_SESSION["tnr"] + $nc;
	
	$dr = $mn;
	$mn = $mn + 250;
	if($_SESSION["tnr"] < $notes->totalNotes){
		if($_GET["re"] != "0"){
	//header("Location: evn_checkfornotes.php");
}
	}
else{
//header("Location: " . $config["root"] . "friends/?a=evns");
}

} else {
	die("Couldn't find a noteboke guid.");
}
?>
