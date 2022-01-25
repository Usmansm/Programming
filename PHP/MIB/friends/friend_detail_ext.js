var cem1 = 0;
var cem2 = 0;
var cem3 = 0;

function EditFriendEmail(type, Fid, UserId){
	//alert('function is called');
	//alert(type);
	if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
		//alert('check3');
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			var ResponseFromAJAX = x.responseText;
			document.getElementById("femail").innerHTML=ResponseFromAJAX;
			document.getElementById('femail').setAttribute('title', ResponseFromAJAX);
			
			//alert('check1');
		}
		}
		x.open("GET", "ex_inc/friend_detail_comp.php?Emailtype=" + type + "&Fid=" + Fid + "&UserId=" + UserId , true);
		x.send(null);
		//alert('check2');
		}
}

/*Code for friendPhone dropdown*/
function EditFriendPhone(type, Fid, UserId){
	//alert('function is called');
	//alert(type);
  if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
		//alert('check3');
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("fphone1").innerHTML=x.responseText
			//alert('check1');
		}
		}
		x.open("GET", "ex_inc/friend_detail_comp.php?Phonetype=" + type + "&Fid=" + Fid + "&UserId=" + UserId , true);
		x.send(null);
		//alert('check2');
		}
}

function makeEditable(wcomp,tpid, UserID ) {
	
    if (wcomp == "pi") {
		if (cem1 !== 0) {

			var tempdiv = document.getElementById('charleft');   // remove temp div which show how many char-s are left
			tempdiv.parentNode.removeChild(tempdiv);

			ccont1 = document.getElementById("femaild").value + ",.,";
			ccont2 = document.getElementById("fphone1d").value + ",.,";
			ccont3 = document.getElementById("fdobd").value + ",.,";
			ccont4 = document.getElementById("ftitled").value + ",.,";
			ccont5 = document.getElementById("fhighschoold").value + ",.,";
			ccont6 = document.getElementById("ffnamed").value + ",.,";
			ccont7 = document.getElementById("fmnamed").value + ",.,";
			ccont8 = document.getElementById("flnamed").value + ",.,";
			ccont9 = document.getElementById("fcompanyd").value + ",.,";
			ccont10 = document.getElementById("fcolleged").value + ",.,";
			ccont11 = document.getElementById("email_sel").value+ ",.,"; // this is working properly 
			ccont12 = document.getElementById("FrDetailPhoneDropDown").value + ",.,"; // ccont12 = cell
			ccont13 = document.getElementById("NoteFrendDetailInput").value + ",.,";
			ccont14 = document.getElementById("DetailOnlineLink1Input").value + ",.,";
			ccont15 = document.getElementById("DetailOnlineLink2Input").value;
			document.getElementById("pi_edit_click").innerHTML = "Saving...";
			
			if (document.getElementById) {
				var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
			}
			if (x) {
				x.onreadystatechange = function() {
					if (x.readyState == 4 && x.status == 200) {
						//alert('are we here ? ');
						document.getElementById("pi_edit_click").innerHTML = "Edit"
						document.getElementById("pi_holder").innerHTML = x.responseText
						//document.getElementById("FrIinfo").innerHTML = x.responseText
						get_friend_detail(tpid)
                        cem1 = 0
                        displaysmallnotification("<img src='images/check.png' /> Information saved successfuly!")
						update_verbubble();
					}
				}
				x.open("GET", "ex_inc/friend_detail_comp.php?a=wu&tpid="+ tpid +"&d=" + ccont1 + ccont2 + ccont3 + ccont4 + ccont5 + ccont6 + ccont7 + ccont8 + ccont9 + ccont10 + ccont11 + ccont12 + ccont13 + ccont14 + ccont15, true);
				x.send(null);

			}
		} else {
			$(".detail_drop").hide();
			//$("#PhoneSel").hide();

      
			document.getElementById("pi_edit_click").innerHTML = "Save"
			ccont = document.getElementById("femail").innerHTML
			document.getElementById("femail").innerHTML = "<input type='text' id='femaild' value='" + ccont + "' />"
			ccont = document.getElementById("fphone1").innerHTML
			document.getElementById("fphone1").innerHTML = "<input type='text' id='fphone1d' value='" + ccont + "' />"
			ccont = document.getElementById("fdob").innerHTML
			document.getElementById("fdob").innerHTML = "<input type='text' id='fdobd' value='" + ccont + "' />"
			//$('#fdobd').datepicker();

			/*$( "#fdobd" ).datepicker({
			  changeMonth: true,
			  changeYear: true,
			  yearRange: "-113:+0" 
			});
			*/
					
			
			ccont = document.getElementById("ftitle").innerHTML
			document.getElementById("ftitle").innerHTML = "<input type='text' id='ftitled' value='" + ccont + "' />"
			ccont = document.getElementById("fhighschool").innerHTML
			document.getElementById("fhighschool").innerHTML = "<input type='text' id='fhighschoold' value='" + ccont + "' />"
			ccont = document.getElementById("ffname").innerHTML
			document.getElementById("ffname").innerHTML = "<input type='text' id='ffnamed' value='" + ccont + "' />"
			ccont = document.getElementById("fmname").innerHTML
			document.getElementById("fmname").innerHTML = "<input type='text' id='fmnamed' value='" + ccont + "' />"
			ccont = document.getElementById("flname").innerHTML
			document.getElementById("flname").innerHTML = "<input type='text' id='flnamed' value='" + ccont + "' />"
			ccont = document.getElementById("fcompany").innerHTML
			document.getElementById("fcompany").innerHTML = "<input type='text' id='fcompanyd' value='" + ccont + "' />"
			ccont = document.getElementById("fcollege").innerHTML
			document.getElementById("fcollege").innerHTML = "<input type='text' id='fcolleged' value='" + ccont + "' />"
			
			ccont = document.getElementById("aDetailOnlineLink1").innerHTML
			document.getElementById("DetailOnlineLink1").innerHTML = "<input type='text' id='DetailOnlineLink1Input' value='" + ccont + "' />"
			ccont = document.getElementById("aDetailOnlineLink2").innerHTML
			document.getElementById("DetailOnlineLink2").innerHTML = "<input type='text' id='DetailOnlineLink2Input' value='" + ccont + "' />"
			
			ccont = document.getElementById("NoteFrDetail").innerHTML
			document.getElementById("NoteFrDetail").innerHTML = "<br /><textarea  rows='5' maxlength='300' id='NoteFrendDetailInput' class='NoteFrendDetInput' onkeyup=note_char_count('NoteFrendDetailInput','charleft')  onchange=note_char_count('NoteFrendDetailInput','charleft')>"+ ccont +"</textarea><div id='charleft'></div>"
			cem1 = 1 //onkeyup=note_char_count('NoteFamilyDetailInput" + i + "','charleft')  onchange=note_char_count('NoteFamilyDetailInput" + i + "','charleft')>"+ ccont +"</textarea><div id='charleft'></div>"
		}
	} else if (wcomp == "ai") {
		if (cem2 != 0) {
  //alert('here?');
    // homeCity homeState homeZip officeaddr officeCity officeState officeZip
    
			ccont = document.getElementById("homeaddrInput").value + ",.,"
			ccont = ccont +  document.getElementById("homeCityInput").value + ",.,"
			ccont = ccont + document.getElementById("homeStateInput").value + ",.,"
			ccont = ccont + document.getElementById("homeZipInput").value + ",.,"
			ccont = ccont + document.getElementById("officeaddrd").value + ",.,"
			ccont = ccont + document.getElementById("officeCityInput").value + ",.,"
			ccont = ccont + document.getElementById("officeStateInput").value + ",.,"
			ccont = ccont + document.getElementById("officeZipInput").value
			document.getElementById("ai_edit_click").innerHTML = "Saving..."

			//alert('here?2');
     if (document.getElementById) {
				var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
			}
			if (x) {
				x.onreadystatechange = function() {
					if (x.readyState == 4 && x.status == 200) {
						document.getElementById("ai_edit_click").innerHTML = "Edit";
					//	document.getElementById("FrIinfo").innerHTML = (x.responseText);
						get_friend_detail(tpid)
            cem2 = 0
            displaysmallnotification("<img src='images/check.png' /> Information saved successfuly!")

					}
				}
				x.open("GET", "ex_inc/friend_detail_comp.php?a=wu2&tpid="+ tpid +"&UsedId=" + UserID +"&d=" + ccont , true);
				x.send(null);
			}   

		} else {
			document.getElementById("ai_edit_click").innerHTML = "Save"
   // homeCity homeState homeZip officeaddr officeCity officeState officeZip
			ccont = document.getElementById("homeaddr").innerHTML
			document.getElementById("homeaddr").innerHTML = "<input type='text' id='homeaddrInput' value='" + ccont + "' />"
			ccont = document.getElementById("homeCity").innerHTML
			document.getElementById("homeCity").innerHTML = "<input type='text' id='homeCityInput' value='" + ccont + "' />"
      ccont = document.getElementById("homeState").innerHTML
			document.getElementById("homeState").innerHTML = "<input type='text' id='homeStateInput' value='" + ccont + "' />"
      ccont = document.getElementById("homeZip").innerHTML
			document.getElementById("homeZip").innerHTML = "<input type='text' id='homeZipInput' value='" + ccont + "' />"
      ccont = document.getElementById("officeaddr").innerHTML
			document.getElementById("officeaddr").innerHTML = "<input type='text' id='officeaddrd' value='" + ccont + "' />"
      ccont = document.getElementById("officeCity").innerHTML
			document.getElementById("officeCity").innerHTML = "<input type='text' id='officeCityInput' value='" + ccont + "' />"
      ccont = document.getElementById("officeState").innerHTML
			document.getElementById("officeState").innerHTML = "<input type='text' id='officeStateInput' value='" + ccont + "' />"
      ccont = document.getElementById("officeZip").innerHTML
			document.getElementById("officeZip").innerHTML = "<input type='text' id='officeZipInput' value='" + ccont + "' />" 
			cem2 = 1
		}
	}
}

