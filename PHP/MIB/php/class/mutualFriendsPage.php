<?php
session_start();
require_once('../config/config.php');
require_once('../php/class/frienddetail.class.php');
require_once('../includes/DisplayAvatar.php');
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);

/*function getsociallink($fid,$src){
	global $mysql;
	if($src == "facebook"){
		$cc = "SELECT * FROM source_import WHERE userId='". $fid ."' AND sourceName='facebook'";
		$cr = $mysql->query($cc);
		$shniggy = $cr->fetch_assoc();
		return "http://facebook.com/".$shniggy["sourceUid"];
	}
	if($src == "linkedin"){
		$cc = "SELECT * FROM source_import WHERE sourceId='". $fid ."' AND sourceName='linkedin'";
		$cr = $mysql->query($cc) OR die($mysql->error());
		$shniggy = $cr->fetch_assoc();
		//print_r($expression)
		return $shniggy["sourceProfileLink"];
	}
}*/
function checkviewable($fid){
	global $mysql;
	$checkq = "SELECT * FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $fid ."'";
	$cres = $mysql->query($checkq);
	$cdat = $cres->fetch_assoc();
	return $cdat["ViewableRow"];
}

function getfriendname($which,$gfid){
	global $mysql;
	$nameq = "SELECT FriendFirstName,FriendMiddleName,FriendLastName FROM user_friend_detail WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $gfid ."'";
	$namer = $mysql->query($nameq);
	$namedata = $namer->fetch_assoc();
	return $namedata;
}
/*function sourceicons($fid) {
	global $mysql;
	$tq = "SELECT * FROM userfrnd_source WHERE userId='" . $_SESSION["userId"] . "' AND friendId='" . $fid . "'";
	$tr = $mysql -> query($tq);
	while ($data = $tr -> fetch_assoc()) {
		if ($data["sourceType"] == "") {
			$aq = "SELECT * FROM source_import WHERE sourceId='" . $data["source_import_Id"] . "'";
			$ar = $mysql -> query($aq);
			while ($dat = $ar -> fetch_assoc()) {
				if ($dat["sourceName"] == "facebook") {
					echo "<a href='". getsociallink($fid,"facebook") ."' target='_blank' class='a_noshow' ><img src='images/facebook.png' style='width: 15px; height: 15px;' /></a> ";
				}
				if ($dat["sourceName"] == "linkedin") {
					echo "<a href='". getsociallink($data["source_import_Id"],"linkedin") ."' target='_blank' class='a_noshow' ><img src='images/linkedin.png' style='width: 15px; height: 15px;' /></a> ";
				}
			}
		} else if ($data["sourceType"] == "source_import_cs") {
			$aq = "SELECT * FROM source_import_cs WHERE sourceId='" . $data["source_import_Id"] . "'";
			$ar = $mysql -> query($aq);
			while ($dat = $ar -> fetch_assoc()) {
				echo "<a href='' target='_blank' class='a_noshow' ><img src='images/" . $dat["sourceName"] . ".png' style='width: 15px; height: 15px;' /></a> ";
			}
		} else if ($data["sourceType"] == "source_import_sf") {
			echo "<a href='' target='_blank' class='a_noshow' ><img src='images/salesforce.png' style='width: 15px; height: 15px;' /></a> ";
		}
	}

}*/

$friend = new friend;
// I changed data to dataaaa
$dataaa = $friend->mutualFriendsColumn($_SESSION['lfid']);
//var_dump($dataaa);
$counter = count($dataaa);
//var_dump($counter);
$counter2 = 1;// + 
if ($counter >= 1){
	//echo $counter2;
	$queryforFriends = "SELECT * FROM  user_friend_detail WHERE userId='". $_SESSION['userId'] ."' AND friendId IN('".$dataaa[0]; 
	while ($counter > $counter2){
		$queryforFriends = $queryforFriends."' , '". $dataaa[$counter2] ;
		$counter2 ++;
		//echo $counter2."<br>";

	}
	$queryforFriends = $queryforFriends. "') ORDER BY FriendFirstName ASC";
}

