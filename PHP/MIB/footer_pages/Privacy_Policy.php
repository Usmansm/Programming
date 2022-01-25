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
		<title>MIB Pivacy Policy</title>
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
				
				<div class="privacyTitle" onclick="showPrivacyContent(1)">- Scope of this Privacy Statement</div>
					<div class="PrivacyContent" id="PrivacyContent1" style="">&nbsp;&nbsp;&nbsp;This Privacy Statement describes how Myiceberg uses, shares and protects the personal information we collect through Myiceberg.com ("Myiceberg.com" or"this site"). Myiceberg includes Myiceberg.com, which may be accessed via links on Facebook, Linked In and other web properties or directly at http://www.Myiceberg.com."Personal information" is information that identifies you personally, either alone or in combination with other information. Personal information may include, for example, your name, address, email address, credit card information, and transaction history."Your Private information" is information you upload or import into Myiceberg. Your Private information may include for example, your friend lists from Facebook, Linked In, Your personal contact application, or information from a corporate or CRM application.   This Privacy Statement is part of and incorporated into the Myiceberg Conditions of Use.  This privacy statement may be modified my us at any time and we will notify you if and when this policy is changed. </div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(2)">- Your Consent to This Privacy Statement</div>
					<div class="PrivacyContent" id="PrivacyContent2" style="">&nbsp;&nbsp;&nbsp;By browsing or using Myiceberg, you are agreeing to the collection, use, and disclosure of your personal information as described in this Online Privacy Statement. If you do not consent to the collection, use, and disclosure of your personal information as described in this Online Privacy Statement, you should not use this site.</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(3)">- Questions Concerning Our Online Privacy Practices</div>
					<div class="PrivacyContent" id="PrivacyContent3" style="">&nbsp;&nbsp;&nbsp;If you have any questions or concerns regarding this Online Privacy Statement or our privacy practices, please email us. </div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(4)">- Privacy Notices</div>
					<div class="PrivacyContent" id="PrivacyContent4" style="">&nbsp;&nbsp;&nbsp;This Online Privacy Statement may be supplemented or amended from time to time by "privacy notices" posted on this site. For example, certain pages of this site contain privacy notices providing details about the ways we use or share the personal information we collect on those pages.</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(5)">- This Online Privacy Statement May Change</div>
					<div class="PrivacyContent" id="PrivacyContent5" style="">&nbsp;&nbsp;&nbsp;We reserve the right to update or modify this Online Privacy Statement, at any time and without prior notice, by posting the revised version on this site. These changes will only apply to personal information we collect after we have posted the revised Online Privacy Statement on this site. Your use of this site following any such change constitutes your agreement that all personal information collected from or about you through Myiceberg after the revised Online Privacy Statement is posted will be subject to the terms of the revised Online Privacy Statement.
