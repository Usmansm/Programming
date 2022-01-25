<?php
error_reporting(E_ALL);

if (!@$_SESSION) {
	session_start();
}

include "../../config/config.php";
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
include "../../config/fb_config.php";
include "../../lib/facebooksdk/src/facebook.php";

$facebook = fbconnect();
$fbtoken = getfbtoken();
$fbuid = getfbuid();

$debug = true;

function createhead($w) {
	if ($w == "start") {
		echo <<<HEAD
	<html>
	<head>
		<title>FB RE POST</title>
		<style>
			body{
				padding: 0px; margin: 0px;
			}
			.debug_div{
				background: #c0c0c0;border-bottom: 5px solid gray; margin-top: 10px; margin-bottom: 10px;padding: 6px;
			}
		</style>
	</head>
	<body>
HEAD;
	} else {
		echo <<<FOOT
		</body>
		</head>
FOOT;
	}
}

function decho($t) {
	global $debug;
	if($debug == true){ echo $t; }
}

createhead("start");

if ($fbtoken != false) {
	$query = "SELECT * FROM user_monitor_terms WHERE userId='" . $_SESSION["userId"] . "'";
	$res = $mysql -> query($query);
	$monterms = array();
	decho("<div class=\"debug_div\" >");
	while ($row = $res -> fetch_assoc()) {
		decho("Found the term \"" . $row["termName"] . "\" adding it to array<br /><br />");
		array_push($monterms, $row["termName"]);
	}
	decho("--<br />Current terms: ");
	foreach ($monterms as $term) {
		decho($term . " ");
	}

	decho("<br />--<br /><br />");
	foreach ($monterms as $term) {
		if($term != "Add new term"){
		$fql = "SELECT post_id, source_id, actor_id, permalink, target_id, created_time, message, updated_time, attribution FROM stream where message != '' and strpos(lower(message),lower('" . $term . "')) >=0 and source_id IN (SELECT uid2 FROM friend WHERE uid1 = '" . $fbuid . "')LIMIT 200";
		$param = array('method' => 'fql.query', 'query' => $fql, 'callback' => '');
		$fqlResult = $facebook -> api($param);
		decho("Sent term '". $term ."' result: <br />");
		print_r($fqlResult);
		foreach($fqlResult as $post){
			if($post != ""){
			decho("<br /><br />Checking if post id ".$post["post_id"] ." exists in database");
			$cqu = "SELECT * FROM fb_stream WHERE fbPostid='". $post["post_id"] ."'";
			$rres = $mysql->query($cqu);
			$dat = $rres->fetch_assoc();
			if($dat["id"] == ""){
				decho("<br />Post doesn't exist, inserting it<br />");
				if($post["actor_id"] != ""){
				decho("<br />Checking facebook for actor id '". $post["actor_id"] ."'");
				$sfql = "SELECT first_name, last_name FROM user where uid='". $post["actor_id"] ."'";
				$sparam = array('method' => 'fql.query', 'query' => $sfql, 'callback' => '');
				$sfqlResult = $facebook -> api($sparam);
				decho("Result of user look up:<br />");
				$actorname = $sfqlResult[0]["first_name"]." ".$sfqlResult[0]["last_name"];
				print_r($sfqlResult);
				decho("<br />");
			}
				if($post["target_id"] != ""){
				decho("<br />Checking facebook for target id '". $post["actor_id"] ."'");
				$sfql = "SELECT first_name, last_name FROM user where uid='". $post["target_id"] ."'";
				$sparam = array('method' => 'fql.query', 'query' => $sfql, 'callback' => '');
				$sfqlResult = $facebook -> api($sparam);
				decho("Result of user look up:<br />");
				$targetname = $sfqlResult[0]["first_name"]." ".$sfqlResult[0]["last_name"];
				print_r($sfqlResult);
				decho("<br />");
				}
else{
	$targetname = "";
}				
				$insmessage = str_ireplace($term, "<span class=fb_term >".$term."</span>", $post["message"]);
				$insmessage = $mysql->real_escape_string($insmessage);
				if($targetname != ""){
				$inq = "INSERT INTO fb_stream(fbPostid, actorId, actorName, targetName, fbPermalink, fbMessage, fbCreatedtime, fbSourceid) VALUES('". $post["post_id"] ."', '". $post["actor_id"] ."', '". $actorname ."', '". $targetname ."', '". $post["permalink"] ."', '". $insmessage ."', '". $post["created_time"] ."', '". $post["source_id"] ."')";
				}
				else{
				$inq = "INSERT INTO fb_stream(fbPostid, actorId, actorName, fbPermalink, fbMessage, fbCreatedtime, fbTargetid, fbSourceid) VALUES('". $post["post_id"] ."', '". $post["actor_id"] ."', '". $actorname ."', '". $post["permalink"] ."', '". $insmessage ."', '". $post["created_time"] ."', '". $post["target_id"] ."', '". $post["source_id"] ."')";
				}
				decho("<BR /><br />Query: ".$inq."<br /><br />");
				$ires = $mysql->query($inq);
			}
			else{
				decho("<br />Post already exists");
			}
			decho("<br />Looking up actor id '". $post["actor_id"] ."' in our system<br />");
			$lq = "SELECT * FROM source_import WHERE sourceUid='". $post["actor_id"] ."'";
			$lres = $mysql->query($lq);
			$lrow = $lres->fetch_assoc();
			if($lrow["sourceId"] == ""){
				decho("No user with that actor id exists in our system.<br /><br />");
			}
			else{
				decho("Found a user with that actor id in our system checking if that user is a friend <br /><br />");
				$selq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $lrow["userId"] ."'";
				$selres = $mysql->query($selq);
				$fdata = $selres->fetch_assoc();
				if($fdata != ""){
					decho("User is a friend of the post source. Creating a record in user_frnd_fbpost<br />");
					$insq = "INSERT INTO user_frnd_fbpost(userId, friendId, fbPostid) VALUES('". $_SESSION["userId"] ."', '". $lrow["userId"] ."', '". $post["post_id"] ."')";
					$inres = $mysql->query($insq);
				}
				else{
					decho("That user is not a friend of the post source<br />");
				}
			}
			
			//--
						decho("<br />Looking up target id '". $post["target_id"] ."' in our system<br />");
			$lq = "SELECT * FROM source_import WHERE sourceUid='". $post["target_id"] ."'";
			$lres = $mysql->query($lq);
			$lrow = $lres->fetch_assoc();
			if($lrow["sourceId"] == ""){
				decho("No user with that actor id exists in our system.<br /><br />");
			}
			else{
				decho("Found a user with that target id in our system checking if that user is a friend <br /><br />");
				$selq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $lrow["userId"] ."'";
				$selres = $mysql->query($selq);
				$fdata = $selres->fetch_assoc();
				if($fdata != ""){
					decho("User is a friend of the post source. Creating a record in user_frnd_fbpost<br />");
					$insq = "INSERT INTO user_frnd_fbpost(userId, friendId, fbPostid, Fbcreatedtime) VALUES('". $_SESSION["userId"] ."', '". $lrow["userId"] ."', '". $post["post_id"] ."', '". $post["created_time"] ."')";
					$inres = $mysql->query($insq);
				}
				else{
					decho("That user is not a friend of the post source<br />");
				}
			}
			}
		}
		}
	}
	decho("</div>");
} else {
	die("No fb auth token");
}

createhead("end");
?>