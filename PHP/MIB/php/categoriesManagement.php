<?php
session_start();
include('../config/config.php');
/*DENI: THIS IS PROBABLY A CODE FOR ADDING FREINDS TO CATEGORY AND I THINK THAT THERE IS 1 MORE PART SOMEWHERE ELSE IN THIS FILE
	PLEASE SAM/COREY WHEN YOU COME HERE AND SEE THIS COMMENT TRY TO WRITE SHOR EXPANIATION IN START OF EACT IF STATEMENT FOR WHAT THIS IF STATEMENT IS FOR ..
 * COREY: i havent worked with this file so i cant write any explainations but i will write them in my future if statements i create
*/
if($_POST['type'] == 'add'){
	$friends = $_POST['friends'];
	$category = $_POST['cat'];
	$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
  $fplodes = explode(",",$friends);
  foreach($fplodes as $fid){
    $query = "SELECT * FROM user_friend_cat WHERE friendId='". $fid ."' AND catId='". $category ."'";
    $res = $mysql->query($query);
    $dat = $res->fetch_assoc();
    if($dat["createdOn"] != ""){

		mysqli_query($mysqli,"SELECT * FROM user_friend_cat WHERE (catId = '". $category ."', AND  userId = '". $_SESSION["userId"] ."' AND friendId  =  '". $fid ."')");
		$Check = mysqli_affected_rows($mysqli);
		
		if ($Check == 0 ){
			 $newq = "INSERT INTO user_friend_cat(catId, userId, friendId) VALUES('". $category ."', '". $_SESSION["userId"] ."', '". $fid ."')";
			 $newres = $mysql->query($newq);
			 $_SESSION["kraken"] = "bbbbbbb";
		}
    }else{
    
    }
  }
	//$query = "SELECT 'catFriends' FROM user_categories WHERE catId = '".$category."'";
	//$results = mysqli_query($conn, $query);
	//$data = mysqli_fetch_row($results);
	//$friendsFinished = $data;
	/* foreach($friends as $friend){
		if(!in_array($friend, $data)){
			array_push($friend, $friendsFinished);
		}
	} */
	//$friendsReady = implode(',', $friendsFinished);
	//$query = "UPDATE user_categories SET catFriends = '".$friendsReady."' WHERE catId = '".$category."'";
	//var_dump($_POST['friends']);
	return true;	
}

if($_POST['type'] == 'remove'){
	$friends = explode(',', $_POST['friends']);
	$category = $_POST['cat'];
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
 foreach($friends as $friend){
	$query = "DELETE FROM user_friend_cat WHERE catId = '".$category."' AND friendId = '".$friend."'";
	$results = mysqli_query($conn, $query);
 }
	return true;	
}

/*DENI: THIS IS PROBABLY A CODE FOR ADDING FREINDS TO CATEGORY AND I THINK THAT THERE IS 1 MORE PART SOMEWHERE ELSE IN THIS FILE
	PLEASE SAM/COREY WHEN YOU COME HERE AND SEE THIS COMMENT TRY TO WRITE SHOR EXPANIATION IN START OF EACT IF STATEMENT FOR WHAT THIS IF STATEMENT IS FOR ..
*/
if($_POST['type'] == 'addCat'){
	$friends = $_POST['friends'];
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "INSERT INTO user_categories (userId, catName, catDescription) VALUES ('".$_SESSION['userId']."', '".$_POST['catTitle']."', 
				'".$_POST['catDesc']."')";
  mysqli_query($conn, $query);
  $catId = mysqli_insert_id($conn);
  foreach($friends as $friend){
		mysqli_query($mysqli,"SELECT * FROM user_friend_cat WHERE (catId = '". $catId ."', AND  userId = '". $_SESSION["userId"] ."' AND friendId  =  '". $friend ."')");
		$Check = mysqli_affected_rows($mysqli);
		
		if ($Check == 0 ){
			$query = 'INSERT INTO user_friend_cat (catId, userId, friendId) VALUES ("'.$catId.'", "'.$_SESSION["userId"].'", "'.$friend.'")';
			mysqli_query($conn, $query);
		}
  }
}

