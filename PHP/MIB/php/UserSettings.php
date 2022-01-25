<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css" />
<link rel="stylesheet" href="../css/UserSettings.css" type="text/css" />
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<title>User Settings</title>
</head>

<body>
<div id="headbar">
	<div id="logohold">
		<div id="logo"><img src="../img/login/mib_temp.png"></div>
	</div>
	<a class="a_noshow" >
		<span class="head_link">Friends</span>
	</a>
		<span class="head_link">News</span>
		<span class="head_link">Events</span>
		<span class="head_link">Albums</span>
	<div id="userhold">
		<div id="usercont">
			<div id="avatar" ><img src="../img/login/mat.jpg"></div>
			<div id="userinfo">
				
				<span id="username">John Rodney</span>
					<select id="selectAccount">
						<option value="My account">My account</option>
						<option value="Settings/Profile">Settings/Profile</option>
						<option value="Logout">Logout</option>
					</select>
			</div>
		</div>
	</div>
	
</div>
<div id="mainbody" >
	<div id="User">
			<div class="UsertitleLine">
			<div class="Usertitle">User Information</div> 
				<div class="aboutInfo">(<a href="www.aboutUserInfo.com"><u>about User Info</u></a>) </div>
				<div id="smallicon"> <img src="../img/login/Edit.png">  Edit</div>
				<div id="minus" ><img src="../img/login/-.png"></div>
			</div>	
			
		<div class="leftSide">

			<div class="infoTabLeft"> 
				<div class="infoTab2">First Name: </div><div class="infoTab3">John</div>
			</div>
			
			<div class="infoTabLeft"> 
				<div class="infoTab2">Middle Name: </div><div class="infoTab3">Rodney</div>
			</div>
			
			<div class="infoTabLeft"> 
				<div class="infoTab2">Last Name: </div><div class="infoTab3">Gabos</div>
			</div>
			
			<div class="infoTabLeft"> 
				<div class="infoTab2">Display Name: </div><div class="infoTab3">John Gabos</div>
			</div>
			
			<div class="infoTabLeft" > 
				<div class="infoTab2" id="infoTabWhitSelect">I am: </div>
					<div class="infoTab3" id="dropMeny">
						<select id="select2">
							<option value="Male">Male</option>
							<option value="Female">Female</option>
						</select>
					</div>
			</div>
			
			<div class="infoTabLeft"> 
				<div class="infoTab2">Date of Birth: 1/21/1959</div><div class="infoTab3"><img src="../img/login/calendar.png" id="calendarImage"></div>
			</div>
		</div>
		
		<div class="rightSide">

			<div class="infoTabRight"> 
			
				<div class="infoTab2">Primary Phone: </div><div class="infoTab3">612.456.1234</div>
			</div>
		
		

			<div class="infoTabRight"> 
				<div class="infoTab2">Phone 2: </div><div class="infoTab3">952.473.1234</div>
			</div>

		

			<div class="infoTabRight"> 
				<div class="infoTab2">Company:</div> <div class="infoTab3">Myiceberg</div>
				<div class="infoTab2">Title:</div> <div class="infoTab3">President</div>
			</div>

		

			<div class="infoTabRight"> 
				<div class="infoTab2">Address 1:</div> <div class="infoTab3">2629 Northview Rd</div> <br />
				<div class="infoTab2">City:</div> <div class="infoTab3">Wayzata</div>
				<div class="infoTab2">State:</div> <div class="infoTab3">MN</div>
				<div class="infoTab2">Zip:</div> <div class="infoTab3">55391</div>
			</div>
			
			<div class="infoTabRight"> 
				<div class="infoTab2">Address 2:</div> <div class="infoTab3">1850 West Wayzata Blvd</div> <br />
				<div class="infoTab2">City:</div> <div class="infoTab3">Long Lake</div>
				<div class="infoTab2">State:</div> <div class="infoTab3">MN</div>
				<div class="infoTab2">Zip:</div> <div class="infoTab3">55356</div>
			</div>
		</div>

	</div>
	
	
	<div class="EmailList">
		<div class="EmailTitleLine">
			<div id="EmailTitte">MIB User Email Accounts</div>	
			<div class="aboutInfo">(<a href="www.aboutUserInfo.com"><u>about Email</u></a>) </div>
			<div class="EmialLineField1">Email Status</div>
			<div class="EmialLineField2">Notification Email</div>
			<div id="minus" ><img src="../img/login/-.png"></div>
		</div>
		
		
		<div id="EmailDetail"> 
			<div class="EmailInfo">
				<div class="Email1">Primary Email:</div><div class="Email2">jgabos@myiceberg.com</div>
			</div>
			<div class="EmailStatus" >Verified</div><div class="EmailCheckbox"><input type="radio" /></div>
		</div>
		
		<div id="EmailDetail"> 
			<div class="EmailInfo">
				<div class="Email1">Email 1:</div><div class="Email2">jgabos@yahoo.com</div>
			</div>
			<div class="EmailStatus">Pending</div><div class="EmailCheckbox"><input type="radio" /></div>
		</div>
		
		<div id="EmailDetail"> 
			<div class="EmailInfo">
				<div class="Email1">Email 2:</div><div class="Email2">jgabos@gmail.com</div>
			</div>
			<div class="EmailStatus">Unverified</div><div class="EmailCheckbox"><input type="radio"/></div>
		</div>
		
		<div id="EmailDetail"> 
			<div class="EmailInfo">
				<div class="Email1">Email 3:</div><div class="Email2"></div>
			</div>
			<div class="EmailCheckbox"></div>
		</div>
		
		<div id="EmailDetail"> 
			<div class="EmailInfo">
				<div class="Email1">Email 4:</div><div class="Email2"></div>
			</div>
			<div class="EmailCheckbox"></div>
		</div>
		
		<div id="EmailDetail"> 
			<div class="EmailInfo">
				<div class="Email1">Email 5:</div><div class="Email2"></div>
			</div>
			<div class="EmailCheckbox"></div>
		</div>
		
		<div id="AddEmail">
			Additional Emails <div><input type="text" id="inputEmail"/></div>
		</div>
		
		<div id="Buttons" > 
			<button id="AddEmailButton" value="AddEmail">Add Email</button> 
			<div id="CancelAndSaveButton">
				<button class="CancelButton" value="Cancel">Cancel</button>
				<button class="CancelButton" value="Save">Save</button> 
			</div>
		</div>
	</div>
	
	<hr />
	
	<div id="UserAccountInfo"> 
		<div class="UsertitleLine">
			<div class="Usertitle">User Account Information </div> 
			<div class="aboutInfo">(<a href="www.aboutUserInfo.com"><u>about Acct Info</u></a>) </div>
			<div id="minus" ><img src="../img/login/-.png"></div>
		</div>	

		<div class="infoAccountTab"> 
			<div class="infoTab2">User Name: </div><div class="infoTab3">jgabos@myiceberg.com</div> 
		</div> 
		
		<div class="infoAccountTab2">
		<div class="infoAccountTab"> 
			<div class="infoTab2">Password: </div><div class="infoTab3">password</div>
		</div>	<button class="ResetPassword" value="Save">Reset Password</button> 
		</div>

		
		<div class="infoAccountTab2">
		<div class="infoAccountTab" > 
			<div class="infoTab2" id="infoTabWhitSelect">Account Type: </div>
				<div class="infoTab3" id="dropMeny">
					<select id="select2">
						<option value="Personal">Personal</option>
						<option value="Personal">Business</option>
						<option value="Personal">Professional</option>
					</select>
				</div>
		</div> <button class="ResetPassword" value="Save">Upgrade Account</button> 
		</div>
		
		<div class="EmailTitleLine">
			<div id="EmailTitte">MIB Account Connections</div>	
		</div>
		
		<div class="accountConnections">
			<div class="accountConnections2">Your MIB account is <u><b>currently connected</b></u> with: 
				<img src="../img/logos/facebook.png" height="30px"  width="30px"/>
				<img src="../img/logos/google+.png" height="30px"  width="30px"/>
				<img src="../img/logos/likedin.png" height="30px"  width="30px"/>
				<img src="../img/logos/twitter.png" height="30px"  width="30px"/>
				<img src="../img/logos/salesforce.png" height="30px"  width="30px" />
			</div>
			<div class="accountConnectionsDecrpition">(select application to disconnect your MIB account)</div>
		</div>
		
		<div class="accountConnections">
			<div class="accountConnections2">Select application <u><b>currently connected</b></u> your MIB account:
				<img src="../img/logos/facebook.png" height="27px"  width="27px"/>
				<img src="../img/logos/google+.png" height="27px"  width="27px"/>
				<img src="../img/logos/likedin.png" height="27px"  width="27px"/>
				<img src="../img/logos/twitter.png" height="27px"  width="27px"/>
				<img src="../img/logos/salesforce.png" height="27px"  width="27px" />
			</div>
			<div class="accountConnectionsDecrpition">(available applications to connect your MIB account)</div>
		</div>
		
		<div class="EditConnectionsInfo" >
			To edit the permission settings for your Myiceberg account
			connected with external accounts please select the icon.
		</div>
		
		<div id="CancelAndSaveButton2">
			<button class="CancelButton" value="Cancel">Cancel</button>
			<button class="CancelButton" value="Save">Save</button> 
		</div>
	</div>
	
	<hr style="margin-top:100px;" />
	
	<div id="NotificationSettings">
		<div class="Usertitle">Notification Settings</div> 
			<div class="aboutInfo">(<a href="www.aboutUserInfo.com"><u>about Notifications</u></a>) </div>
			<div id="smallicon"> <img src="../img/login/Edit.png">  Edit</div>
			<div id="minus" ><img src="../img/login/-.png"></div>
		
		
		<div id="NotificationBody">
			Social Monitoring Notification Frequency 
			
			<select id="select3">
				<option value="Personal">Daily</option>
				<option value="Personal">Every 2 Days</option>
				<option value="Personal">Every 2 Days</option>
				<option value="Personal">Weekly</option>
				<option value="Personal">None</option>
			</select>
		</div>	
		
		<div id="CancelAndSaveButton3">
			<button class="CancelButton" value="Cancel">Cancel</button>
			<button class="CancelButton" value="Save">Save</button> 
		</div>
	</div>
	
</div>

<div id="footer">
	<img id="logo" src="../img/logos/mib.png" width="50px" height="50px;">
	<div id="footerinfo">
	<div id="myicebergLink">www.myiceberg.com</div>
	<div id="AboutUs">About Us </div> <div id="AboutUs"> Pricing </div> <div id="AboutUs">Privacy Policy</div> 
	<div id="AboutUs"><a href="<?php echo $config['root']; ?>footer_pages/TermsOfUse.php">Terms Of Use</a></div>
	
	</div>	
	
	<div id="RightsReserved">© 2012 Myiceberg LLC. All Rights Reserved</div>
</div>

</body>
</html>

















