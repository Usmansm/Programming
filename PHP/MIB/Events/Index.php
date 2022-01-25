<?php
	error_reporting(1);
	include('../config/config.php');

	
	session_start();
	
	/*function for displaying avaar */
	function getAvatarLink(){
        global $config;
            $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
            $result = $mysqli->query('SELECT * FROM source_import WHERE userId = "'.$_SESSION["userId"].'"');
            
            ;
            $sources = array();
             while($row = $result->fetch_array()){
                if($row['sourceProfilePicture'] == '' && $row['sourceName'] == 'facebook'){
                   
                    $link = $config['FBlink'].$row["sourceUid"].'/picture?type=large';
                   
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
?>
<!DOCTYPE html>
<html>
	<html>
		<head>
			<title>MIB Events</title>
			<link rel="stylesheet" href="../css/main.css" media="screen" type="text/css" />
			<link rel="stylesheet" href="../css/sam.css" media="screen" type="text/css" />
			
			<script src="Events.js" type="text/javascript" ></script>
			<script src="../UserSettings/UserSettings.js" type="text/javascript" ></script>
			<script src="../js/jquery-ui-1.10.2.custom.min.js"></script>
		</head>
		
		<div id="headbar" onClick="change_fsbutton_class(2)" >
			<a class="a_noshow" href="<?php echo $config['root'];?>friends/" >
				<div id="logohold"><div id="logo" onClick="growcontent(true)" ></div></div>
			</a>
			
			<a class="a_noshow" href="<?php echo $config['root'];?>friends/" >
				<span class="head_link" id="Events_head_link" >Friends</span> </a>
				<span class="head_link" id="News_head_link" ><a class="a_noshow" href="../friends/index.php?News=News" style="color: white;">News</a></span>
				<a href="../Events/Index.php"><span class="head_link" onClick="Head_meny_Back_Ground('Events_head_link')" id="Friends_head_link">Events</span></a>
				<span class="head_link" id="Albums_head_link" onClick="Head_meny_Back_Ground('Albums_head_link')">Albums</span>
			<div id="userhold" >
				<div id="usercont" >
					<div id="avatar" style="background: url('<?php getAvatarLink(); ?>');background-size: 100% 100%;" ></div>
						<div id="userinfo" >
							<span id="username"> <?php echo $firstname." ".$lastnametrunc."."; ?> 
								<select id="selectAccount" onchange="change(this.value)" >
									<option value="select" >select </option>
									<option value="SettingsProfile" >Settings/Profile</option>
									<option value="Logout"  onclick="change('Logout')">Logout</option>
									<option value="kk">Sess-Id=<?php echo $_SESSION["userId"];?></option>
									<option value="hh">Not-Id=<?php echo $_SESSION["notify"];?></option>
								</select>	
							</span>
						</div>
				</div>
			</div>
		</div>
		
		<div id="actionbar" >
		
			<input type="button" id="actionbarFirstItem" class="ac_button" onClick="promptImportFriends(<?php echo $_SESSION['userId'] ?>)" value="Import friends" />
			<input type="button" class="ac_button" id="verbutton" value="Verify Events" onClick="promptVerifyFriends()" />
			<input onClick="delfriend()" type="button" class="ac_button" value="Delete" />
			<input onClick="checkbox_toggle(this)" type="checkbox" />
			<input type="text" id="friendsearchinput" placeholder="Search Events" class="friendsearchinput" />
			<input type="button" onFocus="change_fsbutton_class(1)" onClick="document.getElementById('friendsearch_button').focus()" class="friendsearch_button" id="friendsearch_button" value=" " />
		  
		</div>
		<div id="apphold" > Event's page is under construction
			<div id="UserSettingsPage" > </div>
		</div>
		
	</html>