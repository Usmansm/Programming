 <script>
      				
			
      		
	  	</script>
<?php
if (@!$_SESSION) {
	session_start();
}
include "../config/config.php";
$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);

$fid = $_GET['fid'];


function getevnnote($nguid) {
	global $mysql;
	$qq = "SELECT * FROM evn_note_detail WHERE evnNoteGuid='" . $nguid . "'";
	$qr = $mysql -> query($qq);
	$dat = $qr -> fetch_assoc();
	return $dat;
}

function listevnnotes() {
	global $fid, $mysql,$evnNoteGuid;
	$gq = "SELECT * FROM user_frnd_evernote WHERE userId='" . $_SESSION["userId"] . "' AND friendId='" . $fid . "' ORDER BY evnNoteCreatedate DESC";
	$res = $mysql -> query($gq);
	while ($row = $res -> fetch_assoc()) {
		$notedetail = getevnnote($row["evnNoteGuid"]);
		$ntitle = $notedetail["evnNoteTitle"];
		$evnNoteGuid = $row["evnNoteGuid"];
		
		$nurl = $notedetail["evnNoteUrl"];
		if(strlen($nurl) > 30){
			$newp = substr($nurl,0,30);
			$rurl = $newp."...";

		}else{
			$rurl = $nurl;
		}
		
		if (strlen($ntitle) > 69){
			$newp = substr($ntitle,0,69);
			$NtitleDisp = $newp."...";
		}else{
			$NtitleDisp = $ntitle;
		}
		$nguid = $notedetail["evnNoteGuid"];
		$ndate = date("M d, Y", $notedetail["evnNoteCreatedate"] / 1000);
		echo <<<POST
      <div class="SocNewsMessage" id="{$nguid}_message" >
					<div id="date">{$ndate}</div>
					<img src="images/evnico.png" alt="linkedIn" width="17px;" height="17px" id="LeftSideIcon" />
					<p title="{$ntitle}" class="pNewsStream">{$NtitleDisp} <br /><a target="_blank" href="{$nurl}" >{$rurl}</a></p>
					<div class="TraashAndReplyIcon">
					
					
POST;
$ts = gettags($nguid);
foreach($ts as $tagn){
	echo '<span class="StreamType">'. $tagn.' </span>';
}
//Calculate the Email and link for Email client
$getEmailQuery = "SELECT * FROM user_friend_detail WHERE ViewableRow='' AND userId='" . $_SESSION["userId"] . "' AND friendId='" . $fid . "'";
	$getEmailResult = $mysql -> query($getEmailQuery);
	$getEmailResultSet=$getEmailResult-> fetch_assoc();
	// get all Emails 
	$e1='';
	$e2='';
	$e3='';
	if($getEmailResultSet['FriendEmail1']!='')
	{
	$e1=$getEmailResultSet['FriendEmail1'].',';
	}
	if($getEmailResultSet['FriendEmail2']!='')
	{
	$e2=$getEmailResultSet['FriendEmail2'].',';
	}
	if($getEmailResultSet['FriendEmail3']!='')
	{
	$e3=$getEmailResultSet['FriendEmail3'].',';
	}
	$to=$e1.$e2.$e3;
	$subject=$notedetail['evnNoteTitle'];
	$body= "Dear ".$getEmailResultSet['FriendFirstName']." I thought you would be intrested in this Article \n \n \n".$notedetail['evnNoteUrl'];
	$completeLink="mailto:".$to."?subject=".$subject."&amp;body=".$body;
	//echo $completeLink;
	//echo $to;

//******END**************************//

if ($row['noteProcessed']==0)
{
?>
<script>
jQuery(function($){
 $('a[href^="mailto:"]').on('click', function(e){
  var email = $(this).attr('href').replace('mailto:', '');
  // submit action to server here.
  alert(email);
});

});
</script>
<a href="<?php echo $completeLink;?>" class="anchorMail" guid="<?php echo $evnNoteGuid; ?>" ><img class="RightSideReply"   title="Mail" width="25px" height="25px" alt="Reply" src="../friends/images/Messagereply.png"></a>

<?php 
}
else
{
?>

<a href="<?php echo $completeLink;?>" class="anchorMail" guid="<?php echo $evnNoteGuid; ?>" ><img class="RightSideReply" title="Mail one more time" width="25px" height="25px" alt="Reply" src="../friends/images/importButton.png"></a>

<?php 
}
echo <<<POST
						
						<img class="SocialNewsTraskicon" title="Delete" id="{$nguid}_icon" onclick="del_evn_frnd_post('{$nguid}_icon','{$nguid}_message','{$nguid}','{$fid}')" width="22px" height="22px" alt="Trash" src="../img/login/Trash_Tiny.png">
					<hr id="hrLeft" /></div></div>

				
      
POST;
	}
}

