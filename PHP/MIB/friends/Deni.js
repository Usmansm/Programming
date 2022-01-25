/*function for showing modal when EVENTS or some non-existing page is clicked */
function PromptNonExistPageModal(title, modalText){
	$('#modalTitleText').html(title);
	$('#modalText').html(modalText);
	overlay();

}


/*function for dispalying insurance and finance in freind detail page this feature is only available yo John*/
function InsuranceFinanceDisplay(whichTohide){
	if (whichTohide == 1 ){
	
		$("#InsuranceContent").show(700);
		$("#FinanceContent").hide(700);

	}
	
	if ( whichTohide == 2 ){
		$("#FinanceContent").show(700);
		$("#InsuranceContent").hide(700);
	}

}
/*this 2  funcions  are for hideing and showing some parts on left side of firends page categories*/
function Disp_Freind_Sources(){

	$("#FrSourcesList").show(700);
	$("#NewsCatList").hide(700);
	
}
//function moveSF(){
//	window.location.href="https://ec2-54-243-154-131.compute-1.amazonaws.com/MIBWORKING/dev/resttest/demo_sf.php" ;
//}

function Disp_News_Categories(){
 
	$("#FrSourcesList").hide(700);
	$("#NewsCatList").show(700);
}
/*function for saving notifications*/

function update_notify(uid){
//alert("intest");
var s = document.getElementById('notify');
var sel = s.options[s.selectedIndex].value;
//alert(sel);
 
		 //document.getElementById("addfamhold").innerHTML = "<img src='images/loader.gif' />"
			
				var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
			
			if (x) {
				x.onreadystatechange = function() {
					if (x.readyState == 4 && x.status == 200) {
						//alert('documenttestAlert');
						document.getElementById("hi").innerHTML = x.responseText;
						//alert('documenttestAlert');
						//alert(x.responseText);
						get_friend_detail(lfid);
					}
					else
					{
					//alert(x.responseText);
					}
				}
				x.open("GET","DeniTest.php?UserId=" + uid + "&NotificationId=" + sel ,true);
				x.send(null);
			}
	

}

/*function for adding camily member*/
/*function addfam(lfid){
   ftype = document.getElementById("famtype").value
    ffname = document.getElementById("famfname").value
    flname = document.getElementById("famlname").value
    femail = document.getElementById("famemail").value
    fphone = document.getElementById("famphone").value
    fdob = document.getElementById("famdob").value
    fnotes = document.getElementById("famnotes").value
    Link1 = document.getElementById("Link1").value
    Link2 = document.getElementById("Link2").value
		//document.getElementById("addfamhold").innerHTML = "<img src='images/loader.gif' />"
			if (document.getElementById) {
				var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
			}
			if (x) {
				x.onreadystatechange = function() {
					if (x.readyState == 4 && x.status == 200) {
						//document.getElementById("part_infohold").innerHTML = x.responseText;
						get_friend_detail(lfid);
						close();
						//window.location.href="index.php?spId="+lfid;
					}
				}
				x.open("GET", "ex_inc/addfamproc.php?fid="+ lfid +"&ftype="+ ftype + "&ffname="+ ffname + "&flname="+ flname + "&femail="+ femail + "&fphone="+ fphone +"&fdob="+ fdob +"&fnotes="+ fnotes + "&Link1=" + Link1 + "&Link2=" + Link2 , false);
				x.send(null);
			}
	//get_friend_detail(lfid);

}*/
/*Function for changeing background  collors in TOP MENY BAR*/
function Head_meny_Back_Ground(BarId){
  $('#'+ BarId).css('background-color', '#2C64AF');
	
	if (BarId == 'Friends_head_link'){
		$('#News_head_link').css('background-color', '#2D4662');
		$('#Events_head_link').css('background-color', '#2D4662');
		$('#Albums_head_link').css('background-color', '#2D4662');
		$(".cat_button").show();
	}
	
	if (BarId == 'News_head_link'){
		$('#Friends_head_link').css('background-color', '#2D4662');
		$('#Events_head_link').css('background-color', '#2D4662');
		$('#Albums_head_link').css('background-color', '#2D4662');
		$('#CategTitle').html('News Categories:');
		//$(".cat_button").hide();
		//$("#PhoneSelect").hide();
		//$("#PhoneSelect").hide();
		
		
	}
  
	if (BarId == 'Events_head_link'){
		$('#Friends_head_link').css('background-color', '#2D4662');
		$('#News_head_link').css('background-color', '#2D4662');
		$('#Albums_head_link').css('background-color', '#2D4662');
		$('#CategTitle').html('Event Categories:');
	}
  
	if (BarId == 'Albums_head_link'){
		$('#Friends_head_link').css('background-color', '#2D4662');
		$('#News_head_link').css('background-color', '#2D4662');
		$('#Events_head_link').css('background-color', '#2D4662');
		$('#CategTitle').html('Album Categories:');
	}
 }
 