if($_POST['type'] == 'removeCat'){
  $mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
  $cat = $_POST["cat"];
  $query = "DELETE FROM user_categories WHERE catId='". $cat ."' AND userId='". $_SESSION["userId"] ."'";
  $res = $mysql->query($query);
  $query = "DELETE FROM user_friend_cat WHERE userId='". $_SESSION["userId"] ."' AND catId='". $cat ."'";
  $res = $mysql->query($query);
/*
	$cat = $_POST['cat'];
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM user_categories WHERE catId = '".$cat."'";
	$results = mysqli_query($conn, $query);
	$row = mysqli_fetch_row($results);
	$query = "DELETE FROM user_categories WHERE catId = '".$cat."'";
	mysqli_query($conn, $query);
	$friends = explode(',', $row[4]);
	foreach($friends as $friend){
		if($friend == ''){
		}
		else {
	$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' AND friendId = '".$friend."'";
			$results = mysqli_query($conn, $query) or die(mysqli_error($conn));
			$data = mysqli_fetch_assoc($results);
			$catsOld = explode(',', $data['FriendCategory']);
			//print_r($catsOld);
			echo $cat;
			echo '<br>';
			$key = array_search($cat, $catsOld);
			echo $key.'<br>';
			unset($catsOld[$key]);
			$cats = implode(',', $catsOld);
			
						
			echo $cats;
			$query = "UPDATE  user_friend_detail SET FriendCategory = '".$cats."' WHERE id = '".$data['id']."'";
			mysqli_query($conn, $query) or die(mysqli_error($conn));
		}
	}
 */
}

if($_POST['type'] == 'catList'){
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM user_categories WHERE userId = '".$_SESSION['userId']."' ORDER BY catName ASC";
	$result = mysqli_query($conn, $query);
	echo '<ul>';
	while($row = mysqli_fetch_row($result)){
	    
		$rquery = "SELECT * FROM user_friend_cat WHERE catId = '".$row[0]."'";
    $result2 = mysqli_query($conn, $rquery);
    $friendsCount = mysqli_num_rows($result2);
		if($friendsCount <= 1){
			$friendsCount = $friendsCount.' friend';
		}
		else {
			$friendsCount = $friendsCount.' friends';
		}
		echo '<div class="modalCat" id="'.$row[0].'"> <li><input type="checkbox" value="'.$row[0].'"/><span class="modalCatTitle">'.$row[2].' </span> - <span class="modalCatFriends"> '.$friendsCount.'</span></li> </div>';	
	}
	echo '</ul>'; 
}
/*DENI: THIS IS PROBABLY A CODE FOR ADDING FREINDS TO CATEGORY AND I THINK THAT THERE IS 1 MORE PART SOMEWHERE ELSE IN THIS FILE
	PLEASE SAM/COREY WHEN YOU COME HERE AND SEE THIS COMMENT TRY TO WRITE SHOR EXPANIATION IN START OF EACT IF STATEMENT FOR WHAT THIS IF STATEMENT IS FOR ..
*/
if($_POST['type'] == 'addToCat'){
$friends = $_POST['friends'];
	$category = $_POST['catId'];
 $_SESSION["coreydebug"] = "Catid: ".$category. " friends: ".$friends;
 //echo "Friends: ".$friends."  ,, Cat:".$category; // this was checking line for debunging (Deni)
	$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
  $fplodes = explode(",",$friends);
  foreach($friends as $fid){
  //echo $fid; 
    $query = "SELECT * FROM user_friend_cat WHERE friendId='". $fid ."' AND catId='". $category ."'";
    $res = $mysql->query($query);
    $dat = $res->fetch_assoc();
    if($dat["createdOn"] == ""){
	
		mysqli_query($mysqli,"SELECT * FROM user_friend_cat WHERE (catId = '". $catId ."', AND  userId = '". $_SESSION["userId"] ."' AND friendId  =  '". $friend ."')");
		$Check = mysqli_affected_rows($mysqli);
		
		if ($Check == 0 ){
			$newq = "INSERT INTO user_friend_cat(catId, userId, friendId) VALUES('". $category ."', '". $_SESSION["userId"] ."', '". $fid ."')";
			$newres = $mysql->query($newq);
		}
    }else{
    
    }
  }

/*
	$friends = $_POST['friends'];
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM user_categories WHERE catId = '".$_POST['catId']."'";
	$result = mysqli_query($conn, $query);
	$data = mysqli_fetch_row($result);
	$friendsOld = explode(',', $data[4]);
	foreach($friends as $friend){
		if(!in_array($friend, $friendsOld)){
			$friendsOld[] = $friend;
			$query = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' AND friendId = '".$friend."'";
			$results = mysqli_query($conn, $query);
			$data = mysqli_fetch_assoc($results);
			//print_r($data);
			$catsRaw = array();
			if(!empty($data['FriendCategory'])){
			$catsRaw = explode(',', $data['FriendCategory']);
			//print_r($catsRaw);
			$catsRaw[] = $_POST['catId'];
			//print_r($catsRaw);
			$cats = implode(',', $catsRaw);
			}
			else {
			$catsRaw[] = $_POST['catId'];
			$cats = implode(',', $catsRaw);
			}			
			echo $cats;
			$query = "UPDATE  user_friend_detail SET FriendCategory = '".$cats."' WHERE id = '".$data['id']."'";
			mysqli_query($conn, $query) or die(mysqli_error($conn));;
		}
	}
	$friendsReady = implode(',', $friendsOld);
	$query = "UPDATE  user_categories SET catFriends = '".$friendsReady."' WHERE catId = '".$_POST['catId']."'";
	mysqli_query($conn, $query);
	echo 'succes';
 */
}