function gettags($nguid){
    global $mysql;
	$cs = array();
    $qq = "SELECT * FROM evn_notes_cat WHERE evnNoteGuid='". $nguid ."'";
    $qr = $mysql->query($qq);
    while($row = $qr->fetch_assoc()){
        $nq = "SELECT * FROM user_categories WHERE catId='". $row["catId"] ."'";
        $nr = $mysql->query($nq);
        $dat = $nr->fetch_assoc();
		array_push($cs,$dat["catName"]);
        //echo $dat["catName"]."  ";
    }
	return $cs;
}
function get_post_underline($post,$terms)
	{
		//echo 'The terms are';
		
	//	print_r($terms);
		
		foreach($terms as $term)
		{
		//echo "$term \n";
		//echo $post;
		//$replace='<span class="fb_term" >'.$term.'</span>';
		$replace='<span style="font-weight:bold; border-bottom: 1px solid #999999;" >'.$term.'</span>';
		$post = str_ireplace($term, $replace, $post,$count);
		//$insmessage = str_ireplace($term, "Pakistan", $post,$count);
		//echo $post;
		//echo $count;
		
		//var_dump($post);
		}
	return $post;
	
	}
function listfbposts(){

	global $fid, $mysql;
	//Get All Terms for users with the ID
	  $termsQuery="select * from user_monitor_terms where userId=".$_SESSION["userId"];
	 // echo $termsQuery;
	   $resultterms=$mysql->query($termsQuery);
	  while($term = $resultterms->fetch_assoc()){
      $terms[]= $term['termName'];
 }
	$qq = "SELECT * FROM user_frnd_fbpost WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fid ."' ORDER BY Fbcreatedtime DESC";
	$res = $mysql->query($qq);
	while($dat = $res->fetch_assoc()){
		$nde = "SELECT * FROM fb_stream WHERE fbPostid='". $dat["fbPostid"] ."'";
		$nres = $mysql->query($nde);
		$post = $nres->fetch_assoc();
		if($post["actorName"] != "" && $post["targetName"] != ""){
			$ntit = $post["actorName"]." posted to ".$post["targetName"];
		}
		else{
			$ntit = $post["actorName"]." posted";
		}
		$ndate = date("M d, Y", $post["fbCreatedtime"]);
		$postcontent = get_post_underline($post["fbMessage"],$terms);
		$postid = $post["fbPostid"];
		$perma = $post["fbPermalink"];
		echo <<<POST
	 <div class="SocNewsMessage" id="post_{$fbpostid}">
	 <div id="date">{$ndate}</div>
	 <a href='{$perma}' target="_blank" class="a_noshow" ><img src="images/facebook.png" alt="facebook" width="17px;" height="17px" id="LeftSideIcon" /></a>
	 <p class="pNewsStream">{$ntit}: "{$postcontent}"</p>
	 <div class="TraashAndReplyIcon">
	 <img class="SocialNewsTraskicon" style="cursor: pointer;" id="trash_{$postid}" onclick="removefbpost('{$fbpostid}','{$fid}')" width="25px" height="25px" alt="Trash" src="../img/login/Trash_Tiny.png">
	 </div>
	 </div>
POST;
	}
}

echo '<div id="mainmenu">
		<div class="normalTab" id="FriendDetail"><a href="#" onclick="get_friend_detail(' . $fid . ')">Friend Detail</a></div>
		<div class="selectedTab"><a href="#" onclick="get_Social_News_Stream(' . $fid . ')">Social/News Stream</a></div>
		<div class="normalTab" ><a href="#" onclick="get_Mutual_Friends(' . $fid . ')">Mutual Friends</a></div>
	</div>';
