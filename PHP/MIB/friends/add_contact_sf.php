<?php
session_start();
if($_SESSION["lerrc"] == 1){
	echo "<font color='red' >All fields marked with * must be filled out.</font>";
	$_SESSION["lerrc"] = 0;
}
?>
<script>
    function Add_Friend_Manualy_SF(Uid){
        fi_fname = document.getElementById("fi_fname1").value + ",.,"
       
        fi_lname = document.getElementById("fi_lname1").value + ",.,"
        fi_company = document.getElementById("fi_company1").value 
         
       // alert(fi_fname  + fi_lname + ' ' + fi_company   )
          //check for Email
		  
		  if(document.getElementById("fi_emailtext1")){
    //alert("Element exists");
	fi_email = document.getElementById("fi_emailtext1").value + ",.,"
	//alert(fi_email);
} else {
    //alert("Element Combo Exist");
	var e = document.getElementById("fi_email1");
var fi_email= e.options[e.selectedIndex].value+ ",.,"
//alert(fi_email);
}
		  
		  
		  
       
        fi_phone = document.getElementById("fi_phone1").value + ",.," 
		  fi_frid = document.getElementById("frid").value + ",.," 
      
        fi_title = document.getElementById("fi_title1").value 
       // alert(fi_email + ' ' + fi_phone  + fi_title   )
          
        //HomeAddInput HomeCityInput  HomeStateInput  HomeZipInput
        Home_Add = document.getElementById("HomeAddInput1").value + ",.,"
        Home_City = document.getElementById("HomeCityInput1").value + ",.,"
        Home_State = document.getElementById("HomeStateInput1").value + ",.,"
        Home_Zip = document.getElementById("HomeZipInput1").value 
        //alert(Home_Add + ' ' + Home_City + ' ' + Home_State + ' ' + Home_Zip  )
        //OfficeAddInput  OfficeCityInput OfficeStateInput OfficeZipInput
          
       
            //alert('here');
        if ((fi_fname == ',.,') || (fi_lname == ',.,')  ){
        
        alert('Insert all  fields marked with *');
		//alert(fi_fname);
			//alert('<?php  echo $_GET["spId"];  ?>');
		
        }  else {
        closeModal();
        
            //alert('start of else');
            // document.getElementById("addfamhold").innerHTML ='tttttttttt ttttttt';
             //document.getElementById("addfamhold").innerHTML = "<img src='images/loader.gif' />"
            if(document.getElementById){
            //alert('start of else2');
            	var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
            }
    
           if(x) {
           //alert('start of else3');
                x.onreadystatechange = function() {
                    if(x.readyState == 4 && x.status == 200) {
                        //alert('start of else3');
                       // document.getElementById("CatHold").innerHTML = "../resttest/demo_rest.php?create=1&Uid="+ Uid + "&NameArray="+ fi_fname+ fi_lname + fi_company + "&EmailArray=" + fi_email + fi_phone+ fi_title  +"&HomeAddArray=" + Home_Add + Home_City + Home_State + Home_Zip ;
						//alert( x.responseText);
					  //alert('sent');
                		get_friend_detail('<?php  echo $_SESSION["lfid"];  ?>');
                    }
        		} 
            	x.open("GET", "../resttest/create_contact.php?create=1&Uid="+ Uid + "&NameArray="+ fi_fname+ fi_lname +fi_frid+fi_company+ "&EmailArray=" + fi_email + fi_phone+ fi_title  +"&HomeAddArray=" + Home_Add + Home_City + Home_State + Home_Zip , false );
				//alert("../resttest/demo_sf.php?create=1&Uid="+ Uid + "&NameArray="+ fi_fname+ fi_lname +fi_frid+fi_company+ "&EmailArray=" + fi_email + fi_phone+ fi_title  +"&HomeAddArray=" + Home_Add + Home_City + Home_State + Home_Zip );
			   x.send(null);
               // alert('sent');
                
                
        	}
			else
			{
			//alert("unable to create AJAX object");
			}
        
        }
      
    
    }

</script>

<?php
require_once ('../config/config.php');
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
	$query = "SELECT * FROM user_friend_detail WHERE userId = '". $_SESSION["userId"] ."' AND friendId = '".$_GET["frId"]."'";
	$result = $mysql->query($query);
	$row = $result->fetch_assoc();
	
	$emailq = "SELECT * FROM user_email where userId = '".$_GET["frId"]."'";
	$remail = $mysql->query($emailq);
	$aemail = $remail->fetch_assoc();
?>
<div class="fi_modal_title">Personal information</div>

<div class="fi_modal_right">
	<span class="fi_modal_formtext" >*Email</span><br>
	<?php
	if(empty($row['FriendEmail1']) && empty($row['FriendEmail2']) && empty($row['FriendEmail3']))
	{
	?>
	<input type="text" name="fi_email" id="fi_emailtext1" class="fi_input" value="<?php echo $aemail['emailAddr'];?> " /><br />
	<?php
	}
	else
	{
	
	?>
	
	<select name="fi_email" id="fi_email1" class="fi_input" >
	<option value="<?php echo $row['FriendEmail1']; ?> "> <?php echo $row['FriendEmail1']; ?> </option>
		<option value="<?php echo $row['FriendEmail2']; ?> "> <?php echo $row['FriendEmail2']; ?> </option>
			<option value="<?php echo $row['FriendEmail3']; ?> "> <?php echo $row['FriendEmail3']; ?> </option>
	<br />
	<?php
	}
	?>
	<span class="fi_modal_formtext" >Phone</span><br>
	<input type="text" name="fi_phone" id="fi_phone1" class="fi_input" value="<?php echo $row['FriendPhoneCell'];?> "/><br />
	
	<span class="fi_modal_formtext" >Title</span><br>
	<input type="text" name="fi_title" id="fi_title1" class="fi_input" value="<?php echo $row['FriendTitle'];?> "/><br />
	
</div>

<div class="fi_modal_left">
	<span class="fi_modal_formtext" >*First name</span><br>
	<input type="text" name="fi_fname" id="fi_fname1" class="fi_input" value="<?php echo $row['FriendFirstName'];?> "/><br />
	
	<span class="fi_modal_formtext" >*Last Name</span><br>
	<input type="text" name="fi_lname" id="fi_lname1" class="fi_input" value="<?php echo $row['FriendLastName'];?> "/><br />
	<span class="fi_modal_formtext" >Department</span><br>
	<input type="text" name="fi_company" id="fi_company1" class="fi_input" value="<?php echo $row['lastName'];?> "/><br />
	
</div>

<div class="fi_modal_title" style="margin-top: 20px;">Address information</div>

<div class="fi_modal_left">
	<span class="fi_modal_formtext" >Home Add</span><br>
	<input type="text" name="HomeAddInput" id="HomeAddInput1" class="fi_input" value="<?php echo $row['FriendAddress1'];?> "/><br />
 <span class="fi_modal_formtext" >Home City</span><br>
	<input type="text" name="HomeAddInput" id="HomeCityInput1" class="fi_input" value="<?php echo $row['FriendCity1'];?> " /><br />
 <span class="fi_modal_formtext" >Home State</span><br>
	<input type="text" name="HomeAddInput" id="HomeStateInput1" class="fi_input" value="<?php echo $row['FriendCity1'];?> "/><br />
 <span class="fi_modal_formtext" >Home Zip</span><br>
	<input type="text" name="HomeAddInput" id="HomeZipInput1" class="fi_input" value="<?php echo $row['FriendZip1'];?> "/><br />
</div>

<input type="hidden" value="<?php echo $_GET["frId"]; ?> " id="frid"/>

</form>

