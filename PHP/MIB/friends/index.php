<?php
error_reporting(0);
include('../config/config.php');
//TC IS GONE
//date commit test this is why this coment exist
session_start();
	
    
    $_SESSION['friendNmr'] = 0;
    if(!isset($_SESSION['userId'])){
    	echo '<script type="text/javascript"> window.location = "'.$config['root'].'" </script>';
		exit;
	}

	$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
	if(mysqli_connect_errno()) {
		die("Connect failed: \n".mysqli_connect_error());
	}
	$query = "SELECT * FROM user_detail_public WHERE userId = '".$_SESSION['userId']."'";
	$result = $mysql->query($query);
	$userdata = $result->fetch_assoc();
	$firstname = $userdata["firstName"];
	$lastname = $userdata["lastName"];
	$lastnametrunc = substr($lastname, 0, 1);
	$query2 =  "SELECT * FROM source_import WHERE userId = '".$userdata['friendId']."'";
	$res2 = $mysql->query($query2);
	$raw = $res2->fetch_assoc();
	//$profilepicurl = "https://graph.facebook.com/". $raw['sourceUid'] ."/picture?type=large";
     
    

    
    function getAvatarLink(){
        global $config;
            $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
            $result = $mysqli->query('SELECT * FROM source_import WHERE userId = "'.$_SESSION["userId"].'"');
            
            ;
            $sources = array();
             while($row = $result->fetch_array()){
                if($row['sourceProfilePicture'] == '' && $row['sourceName'] == 'facebook'){
                   
                    $link = 'https://graph.facebook.com/'.$row["sourceUid"].'/picture?type=large';
                   
                }
                else {
                    $link = $row['sourceProfilePicture'];
                }
                $sources[$row["sourceName"]] = $link;    
            }
            $sourcesPriority = array('facebook', 'linkedin', 'salesforce');
            $done =  false;
            foreach($sourcesPriority as $sourcePriority){
                if(!$done){
                    if(isset($sources[$sourcePriority])){
                        echo  $sources[$sourcePriority];
                        $done = true;
                }
            }
        }
    }
$_SESSION["incatusers"] = array();
$UserId = $_SESSION["userId"];
	 $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
	$FirstTime = $mysqli->query("SELECT * FROM user_monitor_terms WHERE userId = '".$UserId."'");
    $FirstTime = $mysqli->affected_rows;
    $i= 1;
    $test = '';
	
	if ($FirstTime == 0){
		$Term = array ("Birth","Anniversary","Engage","Grad","Congrat","Mov","Sorry","Job","College","Wedding","Help","Find","Look","Need","Assist","Add New Term","Add New Term","Add New Term","Add New Term","Add New Term");
        foreach ( $Term as $value ){  
           
           $mysqli->query('INSERT INTO user_monitor_terms (userId,termId,termName,termActive,termType) VALUES ("'.$UserId.'","'.$i.'","'.$value.'","1","default")');
           $i = $i + 1 ;
        }
    }  
    $query = "SELECT * FROM user_monitor_terms WHERE userId ='".$UserId."'";  
    $result = $mysqli->query($query);
    
    $TermNames = array();
    while ($row = $result->fetch_assoc()) {
        $TermNames[] =  $row['termName'];
	}



?>

