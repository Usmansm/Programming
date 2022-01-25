<?php 

require_once('../config/config.php');
$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
 

  
  
 // 1. user is logged in FOR FIRST TIME insert data in table NOTE:  terimID is AUTO-INCREAMENT -> query is maybe wrong and this need to be done 15 times for 15 default Terms
   
   /*$Term = array ("Birthday","Anniversary","Engagement","Graduation","Congratulations","Moving","Sorry","Job","College","Wedding","Golf","Wine","Minnesota","Hockey","Travel");
      foreach ( $Term as $value ){
         mysqli_query($mysqli,'INSERT INTO user_monitor_terms (userId,termId,termName,termActive,termType) VALUES("'HERE NEED TO INSER VAR OF USER_ID '","","'.$value.'","1","default")');
      }
      
    // 2. import friends after EACH friend is imported do next
      foreach ( $Term as $value ){
        // select TermID from user_monitor_terms
        $query = 'SELECT termID FROM user_monitor_terms WHERE termName = "'.$value."' AND userId = "HERE NEED TO INSER VAR OF USER_ID"'";
        $termID = $mysqli->query($query);
        
        // INSERT That Id in new Row in user_friend_monitor_terms
         mysqli_query($mysqli,'INSERT INTO user_friend_monitor_terms (termId,userId,friendId,) VALUES("'.$termID.'","'.HERE NEED TO INSER VAR OF USER_ID. '","'.HERE NEED TO INSERT VAR OR FREIND ID WHICH IS NOW IMORTED.'"')');
      }
    (*/
    
    
   
   
 
  $fid = $_GET['fid']; 
  
    mysqli_query($mysqli,"SELECT * FROM user_monitor_terms WHERE userId = '".$fid."'");
    $FirstTime = mysqli_affected_rows($mysqli);
    $i= 1;
    $test = '';
    if ($FirstTime == 0){
      $Term = array ("Birthday","Anniversary","Engagement","Graduation","Congratulations","Moving","Sorry","Job","College","Wedding","Golf","Wine","Minnesota","Hockey","Travel");
        foreach ( $Term as $value ){  
           
           mysqli_query($mysqli,'INSERT INTO user_monitor_terms (userId,termId,termName,termActive,termType) VALUES("'.$fid.'","'.$i.'","'.$value.'","1","default")');
           $i = $i + 1 ;
        }
    }   
    $query = "SELECT * FROM user_monitor_terms WHERE userId ='".$fid."'";  
    $result = $mysqli->query($query);
    
    $TermNames = array();
    while ($row = $result->fetch_assoc()) {
        $TermNames[] =  $row['termName'];
	}

echo '<div id="mainmenu">
		<div class="normalTab" id="FriendDetail"><a href="#" onclick="get_friend_detail('.$fid.')">Friend Detail</a></div>
		<div class="normalTab"><a href="#" onclick="get_Social_News_Stream('.$fid.')">Social/News Stream</a></div>
		<div class="selectedTab" ><a href="#" onclick="get_Social_Keyword('.$fid.')">Social Keyword</a></div>
		<div class="normalTab" ><a href="#" onclick="get_Mutual_Friends('.$fid.')">Mutual Friends</a></div>
	</div>
 
	<div id="mainbodySocialStream">
		
		<div id="SocialKeyWordTitle">
			<div id="titleImage">
				<div id="titletext">Social Keyword for Justin Pagel</div>
				<div id="selectAll"> <input class="inputforSocialKeyword" type="checkbox" name="vehicle" value="Bike" id="maincheckbox">Select All</input></div>
			</div>
		</div>
		
		
		<div id="allOptions">
			<div id="LeftOptions">
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['0'].'</label>
                <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['1'].'</label>
                <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['2'].'</label>
                <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['3'].'</label>
                <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['4'].'</label>
                <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['5'].'</label>
                <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['6'].'</label>
                <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['7'].'</label>
                <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['8'].'</label>
                <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['9'].'</label>
                
				</div>
			</div>
			
			<div id="RightOptions">
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['10'].'</label>
                <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['11'].'</label>
                <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['12'].'</label>
                <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['13'].'</label>
				           <hr class="hrSocKeyWord">
    </div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" />'.$TermNames['14'].'</label>
           <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" /> Fishing</label>
					<a href="#" onclick=editTerm()><div class="EditTermImage" ><div class="EditTerm">Edit Term</div></div></a> <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" /> Cabin</label>
					<div class="EditTermImage"><div class="EditTerm">Edit Term</div></div> <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" /> < new ></label>
					<div class="EditTermImage"><div class="EditTerm">Edit Term</div></div> <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword" ><input class="inputforSocialKeyword" type="checkbox" id="checkbox" /> < new ></label>
					<div class="EditTermImage"><div class="EditTerm">Edit Term</div></div> <hr class="hrSocKeyWord">
				</div>
				
				<div id="option">
					<label class="labelSocKeyword"><input class="inputforSocialKeyword" type="checkbox" id="checkbox" /> < new ></label>
					<div class="EditTermImage"><div class="EditTerm">Edit Term</div></div> 
				</div>
			</div>
			
		</div>';
 
 ?>