//print($queryforFriends);
$resultForFriends = $mysql->query($queryforFriends);

//var_dump($resultForFriends);
$data =  array();
while ($resultarray = $resultForFriends->fetch_assoc()){
	array_push($data, $resultarray['friendId']);
}
//var_dump ($data);

/*AND ads.type = 13
AND
(
    ads.county_id = 2
    OR ads.county_id = 5
    OR ads.county_id = 7
    OR ads.county_id = 9
)"*/


/* NOTE FOR DISPLAYING DATA IN MUTUAL FREINDS
	//Sturcture of table for displaying mutual freinds look like this
	<tr> 
		<td>Image of freind 1</td>
		<td>Image of freind 2</td>
		<td>Image of freind 3</td>
		<td>Image of freind 4</td>
	</tr>
	<tr>
		<td>name of freind 1</td>
		<td>name of freind 2</td>
		<td>name of freind 3</td>
		<td>name of freind 4</td>
	</tr>
	<tr>
		<td>SRC icon of freind 1</td>
		<td>SRC icon of freind 2</td>
		<td>SRC icon of freind 3</td>
		<td>SRC icon of freind 4</td>
	</tr>
	
	so logic is this 
	1. get number or mutual vrends
	2. get numver of full rows (each row contain 4 friend)
	3.  loop{
		loop for displaying 4 images 
		loop for displaying 4 names
		loop for displaying 4 SRC icon s 
		}
		
	4. if total number of freinds is NOT divisible whit 4 display rest of friends

*/
$numberOfMutualFreinds = count($data); // get number of mutual freinds 
//var_dump ($numberOfMutualFreinds);
$PossigleRemainInLastRow = $numberOfMutualFreinds % 4; // check is it divisible whit 4
//var_dump ($PossigleRemainInLastRow);
$numberOfRowsWhitFOURFreinds2 = $numberOfMutualFreinds / 4; // check homw mant rows we have
if ($PossigleRemainInLastRow = 0 ){ // if we total number of freinds is divisible whit 4 then we are displaying 1 less row tihs is just a logic trick .. otherwsie we whoud have some loops executed 1 time less in some situations 
	$numberOfRowsWhitFOURFreinds2 = $numberOfRowsWhitFOURFreinds2 - 1; 
}
$numberOfRowsWhitFOURFreinds2 = floor($numberOfRowsWhitFOURFreinds2); // transfer this number to integer for whille loop
//var_dump ($numberOfRowsWhitFOURFreinds2);

$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
$numf = count($data);
$nonv = 0;
	while  ($ii < $numf ){
		$ii ++;
		$isviewable = checkviewable($data[$ii]);
		if ($isviewable != '0'){
			
		}
		$nonv++;
	}
