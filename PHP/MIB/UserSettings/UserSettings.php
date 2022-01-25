<?php
    /*mysqli connection dddd*/
    session_start();
    $UserId =  $_SESSION["userId"];
    require_once('../config/config.php');
    require_once('../php/detail.class.php');
    require_once('UserSettinsProc.php');
    
    $UdetailsPrivate = array("firstName","middleName","lastName" , "displayName");
    $UdetailstPublic = array ("userPhoneCell", "userPhoneHome","userPhoneOffice", "addressOne", "cityOne", "stateOne", "zipOne" , "countryOne", "addressTwo" , "cityTwo" , "stateTwo" , "zipTwo" , "countryTwo" , "bornOn" , "companyName", "companyTitle");
    
    // Full texts   	phoneOne 	phoneTwo 	addressOne 	cityOne 	stateOne 	zipOne 	countryOne 	addressTwo 	cityTwo 	stateTwo 	zipTwo 	countryTwo 	bornOn 	companyName 	companyTitle 
    
    $detail = new detail; 
    
    $data = $detail->dataElement($UserId, false, $UdetailsPrivate, $UdetailstPublic);
    //print_r ($data);
    $datapublic = $detail->dataElementPublic($UserId, $UdetailstPublic);
    //print_r ($datapublic);
    
    $mysql = new mysqli($config['host'], $config['user'], $config['pass'],$config['db']);
    if(mysqli_connect_errno()) {
    die("Connect failed: \n".mysqli_connect_error());
     }
    $queryForCountry = "SELECT * FROM country_name";
    $resultForCountry = $mysql->query($queryForCountry);
   
    //$Countryid=0;
    //Date of Birth: Phone:Company: Title: Address: City: State: Zip
    //$result = $mysqli->query('SELECT firstName FROM user_detail_private WHERE userId = 1'); 
    
    //$email = $mysqli->query("SELECT * FROM user_email WHERE userId = '275'"); 
    //$emailresult = $email->fetch_assoc(); 
    
    function MIB_Current_Con($Uid){
      global $config;
      $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
      
      $result = mysqli_query($mysqli,"SELECT * FROM user_external_accnt WHERE userId = '".$Uid."'");
      
       while ($row = $result->fetch_assoc()) {
			//var_dump($row['authProvider']);
			//var_dump();
			if ($row["authProvider"] == 'salesforce' ){
				echo '<img onClick="disassociateSF()" src="../img/logos/'.$row["authProvider"].'.png" height="27px"  width="27px" style="margin-left: 5px;margin-bottom: -7px;"/>';
			} else {
				echo '<img src="../img/logos/'.$row["authProvider"].'.png" height="27px"  width="27px" style="margin-left: 5px;margin-bottom: -7px;"/>';
			}
	   }
      
      
    }
    
    function MIB_available_conn($Uid){
      global $config;
      
      $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
      
      $result = mysqli_query($mysqli,"SELECT * FROM tech_partners ");
      
      while($row = $result->fetch_assoc()){
        
        
        $result2 = mysqli_query($mysqli,"SELECT * FROM user_external_accnt WHERE authProvider = '".$row['partnerName']."' AND userId='". $_SESSION["userId"] ."'");
        $row2 = $result2->fetch_assoc(); 
        
       
        if ($row2 == NULL){
            $evernoteURL= '';
            if ($row['partnerName'] == 'evernote' ){
                $evernoteURL = 'href="'.$config['root'].'/friends/ex_inc/evn_authw.php"';
            }else{
				$evernoteURL = "href='#'";
			}
			
			if ($row['partnerName'] == 'facebook' ){
				$onclickvar = 'onClick="check5(\'facebook\')"';
			}elseif ($row['partnerName'] == 'linkedin' ){
				$onclickvar = 'onClick="check5(\'linkedin\')"';
			}else{
				$onclickvar = '';
			}
			
            echo '<a '.$evernoteURL.' class="a_noshow" > <img style="margin-left: 5px;margin-bottom: -7px;" src="../img/logos/'.$row["partnerName"].'.png" height="27px"  width="27px" '.$onclickvar.'"/></a> ';
            //echo  '<a href="" class="" > <img src=".../img/logos/evernote.png" height="27px"  width="27px" /> qweqweqweqwe</a> ';
            
        }
      }
      		/*Code for displaying salesforce icon*/
		$result3 = mysqli_query($mysqli,"SELECT * FROM user_external_accnt WHERE userId='". $_SESSION["userId"]."' AND authProvider = 'salesforce'");
       // $row3 = $result3->fetch_assoc(); 
		//var_dump($result3);
		if ($result3->num_rows == NULL){
			echo '<a href="'. $config['root']. 'resttest/proxy_import.php"><img  src="../img/logos/salesforce.png" height="27px" width="27px" style="margin-left: 5px;margin-bottom: -7px;" style="margin" /></a>';
		}
		$resultLI = mysqli_query($mysqli,"SELECT * FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."' AND authProvider = 'linkedin'");
		//var_dump($resultLI);
		if ($resultLI->num_rows == NULL){
			echo '<a onclick="check5(\'linkedin\')" ><img src="../img/logos/linkedin.png"height="27px" width="27px" style="margin-left: 5px;margin-bottom:-7px;" style="margin" alt="LInkedin" /></a>';
		}
		
    }
    
    /*Function ofr displaying Firstname in User Account Information part*/
    function Display_User_info($Uid){
        global $config;
		 $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
        $UserName = $mysqli->query("SELECT * FROM user_email WHERE userId = '".$Uid."' AND EmailStatus = 'verified'");
        
        $row = $UserName->fetch_assoc(); 
        
        echo $row['emailAddr'];
    }
	
	function DisplayAccountType($UserId){
		global $config;
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);

		$AccType = $mysqli->query("SELECT * FROM users WHERE userId = '".$UserId."'");
		$row = $AccType->fetch_assoc(); 
		  
		echo $row['accountType'];
		
	}
	
	/* CODE FOR GETING DARA FOR Social KEyword Section*/
	 $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
	$FirstTime = $mysqli->query("SELECT * FROM user_monitor_terms WHERE userId = '".$UserId."'");
    $FirstTime = $mysqli->affected_rows;
    $i= 1;
    $test = '';
	
	if ($FirstTime == 0){
		$Term = array ("Birthday","Anniversary","Engagement","Graduation","Congratulations","Moving","Sorry","Job","College","Wedding","Golf","Wine","Minnesota","Hockey","Travel","Add new term","Add new term","Add new term","Add new term","Add new term");
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

<link rel="stylesheet" href="../css/jquery-ui-1.10.2.custom.min.css">
<div id="mainbody">
    <div id="contentContainer" >
    	<div id="User">
    		<div class="part_title">
            <span class="UserSettingsPartTitletext">User Information</span>
    				<span class="aboutInfo">(<a href="#" onclick="PromptNonExistPageModal('About User Info','About User Info feature coming soon')"><u>About User Info</u></a>) </span>
    				<span id="smallicon" ><a href='#' class='detail_edit_button'  onclick="EditUsettings(<?php echo $UserId ?>)" id="UinfoEditbutton">Edit</a></span>
    				<span id="minus" ><img id="UInfominus" onclick="hideUserInfo()" src="../friends/images/-.png"></img></span>
    			</div>	
    			
    		<div class="UserPart_infol">
    			<div class="part_infotext"> 
    				<div class="Upart_infotextb">First Name: <span class="ffname" id="FirsteName"><?php echo $data['firstName']; ?></span></div>
    			</div>	
    			<div class="part_infotext"> 
    				<div class="Upart_infotextb">Middle Name: <span class="ffname" id="MiddleName"><?php echo $data['middleName']; ?></span></div>
    			</div>	
    			<div class="part_infotext"> 
    				<div class="Upart_infotextb">Last Name: <span class="ffname" id="lastname"><?php echo $data['lastName']; ?></span></div>
    			</div>	
    			<div class="part_infotext"> 
    				<div class="Upart_infotextb">Display Name: <span class="ffname" id="DisplayName"><?php echo $data['displayName']; ?></span></div>
    			</div>  
          <div class="part_infotext"> 
        		<div class="Upart_infotextb">
                Date of Birth: <span id='_birthday' ></span> <?php echo $datapublic['bornOn']; ?></span><!--<img id="calendarImage" src="../img/login/Calendar.png" />-->
                
                <style type='text/css'>
                  #dateLance {
                    z-index: 10000;
                    position: relative;
                    background-color: white;
                    margin: 0;
                    width: 250px;
                    height: 250px;
                    float: left;
                    
                    -webkit-box-shadow: 0 1px 5px #555;
                    -moz-box-shadow: 0 1px 5px #555;
                    box-shadow: 0 1px 5px #555;
                  }
                  
                  div.pika-single {
                    width: 250px;
                    padding: 10px;
                    font-size: 0.8em;
                    text-align: center;
                    border: 1px solid #ccc;
                    background-color: #f6f6f6;
                    
                    -webkit-box-shadow: 0 1px 7px #999;
                    -moz-box-shadow: 0 1px 7px #999;
                    box-shadow: 0 1px 7px #999;
                  }
                  
                  div.pika-single:hover {
                    border: 1px solid #aaa;
                    
                    -webkit-box-shadow: 0 1px 10px #777;
                    -moz-box-shadow: 0 1px 10px #777;
                    box-shadow: 0 1px 10px #777;
                  }
                  
                  table.pika-table {
                    text-align: justify;
                  }
                  
                  .is-hidden {
                    display: none;
                  }
                </style>
                
               <!-- <input type='text' id="myDate" >
                <script type="text/javascript" src="moment.min.js"></script>
                <script type="text/javascript" src="pikaday.js"></script>
                <script type="text/javascript">
                  //var field = document.getElementById('myDate');
                  var picker = new Pikaday({
                    field: document.getElementById('myDate'),
                    yearRange: [1900,2013],
                    onSelect: function(){
                      $('div.pika-single').blur();
                      //$('input#myDate').val(picker.toString());
                      //picker.hide();
                      //picker.adjustPosition();
                    }
                  });
                  //field.parentNode.insertBefore(picker.el, field.nextSibling);
                  
                  $(document).ready(function(){
                    $('input#myDate').bind("change paste keyup", function(){
                      var myDate = $('input#myDate').val();
					  //alert(myDate);
                      $.post("ex_inc/update-birthday.php", { 'birthday': myDate }, function(data){
                        $('span#_birthday').html(data);
                      });
                      /*
                      $.ajax({
                        url: "ex_inc/update-birthday.php",
                        method: 'POST',
                        data: { 'birthday' : myDate },
                        success: function(data){
                          $('span#_birthday').html(data);
                        }
                      });
                      */
                    });
                  });
                </script>-->
                
            </div>
    			</div>
    	  </div>
    		
    		<div class="UserPart_infor"> 
    			<div class="part_infotext">
                    <span class="Upart_infotextb">Phone: <span class="ffname" id="UserPhone"><?php echo $datapublic['userPhoneCell']; ?></span>
                        <select class="detail_drop" id="PhoneSelect" onchange="AddressAndPhoneDropDown('Phone', <?php echo $UserId ?>)";>
                            <option value="Cell">Cell</option>
                            <option value="Home">Home</option>
                            <option value="Office">Office</option>
                        </select>
    			</div>
    
    			<div class="part_infotext"> 
    				<div class="Upart_infotextb">Company: <span class="ffname" id="UserCompany"><?php echo $datapublic['companyName']; ?></span></div>
    			</div>
                <div class="part_infotext">
                   <div class="Upart_infotextb">Title: <span class="ffname" id="UserTitle"><?php echo $datapublic['companyTitle']; ?></span></div>
    		    </div>
    			<div class="part_infotext">                         
                        <select class="detail_drop" id="AddressSelet" onchange="AddressAndPhoneDropDown('Address',<?php echo $UserId ?>)";>
                            <option value="1">Address 1</option>
                            <option value="2">Address 2</option>
                        </select>
    			    <div class="Upart_infotextb" id="AddressHolder">Address: <span class="ffname" id="UserAddress"><?php echo $datapublic['addressOne']; ?></span><br />
        			    <span class="UTitle">City:</span><span class="ffname" id="UserCity"><?php echo $datapublic['cityOne']; ?></span>
          				<span class="UTitle">State:</span> <span class="ffname" id="UserState"><?php echo $datapublic['stateOne']; ?></span><br />
          				<span class="UTitle">Zip:</span> <span class="ffname" id="UserZip"><?php echo $datapublic['zipOne']; ?></span> 
                  <span class="UTitle">Country:</span><span class="ffname" id="UserCountry"><?php echo $datapublic['countryOne']; ?></span>
              </div>
                    <select id="Country_drop">
                    <?php while ($row = $resultForCountry->fetch_assoc()){
                        echo "<option value=".$row['countryName'].">" . $row['countryName'] . "</option>";
                        $Countryid= $Countryid +1;
                    } ?>
                    </select>
    			</div>
    			
    		</div>
    
    	</div>
    	
    	<div id="UserEmailInfo">
            <div class="part_title">
                <span class="UserSettingsPartTitletext">MIB User Email Accounts</span>
        			<span class="aboutInfo">(<a href="#" onclick="PromptNonExistPageModal('About Email','About Email feature coming soon')"><u>About Email</u></a>) </span>
                    <span class="EmialLineField1">Email Status</span>
        	    	<span class="EmialLineField2">Notification Email</span>
    				<span id="minus" ><img id="EmailInfominus" onclick="HideEmailInfo()" src="../friends/images/-.png"></img></span>
    		</div>	
            
            <div class="EmialUserPart_infol" id="EmailHolder">
            <?php DispEmailInfo($UserId);  ?>
			
			</div>
    	
    	<div id="UserAccountInfo"> 
            <div class="UAccountInfoTitle">
                <span class="UserSettingsPartTitletext">User Account Information </span>
            		<span class="UseraboutInfo">(<a href="#" onclick="PromptNonExistPageModal('About User Account','About User Account feature coming soon')"><u>About User Account</u></a>)</span>
    				<span id="minus" ><img id="USettings" onclick="HideUSettings()"  src="../friends/images/-.png"></span>
    		</div>	<br />
    	
            <div class="UAinfopart_infotext"> 
                	<div class="Upart_infotextb"><span class="UserInfoEmial">User Name: </span><span class="UserEmail"><?php Display_User_info($UserId) ?></span></div>
    		</div>
            
            <div class="UAinfopart_infotext"> 
                    <div class="Upart_infotextb"><span class="UserInfoEmial" style="float:left;">Password: </span><span id="UserPasswordDisplay">**********</span><button class="ResetPassword" id="ChangePasswordButton" onclick="ChangeUserPassword('<?php echo $UserId ?>')"  value="ChangePassword">Change Password</button></div>
    		</div>
    		
        
            <div class="UAinfopart_infotext2"> 
                <div class="Upart_infotextb"><span class="UserInfoEmial">Account Type:</span> 
				<span id="AccountType" class="ffname"><?php DisplayAccountType($UserId); ?></span>
                <select id="accountsel">
            		<option value="Personal">Personal</option>
            		<option value="Business">Business</option>
            		<option value="Professional">Professional</option>
            	</select> <span class="UseraboutInfo">(<a href="#" onclick="PromptNonExistPageModal('About Account Type','About Account Type feature coming soon')"><u>About Account Type</u></a>)</span>
                <button class="ResetPassword2" value="Save" onclick="upgradeacc(<?php echo $UserId ?>)">Upgrade Account</button></div>
        	</div>
    	</div>
        
        <div class="UserAccountInfo">
            <div class="USettingsTitle">
                <span class="UserSettingsPartTitletext">Account Connections</span>
    		</div>
    
    		
    		<div class="accountConnections">
    			<div class="accountConnections2">Your Myiceberg account is <u><b>currently connected</b></u> with: 
              <?php MIB_Current_Con($UserId)  ?>
                      	<div class="EditConnectionsInfo" >
    		</div>
    			</div>
    		</div>
    		
    		<div class="accountConnections">
    			<div class="accountConnections2">Select new application <u><b>to connect</b></u> your Myiceberg Account :
    				<?php MIB_available_conn($UserId);
                /*<a href='ex_inc/evn_authw.php' class='a_noshow' ><img src="images/evnico.png" height="27px"  width="27px"/></a>
                <img src="../img/logos/facebook.png" height="27px"  width="27px"/>
        				<img src="../img/logos/google+.png" height="27px"  width="27px"/>
        				<img src="../img/logos/likedin.png" height="27px"  width="27px"/>
        				<img src="../img/logos/twitter.png" height="27px"  width="27px"/>
        				<img src="../img/logos/salesforce.png" height="27px"  width="27px" /> */
            ?>
			
    			</div>
    			<div class="accountConnectionsDecrpition">(available applications to connect your MIB account)</div>
    		</div>
    		
    		<div id="CancelAndSaveButton2">
    			<button value="Cancel">Cancel</button>
    			<button value="Save">Save</button> 
    		</div>
    	</div>
    	
    	
    	<div id="NotificationSettings">
        
            <div class="NotificationSettingsTitle">
                <span class="UserSettingsPartTitletext">Notification Settings</span>
                	<span class="aboutInfo">(<a href="#" onclick="PromptNonExistPageModal('About Notification Settings','About Notification Settings feature coming soon')"><u>About Notification Settings</u></a>) </span>
    				<span id="minus" ><img id="NSettings" onclick="HideNSettings()" src="../friends/images/-.png"></span>
    		</div>
    
    		<div id="NotificationBody">
    			<div class="part_infotext">
                	<div class="Upart_infotextb"><span class="UserAddEmiail">Social Monitoring Notification Frequency</span>
                    <select id="notify">
        			<option value="1">Daily</option>
    				<option value="2">Every 2 Days</option>
    				<option value="3">Weekly</option>
    				<option value="0">None</option>
    			</select>
                    </div>
					<p id="hi"></p>
    		    </div>
    		</div>	
    		
    		<div id="CancelAndSaveButton3">
    			<button class="CancelButton" value="Cancel">Cancel</button>
    			<button class="CancelButton" value="Save" onclick="update_notify(<?php echo $UserId ?>)">Save</button> 
    		</div>
    	</div>
		
		<div id="SocKeyUserSettings">
		
			<div id="SocialKeyWordTitle">
				<span class="UserSettingsPartTitletext">Social Keywords</span>
				<span class="aboutInfo">(<a href="#" onclick="PromptNonExistPageModal('About Social Keywords','About Social Keywordsfeature coming soon')"><u>About Social Keywords</u></a>) </span>
				
				
				<button class="SocKeyWordSearchButton" onclick="fbharvest(<?php echo $_SESSION["userId"]; ?>)" style="margin-left: 50px;" value="Search">Search</button>
				<span id="minus" ><img id="SociKeywordMinus" onclick="Hide_Social_Keyword()" src="images/-.png"></span>
			</div>
			
		<div id="mainbodySocialStreamUser">
				<label class="labelSocKeyword" ><span ><a href="fb_posts.php" style="text-decoration:none;">About Facebook</a></span></label>
			<div id="allOptionsUserSettings">
				<div id="LeftOptions">
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName0"><?php echo $TermNames['0'] ?> </span></label>
						<button onclick="editTerm( 1, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton0" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName1"><?php echo $TermNames['1'] ?></span></label>
						<button onclick="editTerm( 2, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton1" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName2"><?php echo $TermNames['2'] ?></span></label>
						<button onclick="editTerm( 3, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton2" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName3"><?php echo $TermNames['3'] ?></span></label>
						<button onclick="editTerm( 4, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton3" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName4"><?php echo $TermNames['4'] ?></span></label>
						<button onclick="editTerm( 5, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton4" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName5"><?php echo $TermNames['5'] ?></span></label>
						<button onclick="editTerm( 6, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton5" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName6"><?php echo $TermNames['6'] ?></span></label>
						<button onclick="editTerm( 7, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton6" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName7"><?php echo $TermNames['7'] ?></span></label>
						<button onclick="editTerm( 8, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton7" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName8"><?php echo $TermNames['8'] ?></span></label>
						<button onclick="editTerm( 9, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton8" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName9"><?php echo $TermNames['9'] ?></span></label>
						<button onclick="editTerm( 10, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton9" >Edit Term</button>
					</div>
				</div>
				
				<div id="RightOptions">
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName10"><?php echo $TermNames['10'] ?></span></label>
						<button onclick="editTerm( 11, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton10" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName11"><?php echo $TermNames['11'] ?></span></label>
						<button onclick="editTerm( 12, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton11" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName12"><?php echo $TermNames['12'] ?></span></label>
						<button onclick="editTerm( 13, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton12" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName13"><?php echo $TermNames['13'] ?></span></label>
						<button onclick="editTerm( 14, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton13" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName14"><?php echo $TermNames['14'] ?></span></label>
						<button onclick="editTerm( 15, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton14" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName15"><?php echo $TermNames['15'] ?></span></label>
						<button onclick="editTerm( 16, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton15" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName16"><?php echo $TermNames['16'] ?></span></label>
						<button onclick="editTerm( 17, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton16" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName17"><?php echo $TermNames['17'] ?></span></label>
						<button onclick="editTerm( 18, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton17" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword" ><span id="TermName18"><?php echo $TermNames['18'] ?></span></label>
						<button onclick="editTerm( 19, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton18" >Edit Term</button><hr class="hrSocKeyWord">
					</div>
					
					<div id="option">
						<label class="labelSocKeyword"><span id="TermName19"><?php echo $TermNames['19'] ?></span></label>
						<button onclick="editTerm( 20, <?php echo $UserId ?> )" class="EditTermButton" id="TermEditButton19" >Edit Term</button>
					</div>
				</div>
			
			</div>
		</div>
    </div>
    </div>
    <div id="footerFrendsPage">
        <img id="footerImage" src="../img/logos/mib.png">
        <div id="footerinfo">
    	<div id="myicebergLink">Myiceberg.com</div>
            	<div id="AboutUs"><a href="../footer_pages/About_Us.php">About Us</a></div> 
                <div id="AboutUs"><a href="../footer_pages/priceing.php">Pricing</a> 
				<div id="AboutUs"><a href="../footer_pages/FAQ.php">FAQ</a></div> 
		<div id="AboutUs"><a href="../footer_pages/Privacy_Policy.php">Privacy Policy</a></div> 
		<div id="AboutUs"><a href="<?php echo $config['root']; ?>footer_pages/TermsOfUse.php">Terms Of Use</a></div>
		
    	</div>	
    	
    	<div id="RightsReserved">&copy; 2013 Myiceberg LLC. All Rights Reserved</div>
    </div>
