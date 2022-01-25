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
		<title>MIB FAQ</title>
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
				<h2>Frequently Asked Questions</h2>
				<div class="PrivacyContent" id="PrivacyContent" style=""><h3>Personal Relationship Management</h3><br>
				A private platform that aggregates your contacts and content enabling scalable intimacy
				</div>
				
				<div class="PrivacyContent" id="PrivacyContent" style=""><h3>About Beta</h3> The MIB Team has been developing and testing the Myiceberg features for many months, we do understand that there may be some use cases which the team did not consider and need to be addressed in future releases.  The MIB team would like to thank you for using the Myiceberg platform and providing us your feedback.  Please forward the team your feedback on new features and suggestions for improvement by email to support@myiceberg.com.</div>
				<div class="PrivacyContent" id="PrivacyContent" style=""><h3>What is scalable intimacy?</h3>
					Scalable intimacy is not a new term, Myiceberg refers to the term as a way for an individual to use technology to collect and distribute information in a direct and personal manner in order to facilitate genuine interactions and effective deepen relationships. 
				</div>
				
				<div class="PrivacyContent" id="PrivacyContent" style=""><h3>What can I do with Myiceberg?</h3> We invite you to connect your social media accounts (Facebook and LinkedIn), your CRM application (Salesforce) and your contact applications (Outlook, Gmail, etc.) in order to import and aggregate all of your connections into a single application.  Once you have imported your friends you will be able create categories and contextually organize your friends and contacts into these categories.</div>
				
				<h3>The Service</h3>
				<div class="privacyTitle" id="privacyTitle1" onclick="showPrivacyContent(1)">1.  Do I need to pay for Myiceberg?</div>
				<div class="PrivacyContent" id="PrivacyContent1"style="display: none;">&nbsp;&nbsp;&nbsp;There is no charge to use the Personal edition of Myiceberg. In order to access the additional functions or services you will be required to upgrade to the appropriate subscription.  Please see the pricing page for the services matrix and the appropriate fee.
				</div>
				
				
				
				<div class="privacyTitle" id="privacyTitle2" onclick="showPrivacyContent(2)">2.  What is the value of Myiceberg?</div>
					<div class="PrivacyContent" id="PrivacyContent2" style="display: none;">&nbsp;&nbsp;&nbsp;
					Myiceberg delivers scalable intimacy to our users.  Myiceberg is a private platform for aggregating and managing your entire network of personal and professional contacts. Our Users aggregate their relationships from their technical sources including; contact applications, social networks and CRM applications. The Myiceberg platform facilitates and enables it's users to deepen relationships efficiently through genuine contextual interactions. 
					<br><br>
					Myiceberg provides it's users the unique ability organize friends into custom categories and store important information about their friends and contacts.


					</div>
					
					<div class="privacyTitle" id="privacyTitle3" onclick="showPrivacyContent(3)">3.  What do you do with my personal information when I enter it into Myiceberg?</div>
					<div class="PrivacyContent" id="PrivacyContent3" style="display: none;">&nbsp;&nbsp;&nbsp;
						Myiceberg uses your personal information to improve your experience on our site. Myiceberg will not provide or sell personal information of our users or a list of our users to any other organization or individual.
					</div>


					<div class="privacyTitle" id="privacyTitle4" onclick="showPrivacyContent(4)">4.  Is there a limit to the number of friends or friend sources I can input into Myiceberg?</div>
						<div class="PrivacyContent" id="PrivacyContent4" style="display: none;">&nbsp;&nbsp;&nbsp;
						There is currently no limit to the number friends and contacts a user may import/create and aggegrate within a Myiceberg account. The total number of friends may be limited in the future and would require different service levels.   If there are other third party applications you would like to see Myiceberg integrated with in the future please email your suggestions to <a href="#">Sales@myiceberg.com</a>
						</div>

					<div class="privacyTitle" id="privacyTitle5" onclick="showPrivacyContent(5)">5.  What is the Business or Enterprise service?</div>
					<div class="PrivacyContent" id="PrivacyContent5" style="display: none;">&nbsp;&nbsp;&nbsp;
						Users who wish to integrate their Myiceberg account with productivity and CRM applications such as Evernote and Salesforce.com are required to be at the Business service level. To upgrade an account from Personal to Business please go the the User Settings page in Myiceberg.  Enterprise service level has been established to provide organization with many users (100+) and a need to integrate with coporate applications (CRM, etc.), if interested in more details please contact sales@myiceberg.com.
					</div>
					
					<h2>Import Friends</h2>
					
					<div class="privacyTitle" id="privacyTitle6" onclick="showPrivacyContent(6)">6.  How do I import my friends from Facebook and LinkedIn into Myiceberg?</div>
						<div class="PrivacyContent" id="PrivacyContent6" style="display: none;">&nbsp;&nbsp;&nbsp;
						Once registered and logged into Myiceberg, navigate and click the Import Friends button and a modal will present the options to import a user's Facebook and LinkedIn friends.  
						The user will be asked to authorize Myiceberg to access the users social media account (Facebook, LinkedIn) account upon first import of friends.  
					</div>
					
					<div class="privacyTitle" id="privacyTitle7" onclick="showPrivacyContent(7)">7.  How do I import my friend information from my email applications into Myiceberg?</div>
					<div class="PrivacyContent" id="PrivacyContent7" style="display: none;">&nbsp;&nbsp;&nbsp;
						
					The user may import friends via an exported and locally saved CSV file (Outlook format). Myiceberg supports importing locally saved CSV files (Outlook format) from <b>Outlook, Outlook Express, Yahoo! Mail, Hotmail, and Gmail</b> and other applications offering CSV export in Outlook format function. Import Friends button displays an option to import the locally saved CSV file (Outlook format) into the users Myiceberg account. Below is more information on how to export a CSV file (Outlook format) from some popular applications.  <b>Additional instructions may be found on YouTube, simply search in YouTube on how to create CSV (Outlook format) from your specific application.</b>
					<br><br>
					When saving an exported CSV file (Outlook format) file from an application it is best to save it in a location on local computer that is easy to locate (such as the desktop). 