To alert you to changes to this Online Privacy Statement, we will provide a notice at the top of this Online Privacy Statement for at least 30 days after the new effective date of such changes and highlight the changes so that you can locate them easily.
<br /><br />&nbsp;&nbsp;&nbsp;You may access the current version of this Online Privacy Statement at any time by clicking on the link marked "Privacy Statement" at the bottom of each page of this site. </div>
				
				
				<div class="privacyTitle" onclick="showPrivacyContent(6)">- Information you import from other sources</div>
				<div class="PrivacyContent" id="PrivacyContent6" style="">&nbsp;&nbsp;&nbsp;Myiceberg is a private platform for managing your entire network of personal and professional contacts.  Our users aggregate their friends from their contact applications as well as from the social networks they participate in such as; Facebook, Linked In and Twitter.  The information uploaded, imported or entered by each user is kept private for each user.  This information is not searchable or visible to other users of Myiceberg.  We do leverage the public information available from the various social networks to show relationships among those friends in your network.  </div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(7)">- Privacy for corporate and enterprise users</div>
					<div class="PrivacyContent" id="PrivacyContent7" style="">&nbsp;&nbsp;&nbsp;One of the common failings of enterprise CRM applications is that each user has contact information which they consider personal (private) to them that they do not wish to make available to others within their corporate enterprise.  This private information if viewable contextually to the enterprise application can produce real value to both the user and the enterprise.  The personal information of our users is not made available to the enterprises that our users work for.  This private information is available to each user outside of their enterprise CRM application and is available to our users after they may be disassociated from their enterprise CRM application. </div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(8)">- User information which we do provide to corporate or enterprises clients </div>
					<div class="PrivacyContent" id="PrivacyContent8" style="">&nbsp;&nbsp;&nbsp;Enterprises paying for Myiceberg subscriptions are entitled to know if their users are actually using the Myiceberg system.   If requested we will make information available to the enterprise so they can determine if their users are utilizing the system. Additionally, in the event we are providing services such as Compliance to a client as part of their subscription tier this compliance related information would be made available to the enterprise.  </div>
				<br />
				<div class="privacyTitle"> <u>- What Personal Information Do We Collect on Myiceberg? </u></div> <br />
				
				<div class="privacyTitle" onclick="showPrivacyContent(9)">- Information You Give Us</div>
					<div class="PrivacyContent" id="PrivacyContent9" style="">&nbsp;&nbsp;&nbsp;We collect the personal information you knowingly and voluntarily provide when you use Myiceberg, for example, the information you provide when you register, sign-in, participate in a contest or questionnaire, submit product reviews, sign up for our e-mail newsletters, or communicate with customer service. Depending on how you use this site, you may supply us with information such as your name, address, phone number, email address, or credit card information, or information about people to whom purchases will be shipped (for example, a gift recipient). 
					<br/><br/><u>&nbsp;&nbsp;&nbsp;We do not share any of the personal information you enter into Myiceberg about yourself with any other member of the Myiceberg platform unless you conduct a specific activity on our platform with friends who are members of our platform which requires your personal contact information.  In the event you conduct such activity, you will be notified prior to your information being sent to a friend.</u>
					</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(10)">- Information Sent to Us by Your Web Browser  </div>
					<div class="PrivacyContent" id="PrivacyContent10" style="">&nbsp;&nbsp;&nbsp;We may from time to time collect information that is sent to us by your Web browser, such as your IP address, the address of the Web page you were visiting when you accessed Myiceberg, the date and time of your visit, and information about your computer's operating system. Please check your browser if you want to learn what information your browser sends or how to change your settings. The information provided by your browser does not identify you personally. We use this non-personal information primarily to create statistics that help us improve this site and make it more compatible with the technology used by our visitors. However, if you have created a user identity on one of your visits to this site or if you access this site by clicking on a link in an email we have sent you, we may combine the information provided by your browser to information that identifies you personally.</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(11)">- Information We Collect Using Cookies and Other Web Technologies </div>
					<div class="PrivacyContent" id="PrivacyContent11" style="">&nbsp;&nbsp;&nbsp;We use "cookies" and other Web technologies to collect information and support certain features of this site. For example, we use these technologies to collect information about the ways visitors use our site - which pages they visit, which links they use, and how long they stay on each page. Cookies and other Web technologies are also used to support the features and functionality of our site - for example, to save your user name and password if you connect to Myiceberg.com and wish for us to "remember you". These technologies also permit us to keep a record of your previous choices and preferences so that we can personalize your experience and save you the trouble of reentering information already in our database. Based on the primary function we perform to link multiple third party sites to your personal Myiceberg account, we rely on cookies to help insure the third party accounts you connect are properly connected. </div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(12)">- Information We Collect From Other Sources</div>
					<div class="PrivacyContent" id="PrivacyContent12" style="">&nbsp;&nbsp;&nbsp;We may supplement the information collected through this site with information we collect in other ways. For example, if you email us at our customer support or other customer service email. We may also supplement the information collected through this site with information from third parties, including data brokers. By supplementing your profile with information from other sources, we are better able to provide product recommendations and special offers that will be of genuine interest to you. We may also use this additional information to further personalize our site and improve your user experience.</div>
				
				<div class="privacyTitle"onclick="showPrivacyContent(14)">- How Do We Use Your Personal Information?</div>
					<div class="PrivacyContent" id="PrivacyContent14" style="">&nbsp;&nbsp;&nbsp;We may use the personal information we collect through Myiceberg

   <br /><br />- to provide you with a personalized user experience;
   <br />- to fulfill your order for Myiceberg products or services;
   <br />- to provide you with the information, products and services you request or purchase;
   <br />- for security, credit or fraud prevention purposes; (only if you ever purchase anything through Myiceberg)
   <br />- to register your purchase with the manufacturer or service provider for warranty or similar purposes;
   <br />- to provide you with effective customer service;
   <br />- to display personalized advertising when you visit this site;
   <br />- to contact you with special offers and other information we believe will be of interest to you;
   <br />- to contact you with information and notices related to your use of this site or your purchases;
   <br />- to invite you to participate in surveys and provide feedback to us;
   <br />- to better understand the your needs and interests;
   <br />- to improve the content, functionality and usability of this site;
   <br />- to improve our products and services;
   <br />- to improve our marketing and promotional efforts; and
   <br />- for any other purpose we identify in a specific privacy notice.</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(15)">- With Whom Do We Share the Personal Information We Collect Through Myiceberg?</div>
					<div class="PrivacyContent" id="PrivacyContent15" style="">&nbsp;&nbsp;&nbsp;We may share the personal information we collect through this site as described below and in any applicable privacy notices.</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(16)">-Our Service Providers.</div>
					<div class="PrivacyContent" id="PrivacyContent16" style="">&nbsp;&nbsp;&nbsp;We may share your personal information with companies that perform services on our behalf. Our service providers are required by contract to protect the confidentiality of the personal information we share with them and to use it only to provide services on our behalf.