function makeFamilyEditable(tpid){
// determinate how many family members are there 
  var NumberOfFamilyMembers = $('div.FamilyHold').length;
 // alert(NumberOfFamilyMembers);
  //alert(tpid);
  var i = 0; //coumter
              
      if (document.getElementById("fmedit").innerHTML == 'Save'){ // if edit is one just grab data from imput box-es and send them
         document.getElementById("fmedit").innerHTML = "Edit"
                  
          while (i < NumberOfFamilyMembers ){
          i = i + 1;  
          
          var tempdiv = document.getElementById('charleft' + i);  // remove temp div which show how many char-s are left
          //tempdiv.parentNode.removeChild(tempdiv);
           // This line is breaking  rest of function si I put it in comment and leaveing this note for the man who edited this 
          // DENI
  

         FamilyType = document.getElementById("InputFamilyType"+ i).value
		 //alert(FamilyType);
          FamilyName = document.getElementById("FamilyInputName"+ i).value
         // alert('is this working1');
          FamilyPhone = document.getElementById("FamilyInputPhone"+ i).value
          //alert('is this working2');
          FamilyMail = document.getElementById("FamilyInputMail"+ i).value
          //alert('is this working3');
          FamilyDoB = document.getElementById("FamilyInputDoB"+ i).value
		  //alert(FamilyDoB);
          //alert('is this working4');
          FamilyNote = document.getElementById("NoteFamilyDetailInput"+ i).value
          //alert('is this working5');
          OnlineLink1 = document.getElementById("OnlineLinkInput1"+ i).value
         // alert('is this working6'+OnlineLink1);
          OnlineLink2 = document.getElementById("OnlineLinkInput2"+ i).value
         // alert('is this working7'+OnlineLink2);
          FamilyNote = document.getElementById("NoteFamilyDetailInput"+ i).value
          FamilyMemid = document.getElementById("FamilyMemberid"+ i).innerHTML
          //alert('is this working8');
		
           if (document.getElementById) {
      				var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
      			}
      			if (x) {
      				x.onreadystatechange = function() {
      					if (x.readyState == 4 && x.status == 200) {
      						document.getElementById("fd3").innerHTML = x.responseText // there is no response text
      						get_friend_detail(tpid) // function for displaying all data
						displaysmallnotification("<img src='images/check.png' /> Information saved successfuly!")

      					}
      				}
  				//alert (FamilyMemid + '//--//' + FamilyName + '//--//' + FamilyPhone + '//--//' + FamilyMail + '//--//' + FamilyDoB + '//--//' + OnlineLink1 + '//--//' + OnlineLink2 + '//--//' + FamilyNote);
				x.open("GET", "ex_inc/friend_detail_comp.php?FamilyType=" + FamilyType + "&FamilyMemid=" + FamilyMemid + "&FamilyName=" + FamilyName + "&FamilyPhone=" + FamilyPhone + "&FamilyMail=" + FamilyMail + "&FamilyDoB=" + FamilyDoB + "&OnlineLink1=" + OnlineLink1 + "&OnlineLink2=" + OnlineLink2 + "&FamilyNote=" + FamilyNote, true);
  				//alert("ex_inc/friend_detail_comp.php?FamilyMemid=" + FamilyMemid + "&FamilyName=" + FamilyName + "&FamilyPhone=" + FamilyPhone + "&FamilyMail=" + FamilyMail + "&FamilyDoB=" + FamilyDoB + "&OnlineLink1=" + OnlineLink1 + "&OnlineLink2=" + OnlineLink2 + "&FamilyNote=" + FamilyNote);
				x.send(null);
            
            }
          }

      }
    
      if (document.getElementById("fmedit").innerHTML == 'Edit'){ // make all editable and insert curretn values
          document.getElementById("fmedit").innerHTML = "Save";
    			//part_infoName part_infoPhone1 part_infoMail1  part_infoDoB
          while( i < NumberOfFamilyMembers) {
              i = i + 1;
				ccont = document.getElementById("part_infoFamilyType"+ i).innerHTML;
				document.getElementById("part_infoFamilyType"+ i).innerHTML = "<input type='text' id='InputFamilyType" + i + "' value='" + ccont + "' />";
				
				ccont = document.getElementById("part_infoName"+ i).innerHTML;
				document.getElementById("part_infoName"+ i).innerHTML = "<input type='text' id='FamilyInputName" + i + "' value='" + ccont + "' />";

				ccont = document.getElementById("part_infoPhone"+ i).innerHTML;
				document.getElementById("part_infoPhone"+ i).innerHTML = "<input type='text' id='FamilyInputPhone" + i + "' value='" + ccont + "' />";

				ccont = document.getElementById("part_infoMail"+ i).innerHTML;
				document.getElementById("part_infoMail"+ i).innerHTML = "<input type='text' id='FamilyInputMail" + i + "' value='" + ccont + "' />";

				ccont = document.getElementById("part_infoDoB"+ i).innerHTML;
				document.getElementById("part_infoDoB"+ i).innerHTML = "<input type='text' id='FamilyInputDoB" + i + "' value='" + ccont + "' />";
              	
	$( "#FamilyInputDoB"+i ).datepicker({
      changeMonth: true,
      changeYear: true,
	  yearRange: "-113:+0" 
    });

			
			ccont = document.getElementById("aOnlineLink1"+ i).innerHTML
			document.getElementById("OnlineLink1"+ i).innerHTML = "<input type='text' id='OnlineLinkInput1" + i + "' value='" + ccont + "' />"

			ccont = document.getElementById("aOnlineLink2"+ i).innerHTML
			document.getElementById("OnlineLink2"+ i).innerHTML = "<input type='text' id='OnlineLinkInput2" + i + "' value='" + ccont + "' />"

			ccont = document.getElementById("FamilyNoteDetail"+ i).innerHTML
			document.getElementById("FamilyNoteDetail"+ i).innerHTML = "<br /><textarea  rows='5' cols='90' maxlength='300' class='NoteFrendDetInput' id='NoteFamilyDetailInput" + i + "' onkeyup=note_char_count('NoteFamilyDetailInput" + i + "','FamCharleft" + i + "')  onchange=note_char_count('NoteFamilyDetailInput" + i + "','FamCharleft" + i + "')>"+ ccont +"</textarea><div id='FamCharleft" + i + "'></div>" 
             
          }

    }
}