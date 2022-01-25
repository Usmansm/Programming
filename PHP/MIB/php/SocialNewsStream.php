<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../css/SocialNewsStream.css" type="text/css" />
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<title>Social/News Stream</title>
</head>

<body>
<div id="body">
	<div id="mainmenu">
		<div class="mainmenuItem" id="FriendDetail"><a href="FirendDeteail.php">Firend Detail</a></div>
		<div class="mainmenuItem" id="SocialNewsStream"><a href="SocialNews.php">Social/News Stream</a></div>
		<div class="mainmenuItem" id="SocialKeyword"><a href="SocialKeyword.php">Social Keyword</a></div>
	</div>
	
	<div id="mainbody">
	
			<div id="topMeny">
				<div id="SocialStream">
					
					<div id="SocialStreamText"> Social Stream </div>
					
					<div id="FBandLIbuttons"> 
						
							<input type="radio" id="FBandLi" name="facbook" value="facbook">
							<img src="../img/login/fbTiny.png" alt="facebook" width="22px;" height="21px" /> 
						
							<input type="radio" id="FBandLi" name="li" value="li"> 
							<img src="../img/login/liTiny.png" alt="Li" width="22px;" height="21px" />
						
					</div>
					
					<div id="twoIcons"> 
						<div id="calnedar"><img src="../img/login/Calendar_Tiny.png" alt="Calendar" width="23px;" height="23px" /> </div>
						<img src="../img/login/Edit_Tiny.png" alt="Edit" width="27px;" height="27px" />
					</div>
				</div>
			
				<div id="NewsStream">
					<div id="NewsStreamText"> NewsStream </div>
					
					<div id="NewsStreamRadio"> <input type="radio" name="NewsStream" value="NewsStream"> </div>
					
					
					<div id="twoIcons"> 
						<div id="calnedar"><img src="../img/login/Calendar_Tiny.png" alt="Calendar" width="23px;" height="23px" />  </div>
						<img src="../img/login/Edit_Tiny.png" alt="Edit" width="27px;" height="27px" />
					</div>
				</div>
			</div>
		
		
		<div id="News">
			<div id="LeftSide">
				<div id="date">Feb 21, 2013</div>
				
				<div id="message">
				<img src="../img/login/fbTiny.png" alt="facebook" width="22px;" height="21px" id="LeftSideIcon" />
				<p>Chris Johnson posted to John Doe: "Congratulations on your daughter Suzy's birthday"</p>
				</div> 

				<hr id="hrLeft" />
				
				<div id="date">Feb 25, 2013</div>
				
				<div id="message">
				<img src="../img/login/liTiny.png" alt="linkedIn" width="22px;" height="21px" id="LeftSideIcon" />
				<p>Chris Johnson posted to John Doe: "Congratulations on your daughter Suzy's birthday"</p>
				</div>
				
				<div id="message">
				<img src="../img/login/fbTiny.png" alt="facebook" width="22px;" height="21px" id="LeftSideIcon" />
				<p>Chris Johnson posted to John Doe: "Congratulations on your daughter Suzy's birthday"</p>
				</div> 
				
				<div id="message">
				<img src="../img/login/liTiny.png" alt="linkedIn" width="22px;" height="21px" id="LeftSideIcon" />
				<p>Chris Johnson posted to John Doe: "Congratulations on your daughter Suzy's birthday"</p>
				</div> 
				
			</div>
			
			<div id="RightSide">

				<div id="date">Feb 14th, 2013</div>
					<div id="message">
					<img src="../img/login/MIB_Tiny.png" alt="MIB" width="32px;" height="26px" id="RightSideIcon" />
					<p>MyIceberg announces the appointment of John Doe as CIO</p> 
				</div> 
				<div id="StarTribune">Star Tribune </div>
				<img src="../img/login/Trash_Tiny.png" alt="Trash" width="16px;" height="18px" id="trashicon" /> 
				<hr id="hrRight" />
				
				<div id="date">May 4th, 2013</div>
					<div id="message">
					<img src="../img/login/Newspaper_Tiny.png" alt="News" width="39px;" height="27px" id="RightSideIcon"/>
					<p>Zebra muscles found in Lake Minnetonka</p> 
				</div> 
				<div id="StarTribune">Star Tribune </div>
				<img src="../img/login/Trash_Tiny.png" alt="Trash" width="16px;" height="18px" id="trashicon" /> 
				<hr id="hrRight" />
				
				<div id="date">Feb 14th, 2013</div>
				<div id="message">
					<img src="../img/login/NewspaperReplyed_Tiny.png" alt="NewsReplyed" width="37px;" height="31px" id="RightSideIcon"/>
					<p>Cloud based Applications easy to scale</p> 
				</div> 
				<div id="StarTribune">Tecdaily</div>
				<img src="../img/login/Trash_Tiny.png" alt="Trash" width="16px;" height="18px" id="trashicon" /> 
				<hr id="hrRight" />
				
				<div id="date">Feb 14th, 2013</div>

				<div id="message">
					<img src="../img/login/MIB_Tiny.png" alt="MIB" width="32px;" height="26px" id="RightSideIcon" />
					<p>Tony Clevland announces his resingnations from Carlson Travel</p> 
				</div> 
				<div id="StarTribune">Travel Weekly</div>
				<img src="../img/login/Trash_Tiny.png" alt="Trash" width="16px;" height="18px" id="trashicon" /> 
				<hr id="hrRight" />
				
				<div id="date">Feb 14th, 2013</div>
				<div id="message">
					<img src="../img/login/Newspaper_Tiny.png" alt="News" width="39px;" height="27px" id="RightSideIcon"/>
					<p>Carlson travel awarded $2 billion GSA contract</p> 
				</div> 
				<div id="StarTribune">Star Tribune </div>
				<img src="../img/login/Trash_Tiny.png" alt="Trash" width="16px;" height="18px" id="trashicon" /> 
				<hr id="hrRight" />
			</div> 
		</div>
	</div>
</div>


</body>
</html>