<br /><br />&nbsp;&nbsp;&nbsp;Other Business Partners and Selected Vendors
<br /><br />&nbsp;&nbsp;&nbsp;We work closely with certain business partners and carefully selected vendors to provide you with access to their product offerings. We may share personally identifiable information about you with these business partners or selected vendors, and they may use this information to offer you products and/or services that they believe will be of interest to you. </div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(17)">- Business Transfers </div>
					<div class="PrivacyContent" id="PrivacyContent17" style="">&nbsp;&nbsp;&nbsp;Information about your friends and contacts which you have uploaded or imported remains your information and may be deleted from the Myiceberg system if you so choose. 
<br /><br />&nbsp;&nbsp;&nbsp;Your user information submitted to Myiceberg is an asset of Myiceberg and will become part of our normal business records. Your personal information may be transferred to another company (either a corporate affiliate or an unrelated third party) that has acquired substantially all the stock or assets of Myiceberg, for example, as the result of a sale, merger, reorganization, dissolution or liquidation. Myiceberg shall provide notice of such transfer by sending you an email to the email address in your Myiceberg account settings.   </div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(18)">- Government and Legal Disclosures </div>
					<div class="PrivacyContent" id="PrivacyContent18" style="">&nbsp;&nbsp;&nbsp;We may disclose the personal information we collect through Myiceberg when we, in good faith, believe disclosure is appropriate to comply with the law (or a court order or subpoena); to prevent or investigate a possible crime, such as fraud or identity theft; to enforce the Terms of Use or other agreements that govern your use of this site; to protect the rights, property or safety of Myiceberg, our users or others; or to protect your vital interests. If you have purchased products from us on extended payment terms, it may be necessary for us to file certain information about you with state or local government offices to perfect our security interest in those products or to provide certain information about you to collection agencies in the event of non-payment or a payment dispute.</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(19)">- What Choices Do You Have?</div>
					<div class="PrivacyContent" id="PrivacyContent19" style="">&nbsp;&nbsp;&nbsp;Myiceberg respects your right to make choices about the ways we collect, use and disclose your personal information. We generally ask you to indicate your choices at the time that you register as a user of Myiceberg or the time and on the page where you provide your personal information. You may view cards or invitations from Myiceberg without providing any information that identifies you personally and may choose not to provide the information we request, although this may mean that you cannot make a purchase or take advantage of other Myiceberg features. When you register as a Myiceberg user, you may start to receive information and product promotions periodically via e-mail. You may "opt-out" of receiving our promotional e-mails at any time by following the instructions contained at the end of the particular newsletter or e-mail. You may also manage your subscription and email preferences within your user settings or by sending us an email. To send us an email, please click <here>.<Launch email client and populate the to field with Support@myiceberg.com></div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(20)">- How Can You Update or Correct Your Personal Information?</div>
					<div class="PrivacyContent" id="PrivacyContent20" style="">&nbsp;&nbsp;&nbsp;You may update your "account settings" information, including your name, contact information, credit card information, and subscription and email preferences at any time by clicking on "Settings" at Myiceberg. You can change your account settings at any time and as often as necessary. </div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(21)">- Security</div>
					<div class="PrivacyContent" id="PrivacyContent21" style="">&nbsp;&nbsp;&nbsp;Myiceberg has industry standard measures in place to protect the security of the personal information we collect through Myiceberg. For example, we use Secure Socket Layers ("SSL") to encrypt your information as it travels over the Internet. It is important that you understand, however, that no Web site or database is completely secure or "hacker proof." You are also responsible for taking reasonable steps to protect your personal information against unauthorized disclosure or misuse, for example, by protecting your passwords.</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(22)">- Safety of Credit Card Transactions</div>
					<div class="PrivacyContent" id="PrivacyContent22" style="">&nbsp;&nbsp;&nbsp;In the event you conduct an activity with Myiceberg requiring payment, Under the Fair Credit Billing Act, your bank cannot hold you liable for more than $50.00 of fraudulent credit card charges. If your bank does hold you liable for any of this $50.00, Myiceberg will cover the entire liability for you, up to the full $50.00. We will only cover this liability if the unauthorized use of your credit card resulted through no fault of your own from purchases made while using Myiceberg. In the event of unauthorized use of your credit card, you must notify your credit card provider in accordance with its reporting rules and procedures.</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(23)">- Links to Other Web Sites</div>
					<div class="PrivacyContent" id="PrivacyContent23" style="">&nbsp;&nbsp;&nbsp;Myiceberg contains links to other Web sites that are owned and operated by unrelated third parties. This Online Privacy Statement applies only to personal information collected through Myiceberg. It does not apply to personal information collected on any other Web site operated by any third party. These Web sites have their own policies regarding privacy. We recommend you review the privacy policy posted on any site you visit before using the site or providing any personal information about yourself.
