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
		<head>
			<title>MIB About Us</title>
			<link rel="stylesheet" href="../css/main.css" media="screen" type="text/css" />
			<link rel="stylesheet" href="../css/sam.css" media="screen" type="text/css" />
			<link rel="stylesheet" href="../css/FooterPages.css" media="screen" type="text/css" />
			<script src="Events.js" type="text/javascript" ></script>
			<script src="../UserSettings/UserSettings.js" type="text/javascript" ></script>
			<script src="../js/jquery-1.9.1.min.js"></script>
			<script src="../js/jquery-ui-1.10.2.custom.min.js"></script>
			<script src="../friends/Deni.js"></script>
			<script src="FooterPages.js"></script>
		</head>
		<div id="headbar" onClick="change_fsbutton_class(2)" >
			<a class="a_noshow" href="<?php echo $config['root'];?>friends/" >
				<div id="logohold"><div id="logo" onClick="growcontent(true)" ></div></div>
			</a>
			
			<a class="a_noshow" href="<?php echo $config['root'];?>friends/" >
				<span class="head_link" id="Events_head_link" >Friends</span> </a>
				<span class="head_link" id="News_head_link" ><a class="a_noshow" href="../friends/index.php?News=News" style="color: white;">News</a></span>
				<a href="../Events/Index.php"><span class="head_link" onClick="PromptNonExistPageModa('Events feature coming soon','Events feature coming soon'); Head_meny_Back_Ground('Events_head_link')" id="Friends_head_link">Events</span></a>
				<span class="head_link" id="Albums_head_link" onClick="PromptNonExistPageModa('Albums feature coming soon','Albums feature coming soon'); Head_meny_Back_Ground('Albums_head_link')">Albums</span>
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
		<div id="apphold" >
			<div class="screen_about">
				<div class="about_text_box">
					<div class="about_heading">About Us... People, Relationships and Experiences</div>
					<div class="about_text">
						Myiceberg is a private platform for managing your entire network of personal and professional contacts. 
						Our Users aggregate their friends from their contact applications as well as from the social networks they participate in such as; facebook, linkedin and twitter. 
						The myiceberg platform helps people deepen relationships efficiently through genuine interactions. 
						For individuals we help enrich experiences and for professionals these enriched experiences improve their business results.
					</div>
				</div>
				
				<div class="our_philosphy">
				<div class="our_philosphy_heading">Our Philosophy</div>
					<div class="our_philosphy_text">
						<div id="OurPhilosophy" class="comment more">
						A person has one single network of relationships which is comprised of  both personal and professional contacts.
						People need a single private platform where they can manage all of their relationships and conduct and track the activties and information that can enrich these relationships. 
						If our professional users have a private place to track this informations which is connected to their CRM applications they will be able 
						<span class="ReadMore" id="ReadMore1" onclick="ReadMoree1(1)">&nbsp;&nbsp;Read More... </span>
						<span id="OurPhilosophy2">to think about their entire network as an opportunity to improve their business result.
						They will become more sucessful and engage with their CRM system more often.
						<span class="ReadLess" id="ReadLess1" onclick="ReadMoree1(2)">&nbsp;&nbsp;Read Less... </span>
						</span>
						</div>
					</div>
				</div>
				
				<div class="our_philosphy">
				<div class="our_philosphy_heading">"Tip of the Iceberg"</div>
					<div class="our_philosphy_text">
						<div class="comment more">
						With the Myiceberg platform we allow our users to organize all of their friends and information about these friends into a simple dashboard by connecting to contact applications, 
						the WWW, social media sites, 
						On-line photo sites and personally discovered information.  
						While Myiceberg is not a social network, we do link you to your friends and their publically known networks such as Linked In and Facebook 
						and help with the discovery of relevant
						<span class="ReadMore" id="ReadMore2" onclick="ReadMoree2(1)">&nbsp;Read More... </span>
						<span id="TipoftheIceberg2"> information. We make it easier for you to plan and participate in social activities with your network. </span>
						<span class="ReadLess" id="ReadLess2" onclick="ReadMoree2(2)">&nbsp;&nbsp;Read Less... </span>
						</div>
					</div>
				</div>
				
				<div class="our_philosphy">
				<div class="our_philosphy_heading">Privacy</div>
					<div class="our_philosphy_text">
						<div class="comment more">
						All of the information our users import into their Myiceberg account is private, 
						visible only to the user who has imported the information.  
						Your contacts and the information you input for these friends is visible only to you.  
						Other Myiceberg users can not search or see any of your information.  
						Enterprise clients cannot access any information input by their users into 
						Myiceberg with the exception of those clients using a "social 
						
						<span class="ReadMore" id="ReadMore3" onclick="ReadMoree3(1)">&nbsp;Read More... </span>
						<span id="Privacy2">compliance" module which alerts a user to 
						any social posts they are about to make which would be archived for compliance purpose. 
						Our enterprise clients understand the difference between personal data and corporate data 
						and how it should be used by their employees and agents to improve their careers and business results.</span>
						<span class="ReadLess" id="ReadLess3" onclick="ReadMoree3(2)">&nbsp;&nbsp;Read Less... </span>
						</div>
					</div>
				</div>
				<div class="about_text_box">
					<div class="about_heading">About partners</div>
					<div class="about_text">
						With respect to the term "Partner" we are not implying that we have a relationship with each third party that extends 
						beyond our agreeing to the terms and conditions to use their API.  We are mindful that many of these partners sell the 
						information about their users via various search features and rely on users being on their site to view promotional 
						messages.  We are respectful that as we use their content to create value for our users, we must not inhibit the revenue
						opportunity for these third party partner sites. We direct users to our partner's sites as often as possible sometimes
						because it is the best user experience and other times because it is the proper way to support these partners.
					</div>
				</div>
			</div>
		
		<div id="UserSettingsPage" > </div>
		</div>
		
		<div id="footerFrendsPage">
    	<br>
        <img id="footerImage" src="../img/logos/mib.png">
        <div id="footerinfo">
            <div id="myicebergLink">Myiceberg.com</div>
        	<div id="AboutUs"><a href="About_Us.php">About Us</a></div> 
            <div id="AboutUs"><a href="priceing.php">Pricing</a></div> 
            <div id="AboutUs"><a href="../footer_pages/FAQ.php">FAQ</a></div> 
			<div id="AboutUs"><a href="Privacy_Policy.php">Privacy Policy</a></div> 
			<div id="AboutUs"><a href="<?php echo $config['root']; ?>footer_pages/TermsOfUse.php">Terms Of Use</a></div>
			
		</div>	
    	
    	<div id="RightsReserved">&copy; 2013 Myiceberg LLC. All Rights Reserved</div>
		</div>
		
</html>
















