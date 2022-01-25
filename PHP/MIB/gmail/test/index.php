<?php
session_start();
$_SESSION["return_url"] = "http://ec2-54-243-154-131.compute-1.amazonaws.com/MIBWORKING/dev/gmail/test/";
//Include the config/php sdk files 
require_once "../../config/config.php";
require_once "../../google-api-php-client/src/Google_Client.php";
include "../../contacts/contacts.class.php";
echo <<<STYLE
<style>
body{
    padding: 0px;
}
.chold{
    background: #e2e2e2; /* Old browsers */
background: -moz-linear-gradient(top,  #e2e2e2 0%, #ffffff 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#e2e2e2), color-stop(100%,#ffffff)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #e2e2e2 0%,#ffffff 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #e2e2e2 0%,#ffffff 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #e2e2e2 0%,#ffffff 100%); /* IE10+ */
background: linear-gradient(to bottom,  #e2e2e2 0%,#ffffff 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e2e2e2', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */

}
.contact_box{
    padding: 4px;
    background: #E3E3E3;
    border-bottom: 3px solid #BABABA;
    margin: 10px;
}
.contact_box:hover{
    background: #E3E3E3;
    border-left: 2px solid #D4D4D4;
    cursor: pointer; 
}
</style>
<script>
function togel(elid){
    cdis = document.getElementById(elid).style.display
    if(cdis == "none"){
        document.getElementById(elid).style.display="inline"
    }
    else{
        document.getElementById(elid).style.display="none"
    }
}
</script>
STYLE;
echo "<div style='position: fixed; top: 0px;left: 0px; background: white; font-size: 18pt;width: 100%;box-shadow: 0px 2px 5px gray;' > <a href='". $_SESSION["return_url"] ."?refresh=true' >Refresh token</a> | <a href='#top' >Top</a> | <a href='". $config["root"] ."gmail/test/?disconnect=true' >Disconnect </a> | <span style='font-size: 12pt;' >". $_SESSION["gmail_token"] ."</span></div>";
 $gclient = new Google_Client();
 $gclient -> setApplicationName("Myiceberg");
 $gclient -> setClientId($config["gmail"]["app_id"]);
 $gclient -> setClientSecret($config["gmail"]["secret"]);
 $gclient -> setRedirectUri($config["gmail"]["redirect_uri"]);
 $gclient -> setDeveloperKey($config["gmail"]["dev_key"]);
 $gclient -> setScopes("http://www.google.com/m8/feeds/");
$base = new mib_contacts;
echo "<br /><br />";
echo "Now checking your token... ";
$hastoken = $base->gmail_check_internal_token();
if($hastoken == true){
    echo "<font color=green>OK</font>";
    echo "<br /><br />Getting your contacts... <br />Gmail Contacts <span onclick=\"togel('gcont')\" style='cursor: pointer;color: blue;text-decoration: underline;'>Hide/Show</span>";
    $contacts = $base->gmail_get_all_sorted();
    echo "<div id='gcont' style='display: inline;' class='chold' >";
    $base->gmail_display_contacts($contacts);
    echo "</div>";
}
else{
    echo "<font color=red>No token found</font>";
    if(! isset($_GET["callback"])){
    echo "<br /></br><a href='". $_SESSION["return_url"] ."?requestauth=true' >Click here to get a token</a>";
    header("Location: ". $_SESSION["return_url"] ."?requestauth=true");
    }
    if(isset($_GET["requestauth"])){
        $authurl = $base->gmail_request_oauth();
        echo "<br /><br /><a href='". $authurl ."' >". $authurl ."</a>";
        header("Location: ".$authurl);
    }
    if(isset($_GET["callback"])){
        echo "<br /><br />Got a callback code: ".$_GET["code"]."<br />";
        echo "Sending this code to google...";
        $base->gmail_process_code();
        echo "<br /><br />The code been process. <a href='". $_SESSION["return_url"] ."' >Click here to continue</a>";
        header("Location: ".$_SESSION["return_url"]);
    }
}

mysqli_close($_SESSION["mysql"]);
?>