/* function for redirecting to Socail/News Stream */

function get_Mutual_Friends(fid){
	$('#modalTitleText').html('Mutual Friends');
	$('#modalText').html(' Please wait while we determine your mutual friends.<br /><img src="../img/loader.gif" width="480px"/>');
	overlay();
	setTimeout("get_Mutual_Friends2("+ fid +")",500)
	}
	function get_Mutual_Friends2(fid){   
		if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			close()
			document.getElementById("FrIinfo").innerHTML=x.responseText
			smallflist(ffid)
			
		}
		}
		x.open("GET", "MutualFriends.php?fid="+fid, true);
		x.send(null);
		}

}


/* function for redirecting to Socail/News Stream .... */

function get_Social_News_Stream(fid){

    //alert(fid);
   if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("FrIinfo").innerHTML=x.responseText
			smallflist(fid)
		}
		} 
		x.open("GET", "SocialNewsStream.php?fid="+fid, true);
		x.send(null);
		}

}
/*

FUNCTION FOR SEARCH FREINDS FEATURE PROABALY NOT NEEDED 
$(document).ready(function() {
	 //alert('Hi!');
  //Event handler for search feature.
  //18 june 2013.



  $("#friendsearchinput").keyup(function () {
    var key =  $('#friendsearchinput').val();

    key = key.split(' ').join('+');
	alert("BOO")
    $('#friend_list_large_hold').load('ex_inc/SearchFriends.php?key='+ key);
  });


  
})
*/
/*function for edit feature */