<!DOCTYPE html>
	<head>
		<!-- Page Title -->
    <title>Myiceberg</title>
    
    <!-- External CSS Stylesheets -->
		<link rel="stylesheet" href="../Users/css/News.css" media="screen" type="text/css"  />
		<link rel="stylesheet" href="../css/main.css" media="screen" type="text/css" />
		<link rel="stylesheet" href="../css/sam.css" media="screen" type="text/css" />
    <!--<link rel="stylesheet" href="jquery.datepick.css" media="screen" type="text/css" />-->
    <!--<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />-->
    
    <!-- External JS/jQuery Scripts -->
    <script src="config.js" type="text/javascript" ></script>
    <script src="../Users/news.js" type="text/javascript" ></script>
 	<script src="../UserSettings/UserSettings.js" type="text/javascript" ></script>
    <script src="friend_detail_ext.js" type="text/javascript" ></script>
	<script src="example.js" type="text/javascript" ></script>
    <script src="sam.js" type="text/javascript" ></script>
    <script src="categories.js" type="text/javascript" ></script>
    <script src="Deni.js" type="text/javascript" ></script>
    
 	<script src="../js/jquery-1.9.1.min.js"></script>
	<script src="../js/jquery-ui-1.10.2.custom.min.js"></script>
	
  	<script src="../js/jquery-1.9.1.js"></script>
	  <script src="../js/jquery-ui.js"></script>

        <?php
          if($_SESSION["lerrc"] == 1){
          	echo "<script>promptNewUser();</script>";
          }
        ?>
	 <!--Usman Code is thi-->
 <link rel="stylesheet" href="../css/jquery-ui.css" />

	
        
        <script>
      		$(function() {
      		 $('#modal').draggable({ handle: "p" });
			 
			 $("#loading").dialog({
 position: { my: "left top", at: "left bottom", of: window } ,
    hide: 'slide',
	show: 'slide',
	autoOpen: false
});
//$("#loading").dialog('open').html("<p>Please Wait... <img src='../img/loader.gif'/></p>");
//$( "#loading" ).dialog( "option", "hide", "explode" );		 
			 
			 	$('body').on("click","a[class=anchorMail]", function(e) {
				//alert($(this).attr('guid'));
				var fidc=$("#hdnFid").val();
				//var guidc=$("#hdnEvnNoteGuid").val();
				var guidc=$(this).attr('guid');
						///alert("$fid : " + $("#hdnFid").val() + " - $evnNoteGuid : " + $("#hdnEvnNoteGuid").val());
						$.ajax({
							  type: 'POST',
							  url: 'addEmailtoClient.php',
							 data: { 'fidc' : fidc, 'guidc' : guidc},
							  complete:function(data)
							  {
								setTimeout(function(){location.reload(true);},10);
							  },
								async: false
							}).fail(function(xhr, status, error){
     alert('error:' + status + ':' + error+':'+xhr.responseText)
	
}).always(function(){
   // setTimeout(function(){location.reload(true);},10);
	
});
						
					});	
			 
      		});
	  	</script>
		 <?php 
		if(isset($_GET['GettingStarted']) and ($_GET['GettingStarted'] == true)){
		 $HAveFreindsOrnot = "SELECT * FROM user_friend_detail WHERE userId = '".$_SESSION['userId']."' LIMIT 1";
		 $ressofFreinds = $mysqli->query($HAveFreindsOrnot);
		 if($ressofFreinds->num_rows == 0 ){
		 ?>
  <script>
  $(function() {
    promptGetingStarted();
  });
  </script>
  <?php } 
		}
?>
		
		
          <script>
  $(function() {
    $( "#accordion" ).accordion();
  });
  </script>
 <script type="text/javascript" src="scroll.js"></script>
