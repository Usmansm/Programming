<?php
session_start();
require_once('../../config/config.php');
require_once('friend.class.php');
require_once('fb.class.php');
require_once('li.class.php');
require_once('import.class.php');
//require_once('default.class.php');
//require_once('sanitize.class.php');
require_once('user.class.php');
require_once('verify.class.php');
require_once('urlCreator.class.php');
//require_once('login.class.php');
//require_once('detail.class.php');
require_once("../../lib/facebooksdk/src/facebook.php");
//require_once('cookie.class.php');
if(isset($_POST['check']) && $_POST['check'] == true){
    echo executeCheck();    
}
if(isset($_GET['import'])){
    $source = $_GET['import'];
    $import = new import;
    if($source == 'facebook'){
        if(isset($_SESSION['facebook_id'])){
            $import->facebook($_SESSION['userId']);
            unset($_SESSION['facebook_id']);
            echo 'true';
        }
        else if (isset($_SESSION['multiplefb']))
        {
        unset($_SESSION['multiplefb']);
        echo 'multiple';
        }
        else {
            echo $config["root"].'/index.php?fbLogin=true';
        }
    }
    if($source == 'linkedin'){
        //Code to check token{
            $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
        $token_query = "SELECT * FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."' AND authProvider='linkedin'";
        
        $token_res = $mysqli->query($token_query);
        $tokennum = $token_res->num_rows;
        if($tokennum > 0){
        $token = $token_res->fetch_assoc();
        $token_created_unix = strtotime($token["createdOn"]);
        $current_unix = time();
         if(strtotime($token["createdOn"])<strtotime('-60 days')){
            
            $mysqli->query("DELETE FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."' AND authProvider='linkedin'");
            die($config["root"].'index.php?liLogin=true');
            //unset($_SESSION['linkedin_id']);
         }}
       //   } 
    // echo ' 9';  
        if(isset($_SESSION['linkedin_id'])){
       //  echo ' 8'; 
                $import->linkedin($_SESSION['userId']);
                $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
                //$result = $mysqli->query('SELECT * FROM file_system') or die ($mysqli->error);
                //$data = $result->fetch_array();
                
                /*if($data['locked'] == 'false'){
                $mysqli->query('UPDATE file_system SET locked = "true"');
                $url = $data["link"];
                
                //open connection``````````````````````````````````````````````````````````````````````````````````````````````
                $ch = curl_init();
                
                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, $url);
                //execute post
                $result = curl_exec($ch);
                
                //close connection
                curl_close($ch);
                $mysqli->query('UPDATE file_system SET locked = "false"');
                }*/
                //var_dump($result);
                unset($_SESSION['linkedin_id']);
                echo 'true';
        }
        else if (isset($_SESSION['multipleli']))
        {
        unset($_SESSION['multipleli']);
        echo 'multiple';
        }
        else {
            echo $config["root"].'index.php?liLogin=true';
        }
    }
}
function executeCheck(){
    $logout = new logout;
    $notification = array();
    if($_SESSION['logged_in'] != true){
        $logout->logout();
        $url = new urlCreator;
        $logoutUrl = $url->createUrl('logout', false);
        $notification = array('notification', 'ok', 'Your session has expired.<br> Please log back in.', 'redirect', $logoutUrl);
        return $notification;
    }
    if($_SESSION['notification'] == true){
        $type = $_SESSION['notification_type'];
        $msg = $_SESSION['notification_msg'];
        if(isset($_SESSION['notification_action']) && !empty($_SESSION['notification_action'])){
            $action = $_SESSION['notification_action'];
            unset($_SESSION['notification_action']);
        }
        else {
            $action = false;
        }
        if(isset($_SESSION['notification_redirect_url']) && !empty($_SESSION['notification_redirect_url'])){
            $url = $_SESSION['notification_redirect_url'];
            unset($_SESSION['notification_redirect_url']);
        }
        else {
            $url = false;
        }
        $notification = array('notification', $type, $msg, $action, $url);
        $_SESSION['notification'] = false;
        return $notification;
    }
}

?>