<br /><br />&nbsp;&nbsp;&nbsp;Our connection to these sites is available as is granted to Myiceberg by the terms and conditions of each specific site and only as they have enabled for other third parties.  There is no guarantee our access is identical to other parties connecting to these sites.  Our ability to conduct activity with these third party sites is at their discretion and is made available subject to this permission being granted by the third party site. This permission may be revoked by each third party site at their discretion.
<br /><br />&nbsp;&nbsp;&nbsp;Some third party sites we connect to may require financial compensation. If we require compensation you will be properly informed.  </div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(24)">- Children and Minors</div>
					<div class="PrivacyContent" id="PrivacyContent24" style="">
					&nbsp;&nbsp;&nbsp;Myiceberg is not directed at nor targeted to children. We do not use this site to knowingly solicit personal information from or market to children. If you are under 13 years of age, you should not submit any personal information to us.

<br /><br />&nbsp;&nbsp;&nbsp;If you are under age 13, please do not attempt to register for Myiceberg or provide any personal information about yourself to us. If we learn that we have collected personal information from a child under age 13, we will delete that information as quickly as possible. If you believe that we might have any information from a child under age 13, please contact us at email, please click here.<Launch email client and populate the to field with Support@myiceberg.com>

<br /><br />&nbsp;&nbsp;&nbsp;We strongly recommend that minors 13 years of age or older ask their parents for permission before sending any information about themselves to anyone over the Internet and we encourage parents to teach their children about safe internet use practices.
					</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(25)">- About email addresses in Myiceberg  </div>
					<div class="PrivacyContent" id="PrivacyContent25" style="">
					&nbsp;&nbsp;&nbsp;Your privacy is important to us so here is what you should know about email address and how we treat them.
		<br /><br />We never give out or expose your email address or any email addresses of friends and contacts that you upload or import into Myiceberg. We do not send these to Facebook or any other social network.
		<br /><br />Myiceberg users are allowed to have more than one email address in Myiceberg.  Some of your friends may know you by your personal email address (likely the one you use in Facebook) some of your friends/acquaintances may know you by your work email address.  We do not display any email addresses you have entered into Myiceberg or the ones your friends may have uploaded for you.
		<br /><br />When you set up your Myiceberg account it is best if you enter and validate all of the active email addresses you have.  This will eliminate the need for you to combine accounts in the future when someone sends a Myiceberg card to you at that email address.
		<br /><br />We will send an email to you or your friends under the following conditions:
		<br /><br />You perform some function on our site which specifically notifies you that you are sending a message to a friend or a list of friends.        
		<br /><br /> You receive a card in Myiceberg and it was sent from a user who uploaded your email address and you have not yet activated your Myiceberg account to set your message settings.
        <br /><br /> You are verifying an email address in the Myiceberg system.
        <br /><br /> You are merging or combining albums associated with different accounts.
        <br /><br /> We are making changes to the Myiceberg service that you should know about.
        <br /><br /> We are confirming an action you have taken within the Myiceberg service.
        <br /><br /> We discover something about your Myiceberg account we need to notify you about.
		<br /><br /> When you subscribe to a service tier which produces alert emails.
        <br /><br /> In the event we eventually have the capability to send you a message from one of our sponsors or partners, we will first ask you for permission to do this. 
					</div>
				
				<div class="privacyTitle"onclick="showPrivacyContent(26)">- About using your information within Myiceberg for activity within third party sites </div>
					<div class="PrivacyContent" id="PrivacyContent26" style="">&nbsp;&nbsp;&nbsp;Privacy is very important to us. Especially with the experience we are trying to create for both senders of cards and recipients of cards and event organizers and invitees.