</div>

  <!-- Lance's Datepicker Script (Not right now) -->
  <script type='text/javascript' language='javascript'>
    
    //rfaergalekrujehgvuirbguyarfdugyerfiufvygareiuyfgaireuygfq  
    /*
    $('input#datepicker').bind("change keyup paste", function(){
      $('#_birthday').html($(this).val());
    });
    
*/    
      /*
      $(document).ready(function(){
        $("#datepicker").val('Working...');
        //$("input#datepicker").datepicker();
        
       // $("img#calendarImage").click(function(){
        //  $("img#calendarImage").hide();
       // });
        
      });
      */
      /*
      $(function(){
        $("input#datepicker").datepicker({
          changeYear: true,
          yearRange: "1900:+0",
          showOn: 'both',
          buttonImage: '../img/login/Calendar.png',
          buttonImageOnly: true
        });
      });
      */
      // Functions
      // Format Header of Calendar
      /*
      $(function(){
          var oldMethod = $.datepicker._generateMonthYearHeader;
          $.datepicker._generateMonthYearHeader = function(){
              var html = $("<div />").html(oldMethod.apply(this,arguments));
              var monthselect = html.find(".ui-datepicker-year");
              $(".ui-datepicker-next.ui-corner-all").removeClass(".ui-corner-all");
              var thingselect = html.find("a.ui-datepicker-next.ui-corner-all");
              monthselect.insertAfter(thingselect);
              return html.html();
          }
          $('span#_birthday').datepicker();
      });
      */
      // Init Vars
      //var show = 1;
      
      // Formatting
      //$(".ui-datepicker-next").insertBefore(".ui-datepicker-year");
      
      /*
      $(function(){
        
        // Datepicker Options
        $("input#datepicker").datepicker({ 
                            changeYear: true,
                            yearRange: "1900:+0",
                            showOn: 'both',
                            buttonImage: '../img/login/Calendar.png',
                            buttonImageOnly: true
        });
      
});
      */
      // Watch for changes in the date textbox
      // Update on a Change, Paste Into, or Key Pressed (and released)
      /*
      $('#_birthday').bind("change paste keyup", function(){
          //$('#updated').stop().hide();
          //$('#loading').show();
          var birthday = $('#_birthday').val();
          $.post("ex_inc/update-birthday.php", { 'stuff': [birthday] }, function(){
              //$('#loading').hide();
              //$('#updated').fadeIn(250).delay(800).fadeOut(750);
          });
      });
      
*/
      /*
      // Editable or Not Editable
      $('a.click').click(function(){
          if (show == 1) {
              $('a.click').text('Stop Editing');
              $('.ui-datepicker-trigger').fadeIn();
              show = 0;
          } else if (show === 0) {
              $('a.click').text('Enable Editing');
              $('.ui-datepicker-trigger').fadeOut();
              show = 1;
          }
      });
      */
  </script>
  
</body>

</html>