if($_POST['type'] == 'catEditData'){
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM user_categories WHERE catId = '".$_POST['catId']."'";
	$result = mysqli_query($conn, $query);
	$data = mysqli_fetch_row($result);
	echo '<table border="0">            <tr>         	<form>            <td><label for="catTitle">Title:</label></td>            <td><input id="catTitle" type="text" 
	value="'.$data[2].'" name="catTitle"/></td></tr><tr>            <td style="vertical-align:top;">Description:</td>            <td><textarea cols="30" id="catDesc" rows="8" name="catDesc"> '.$data[3].' </textarea></td>            </form>            </tr>            </table>';
	
}

if($_POST['type'] == 'editCat'){
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "UPDATE  user_categories SET catDescription = '".$_POST['catDesc']."', catName = '".$_POST['catTitle']."' WHERE catId = '".$_POST['catId']."'";
	echo $query;
	mysqli_query($conn, $query) or die(mysqli_error($conn));
}

if($_POST['type'] == 'cloneCat'){
    echo "cloned";
    echo "<br />catid to clone: ".$_POST["catId"];
    $mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);	$query = "SELECT * FROM user_categories WHERE catId = '".$_POST['catId']."'";
	$query = "SELECT * FROM user_categories WHERE userId='". $_SESSION["userId"] ."' AND catId='". $_POST["catId"] ."'";
    $res = $mysql->query($query);
    $data = $res->fetch_assoc();
    $query = "INSERT INTO user_categories(userId,catName,catDescription) VALUES('". $data["userId"] ."','". $_POST["catTitle"] ."','". $data["catDescription"] ."')";
    $res = $mysql->query($query);
    $li = $mysql->insert_id;
    echo "<br />last insert: ".$li;
    $query = "SELECT * FROM user_friend_cat WHERE userId='". $_SESSION["userId"] ."' AND catId='". $_POST["catId"] ."'";
    $res = $mysql->query($query);
    while($rdata = $res->fetch_assoc()){
        $inq = "INSERT INTO user_friend_cat(catId,userId,friendId) VALUES('". $li ."','". $_SESSION["userId"] ."','". $rdata["friendId"] ."')";
        echo "<br />insert query: ".$inq;
        $ires = $mysql->query($inq);
    }
}