<br><br>
					When importing a CSV file (Outlook formate there are three rules which are required: 
<br>					
1. We will only import friends/contacts if there is a unique identifying data element which is part of the data record. 
<br>
- The two qualifying elements are an email address or a mobile phone number. Only one is required. 
<br><br>
2. Each friend much have both a first and last name along with the unique data element. 
<br>
3. CSV file must be in saved in Outlook format, this is selected when exporting/creating CSV file. 
<br>
- After creating a CSV file user should review the data to ensure requirements are met prior to importing CSV file (Outlook format) is imported into Myiceberg. 
<br><br>
If any issues with not meeting requirements user should correct data issues in application and re-export data and save as CSV file (Outlook format), another option is to correct data issues directly in the CSV file. 
<br><br>
<b>Microsoft Outlook and Outlook Express:</b> Some general directions to follow, though instructions may vary by version. For more detailed instructions, open 'Help' in Outlook or Outlook Express and type 'export' in the search box. Look for topics that include 'export wizard,' 'export information,' 'exporting contacts' or 'exporting address book contacts' in the title.
<br><br>
From <b><u>Outlook:</b></u>  Select File >(open/Export Outlook 13)> Import/Export > "Export to a file" from the main menu.  Choose Comma Separated Values (must be Outlook format, not DOS or others) > Select "Contacts" > Recommended that user saves exported file to a location on local computer that is easy to locate such as the desktop.
<br><br>
From <b><u>Outlook Express</b></u>:  Select File > Export > Address Book from the main menu.  Select Text File (Comma Separated Values).  Click Export. 
<br><br>
<b>Gmail:</b>  To import Gmail contacts, export and save contact file as a CSV (select Outlook format) from Gmail. Instructions are available in the Gmail help section at:<a href="http://mail.google.com/support/bin/answer.py?hl=en&answer=24911 ">http://mail.google.com/support/bin/answer.py?hl=en&answer=24911 </a>
<b>Select Outlook CSV</b> format
<br><br>
<b>Yahoo!:</b>To import  Yahoo! Mail address book, save file as a CSV <br>
<a href="http://answers.yahoo.com/question/index?qid=20100826124835AAIwygP">http://answers.yahoo.com/question/index?qid=20100826124835AAIwygP</a>

</div>
				
				
				
				
				
				
				<div class="privacyTitle" id="privacyTitle8" onclick="showPrivacyContent(8)">8.  Can I add friends who are not in any email application?</div>
					<div class="PrivacyContent" id="PrivacyContent8" style="display: none;">&nbsp;&nbsp;&nbsp;
					This feature is coming soon. 
					</div>
				
				<div class="privacyTitle" id="privacyTitle9" onclick="showPrivacyContent(9)">9. How long does importing friends take?</div>
					<div class="PrivacyContent" id="PrivacyContent9" style="display: none;">&nbsp;&nbsp;&nbsp;
					Myiceberg is continuing to ensure the performance and users experience is improving, currently we estimate that importing approximately 500 friends from either Facebook or LinkedIn may take between 2 and 4 minutes.  Importing a CSV file with 2000 contacts may take between 3 and 4 minutes.
				</div>
					
				<div class="privacyTitle" id="privacyTitle10" onclick="showPrivacyContent(10)">10. What is Verify Friends?</div>
					<div class="PrivacyContent" id="PrivacyContent10" style="display: none;">&nbsp;&nbsp;&nbsp;
					One important value of Myiceberg is the ability to merge friends and contacts from multiple sources into a single identity in Myiceberg.  When a user imports a friend or contact and there is an existing friend of the user with a matching first name and last name, the user will be asked to verify if the two (or more) friends are the same or different.  If users selects same for matched friend then that friend will be merged with existing matched friend and there will only be one friend for that user (ONLY) in their friend list (Myiceberg does maintain and indicate to the user how they are connected to the friends, even after merged) in Myiceberg. 
				</div>
				
				
				<div class="privacyTitle" id="privacyTitle11" onclick="showPrivacyContent(11)">11.  I have two friends who are the same person who did not show up in Verify Friends modal how can I combine them into a single friend account?</div>
					<div class="PrivacyContent" id="PrivacyContent11" style="display: none;">&nbsp;&nbsp;&nbsp;
					Myiceberg matches on both first and last name.  To merge an identity with the Verify Friends process the user must edit the name of the friend (click on friend in friend list to edit the friend detail attributes) so it matches exactly on the first and last name with friend the user would like to merge.
				</div>
				
				
				<div class="privacyTitle" id="privacyTitle12" onclick="showPrivacyContent(12)">12. Yikes! I accidently combined two people who were not really the same person.  How can I fix this.</div>
					<div class="PrivacyContent" id="PrivacyContent12" style="display: none;">&nbsp;&nbsp;&nbsp;
					Please contact Support@myiceberg.com and the team will work to resolve the issue.  Provide your name and email address and the name of the friend who is currently viewable and the friend who was combined (you cannot see any longer). 
					</div>
					
					
				<h2>Categories</h2>
				
				
				<div class="privacyTitle" id="privacyTitle13" onclick="showPrivacyContent(13)">13. What are categories and how do I use them?</div>
					<div class="PrivacyContent" id="PrivacyContent13" style="display: none;">&nbsp;&nbsp;&nbsp;
						Categories are a method for a user to contextually organize friends providing the ability to manage communication (share web stories, etc.), plan events (coming soon) and share photos (coming soon). A user may include friends in as many categories as desired.
						<br/><br/>
						Some examples:
						<br/><br/>
						Creating categories such as college, name of highschool, social group or business group.  User may also consider creating categories based on affiliation with people such as family, relatives, neighbors, etc. 
					</div>
					
				<div class="privacyTitle" id="privacyTitle14" onclick="showPrivacyContent(14)">14.  What is Clone Categories?</div>
					<div class="PrivacyContent" id="PrivacyContent14" style="display: none;">&nbsp;&nbsp;&nbsp;
					Allows users to clone and existing category, user may want to create a new category with many of the same friends that are in an existing category and this feature facilitates the that process.  Selected category to be cloned, selected clone category, rename new category and edit friends inside new category.
					</div>
				
				
				<h2>Facebook</h2>
				<div class="privacyTitle" id="privacyTitle15" onclick="showPrivacyContent(15)">15.  How often do you import my Facebook Friends?  Will you automatically add my new Facebook friends to Myiceberg?</div>
					<div class="PrivacyContent" id="PrivacyContent15" style="display: none;">&nbsp;&nbsp;&nbsp;
					Currently Myiceberg does not automatically updated friends lists from social media accounts connected to Myiceberg accounts.  The user will need to reimport friends from specific accounts to update friends lists inside Myiceberg, friends that already exists in user friends list will not be reimported through this process. 
				</div>
					
				
				<div class="privacyTitle" id="privacyTitle16" onclick="showPrivacyContent(16)">16. Is Myiceberg able to import my entire list of friends from Facebook?</div>
					<div class="PrivacyContent" id="PrivacyContent16" style="display: none;">&nbsp;&nbsp;&nbsp;
					No.  Some social media users have established privacy setting which prohibits the social media application from exporting the users information to third party applications.
					<br/><br/>
					One option for a user to include a link to friends that have not been imported from social media applications (such as Facebook) is to copy the friends public social media URL into their friend detail page online link field.
				</div>
				
				<div class="privacyTitle" id="privacyTitle17" onclick="showPrivacyContent(17)">17.  What do you do with my Facebook application information and permissions you have requested from me?</div>
					<div class="PrivacyContent" id="PrivacyContent17" style="display: none;">&nbsp;&nbsp;&nbsp;
					We request access to your <b>basic information</b> as allowed by the social media application and privacy settings established by the user. Myiceberg request permission to export your friends list and their basic information (as allowed by social media application and users privacy settings). 
					<br/><br/>
					Myiceberg does request permission to <b>access Facebook on your behalf while you offline</b> this is part used to support our social monitoring service included in business and enterprise offering. 
				</div>
				
				
				<h2>LinkedIn</h2>
				<div class="privacyTitle" id="privacyTitle18" onclick="showPrivacyContent(18)">18. How often do you import My LinkedIn connections?  Will you automatically add my new LinkeIn friends to Myiceberg?</div>
					<div class="PrivacyContent" id="PrivacyContent18" style="display: none;">&nbsp;&nbsp;&nbsp;
					Currently Myiceberg does not automatically updated friends lists from social media accounts connected to Myiceberg accounts.  The user will need to reimport friends from specific accounts to update friends lists inside Myiceberg, friends that already exists in user friends list will not be reimported through this process. 
				</div>
					
				<div class="privacyTitle" id="privacyTitle19" onclick="showPrivacyContent(19)">19. Is Myiceberg able to import my entire list of friends from LinkedIn?</div>
					<div class="PrivacyContent" id="PrivacyContent19" style="display: none;">&nbsp;&nbsp;&nbsp;
					No.  Some social media users have established privacy setting which prohibits the social media application from exporting the users information to third party applications.
					<br/><br/>
					One option for a user to include a link to friends that have not been imported from social media applications (such as LinkedIn) is to copy the friends public social media URL into their friend detail page online link field.
					</div>
					
				<div class="privacyTitle" id="privacyTitle20" onclick="showPrivacyContent(20)">20. What do you do with my LinkedIn application information and permissions you have requested from me?</div>
					<div class="PrivacyContent" id="PrivacyContent20" style="display: none;">&nbsp;&nbsp;&nbsp;
					We request access to your <b>basic information</b> as allowed by the social media application and privacy settings established by the user. Myiceberg request permission to export your 1st Degree connections and their basic information (as allowed by social media application and users privacy settings).				<br/><br/>
					</div>
				
				<h2>Salesforce</h2>
				<div class="privacyTitle" id="privacyTitle21" onclick="showPrivacyContent(21)">21. What information do you export from Salesforce into my account?</div>
					<div class="PrivacyContent" id="PrivacyContent21" style="display: none;">&nbsp;&nbsp;&nbsp;
					Myiceberg exports the basic contact information where the Myiceberg user is the owner of the contact inside of Salesforce.  Examples, First Name, Last Name, Address, email and phone numbers. <b>We do not export company information or any other sales related information.</b>
	
					<br/><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;There are two rules which we require: <br />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. We will only import contacts if there is a unique identifying data element which is part of the data record. <br />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- The two qualifying elements are an email address or a mobile phone number. Only one is required. <br />

					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. Each friend much have both a first and last name along with the unique data element. 
					User should review a summary of all contacts to ensure the contacts to be import into Myiceberg have the qualifying information.

					<b><li>Only contacts where Myiceberg user is the contact owner will be exported into a Myiceberg account</li></b>
					</div>
					
					<div class="privacyTitle" id="privacyTitle22" onclick="showPrivacyContent(22)">22.  What information in my Myiceberg account is visible to other Salesforce users within my company?</div>
						<div class="PrivacyContent" id="PrivacyContent22" style="display: none;">&nbsp;&nbsp;&nbsp;
						The Myiceberg account is private a data is not viewable by others inside an organization, users should NEVER share username and passord to Myicebeg account to ensure privacy.
					</div>
					
					<div class="privacyTitle" id="privacyTitle23" onclick="showPrivacyContent(23)">23.  What information do you write from Myiceberg into my Sales force Account?</div>
						<div class="PrivacyContent" id="PrivacyContent23" style="display: none;">&nbsp;&nbsp;&nbsp;
						There are two places where Myiceberg may write information into your Salesforce contact records.
						<br/><br/>
						For enterprise users will work with the organizations Salesforce administrator to add a Myiceberg button and data field in which will be populated with a clickable link to the Myiceberg users account and the private friend detail page for a contact.  When the custome Myiceberg field is not part of a users Salesforce contact template then Myiceberg will write a clickable URL into contact description field taking the user back to the Myiceberg account and private friend detail page of the contact. 
					</div>
					
					<div class="privacyTitle" id="privacyTitle24" onclick="showPrivacyContent(24)">24.  What happens to my information if I stop using this Salesforce account?</div>
						<div class="PrivacyContent" id="PrivacyContent24" style="display: none;">&nbsp;&nbsp;&nbsp;
						The friend record and the private information entered for that friend wil remain in the Myiceberg account.  The link inside Myiceberg back to a Salesforce account contact record is removed and the URL inside the contact description field inside Salesforce is removed.  All Salesforce icons indicating a relationship with friend inside Salesforce is also removed from Myiceberg.			
					</div>
					
					<h2>Evernote</h2>
					<div class="privacyTitle" id="privacyTitle25" onclick="showPrivacyContent(25)">25.  What would I user Evernote for?</div>
						<div class="PrivacyContent" id="PrivacyContent25" style="display: none;">&nbsp;&nbsp;&nbsp;
						Evernote is a very popular FREE cloud based application used by over 40 million users as a service to store and manage personal content.   Evernote provides the capability 'clip' webpages and create Notes into a user defined Evernote Notebooks along with adding Evernote Tags to Notes with words or topics that make it easy for a user to categorize Notes for easy reference and management.
						<br/><br/>
						<a href="https://evernote.com/">https://evernote.com/</a>

					</div>
					
					<div class="privacyTitle" id="privacyTitle26" onclick="showPrivacyContent(26)">26. What specific integration has Myiceberg done with Evernote?</div>
						<div class="PrivacyContent" id="PrivacyContent26" style="display: none;">&nbsp;&nbsp;&nbsp;
						When a user intergrates their Evernote account with their Myiceberg account the integration process will create a Evernote Notebook named Myiceberg inside the users Evernote account, all Notes placed into this Notebook will be imported into a user's Myiceberg account on a nightly basis, Myiceberg DOES NOT import from other Evernote Notebooks inside a users Evernote account.  Myiceberg will import all Notes and Tags from the Myiceberg Notebook and create a new Myiceberg Category for each unique Evernote Tag, all Evernote Notes associated with a Tab will be added to Category inside Myiceberg and all Myiceberg existing Categories will be created as Tags in the Myiceberg Notebook.
						<br/><br/>
						A Note that has been imported into a Myiceberg account will include the Note details and a URL link to the note and will automatically be added to a user's Friend's Social/News page for all friends that are in a Myiceberg Category that the Note was associated with based on the imported Tag name (See more in Categories FAQ section).
					</div>
					
					<div class="privacyTitle" id="privacyTitle27" onclick="showPrivacyContent(27)">27.  Can I manually update my Evernote account with Myiceberg and what information is Sync'd. </div>
						<div class="PrivacyContent" id="PrivacyContent27" style="display: none;">&nbsp;&nbsp;&nbsp;			
					Yes, on the News tab there is an Evernote icon, clicking on the icon will initiate an synchronize Categories, Tags and Notes between users Evernote account and Myiceberg account. 			
					</div>			
					
					<div class="privacyTitle" id="privacyTitle28" onclick="showPrivacyContent(28)">28. How do I delete a Evernote item from Myiceberg. </div>
						<div class="PrivacyContent" id="PrivacyContent28" style="display: none;">&nbsp;&nbsp;&nbsp;
						Clicking on News tab, find the appropriate story and click the trash icon. The process will delete the story from all users Friend Detail News page.  A user also is able delete a story from a specific Friend's Detail News page and this will NOT delete the Evernote Note from other friends.
					</div>
					
					<h2>User Settings</h2>
					
					<div class="privacyTitle" id="privacyTitle29" onclick="showPrivacyContent(29)">29. On the user settings page there are placeholders for more email addresses.  Why is this?</div>
						<div class="PrivacyContent" id="PrivacyContent29" style="display: none;">&nbsp;&nbsp;&nbsp;
						You can change the primary email address to any verified email.  You can delete any non-primary email address from Myiceberg.
						<br/><br/>
						When entering a secondary email address after you have set up your account, Fill in the appropriate email and click the Add Email button.  All new email addresses are sent a validation email that must be clicked before access is activated for that email address.

					</div>
					
					<div class="privacyTitle" id="privacyTitle30" onclick="showPrivacyContent(30)">30.  What is the meaning of Notification email?</div>
						<div class="PrivacyContent" id="PrivacyContent30" style="display: none;">&nbsp;&nbsp;&nbsp;
						This is the email address a user elects to receive notifications from Myiceberg.
					</div>
					
					<div class="privacyTitle" id="privacyTitle31" onclick="showPrivacyContent(31)">31. What are social terms?</div>
						<div class="PrivacyContent" id="PrivacyContent31" style="display: none;">&nbsp;&nbsp;&nbsp;
						As part of achieving scalable intimacy, business users may subscribe to a service to monitor the user's Facebook news feed, and match against defined social terms.  When a post in a user's Facebook stream is successfully matched on a social term Myiceberg will notify the user of the match and provide a link to Facebook post in a notification email and Friend Detail page.  The user may choose to delete a post from a Friends Detail page as well. For more detailed information on this service please contact:
						<!--make a link -->
						<a href="#">Support@myiceberg.com</a>
					</div>
					
					<div class="privacyTitle" id="privacyTitle32" onclick="showPrivacyContent(32)">32.  How are the Social terms matched? </div>
						<div class="PrivacyContent" id="PrivacyContent32" style="display: none;">&nbsp;&nbsp;&nbsp;
						A user may choose to use a portion of or a complete word for their social terms.  An example of using the term 'Congrat' would match on Congratulate or Congratulations and the term 'Birth' would match on Birthday or Birth.
					</div>
					
					<div class="privacyTitle" id="privacyTitle33" onclick="showPrivacyContent(33)">33. How do I disconnect a third party account from Myiceberg?</div>
						<div class="PrivacyContent" id="PrivacyContent33" style="display: none;">&nbsp;&nbsp;&nbsp;
						On the User Settings there is a section titled Account Connections, this lists a users integrated applications with their Myiceberg account.  Double click the appropriate icon and follow the instructions.
					</div>
					
					<div class="privacyTitle" id="privacyTitle34" onclick="showPrivacyContent(34)">34. What is Mutual Friends and how does it work?</div>
						<div class="PrivacyContent" id="PrivacyContent34" style="display: none;">&nbsp;&nbsp;&nbsp;
						Myiceberg uses existing functions made available to return lists of the mutual friends from Facebook and LinkedIn.  Myiceberg aggregates these lists into one view of mutual friends from both social media networks. This feature only displays mutual friends from the public social media sites Facebook and LinkedIn.
					</div>
					
					<div class="privacyTitle" id="privacyTitle35" onclick="showPrivacyContent(35)">35.  If I purchase something is my credit card information secure?</div>
						<div class="PrivacyContent" id="PrivacyContent35" style="display: none;">&nbsp;&nbsp;&nbsp;
						All processing and storage of Credit Card data is peformed by a PCI compliant payment processing partner.
					</div>
					
					<div class="privacyTitle" id="privacyTitle36" onclick="showPrivacyContent(36)">36. Can I downgrade my account from Business to Personal? If so what happens?</div>
						<div class="PrivacyContent" id="PrivacyContent36" style="display: none;">&nbsp;&nbsp;&nbsp;
						Currently to downgrade an account; in User Settings page and select the account type Personal and click the Upgrade button.  Functions and tabs available to Business or Enterprise users will become disabled.  Myiceberg will retain user information for six months in the event a user upgrades back to Business or Enterprise account. Myiceberg will after 6 months will delete content from Evernote which may have been imported into Myiceberg. 
					</div>
					
					<div class="privacyTitle" id="privacyTitle37" onclick="showPrivacyContent(37)">37. What happens to my information when I delete my account?</div>
						<div class="PrivacyContent" id="PrivacyContent37" style="display: none;">&nbsp;&nbsp;&nbsp;
						Myiceberg will delete all of the friends in the account.  Myiceberg will delete all of the data associated with friends in the account.
					</div>
					
					
					<div class="privacyTitle" id="privacyTitle38" onclick="showPrivacyContent(38)">38.  Can I export or download the information in Myiceberg account.</div>
						<div class="PrivacyContent" id="PrivacyContent38" style="display: none;">&nbsp;&nbsp;&nbsp;
						Not at this time. 
					</div>
					
					<div class="privacyTitle" id="privacyTitle39" onclick="showPrivacyContent(39)">39. What does the events tab do?</div>
						<div class="PrivacyContent" id="PrivacyContent39" style="display: none;">&nbsp;&nbsp;&nbsp;
						Nothing at the moment  but this will be a place for a user to track milestone dates that are important to friends in your network as well as create and manage social events that a user may want to share with friends.
					</div>
					
					<div class="privacyTitle"  id="privacyTitle40" onclick="showPrivacyContent(40)">40. What does the Albums Tab do?</div>
						<div class="PrivacyContent" id="PrivacyContent40" style="display: none;">&nbsp;&nbsp;&nbsp;
						Currently this feature is not completed, the feature will allow users to integrate with third party applications to aggregate and manage photo content. 
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

<?php
	if( isset($_GET['showcontent'])){
		echo "SHOW CONTOENT IS CALLED";
		echo '<script >
		showPrivacyContent('.$_GET['showcontent'].');
		

		showIt("privacyTitle'.$_GET['showcontent'].'");
		</script>';
	}
?>