/*function EditUsettings(UserId){
var SelectedPhone =$("#PhoneSelect").val()
var SelectedAdd =$("#AddressSelet").val()

  

    if (document.getElementById("UinfoEditbutton").innerHTML == "Save") {
        var Cdrop = document.getElementById('Country_drop');
        Cdrop.style.visibility = 'hidden';
        var UCoun= document.getElementById('UserCountry');
        UCoun.style.visibility = 'visible';
        $("#PhoneSelect").show();
        $("#AddressSelet").show();


		ccont1 = document.getElementById("FirsteNameInput").value;
		ccont2 = document.getElementById("MiddleNameInput").value;
		ccont3 = document.getElementById("lastnameInput").value;
		ccont4 = document.getElementById("DisplayNameInput").value;
		ccont5 = document.getElementById("UserPhoneInput").value;
		ccont6 = document.getElementById("UserCompanyInput").value;
		ccont7 = document.getElementById("UserTitleInput").value;
		ccont8 = document.getElementById("UserAddressInput").value;
		ccont9 = document.getElementById("UserCityInput").value; 
		ccont10 = document.getElementById("UserStateInput").value;
        ccont11 = document.getElementById("UserZipInput").value;
        ccont12 = document.getElementById("udob").value ;
		//alert(ccont12);
        //alert (ccont1 + ccont2 + ccont3 + ccont4 + ccont5 + ccont6 + ccont7 + ccont8 + ccont9 + ccont10 + ccont11);
		document.getElementById("UinfoEditbutton").innerHTML = "Saving...";
        var xmlhttp;
        
        if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }else{// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
        xmlhttp.onreadystatechange=function(){
            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                document.getElementById("User").innerHTML=xmlhttp.responseText;
            }
        }   
        xmlhttp.open("GET","DeniTest.php?UserId=" + UserId + "&FN=" + ccont1 + "&MN=" + ccont2 + "&LN=" + ccont3 + "&DN=" + ccont4 + "&UP=" + ccont5 + "&UCT=" + ccont6 + "&UT=" + ccont7 + "&UA="  + ccont8 + "&UC=" + ccont9 + "&US=" + ccont10 + "&UZip=" + ccont11 +  "&PhNum=" + SelectedPhone + "&AddNum=" + SelectedAdd + "&udob=" + ccont12,true);
        xmlhttp.send();
		}
	
    
    if (document.getElementById("UinfoEditbutton").innerHTML == "Edit"){
        var Cdrop = document.getElementById('Country_drop');
        Cdrop.style.visibility = 'visible';
        var UCoun= document.getElementById('UserCountry');
        UCoun.style.visibility = 'hidden';
        $("#PhoneSelect").hide();
        $("#AddressSelet").hide();
        
        document.getElementById("UinfoEditbutton").innerHTML="Save";
        var ccont = document.getElementById("FirsteName").innerHTML;
        document.getElementById("FirsteName").innerHTML = "<input type='text' id='FirsteNameInput' value='" + ccont + "' />";
        ccont = document.getElementById("MiddleName").innerHTML;
        document.getElementById("MiddleName").innerHTML = "<input type='text' id='MiddleNameInput' value='" + ccont + "' />";
        ccont = document.getElementById("lastname").innerHTML;
        document.getElementById("lastname").innerHTML = "<input type='text' id='lastnameInput' value='" + ccont + "' />";
        ccont = document.getElementById("DisplayName").innerHTML;
        document.getElementById("DisplayName").innerHTML = "<input type='text' id='DisplayNameInput' value='" + ccont + "' />";
        ccont = document.getElementById("UserPhone").innerHTML;
        document.getElementById("UserPhone").innerHTML = "<input type='text' id='UserPhoneInput' value='" + ccont + "' />";
        ccont = document.getElementById("UserCompany").innerHTML;
        document.getElementById("UserCompany").innerHTML = "<input type='text' id='UserCompanyInput' value='" + ccont + "' />";
        ccont = document.getElementById("UserTitle").innerHTML;
        document.getElementById("UserTitle").innerHTML = "<input type='text' id='UserTitleInput' value='" + ccont + "' />";
        ccont = document.getElementById("UserAddress").innerHTML;
        document.getElementById("UserAddress").innerHTML = "<input type='text' id='UserAddressInput' value='" + ccont + "' />";
        ccont = document.getElementById("UserCity").innerHTML;
        document.getElementById("UserCity").innerHTML = "<input type='text' id='UserCityInput' value='" + ccont + "' />";
        ccont = document.getElementById("UserState").innerHTML;
        document.getElementById("UserState").innerHTML = "<input type='text' id='UserStateInput' value='" + ccont + "' />";
        ccont = document.getElementById("UserZip").innerHTML
        document.getElementById("UserZip").innerHTML = "<input type='text' id='UserZipInput' value='" + ccont + "' />";
        ccont = document.getElementById("_birthday").innerHTML
			document.getElementById("_birthday").innerHTML = "<input type='text' id='udob' value='" + ccont + "' />";
			
			$( "#udob" ).datepicker({
      changeMonth: true,
      changeYear: true,
	  yearRange: "-113:+0" 
    });
		
    }
}*/

/*ustman function for add from email */
function cs(){
	//alert('function cs is calleed');
closeModal();
 $('#init').trigger('click');
}