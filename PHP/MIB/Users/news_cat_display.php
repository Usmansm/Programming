<?php
/*
 * Corey Masslock
 * File to display only news stories in 1 category when user clicks on news category
 * in news page
 * Loaded with ajax into news_index.php
 */
if (@!$_SESSION) {
	session_start();
}
include "../config/config.php";
$newscat = $_GET["ncid"];
$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);

//Function to get all tags for note
function gettags($nguid) {
	global $mysql;
	$cs = array();
	$qq = "SELECT * FROM evn_notes_cat WHERE evnNoteGuid='" . $nguid . "'";
	$qr = $mysql -> query($qq);
	while ($row = $qr -> fetch_assoc()) {
		$nq = "SELECT * FROM user_categories WHERE catId='" . $row["catId"] . "'";
		$nr = $mysql -> query($nq);
		$dat = $nr -> fetch_assoc();
		array_push($cs, $dat["catName"]);
		//echo $dat["catName"]."  ";
	}
	return $cs;
}

//Function to display the notes
function listevnnotes() {
	global $mysql, $config, $newscat;
	$nc = 0;
	$nds = array();
	$fc = array();
	$dnotes = array();
	$catq = "SELECT * FROM user_categories WHERE userId='" . $_SESSION["userId"] . "' AND catId='". $newscat ."'";
	$catres = $mysql -> query($catq);
	while ($row = $catres -> fetch_assoc()) {
		$query = "SELECT * FROM evn_notes_cat WHERE catId='" . $newscat . "'";
		$qres = $mysql -> query($query);
		while ($raw = $qres -> fetch_assoc()) {

			$nc++;
			$que = "SELECT * FROM evn_note_detail WHERE evnNoteGuid='" . $raw["evnNoteGuid"] . "'";
			$result = $mysql -> query($que);
			$dat = $result -> fetch_assoc();
			$ntitle = $dat["evnNoteTitle"];
			$nurl = $dat["evnNoteUrl"];
			$ncat = $row["catName"];
			$nguid = $dat["evnNoteGuid"];
			if (!in_array($nguid, $fc)) {
				$ndate = date("M d, Y", $dat["evnNoteCreatedate"] / 1000);
				$ntags = gettags($nguid);
				$nds[$nguid] = $dat["evnNoteCreatedate"];
				$dnotes[$nguid] = array("title" => $ntitle, "url" => $nurl, "tags" => $ntags);

				array_push($fc, $nguid);
			}
		}
	}
	arsort($nds);
	foreach ($nds as $tkey => $tnote) {
		$tguid = $tkey;
		$ttitle = $dnotes[$tkey]["title"];
		$turl = $dnotes[$tkey]["url"];
		$ttags = $dnotes[$tkey]["tags"];
		$tdate = date("M d, Y", $tnote / 1000);
		$slen = strlen($turl);
		if ($slen > 30) {
			$newp = substr($turl, 0, 30);
			$rurl = $newp . "...";
		} else {
			$rurl = $turl;
		}
		echo <<<POST
		<div id="{$tguid}_message" class="Newholder">
				<div id="date">{$tdate}</div>
					<div id="message">
						<img src="../friends/images/evnico.png" alt="MIB" id="RightSideIcon" />
						<p class="paragraph">{$ttitle}</p>  <br/>
						<a href="{$turl}" target="_blank" class="NewsStreamLink">{$rurl}</a>
					</div> 
					<div class ="messageStatus">
						
POST;
		foreach ($ttags as $tname) {
			echo "<span id='StarTribune'>" . $tname . "</span>";
		}
		echo <<<POST
<img class="Newsicon" alt="Reply" src="../friends/images/Messagereply.png"> <img class='trashiconNews' id="{$tguid}_icon" width="24px;" height="24px" onclick="del_evn_post('{$tguid}_icon','{$tguid}_message','{$tguid}')" alt="Trash" src="../img/login/Trash_Tiny.png">
					</div>
			</div>	
POST;

	}

	echo "Total: " . $nc;
}
?>
<div id="NewsStreamText"> NewsStream </div>
<?php
listevnnotes();
?>