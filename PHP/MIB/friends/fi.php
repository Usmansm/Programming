<?php
session_start();
//This is a comment for commit testing
if($_SESSION["lerrc"] == 1){
	echo "<font color='red' >All fields marked with * must be filled out.</font>";
	$_SESSION["lerrc"] = 0;
}
?>
<script>
    function Add_Friend_Manualy(Uid){
        fi_fname = document.getElementById("fi_fname").value + ",.,"
        fi_mname = document.getElementById("fi_mname").value + ",.,"
        fi_lname = document.getElementById("fi_lname").value + ",.,"
        fi_company = document.getElementById("fi_company").value + ",.,"
        fi_college = document.getElementById("fi_college").value
         
        //alert(fi_fname + ' ' + fi_mname + ' ' + fi_lname + ' ' + fi_company + ' ' + fi_college  )
          
        fi_email = document.getElementById("fi_email").value + ",.,"
        fi_phone = document.getElementById("fi_phone").value + ",.," 
        fi_bday = document.getElementById("fi_bday").value + ",.,"
        fi_title = document.getElementById("fi_title").value + ",.,"
        fi_school = document.getElementById("fi_school").value
        //alert(fi_email + ' ' + fi_phone + ' ' + fi_bday + ' ' + fi_title + ' ' + fi_school  )
          
        //HomeAddInput HomeCityInput  HomeStateInput  HomeZipInput
        Home_Add = document.getElementById("HomeAddInput").value + ",.,"
        Home_City = document.getElementById("HomeCityInput").value + ",.,"
        Home_State = document.getElementById("HomeStateInput").value + ",.,"
        Home_Zip = document.getElementById("HomeZipInput").value + ",.,"
        Phone_DD = document.getElementById("PhoneSel").value
        //alert(Home_Add + ' ' + Home_City + ' ' + Home_State + ' ' + Home_Zip  )
        //OfficeAddInput  OfficeCityInput OfficeStateInput OfficeZipInput
          
        Office_Add = document.getElementById("OfficeAddInput").value + ",.,"
        Office_City = document.getElementById("OfficeCityInput").value + ",.,"
        Office_State = document.getElementById("OfficeStateInput").value + ",.,"
        Office_Zip = document.getElementById("OfficeZipInput").value
        //alert(Office_Add + ' ' + Office_City + ' ' + Office_State + ' ' + Office_Zip  );
        
        //alert('here');
        if ((fi_fname == ',.,') || (fi_lname == ',.,') || (fi_lname == ',.,')  ){
        
        alert('Insert all  fields marked whit *');
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
                        document.getElementById("addfamhold").innerHTML = x.responseText;
                		get_friend_detail('<?php  echo $_SESSION["lfid"];  ?>');
                    }
        		} 
            	x.open("GET", "ex_inc/add_Fr_Man_Proc.php?Uid="+ Uid + "&NameArray="+ fi_fname + fi_mname + fi_lname + fi_company + fi_college + "&EmailArray=" + fi_email + fi_phone + fi_bday + fi_title + fi_school +"&HomeAddArray=" + Home_Add + Home_City + Home_State + Home_Zip + Phone_DD +"&OfficeAddArray="+Office_Add + Office_City + Office_State + Office_Zip , false );
                x.send(null);
                //alert('sent');
                
                
        	}
        
        }
      
    
    }

</script>


<div class="fi_modal_title">Personal information</div>

<div class="fi_modal_right">
	<span class="fi_modal_formtext" >*Email</span><br>
	<input type="text" name="fi_email" id="1fi_email" class="fi_input" /><br />
	<span class="fi_modal_formtext" >Phone</span>
      <select class="detail_drop" id="1PhoneSel">
        <option value="Cell">Cell</option>
        <option value="Home">Home</option>
        <option value="Office">Office</option>
    </select>
	<input type="text" name="fi_phone" id="1fi_phone" class="fi_input" /><br />
	<span class="fi_modal_formtext" >Birthday</span><br>
	<input type="text" name="fi_bdassy" id="1fi_bdssay" class="fi_input" /><br />
	<span class="fi_modal_formtext" >Title</span><br>
	<input type="text" name="fi_title" id="1fi_title" class="fi_input" /><br />
	<span class="fi_modal_formtext" >High school</span><br>
	<input type="text" name="fi_school" id="1fi_school" class="fi_input" /><br />
</div>

<div class="fi_modal_left">
	<span class="fi_modal_formtext" >*First name</span><br>
	<input type="text" name="fi_fname" id="1fi_fname" class="fi_input" /><br />
	<span class="fi_modal_formtext" >Middle name</span><br>
	<input type="text" name="fi_mname" id="1fi_mname" class="fi_input" /><br />
	<span class="fi_modal_formtext" >*Last Name</span><br>
	<input type="text" name="fi_lname" id="1fi_lname" class="fi_input" /><br />
	<span class="fi_modal_formtext" >Company</span><br>
	<input type="text" name="fi_company" id="1fi_company" class="fi_input" /><br />
	<span class="fi_modal_formtext" >College</span><br>
	<input type="text" name="fi_college" id="1fi_college" class="fi_input" /><br />
</div>

<div class="fi_modal_title" style="margin-top: 20px;">Address information</div>

<div class="fi_modal_right">
	<span class="fi_modal_formtext" >Home Add</span><br>
	<input type="text" name="HomeAddInput" id="1HomeAddInput" class="fi_input" /><br />
 <span class="fi_modal_formtext" >Home City</span><br>
	<input type="text" name="HomeAddInput" id="1HomeCityInput" class="fi_input" /><br />
 <span class="fi_modal_formtext" >Home State</span><br>
	<input type="text" name="HomeAddInput" id="1HomeStateInput" class="fi_input" /><br />
 <span class="fi_modal_formtext" >Home Zip</span><br>
	<input type="text" name="HomeAddInput" id="1HomeZipInput" class="fi_input" /><br />
</div>


<div class="fi_modal_left">
	<span class="fi_modal_formtext" >Office Add</span><br>
	<input type="text" name="HomeAddInput" id="1OfficeAddInput" class="fi_input" /><br />
 <span class="fi_modal_formtext" >Office City</span><br>
	<input type="text" name="HomeAddInput" id="1OfficeCityInput" class="fi_input" /><br />
 <span class="fi_modal_formtext" >Office State</span><br>
	<input type="text" name="HomeAddInput" id="1OfficeStateInput" class="fi_input" /><br />
 <span class="fi_modal_formtext" >Office Zip</span><br>
	<input type="text" name="HomeAddInput" id="1OfficeZipInput" class="fi_input" /><br />
</div>


</form>