?>
<?php /*
<div class="socialhead" >
<div class="socialstreamleft" >Social Stream
<input type="radio" id="FBandLi" name="facbook" value="facbook">
<img src="../img/login/fbTiny.png" alt="facebook" width="17px;" height="17px" />
<input type="radio" name="li" id="FBandLi" value="li">
<img src="../img/login/liTiny.png" alt="Li" width="17px;" height="17px" />
</div>
<div class="socialstreamright" >News Stream</div>
</div>

<div class="streamconthold" >
<div class="streamcontleft" >Hi</div>
<div class="streamcontright" ><?php listevnnotes();  ?></div>
</div>
*/ 
?>


	 <div id="SocialStream">

	 <div id="SocialStreamText"> Social Stream </div>
	 <div id="FBandLIbuttons">
	 <!-- <input type="radio" id="FBandLi" name="facbook" value="facbook"> 
	 <img src="../img/login/fbTiny.png" alt="facebook" width="17px;" height="17px" style="margin-left:20px;" />

	 <!-- <input type="radio" name="li" id="FBandLi" value="li"> 
	 <img src="../img/login/liTiny.png" alt="Li" width="17px;" height="17px" style="margin-left:20px;" />  -->

	 </div>
	 </div>

	 <div id="SocialStreamRight">
	 <div id="SocNewsStreamText"> NewsStream </div>
	 </div>

	 <div id="LeftSide">
	 	<?php
	 	listfbposts();
	 	?>
	 
	 <!-- RIFHT SIDE  -->