if($_POST['type'] == 'catList2'){
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM user_categories WHERE userId = '".$_SESSION['userId']."'ORDER BY catName ASC";
	$result = mysqli_query($conn, $query);
	echo '<ul>';
	while($row = mysqli_fetch_row($result)){
		$rquery = "SELECT * FROM user_friend_cat WHERE catId = '".$row["catId"]."'";
    $result2 = mysqli_query($conn, $rquery);
    $friendsCount = mysqli_num_rows($result2);
    /*
		if($friendsCount <= 1){
			$friendsCount = $friendsCount.' friend';
		}
		else {
			$friendsCount = $friendsCount.' friends';
		}*/
		echo '<div class="modalCat" id="'.$row[0].'3"> <li><span class="modalCatTitle"><input name="delete" type="checkbox" value="'.$row[0].'"/>   '.$row[2].' </span> - <span class="modalCatFriends"> '.$friendsCount.' here?</span></li> </div>';	
	}
	echo '</ul>'; 
}
if(isset($_GET['firendsId'])){

foreach($catId as $category){
	//echo 'initiated';
	$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
	//echo 'initiated';
	//var_dump($friends);
	if($catId != '' && $friendsIDs != ''){
		$friendsIDs = explode(',', $friendsIDs);
		$CadIds = explode(',', $catId);
		
		foreach($friendsIDs as $friend){
		foreach($CadIds as $cat){
			$result = $mysqli->query('SELECT * FROM user_friend_cat WHERE catId = "'.$cat.'" AND userId = "'.$_SESSION["userId"].'" AND friendId = "'.$friend.'"') or die ($mysqli->error);
				if($result->num_rows > 0){
					echo "we are werwerwer";
					$result = $mysqli->query('DELETE FROM user_friend_cat WHERE catId = "'.$cat.'" AND userId = "'.$_SESSION["userId"].'" AND friendId = "'.$friend.'"') or die ($mysqli->error);
				}
		}
		}
	} 
}
  return true; 
}

/*
public function deleteFromCat ($catId, $friends){
  global $config;
  //$_SESSION['calling'] = true;
        $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
  if($catId != '' && $friends != ''){
   echo '2';
   $friends = explode(',', $friends);
   //$_SESSION['friends'] = $friends;
    //var_dump($cat);
   foreach($friends as $friend){
    
    $result = $mysqli->query('SELECT * FROM user_friend_cat WHERE catId = "'.$catId.'" AND userId = "'.$_SESSION["userId
"].'" AND friendId = "'.$friend.'"') or die ($mysqli->error);
    echo'SELECT * FROM user_friend_cat WHERE catId = "'.$catId.'" AND userId = "'.$_SESSION["userId"].'" AND friendId = 
"'.$friend.'"';
    
    
    if($result->num_rows > 0){
     $result = $mysqli->query('DELETE FROM user_friend_cat WHERE catId = "'.$catId.'" AND userId = "'.$_SESSION["userId"
].'" AND friendId = "'.$friend.'"') or die ($mysqli->error);
    }
   }
     
  }
 }
*/

/*
	$friends = $_POST['friends'];
	//print_r($_POST);
	$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
	$query = "SELECT * FROM user_categories WHERE catId = '".$_POST['catId']."'";
	$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
	//echo mysqli_num_rows($result);
	$data = mysqli_fetch_row($result);
	//print_r($data);
	$friendsOld = explode(',', $data[4]);
	//print_r($friendsOld);
	foreach($friends as $friend){
		if(in_array($friend, $friendsOld)){
			$key = array_search($friend, $friendsOld);
			//echo $key.'<br>';
			unset($friendsOld[$key]);	
		}
		$query = "SELECT * FROM user_friend_detail WHERE userId =  '".$_SESSION['userId']."' AND friendId = '".$friend."'";
		$result = mysqli_query($conn, $query);
		$data = mysqli_fetch_row($result);
		//print_r($data);
		$friendCats = explode(',', $data[34]);
		$friendCatsDone = explode(',', $data[34]);
		$key = 0;
		$catId = $_POST['catId'];
		foreach($friendCats as $friendCat){
			if($friendCat == $catId){
				echo 'yup <br>';
				echo $friendCatsDone[$key];
				unset($friendCatsDone[$key]);
				
			}
			$key++;
		}
		//print_r($friendCatsDone);
		$friendCatsDone2 = implode(',', $friendCatsDone); 
		$query = "UPDATE  user_friend_detail SET FriendCategory = '".$friendCatsDone2."' WHERE id = '".$data[0]."'";
		mysqli_query($conn, $query);
	}
	//print_r($friendsReady);
	$friendsReady = implode(',', $friendsOld);
	$query = "UPDATE  user_categories SET catFriends = '".$friendsReady."' WHERE catId = '".$_POST['catId']."'";
	mysqli_query($conn, $query);
	echo 'succes';
 */


?>