echo "We discovered ". $nonv ."  mutual friends.";
if(empty($data)){
echo ' No friends in Common!';

}else {
	$x = 0; // counter for images
	$y = 0; // counter for Names
	$z = 0; // counter for src icons
		echo'<table id="MutualFirendsTable">'; // start of table 
	while ( $i < $numberOfRowsWhitFOURFreinds2 ){ // big while loop see number 3.
	
		echo'<tr id="ImageRow">'; // loop for displaying images in rows
		
		for ($j1=1; $j1<=4; $j1++){
			//$isviewable = checkviewable($data[$x]);
			//if($isviewable != "0"){
			$link = DispFreProfilePic($data[$x]); // get data
			echo '<td class="ImageOfFriend"><a href="'. $config["root"] .'friends/?a=f&f='. $data[$x] .'" target="_blank" class="a_noshow" ><img class="InnerImgOfFriend" src="'.$link.'" /></a></td>'; // display this fiend
			$x++; // increase counter for images 
			$numberOfMutualFreinds = $numberOfMutualFreinds - 1 ; // dertease counter for total number of freinds we will use this later...
			//}
		}
		
		echo '</tr><tr id="NameRow" >'; 
		
		for ($j2=1; $j2<=4; $j2++){ // loop for displaying Names logic is same in all three if those loops
			//$isviewable = checkviewable($data[$y]);
			//if($isviewable != "0"){
			//var_dump($data[$y]);
			$query2 = 'SELECT * FROM source_import WHERE userId = "'.$data[$y].'"'; 
			$result2 = $mysqli->query($query2);
			$data2 = $result2->fetch_array();
			$fname = getfriendname(" ", $data[$y]);
			echo '<td class="NameOfFriend" ><span class="InnernameOfFriend">'.$fname["FriendFirstName"].' '. $fname["FriendLastName"].'</span></td>';
			$y++; // increase counter for Names 
			//}
			
		}
		
		echo '</tr><tr id="ConnIconsRow" >';
		
		for ($j3=1; $j3<=4; $j3++){ // loop for displaying Src Icons in rows
			//$isviewable = checkviewable($data[$z]);
			//if($isviewable != "0"){
				//$query3 = 'SELECT * FROM source_import WHERE userId = "'.$data[$z].'"';
				//$result3 = $mysqli->query($query3);
				//$data3 = $result3->fetch_array();
				
				echo '<td class="ConnIcon" >';
				//var_dump($data[$z]);
				$fid = $data[$z];
				sourceicons($fid);
				echo '</td>';
				$z++; // increase counter for src icons
			//}
		}
		
		echo '</tr>';
		$i++;// counter for number of 4 freind row-s... 
	}	
	
	if ($numberOfMutualFreinds != 0){ // if we not displayed all friends display them almost same same logic as above
			echo'<tr id="ImageRow">';
		for ($j1=1; $j1<=$numberOfMutualFreinds; $j1++){ // we are displaying only freinds which are not duisplayed yet and at this point it is possible that there is 1 , 2 or 3 of them 
			//$isviewable = checkviewable($data[$x]);
			//if($isviewable != "0"){
				$link = DispFreProfilePic($data[$x]);
				$x++;
				echo '<td class="ImageOfFriend"><a href="'.
							 $config["root"] .
							 'friends/?a=f&f='. 
							 $data[$x] .
							 '" target="_blank" class="a_noshow" >
                             <object src="'.$link.'>'.'
							 <img class="InnerImgOfFriend" src = "views/images/noimage.jpg"'.
							 '" /></object></a></td>';
			//}
		}
		echo '</tr><tr id="NameRow" >';
		for ($j2=1; $j2<=$numberOfMutualFreinds; $j2++){
			//$isviewable = checkviewable($data[$y]);
			//if($isviewable != "0"){
				$query2 = 'SELECT * FROM source_import WHERE userId = "'.$data[$y].'"';
				$result2 = $mysqli->query($query2);
				$data2 = $result2->fetch_array();
				$fname = getfriendname(" ", $data2["userId"]);
				echo '<td class="NameOfFriend" ><span class="InnernameOfFriend">'.$fname["FriendFirstName"].' '. $fname["FriendLastName"].'</span></td>';
				$y++;
			//var_dump($y);
			//}
		}
		echo '</tr><tr id="ConnIconsRow" >';
		for ($j3=1; $j3<=$numberOfMutualFreinds; $j3++){
			//$isviewable = checkviewable($data[$z]);
			//if($isviewable != "0"){
			//$query3 = 'SELECT * FROM source_import WHERE userId = "'.$data[$z].'"';
			//$result3 = $mysqli->query($query3);
			//$data3 = $result3->fetch_array();
			
			echo '<td class="ConnIcon" >';
			//var_dump($data[$z]);
			$fid = $data[$z];
			sourceicons($fid);
			echo '</td>';
			$z++;
			//var_dump($z);
			//}
		}
		echo '</tr>';
		
	}
	
	echo'</table>'; // end of table
}

?>