<br /><br/>With your permission, we may post to a social media site which you connect your Myiceberg account to, that you have started using Myiceberg.  We will not post on your Third party social media site without your specific request and approval for each instance, who you have sent a card or invitation to so we respect your ability to use Myiceberg without any other users knowing who you are sending cards to, other than the card recipient. 
<br /><br/>We will utilize the most frictionless method allowable by the various third party sites to inform users on their sites that you have sent them a card or invitation.   </div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(27)">- Privacy within your Myiceberg albums  </div>
					<div class="PrivacyContent" id="PrivacyContent27" style="">&nbsp;&nbsp;&nbsp;Once you have received a card/picture from a friend into your Myiceberg album, you are free to privately manipulate that picture as you wish, by leaving it in your album, hiding or deleting it.  No sender will be able to know how you have treated their picture.</div>
				
				<div class="privacyTitle" onclick="showPrivacyContent(28)">- E-commerce check out and credit card information </div>
					<div class="PrivacyContent" id="PrivacyContent28" style="">&nbsp;&nbsp;&nbsp;We have chosen to leverage PayPal as our check out Partner.  In addition to the major credit cards, PayPal accepts your PayPal account.  This PayPal payment platform is secure as any other we investigated and gives our users the most flexibility.  We do not store any user credit card information in Myiceberg.  All user credit card information is entered in directly at, and stored at PayPal.  </div>
				
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