</script>
      <script src="Jordy.js" type="text/javascript" ></script>
	</head>
	<body onLoad="<?php
  if($_GET["a"] == "evns"){
    echo "evns()";
  }
    if(isset($_GET["spId"])){
    echo "get_friend_detail('".$_GET["spId"]."');";
  }
  if($_GET["a"]=="f"){
  	echo "get_friend_detail('".$_GET["f"]."');";
  }
  if($_GET["csv"] == "true"){
  	echo "ImportingFromCSVDONE();";
  }
  ?>;position_verbubble();update_verbubble();" id="body" >
  <div id="debug_display" style="z-index: 9999; position: absolute; top: 0px; left: 0px;display: none;background: white; -moz-box-shadow: 0 0 5px #888; -webkit-box-shadow: 0 0 5px#888; box-shadow: 0 0 5px #888;" ><span id="debug_displayout" ></span><a href="#" onclick="hide_debug()" >-- Hide debug output --</a></div>
  <a name="top" ></a>

		<div id="context_menu" style="display: none;" oncontextmenu="return false;" ><div class="context_menusel" >Option</div><div class="context_menusel" >Option</div><div class="context_menusel" >Option</div><div class="context_menusel" onClick="hide_cmenu();raw_friend_reload();" >Refresh</div></div>
		<div id="searchfilter_box" style="display: none;" class="searchfilter_box" >Search filters<br /><br /><br /><br /><br /><br /><br /><br /><a href="#" onClick="change_fsbutton_class(2,true)" >Close</a></div>
		<div id="modtrans" style="display: none;" >
			<div id="fbimportmodhold" >
				<div id="fbimportmodtop" ></div>
				<div id="fbimportbody" class="modboxbody" style="display: none;" >
					<img src="images/loader.gif" /><br/>Please wait while we import your facebook friends. This may take a few moments...
				</div>
				<div id="delfriend" class="modboxbody" style="display: none;" >
					<span id="deltext" ></span><br />
					<input type="button" value="Yes" /> <input type="button" onClick="cancel_mod('delfriend')" value="Cancel" />
				</div>
				<div id="liimportbody" class="modboxbody" style="display: none;" >
					<img src="images/loader.gif" /><br/>Please wait while we import your linkedin friends. This may take a few moments...
				</div>
				<div id="fbimportmodbottom" ></div>
			</div>
		</div>



		<div class="verbubble" id="verbubble" style="display: none;position: absolute;" onClick="promptVerifyFriends()" ></div>
		<div id="headbar" onClick="change_fsbutton_class(2)" >
			<a class="a_noshow" href="<?php echo $config['root'];?>friends/" ><div id="logohold">
			<div id="logo" onClick="growcontent(true)" ></div>
			</div></a><a class="a_noshow" href="<?php echo $config['root'];?>friends/" >
				<span class="head_link" id="Friends_head_link" onClick="Head_meny_Back_Ground('Friends_head_link'); $('#pageNo').val(1);">Friends</span> </a>
				<span class="head_link" id="News_head_link" ><a class="a_noshow" href="#" style="color: white;" onClick="Load_News_Page(<?php echo $_SESSION['userId'] ?>); $('#pageNo').val(2); Head_meny_Back_Ground('News_head_link');" >News</a></span>
				<span class="head_link" onClick="PromptNonExistPageModal('Events feature coming soon', 'Events feature coming soon'); Head_meny_Back_Ground('Events_head_link')" id="Events_head_link">Events</span>
				<span class="head_link" id="Albums_head_link" onClick="PromptNonExistPageModal('Albums','Albums feature coming soon'); Head_meny_Back_Ground('Albums_head_link')">Albums</span>
			<div id="userhold" >
				<div id="usercont" >
					<div id="avatar" style="background: url('<?php getAvatarLink(); ?>');background-size: 100% 100%;" ></div>
						<div id="userinfo" >
							<span id="username"> <?php echo $firstname." ".$lastnametrunc."."; ?> 
								<select id="selectAccount" name="SelectPage" onChange="change(this.value)">
									<option value="select">select </option>
									<option value="SettingsProfile">Settings/Profile</option>
									<option value="Logout">Logout</option>
							
								</select>	
							</span>
						</div>
				</div>
			</div>
		</div>
		
		<div id="actionbar" >
        <input type="button" id="actionbarFirstItem" class="ac_button" onClick="promptImportFriends(<?php echo $_SESSION['userId'] ?>)" value="Import friends" />
        <input type="button" class="ac_button" id="verbutton" value="Verify friends" onClick="promptVerifyFriends()" />
        <input onClick="frienddelete()" type="button" class="ac_button" value="Delete" />
        <input onClick="checkbox_toggle(this)" type="checkbox" />
        <input type="text" id="friendsearchinput" placeholder="Search friends" class="friendsearchinput" />
        <input type="button" onFocus="change_fsbutton_class(1)" onClick="document.getElementById('friendsearch_button').focus()" class="friendsearch_button" id="friendsearch_button" value=" " />
      
    </div>
		<div id="apphold" >
			<div id="category_hold" >
            <?php 
            
            require "../php/class/categories.class.php";
            $categories = new categories;
            echo $categories->getAllCategories();
            
            ?>
			</div>
			<div id="alphalist" ><div class="alpha_options"  > </div>
			<div class="alphspan" id="b-z" name="0" >a</div>
			<div class="alphspan" id="c-z" name="a">b</div>
			<div class="alphspan" id="d-z" name="a-b">c</div>
			<div class="alphspan" id="e-z" name="a-c">d</div>
			<div class="alphspan" id="f-z" name="a-d">e</div>
			<div class="alphspan" id="g-z" name="a-e">f</div>
			<div class="alphspan" id="h-z" name="a-f">g</div>
			<div class="alphspan" id="i-z" name="a-g">h</div>
			<div class="alphspan" id="j-z" name="a-h">i</div>
			<div class="alphspan" id="k-z" name="a-i">j</div>
			<div class="alphspan" id="l-z" name="a-j">k</div>
			<div class="alphspan" id="m-z" name="a-k">l</div>
			<div class="alphspan" id="n-z" name="a-l">m</div>
			<div class="alphspan" id="o-z" name="a-m">n</div>
			<div class="alphspan" id="p-z" name="a-n">o</div>
			<div class="alphspan" id="q-z" name="a-o">p</div>
			<div class="alphspan" id="r-z" name="a-p">q</div>
			<div class="alphspan" id="s-z" name="a-q">r</div>
			<div class="alphspan" id="t-z" name="a-r">s</div>
			<div class="alphspan" id="u-z" name="a-s">t</div>
			<div class="alphspan" id="v-z" name="a-t">u</div>
			<div class="alphspan" id="w-z" name="a-u">v</div>
			<div class="alphspan" id="x-z" name="a-v">w</div>
			<div class="alphspan" id="y-z" name="a-w">x</div>
			<div class="alphspan" id="z" name="a-x">y</div>
			<div class="alphspan" id="0"  name="a-y">z</div>
			</div>
			<div id="friend_list_large_hold" >
   <?php  $import = $_SESSION['import'];?>
            <span id='alphaFriends'>
            </span>
            <div id="fl" ><?php

       
			//	require ("ex_inc/f_list_l.php");
				
				
    include '/ex_inc/f_list_l.php';
 
		
			?></div>
			</div>
       
		<img src="loading.gif" id="loadingImage" style="position:fixed; top:200px; left:500px; height:200px; display:none;" /> <!-- Loading Image-->
            <div id="UserSettingsPage" > </div>
	<input type="hidden" id="counter" name="0" /><!-- Counter For Scrolling -->
		<input type="hidden" id="counter2" value="1"  /><!-- Counter2 For Scrolling -->
		<input type="hidden" id="category" value=""  /><!-- Category -->
		<input type="hidden" id="alpha" value="0" /><!-- Refining as per alphabets -->
		<input type="hidden" id="pageNo" value="1" /><!-- Page No -->
		<input type="hidden" id="userDisplayId"  /><!-- User Display Id -->
		
		
		
		<div class="fd" id="fd" style="display:none"></div>
        
		<div id="overlay">
			<div id="modal">
				<p id="ModalTitleParagraph"><span id="modalTitleText"></span> <a id="saveButton"  href="javascript:void(0)" value="Save">Save</a> <a id="closeButton" href="javascript:close()">X</a></p>
				<center><div id="modalButtons"></div></center>
				<hr>
				<div id="modalError" class="friend_div"></div>
				<div id="modalText"></div>
			</div> 
		</div>
