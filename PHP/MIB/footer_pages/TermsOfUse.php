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

<html>
	<head>
		<title>MIB Terms of Use</title>
		<link rel="stylesheet" href="../css/main.css" media="screen" type="text/css" />
		<link rel="stylesheet" href="../css/sam.css" media="screen" type="text/css" />
		<link rel="stylesheet" href="../css/FooterPages.css" media="screen" type="text/css" />
		
		<script src="Events.js" type="text/javascript" ></script>
		<script src="../UserSettings/UserSettings.js" type="text/javascript" ></script>
		<script src="FooterPages.js" type="text/javascript" ></script>
		<script src="../js/jquery-1.9.1.min.js"></script>
		<script src="../js/jquery-ui-1.10.2.custom.min.js"></script>
		<script src="../friends/Deni.js"></script>
	</head>
		
	<div id="headbar" onClick="change_fsbutton_class(2)" >
		<a class="a_noshow" href="<?php echo $config['root'];?>friends/" >
			<div id="logohold"><div id="logo" onClick="growcontent(true)" ></div></div>
		</a>
		
		<a class="a_noshow" href="<?php echo $config['root'];?>friends/" >
			<span class="head_link" id="Events_head_link" >Friends</span> </a>
			<span class="head_link" id="News_head_link" ><a class="a_noshow" href="../friends/index.php?News=News" style="color: white;">News</a></span>
			<a href="../Events/Index.php"><span class="head_link" onClick="PromptNonExistPageModa('Events feature coming soon','Events feature coming soon'); Head_meny_Back_Ground('Events_head_link')" id="Friends_head_link">Events</span></a>
			<span class="head_link" id="Albums_head_link" onClick="PromptNonExistPageModa('Albums','Albums feature coming soon'); Head_meny_Back_Ground('Albums_head_link')">Albums</span>
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
		<div id="AboutUsHold" >
			<div class="screen_about">
				<div class="privacyTitle">Revised September 1, 2013</div> <br />
				
				<div class="privacyTitle" onclick="showPrivacyContent(1)">- Introduction</div>
					<div class="PrivacyContent" id="PrivacyContent1" style="">&nbsp;&nbsp;&nbsp;Our fundamental belief is that a person has a single network of relationships.  Within this network some of these relationships are strictly personal, some strictly business, and there are varying degrees in between.  We believe people are entitled to have a private place to manage their relationships, keeping private what they wish to track privately and in turn leverage this knowledge, within their network, to deepen their relationships and improve their business results
					<br /><br />Myiceberg (the Service) is a private platform for managing your entire network of personal and professional contacts.  Our users aggregate their friends from their contact applications as well as from the social networks they are users of 
					such as; Facebook, Linked In and Twitter.  The Myiceberg platform helps people deepen relationships efficiently through genuine interactions.
					
					<br /><br />Business and/or Enterprise clients of Myiceberg understand and respect Myiceberg user's right to privately store, and access their Myiceberg data.
					
					<br /><br />
					As the platform expands we'll integrate with other technologies where Myiceberg users store or access photos, music and other personal interest content. The functionality of our platform will change over time and this may result in changes to our terms of use.  Users may be required to agree to updated terms of use in the future as a condition of continued use of our service. 
					</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(2)">- YourContent</div>
					<div class="PrivacyContent" id="PrivacyContent2" style="">&nbsp;&nbsp;&nbsp;
					The Myiceberg Service is a multi-tenant platform similar to an apartment building having private apartments for individual tenants but sharing some common areas. Similar platforms such as Salesforce.com and Dropbox.com among many others operate the same way.
					<br /><br />
					Your lists of friends and contacts that you import into the Myiceberg platform are your private lists.  No other user of the service has visibility into your list of friends.  Note however, if you are a member of a public social network such as Facebook, Linked In, Twitter, Google+ ect. and these networks make visible the relationships you have with others within those networks, this is public information and we do display this public information to our users under the same terms it is visible to other users within those networks.
					<br/> <br/>
					You are the owner of the content you upload into the system. If you so choose you may discontinue use of the system and may delete all of the content you have imported into the Myiceberg platform.
					<br/> <br/>
					The Myiceberg platform is not a searchable network like the previously mentioned social networks. Meaning, another user cannot search for and find a person you have privately imported into the service.  Users may only search for a friend within their list of friends and contacts whom they have directly imported into the service. 
					<br/> <br/>
				</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(3)">- Who May Use MyIceberg</div>
					<div class="PrivacyContent" id="PrivacyContent3" style="">&nbsp;&nbsp;&nbsp;
						There is a basic tier of service available to all users at no charge. You must be older than 13 years old to use the service.
						<br/> <br/>
						Premium tiers of service will be made available to users at a stated fee.  Users individually may select to purchase these services at their discretion.  Enterprise services may be purchased at the corporate level for companies wishing to deploy our service to many of their users.  For enterprise services please contact Sales@myiceberg.com.
						<br/> <br/>
						Users may from time to time be offered free or discounted trial of the platform and its features which would normally require payment.  You agree that you are aware that this trial offer may be rescinded by us at any time and your continued use of these features could require a subscription requiring payment.
						<br/> <br/>
						Features and prices for the service tiers may change from time to time.  In the event we make these changes you will not lose any features for which you have paid, for the remainder of the paid term. At the conclusion of a term for which you have paid, you may be required to subscribe for a higher level of service to retain the desired features.
						<br/> <br/>
						See compare services to see which Myiceberg edition you may wish to use. 
					</div>
				
				
				
				
				<div class="privacyTitle" onclick="showPrivacyContent(4)">-Summary of terms </div>
					<div class="PrivacyContent" id="PrivacyContent4" style="">&nbsp;&nbsp;&nbsp;
					<br/> <br/>
					1.	You will not attempt to reverse engineer or copy any of the functionality of the Myiceberg platform. 
					<br/> <br/>
					2.	You will not upload viruses or other malicious code.
					<br/> <br/>
					3.	You will not attempt to acquire data, information or files which you have not uploaded or imported.
					<br/> <br/>
					4.	You will not attempt to or actually override any security component included in or underlying the service.
					<br/> <br/>
					5.	You will not attempt to connect your Myiceberg account with any other third party account which does not belong to the register Myiceberg user.
					<br/> <br/>
					6.	You will not do anything that could disable, overburden, or impair the proper working or appearance of Myiceberg, such as a denial of service attack or interference with page rendering or other Myiceberg functionality.
					<br/> <br/>
					7.	You will not solicit or attempt to learn login information or access an account belonging to another registered user of Myiceberg. 
					<br/> <br/>
					8.	As a business who, may or may not be paying for any portion of our user's license fee, you will not ask or require a user to provide access to their Myiceberg account. 
				</div>
				
				
				<div class="privacyTitle" onclick="showPrivacyContent(5)">- Termination</div>
					<div class="PrivacyContent" id="PrivacyContent5" style="">&nbsp;&nbsp;&nbsp;
					If you violate the letter or spirit of this Statement, or otherwise create risk or possible legal exposure for us, we can stop providing all or part of the service to you. We will notify you by email or at the next time you attempt to access your account. You may also delete your account or disconnect your Myiceberg account from any of your third party accounts at any time. In all such cases, while your use of the platform may have ceased, your obligations to these terms will still apply. 
					</div>
				
				
				<div class="privacyTitle" onclick="showPrivacyContent(6)">- Limitations of Liability</div>
				<div class="PrivacyContent" id="PrivacyContent6" style="">&nbsp;&nbsp;&nbsp;
				WE TRY TO KEEP MYICEBERG UP, BUG-FREE, AND SAFE, BUT YOU USE IT AT YOUR OWN RISK. WE ARE PROVIDING MYICEBERG AS IS WITHOUT ANY EXPRESS OR IMPLIED WARRANTIES INCLUDING, BUT NOT LIMITED TO, IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT. WE DO NOT GUARANTEE THAT MYICEBERG WILL ALWAYS BE SAFE, SECURE OR ERROR-FREE OR THAT MYICEBERG WILL ALWAYS FUNCTION WITHOUT DISRUPTIONS, DELAYS OR IMPERFECTIONS. MYICEBERG IS NOT RESPONSIBLE FOR THE ACTIONS, CONTENT, INFORMATION, OR DATA OF THIRD PARTIES, AND YOU RELEASE US, OUR DIRECTORS, OFFICERS, EMPLOYEES, AND AGENTS FROM ANY CLAIMS AND DAMAGES, KNOWN AND UNKNOWN, ARISING OUT OF OR IN ANY WAY CONNECTED WITH ANY CLAIM YOU HAVE AGAINST ANY SUCH THIRD PARTIES. WE WILL NOT BE LIABLE TO YOU FOR ANY LOST PROFITS OR OTHER CONSEQUENTIAL, SPECIAL, INDIRECT, OR INCIDENTAL DAMAGES ARISING OUT OF OR IN CONNECTION WITH THIS STATEMENT OR MYICEBERG, EVEN IF WE HAVE BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. OUR AGGREGATE LIABILITY ARISING OUT OF THIS STATEMENT OR MYICEBERG WILL NOT EXCEED THE AMOUNT YOU HAVE PAID US IN THE PAST TWELVE MONTHS. APPLICABLE LAW MAY NOT ALLOW THE LIMITATION OR EXCLUSION OF LIABILITY OR INCIDENTAL OR CONSEQUENTIAL DAMAGES, SO THE ABOVE LIMITATION OR EXCLUSION MAY NOT APPLY TO YOU. IN SUCH CASES, MYICEBERG'S LIABILITY WILL BE LIMITED TO THE FULLEST EXTENT PERMITTED BY APPLICABLE LAW.
				</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(7)">- Rights</div>
					<div class="PrivacyContent" id="PrivacyContent7" style="">&nbsp;&nbsp;&nbsp;
					This Statement makes up the entire agreement between the parties regarding Myiceberg and our service, and supersedes any prior agreements.If any portion of this Statement is found to be unenforceable, the remaining portion will remain in full force and effect.If we fail to enforce any of this Statement, it will not be considered a waiver.Any amendment to or waiver of this Statement must be made in writing and signed by us.You will not transfer any of your rights or obligations under this Statement to anyone else without our consent.All of our rights and obligations under this Statement are freely assignable by us in connection with a merger, acquisition, or sale of assets, or by operation of law or otherwise.Nothing in this Statement shall prevent us from complying with the law.This Statement does not confer any third party beneficiary rights.We reserve all rights not expressly granted to you
					<br/> <br/>
					You will comply with all applicable laws when using or accessing Myiceberg.
					</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(8)">- Dispute Resolution</div>
					<div class="PrivacyContent" id="PrivacyContent8" style="">&nbsp;&nbsp;&nbsp;
					You will resolve any claim, cause of action or dispute you have with us arising out of or Minnesota relating to this Statement or our service exclusively in a state or federal court located in Hennepin County, Minnesota. The laws of the State of Minnesota will govern this Statement, as well as any claim that might arise between you and us, without regard to conflict of law provisions. You agree to submit to the personal jurisdiction of the courts located in Hennepin County, Minnesota. for the purpose of litigating all such claims.
					<br/><br/>
					In the event of a dispute you may contact us at Support@myiceberg.com  or Myiceberg Suite 150, 1850 West Wayzata Blvd. Long Lake, MN 55356.
					</div>
					
				<div class="privacyTitle" onclick="showPrivacyContent(9)"> <u>- You Acknowledge That You Agree To All The Terms Of This Agreement. </u></div> 
					<div class="PrivacyContent" id="PrivacyContent9">YOU ACKNOWLEDGE THAT YOU HAVE READ THIS AGREEMENT, UNDERSTAND IT AND WILL BE BOUND BY ITS TERMS AND CONDITIONS. YOU FURTHER ACKNOWLEDGE THAT THIS AGREEMENT REPRESENTS THE COMPLETE AND EXCLUSIVE STATEMENT OF THE AGREEMENT BETWEEN US AND THAT IT SUPERSEDES ANY PROPOSAL OR PRIOR AGREEMENT, ORAL OR WRITTEN, AND ANY OTHER COMMUNICATIONS BETWEEN US RELATING TO THE SUBJECT MATTER OF THIS AGREEMENT.
					
					<br /><br /><br />
					Thank you and enjoy Myiceberg.
					<br /><br />
					
					Cheers!
					
					</div> 
				<br />
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