</div>
	 <div id="RightSide">
	 <?php
	 listevnnotes();
	 ?>
	 <input type="hidden" id="hdnFid" value="<?php echo $fid; ?>" />
	 <input type="hidden" id="hdnEvnNoteGuid" value="<?php echo $evnNoteGuid; ?>" />
	 
	 <!--
	 <div class="SocNewsMessage">
	 <div id="date">Feb 25, 2013</div>
	 <img src="images/evnico.png" alt="linkedIn" width="17px;" height="17px" id="LeftSideIcon" />
	 <p class="pNewsStream">Chrls Johnson posted to john doe: "Conguratilations on your daughter birthday Suzy"</p>
	 <div class="TraashAndReplyIcon">
	 <span class="StreamType"> FINRA </span> <span class="StreamType">  CRM </span>
	 <img class="RightSideReply" width="25px" height="25px" alt="Reply" src="../friends/images/Messagereply.png">
	 <img class="SocialNewsTraskicon" width="25px" height="25px" alt="Trash" src="../img/login/Trash_Tiny.png">
	 </div>
	 </div>
	 <hr id="hrLeft" />

	 <div class="SocNewsMessage">
	 <div id="date">Feb 25, 2013</div>
	 <img src="images/evnico.png" alt="linkedIn" width="17px;" height="17px" id="LeftSideIcon" />
	 <p class="pNewsStream">Chrls Johnson posted to john doe: "Conguratilations on your daughter birthday Suzy"</p>
	 <div class="TraashAndReplyIcon">
	 <span class="StreamType"> FINRA </span> <span class="StreamType">  CRM </span>
	 <img class="RightSideReply" width="25px" height="25px" alt="Reply" src="../friends/images/Messagereply.png">
	 <img class="SocialNewsTraskicon" width="25px" height="25px" alt="Trash" src="../img/login/Trash_Tiny.png">
	 </div>
	 </div>
	 <hr id="hrLeft" />

	 <div class="SocNewsMessage">
	 <div id="date">Feb 25, 2013</div>
	 <img src="images/evnico.png" alt="linkedIn" width="17px;" height="17px" id="LeftSideIcon" />
	 <p class="pNewsStream">Chrls Johnson posted to john doe: "Conguratilations on your daughter birthday Suzy"</p>
	 <div class="TraashAndReplyIcon">
	 <span class="StreamType"> social media </span>
	 <img class="RightSideReply" width="25px" height="25px" alt="Reply" src="../friends/images/Messagereply.png">
	 <img class="SocialNewsTraskicon" width="25px" height="25px" alt="Trash" src="../img/login/Trash_Tiny.png">
	 </div>
	 </div>
	 <hr id="hrLeft" />

	 <div class="SocNewsMessage">
	 <div id="date">Feb 25, 2013</div>
	 <img src="images/evnico.png" alt="linkedIn" width="17px;" height="17px" id="LeftSideIcon" />
	 <p class="pNewsStream">Chrls Johnson posted to john doe: "Conguratilations on your daughter birthday Suzy"</p>
	 <div class="TraashAndReplyIcon">
	 <span class="StreamType"> FINRA </span> <span class="StreamType">  CRM </span>
	 <img class="RightSideReply" width="25px" height="25px" alt="Reply" src="../friends/images/Messagereply.png">
	 <img class="SocialNewsTraskicon" width="25px" height="25px" alt="Trash" src="../img/login/Trash_Tiny.png">
	 </div>
	 <hr id="hrLeft" />

	 <div class="SocNewsMessage">
	 <div id="date">Feb 25, 2013</div>
	 <img src="images/evnico.png" alt="linkedIn" width="17px;" height="17px" id="LeftSideIcon" />
	 <p class="pNewsStream">Chrls Johnson posted to john doe: "Conguratilations on your daughter birthday Suzy"</p>
	 <div class="TraashAndReplyIcon">
	 <span class="StreamType"> FINRA </span> <span class="StreamType">  CRM </span>
	 <img class="RightSideReply" width="25px" height="25px" alt="Reply" src="../friends/images/Messagereply.png">
	 <img class="SocialNewsTraskicon" width="25px" height="25px" alt="Trash" src="../img/login/Trash_Tiny.png">
	 </div>
	 </div>
	 <hr id="hrLeft" />

	 <div class="SocNewsMessage">
	 <div id="date">Feb 25, 2013</div>
	 <img src="images/evnico.png" alt="linkedIn" width="17px;" height="17px" id="LeftSideIcon" />
	 <p class="pNewsStream">Chrls Johnson posted to john doe: "Conguratilations on your daughter birthday Suzy"</p>
	 <div class="TraashAndReplyIcon">
	 <span class="StreamType"> FINRA </span> <span class="StreamType">  CRM </span>
	 <img class="RightSideReply" width="25px" height="25px" alt="Reply" src="../friends/images/Messagereply.png">
	 <img class="SocialNewsTraskicon" width="25px" height="25px" alt="Trash" src="../img/login/Trash_Tiny.png">
	 </div>
	 </div>
	 <hr id="hrLeft" />

	 <div class="SocNewsMessage">
	 <div id="date">Feb 25, 2013</div>
	 <img src="images/evnico.png" alt="linkedIn" width="17px;" height="17px" id="LeftSideIcon" />
	 <p class="pNewsStream">Chrls Johnson posted to john doe: "Conguratilations on your daughter birthday Suzy"</p>
	 <div class="TraashAndReplyIcon">
	 <span class="StreamType"> FINRA </span> <span class="StreamType">  CRM </span>
	 <img class="RightSideReply" width="25px" height="25px" alt="Reply" src="../friends/images/Messagereply.png">
	 <img class="SocialNewsTraskicon" width="25px" height="25px" alt="Trash" src="../img/login/Trash_Tiny.png">
	 </div>

	 </div>
	 <hr id="hrLeft" />

	 <div class="SocNewsMessage">
	 <div id="date">Feb 25, 2013</div>
	 <img src="images/evnico.png" alt="linkedIn" width="17px;" height="17px" id="LeftSideIcon" />
	 <p class="pNewsStream">Chrls Johnson posted to john doe: "Conguratilations on your daughter birthday Suzy"</p>
	 <div class="TraashAndReplyIcon">
	 <span class="StreamType"> FINRA </span> <span class="StreamType">  CRM </span>
	 <img class="RightSideReply" width="25px" height="25px" alt="Reply" src="../friends/images/Messagereply.png">
	 <img class="SocialNewsTraskicon" width="25px" height="25px" alt="Trash" src="../img/login/Trash_Tiny.png">
	 </div>
	 </div>
	 <hr id="hrLeft" />

	 <div class="SocNewsMessage">
	 <div id="date">Feb 25, 2013</div>
	 <img src="images/evnico.png" alt="linkedIn" width="17px;" height="17px" id="LeftSideIcon" />
	 <p class="pNewsStream">Chrls Johnson posted to john doe: "Conguratilations on your daughter birthday Suzy"</p>
	 <div class="TraashAndReplyIcon">
	 <span class="StreamType"> FINRA </span> <span class="StreamType">  CRM </span>
	 <img class="RightSideReply" width="25px" height="25px" alt="Reply" src="../friends/images/Messagereply.png">
	 <img class="SocialNewsTraskicon" width="25px" height="25px" alt="Trash" src="../img/login/Trash_Tiny.png">
	 </div>

	 </div>
	 <hr id="hrLeft" />

	 <div class="SocNewsMessage">
	 <div id="date">Feb 25, 2013</div>
	 <img src="images/evnico.png" alt="linkedIn" width="17px;" height="17px" id="LeftSideIcon" />
	 <p class="pNewsStream">Chrls Johnson posted to john doe: "Conguratilations on your daughter birthday Suzy"</p>
	 <div class="TraashAndReplyIcon">
	 <span class="StreamType"> FINRA </span> <span class="StreamType">  CRM </span>
	 <img class="RightSideReply" width="25px" height="25px" alt="Reply" src="../friends/images/Messagereply.png">
	 <img class="SocialNewsTraskicon" width="25px" height="25px" alt="Trash" src="../img/login/Trash_Tiny.png">
	 </div>

	 </div>
	 <hr id="hrLeft" />

	 <div class="SocNewsMessage">
	 <div id="date">Feb 25, 2013</div>
	 <img src="images/evnico.png" alt="linkedIn" width="17px;" height="17px" id="LeftSideIcon" />
	 <p class="pNewsStream">Chrls Johnson posted to john doe: "Conguratilations on your daughter birthday Suzy"</p>
	 <div class="TraashAndReplyIcon">
	 <span class="StreamType"> FINRA </span> <span class="StreamType">  CRM </span>
	 <img class="RightSideReply" width="25px" height="25px" alt="Reply" src="../friends/images/Messagereply.png">
	 <img class="SocialNewsTraskicon" width="25px" height="25px" alt="Trash" src="../img/login/Trash_Tiny.png">
	 </div>
	 -->
	 </div>
	 <!--
	 <div id="date">Feb 14th, 2013</div>
	 <div id="message">
	 <img src="../img/login/MIB_Tiny.png" alt="MIB" width="32px;" height="26px" id="RightSideIcon" />
	 <p class="pNewsStream">MyIceberg announces the appointment of John Doe as CIO</p>
	 </div>
	 <div id="StarTribune">Star Tribune </div>
	 <img src="../img/login/Trash_Tiny.png" alt="Trash" width="16px;" height="18px" id="trashicon" />
	 <hr id="hrRight" />

	 <div id="date">May 4th, 2013</div>
	 <div id="message">
	 <img src="../img/login/Newspaper_Tiny.png" alt="News" width="39px;" height="27px" id="RightSideIcon"/>
	 <p class="pNewsStream">Zebra muscles found in Lake Minnetonka</p>
	 </div>
	 <div id="StarTribune">Star Tribune </div>
	 <img src="../img/login/Trash_Tiny.png" alt="Trash" width="16px;" height="18px" id="trashicon" />
	 <hr id="hrRight" />

	 <div id="date">Feb 14th, 2013</div>
	 <div id="message">
	 <img src="../img/login/NewspaperReplyed_Tiny.png" alt="NewsReplyed" width="37px;" height="31px" id="RightSideIcon"/>
	 <p class="pNewsStream">Cloud based Applications easy to scale</p>
	 </div>
	 <div id="StarTribune">Tecdaily</div>
	 <img src="../img/login/Trash_Tiny.png" alt="Trash" width="16px;" height="18px" id="trashicon" />
	 <hr id="hrRight" />

	 <div id="date">Feb 14th, 2013</div>

	 <div id="message">
	 <img src="../img/login/MIB_Tiny.png" alt="MIB" width="32px;" height="26px" id="RightSideIcon" />
	 <p class="pNewsStream">Tony Clevland announces his resingnations from Carlson Travel</p>
	 </div>
	 <div id="StarTribune">Travel Weekly</div>
	 <img src="../img/login/Trash_Tiny.png" alt="Trash" width="16px;" height="18px" id="trashicon" />
	 <hr id="hrRight" />

	 <div id="date">Feb 14th, 2013</div>
	 <div id="message">
	 <img src="../img/login/Newspaper_Tiny.png" alt="News" width="39px;" height="27px" id="RightSideIcon"/>
	 <p class="pNewsStream">Carlson travel awarded $2 billion GSA contract</p>
	 </div>
	 <div id="StarTribune">Star Tribune </div>
	 <img src="../img/login/Trash_Tiny.png" alt="Trash" width="16px;" height="18px" id="trashicon" />
	 <hr id="hrRight" />

	 -->

 