</div>

    <div id="footerFrendsPage">
    	<br>
        <img id="footerImage" src="../img/logos/mib.png">
	<div id="footerinfo">
		<div id="myicebergLink">Myiceberg.com</div>
		<div id="AboutUs"><a href="../footer_pages/About_Us.php">About Us</a></div> 
		<div id="AboutUs"><a href="../footer_pages/priceing.php">Pricing</a></div> 
		<div id="AboutUs"><a href="../footer_pages/FAQ.php">FAQ</a></div> 
		<div id="AboutUs"><a href="../footer_pages/Privacy_Policy.php">Privacy Policy</a></div> 
		<div id="AboutUs"><a href="<?php echo $config['root']; ?>footer_pages/TermsOfUse.php">Terms Of Use</a></div>
		
	</div>	
    	
    	<div id="RightsReserved">&copy; 2013 Myiceberg LLC. All Rights Reserved</div>
    </div>

<a id='init' class="cs_import" style="visibility:hidden;" >Add from Address Book</a>

<img src="loading.gif" id="loadingMails" style="display:none; position:fixed; left:00px; top:150px; height:200px; z-index:9999;"/>


<!-- This textarea will be populated with the contacts returned by CloudSponge -->
<script type="text/javascript" src="https://api.cloudsponge.com/address_books.js"></script>
<!-- Set your options, including the domain_key -->
<script type="text/javascript">

function populateTextarea(contacts, source, owner) {
$("#loadingMails").css('display','block');
$.ajax({
     url: "../php/class/cs.php",
    type: "POST",
    async: false,
    dataType: 'text',
    data:  { 'json': JSON.stringify(contacts) , 'source': source} ,
		
    success: function(response){
	$("#loadingMails").css('display','none');
       // alert("Import done successfully with "+contacts.length+ " contacts");
	   alert("Import done successfully");
		
		    }
}).fail(function(xhr, status, error){
     alert('error:' + status + ':' + error+':'+xhr.responseText)
	
}).always(function(){
   // location.reload();
});
//alert("hello");

}

var csPageOptions = {
 domain_key:"SJX5JD8ZYFCBFEJ68C4Y", 
  display_branding:false,
  include:['mailing_address','name','email'],
  afterSubmitContacts:populateTextarea
};




</script>

<div id="loading" title="Cloud Sponge Contact Import" > 
    <p></p>
</div>


<!-- Include the script at the end of the body section -->

<div id="smallnotification" style="display: none;" ><div class="noti_close" onClick="hidesmallnotification()" >x</div><div id="smallnotificationtext" ></div></div>
 <script>
       <?php
       if($_SESSION['import'] != ''){
       echo 'check5("'.$import.'");';
         $_SESSION['import'] = '';
       }
       else {
         echo 'check5(false)';
       }
       ?>
  </script>
  
</body>
</html>

	  <script>
$(function() {
    $("body").delegate("#fdobd, #famdob", "focusin", function(){
        $(this).datepicker({
      changeMonth: true,
      changeYear: true,
	  yearRange: "-113:+0" 
    });
    });
});


/*	
$(function() {
    $("body").delegate("#FI_BDAY_1_here", "focusin", function(){
        $(this).datepicker({
      changeMonth: true,
      changeYear: true,
	  yearRange: "-113:+0" 
    });
    });
});*/
  
  </script>
 	

