<?php
echo '<center><ul id="friends_cat">';
require_once('../../config/config.php');
$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);




$result = $mysqli->query('SELECT * FROM user_friend_cat WHERE userId="'.$_SESSION["userId"].'" AND friendId =  "'.$_SESSION["lfid"].'" ') or die ($mysqli->error);

while($data = $result->fetch_array()){
    $query = 'SELECT * FROM user_categories WHERE catId = "'.$data["catId"].'"';
    $resultr = $mysqli->query($query);
    $row = $resultr->fetch_array();
    echo '<li >'.$row["catName"].'</li>';
    

}
echo "</ul></center>";

?>