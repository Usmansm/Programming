<?php
session_start();
?>
<div id="mainmenu">
	<div class="normalTab" id="FriendDetail">
		<a href="#" onclick="get_friend_detail(<?php echo $_GET["fid"]; ?>)">Friend Detail</a>
	</div>
	<div class="normalTab">
		<a href="#" onclick="get_Social_News_Stream(<?php echo $_GET["fid"]; ?>)">Social/News Stream</a>
	</div>
	<div class="selectedTab" >
		<a href="#" onclick="get_Mutual_Friends(<?php echo $_GET["fid"]; ?>)">Mutual Friends</a>
	</div>
</div>

<table id="MutualFirendsTable">
	<?php
	require_once ('../config/config.php');
	include('../php/class/mutualFriendsPage.php');
	?>
	<!--
	<tr id="ImageRow">
		<td class="ImageOfFriend"><img class="InnerImgOfFriend" src="images/noimage.png" /></td>
		<td class="ImageOfFriend"><img class="InnerImgOfFriend" src="images/noimage.png" /></td>
		<td class="ImageOfFriend"><img class="InnerImgOfFriend" src="images/noimage.png" /></td>
		<td class="ImageOfFriend"><img class="InnerImgOfFriend" src="images/noimage.png" /></td>
	</tr>

	<tr id="NameRow" >
		<td class="NameOfFriend" ><span class="InnernameOfFriend">Name
			<br />
			Name</span></td>
		<td class="NameOfFriend" ><span class="InnernameOfFriend">Name
			<br />
			Name</span></td>
		<td class="NameOfFriend" ><span class="InnernameOfFriend">Name
			<br />
			Name</span></td>
		<td class="NameOfFriend" ><span class="InnernameOfFriend">Name
			<br />
			Name</span></td>
	</tr>

	<tr id="ConnIconsRow" >
		<td class="ConnIcon" ><img width="17px;" height="17px" alt="linkedIn" src="../img/login/liTiny.png"></td>
		<td class="ConnIcon" ><img width="17px;" height="17px" alt="linkedIn" src="../img/login/fbTiny.png"></td>
		<td class="ConnIcon" ><img width="17px;" height="17px" alt="linkedIn" src="../img/login/fbTiny.png">
		<img width="17px;" height="17px" alt="linkedIn" src="../img/login/liTiny.png"></td>
		<td class="ConnIcon" ><img width="17px;" height="17px" alt="linkedIn" src="../img/login/liTiny.png"></td>
	</tr>
	-->
</table>
