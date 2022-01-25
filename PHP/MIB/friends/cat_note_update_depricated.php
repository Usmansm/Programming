<?php
if (@!$_SESSION) {
	session_start();
}
include "../../config/config.php";
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);

//Repeat this for every user that has been added to a cat \/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/

//Need an array of which cats(id's) the user has been added to, and the id of the friend that has been added
$cats = array("123", "456", "789");
$faid = "";

//This function checks if the logged in user is connected with evernote
function checkifevn() {
	global $mysql;
	$query = "SELECT * FROM user_external_accnt WHERE userId='" . $_SESSION["userId"] . "' AND authProvider='evernote'";
	$result = $mysql -> query($query);
	$data = $result -> fetch_assoc();
	if ($data["id"] == "") {
		return false;
	} else {
		return true;
	}

}

//Call the function to check if user is connected with evernote
$isevn = checkifevn();

//If the user is connected with evernote continue the process, else ignore
if ($isevn == true) {
	//Loop thru the cats the user has been added to
	foreach ($cats as $cat) {
		//Check if there any notes associated with this cat
		$checkquery = "SELECT * FROM evn_notes_cat WHERE catId='". $cat ."'";
		$checkres = $mysql->query($checkquery);
		while($data = $checkres->fetch_assoc()){
			$noteq = "SELECT * FROM evn_note_detial WHERE evnNoteGuid='". $data["evnNoteGuid"] ."'";
			$rres = $mysql->query($noteq);
			$dat = $rres->fetch_assoc();
			
			$nn = "INSERT INTO user_frnd_evernote(userId,friendId,evnNoteGuid,evnNoteCreatedate) VALUES(". $_SESSION["userId"] .",". $faid .",". $data["evnNoteGuid"] .",". $data["evnNoteCreatedate"] .")";
		}
	}
}

?>