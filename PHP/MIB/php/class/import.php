<?php
session_start();
    require_once('../../config/config.php');
	require_once('friend.class.php');
	require_once('fb.class.php');
	require_once('li.class.php');
	require_once('import.class.php');
	require_once('default.class.php');
	require_once('sanitize.class.php');
	require_once('user.class.php');
	require_once('verify.class.php');
	require_once('urlCreator.class.php');
	require_once('login.class.php');
	require_once('detail.class.php');
	require_once("../../lib/facebooksdk/src/facebook.php");
	
if($_GET['type'] == 'facebook'){
    $fb = new fb;
    $import = new import;
    if(!isset($_SESSION['facebook_id'])){
         $_SESSION['importFb'] = true;
     
    }
    else {
    $import->facebook($_SESSION['userId']);
    }
}
if($_GET['type'] == 'linkedin'){
    $li = new li;
    $import = new import;
    if(!isset($_SESSION['linkedin_id'])){
        $_SESSION['importLi'] = true;
        $li->login();
    }
    else {
    $import->linkedin($_SESSION['userId']);
    $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
    //$result = $mysqli->query('SELECT * FROM file_system') or die ($mysqli->error);
    //$data = $result->fetch_array();
    
    /*if($data['locked'] == 'false'){
    $mysqli->query('UPDATE file_system SET locked = "true"');
    $url = $data["link"];
    
    //open connection
    $ch = curl_init();
    
    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    //execute post
    $result = curl_exec($ch);
    
    //close connection
    curl_close($ch);
    $mysqli->query('UPDATE file_system SET locked = "false"');
    //var_dump($result);

}*/}}
    


?>