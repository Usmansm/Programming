<?php
session_start();
include('../config/config.php');
$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
$query = 'SELECT * FROM user_friend_detail WHERE userId = "'.$_SESSION["userId"].'" AND ViewableRow != "0"';
$results = mysqli_query($mysql, $query);
$liFriends = 0;
$fbFriends = 0;
$nmrFriends = 0;


while($row = mysqli_fetch_row($results)){
	$nmrFriends++;
}

function count_friends($catid){
  global $mysql;
  $numq = "SELECT * FROM user_friend_cat WHERE catId='". $catid ."'";
  $res = $mysql->query($numq);
  $nums = $res->num_rows;
  return $nums;
}

function display_social_cats(){
  global $mysql;
  $fbcount = 0;
  $licount = 0;
  $fbq = "SELECT * FROM userfrnd_source WHERE userId='". $_SESSION["userId"] ."'";
  $res = $mysql->query($fbq);
  while($row = $res->fetch_assoc()){
    $newq = "SELECT * FROM source_import WHERE sourceId='". $row["source_import_Id"] ."'";
    $newres = $mysql->query($newq);
    $row = $newres->fetch_assoc();
    if($row["sourceName"] == "facebook"){
     $fbcount++;
    }
    elseif($row["sourceName"] == "linkedin"){
      $licount++;
    }
  }
  if($fbcount > 0){
  echo  "<div class='cc' ><span class='cat' >Facebook</span> - <span class='numfr' >". $fbcount ." friends</span></div>";
  }
  if($licount > 0){
  echo  "<div class='cc' ><span class='cat' >Linkedin</span> - <span class='numfr' >". $licount ." friends</span></div>";
  }
}

echo '<div class="cc"> <span class="cat" onClick="raw_friend_reload()">All friends</span> - <span class="numfr" >'.$nmrFriends.' friends</span></div>';

/*
$query = "SELECT * FROM  user_categories  WHERE userId = '".$_SESSION['userId']."'";
$results = mysqli_query($conn, $query);
while($row = mysqli_fetch_row($results)){
	//$friends = explode(',', $row[4]);
	//$nmrFriends = count($friends);
    if($row[5] == 1){
        echo '<div class="cc"> <span class="cat"  onClick="getCat(\"'.$row[0].'\")">'.$row[2].'</span> - <span class="numfr" >'.$nmrFriends.' friends</span></div>';
    }
    else {
	echo '<div class="cc" id="'.$row[0].'2"><img src="images/+.png" onClick="showCat('.$row[0].')"/> <span class="cat"  onClick="getCat('.$row[0].')">'.$row[2].'</span> - <span class="numfr" >'.$nmrFriends.' friends</span><br />
			  <span class="cat_options" ><span onClick="promptEditCat('.$row[0].')">Edit</span>  <span onClick="promptCloneCat('.$row[0].')">Clone</span>  <span onClick="promptDeleteCat('.$row[0].')">Delete</span> </span>
		  </div>';
    }
}
*/
display_social_cats();
$que = "SELECT * FROM user_categories WHERE userId='". $_SESSION["userId"] ."'";
$res = $mysql->query($que);
while($row = $res->fetch_assoc()){
  // oops echo "<div class='cc'> <span class='cat'  onClick='getCat(\"".$row["catId"]."\")'>". $row["catName"] ."</span> - <span class='numfr' >".$nmrFriends." friends</span></div>";
  $radum = rand(1,400000);
  echo "<div class='cc' id='". $row["catId"] ."2' ><img onclick=\"fdcollapse('". $radum ."_img','". $radum ."')\" src='images/+.png' id='". $radum ."_img' /> <span class='cat' onclick='getCat(\"". $row["catId"] ."\")' >". $row["catName"] ."</span> - <span class='numfr' >". count_friends($row["catId"]) ." friends</span></div>";
  echo "<span class='cat_options' style='display: none;' id='". $radum ."' ><span onclick='promptEditCat(\"". $row["catId"] ."\")' style='cursor: pointer;' >Edit</span> <span onclick='promptCloneCar(\"". $row["catId"] ."\")' style='cursor: pointer;' >Clone</span> <span onclick='promptDeleteCat(\"". $row["catId"] ."\")' style='cursor: pointer;' >Delete</span></span>";
}
?>
<center>
	<input type="button" class="cat_button" value="Add Category" onClick="promptAddCat()" /><div class="cat_right" ></div><br />
    <input type="button" class="cat_button" value="Delete From Category" onClick="promptDeleteFromCat()" /><div class="cat_right" ></div><br />
	<input type="button" class="cat_button" value="Add Selected To Category" onClick="promptAddToCat()" /><div class="cat_right" ></div>
</center>