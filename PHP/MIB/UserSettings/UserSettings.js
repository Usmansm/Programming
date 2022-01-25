function disassociateSF(){
	$('#modalTitleText').html('Are you sure?');
   	$('#modalText').html("Do you wish to disassociate your Salesforce account from your Myiceberg account?<br><br><br> <button value='Yes' onClick='disassociateSF2()' >Yes </button>&nbsp&nbsp&nbsp<a href='javascript:close()'><button value='No' >No </button></a>");	
	
	overlay();
}
function disassociateSF2(){
	$('#modalTitleText').html('Delete Myiceberg information?');
	$('#modalText').html(" Delete Myiceberg information from Salesforce?<br><br><br> <button value='Delete' onClick='DisplayPenginunMOdal()' >Delete </button>&nbsp&nbsp&nbsp<a href='javascript:close()'><button value='Quit ' >Quit  </button></a>");	
	modalWidth(1000);
}

function pmodal2(){
	$('#modalTitleText').html('We have deleted the Myiceberg information from your Salesforce contact.');
	$('#modalText').html('We have successfuly removed all Myiceberg data from your salesforce account.');
	$('#modalTitleText').css('margin-left', '30px');
	$("#saveButton").hide();
}

function DisplayPenginunMOdal(){
	$('#modalTitleText').html('We are deleting the Myiceberg information from your Salesforce contact.');
	$('#modalText').html('<img src="../img/loader.gif" width="480px"/>');
	$('#modalTitleText').css('margin-left', '30px');
	modalWidth(1000);
	
	var xmlhttp;
        
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}else{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
		xmlhttp.onreadystatechange=function(){
	if (xmlhttp.readyState==4 && xmlhttp.status==200){
		//alert(xmlhttp.responseText);
		pmodal2()
	}
	} 
	xmlhttp.open("GET","../resttest/sf_disconnect.php" ,true);
	xmlhttp.send();
	change('SettingsProfile');
	
}
/*Function for changeing notification mail in USerSettingsPage*/
function notemail(){
	divid ='EmailHolder';
	
	$('#'+ divid +' input[type=radio]').each(function () {
	if(document.getElementById(this.id).checked == true){
		checkedRadiobuttonId = this.id;
	}
});
}
function NotificationEmail(emailType,UserId){

	//alert( emailType + '--' + UserId );
	/*divid ='EmailHolder';
	cunter = 0;
	$('#'+ divid +' input[type=radio]').each(function () {
		if(document.getElementById(this.id).checked == true){
			checkedRadiobuttonId = this.id;
			alert(checkedRadiobuttonId);
		}
	});
	*/
	//document.getElementById('NotificationEmail' + emailType).checked = false;
	cunter = 0;
	$('#'+ divid +' input[type=radio]').each(function () {
		cunter = cunter + 1;
		document.getElementById('NotificationEmail' + cunter).checked = false;
	});
	// EmailHolder
	//document.getElementById('NotificationEmail' + emailType).checked = true;
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
	}else{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
		
	xmlhttp.onreadystatechange=function(){
	if (xmlhttp.readyState==4 && xmlhttp.status==200){
		if (xmlhttp.responseText == '1'){
			document.getElementById('NotificationEmail' + emailType).checked = true;
		}else{
			document.getElementById(checkedRadiobuttonId).checked = true;
			$('#modalTitleText').html('ERROR');
			$('#modalText').html('We choud not set this Email as notification Email because it is not Verified');
			$('#modalTitleText').css('margin-left', '30px');
			$("#saveButton").hide();
			overlay();
		}
	}
	}
	xmlhttp.open("GET","../UserSettings/UserSettinsProc.php?ChangeNotificationEmail=" + emailType + "&UserID="+ UserId, true);
	xmlhttp.send();
	//alert( seoomthing1 + '--' + something2 );

}

function ChangeUserPassword(Uid){
	Changeing = true;
	//alert('Function is called');
	currentPassword = document.getElementById("UserPasswordDisplay").textContent ;
	CurrentButtonState = document.getElementById("ChangePasswordButton").textContent ;
	//alert(currentPassword + '----' + CurrentButtonState );
	if (CurrentButtonState == "Save Password" ){
		Changeing = false;
		document.getElementById("ChangePasswordButton").textContent = 'Change Password';
		PasswordInput1 = document.getElementById("PasswordInut1").value;
		PasswordInput2 = document.getElementById("PasswordInut2").value;
		//alert(PasswordInput1 + '------' + PasswordInput1);
		//create ajax for processing data and saveing information
		if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}else{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById("UserPasswordDisplay").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","../UserSettings/UserSettinsProc.php?ChangeCurrentPassword=true&PasswordInput1="+ PasswordInput1 + "&PasswordInput2=" + PasswordInput2,true);
		xmlhttp.send();
	}
	
	if(Changeing == true){
		document.getElementById("ChangePasswordButton").textContent ='Save Password';
		document.getElementById("UserPasswordDisplay").innerHTML='<input id="PasswordInut1" type="password"></input> <span class="passwordInputInformation">Please Inser your new password</span> <br /><input id="PasswordInut2" type="password"></input> <span class="passwordInputInformation">Retype Password</span>';
	
	}
}

/*function for Editing Social Keyword on USerSettings page */
function editTerm(TidR, Uid){
	Tid = TidR - 1 ;
	if (document.getElementById("TermEditButton" + Tid).innerHTML == "Save Term"){
		
	document.getElementById("TermEditButton" + Tid).innerHTML = "Edit Term";

	ccont = document.getElementById("EditTermName" + Tid).value ;

	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
	}else{// code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
		
		xmlhttp.onreadystatechange=function(){

	if (xmlhttp.readyState==4 && xmlhttp.status==200){
		document.getElementById("TermName" + Tid).innerHTML=xmlhttp.responseText;
	}
	}
		xmlhttp.open("GET","../UserSettings/UserSettinsProc.php?EditTerm=" + ccont + "&Tid=" + TidR ,true);
		xmlhttp.send();
			
	} else {
		document.getElementById("TermEditButton" + Tid).innerHTML = "Save Term";
		ccont = document.getElementById("TermName" + Tid).innerHTML;

		document.getElementById("TermName" + Tid).innerHTML = "<input type='text' id='EditTermName" + Tid +"' value='" + ccont + "' />";
	}
		
	
}



/*function for editing email on USerSettings page  */
function DeleteEmail(id, Eid){
	/*
		- Get id of this email 
		- send data for deleteing this email 
	
	*/
	var ccont = document.getElementById("Eholder"+ Eid).innerHTML;

	var xmlhttp;// create AJAX request
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}else{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange=function(){

	if (xmlhttp.readyState==4 && xmlhttp.status==200){
		if (xmlhttp.responseText == 'false'){
			$('#modalTitleText').html('ERROR');
			$('#modalText').html('We choud DELETE your email you must have at least 1 Verified Email');
			$('#modalTitleText').css('margin-left', '30px');
			$("#saveButton").hide();
			overlay();
		
		}else{
			document.getElementById("EmailHolder").innerHTML=xmlhttp.responseText;
		}
	}
	}
	xmlhttp.open("GET","../UserSettings/UserSettinsProc.php?DeleteEmail=" + ccont + "&id=" + id + "&Eordinal=" + Eid ,true);
	xmlhttp.send();


}

/*function for email feature*/
function emailButton(UserId){

    Id = UserId;
    ccont1 = document.getElementById("inputEmail").value;

    var xmlhttp;
    if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }else{// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange=function(){
  
    if (xmlhttp.readyState==4 && xmlhttp.status==200){
		if (xmlhttp.responseText == 'false'){
			$('#modalTitleText').html('ERROR');
			$('#modalText').html('Wrong email adress');
			$('#modalTitleText').css('margin-left', '30px');
			$("#saveButton").hide();
			overlay();
		}else{
			document.getElementById("EmailHolder").innerHTML=xmlhttp.responseText;
			$('#modalTitleText').html('Mail Inserted');
			$('#modalText').html('Thank you for adding this additional email to your Myiceberg account. Please go to your inbox and verify this email');
			$('#modalTitleText').css('margin-left', '30px');
			$("#saveButton").hide();
			overlay()
		}
      }
    }
      xmlhttp.open("GET","../UserSettings/UserSettinsProc.php?addEmail=" + ccont1 + "&id=" + Id ,true);
      xmlhttp.send();
  
}


/*function for address and hpone drop down */
function AddressAndPhoneDropDown(PhoneOrAddress, UserId){

    
    if ( PhoneOrAddress == "Phone") {
	
			var e = document.getElementById("PhoneSelect");
			var AorPId = e.options[e.selectedIndex].value;
			//alert(AorPId);
		
       var xmlhttp;
            if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }else{// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            
        xmlhttp.onreadystatechange=function(){
            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                document.getElementById("UserPhone").innerHTML=xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","../UserSettings/UserSettinsProc.php?PhoneorAdd=Phone&id=" + UserId + "&ordinal=" + AorPId ,true);
        xmlhttp.send();
        
    }
    if ( PhoneOrAddress == "Address") {
       
		var e = document.getElementById("AddressSelet");
		var AorPId = e.options[e.selectedIndex].value;
		//alert(AorPId);
	   var xmlhttp;
        
        if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }else{// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
        xmlhttp.onreadystatechange=function(){
            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                document.getElementById("AddressHolder").innerHTML=xmlhttp.responseText;
            }
        }    
            
        xmlhttp.open("GET","../UserSettings/UserSettinsProc.php?PhoneorAdd=Address&id=" + UserId + "&ordinal=" + AorPId ,true);
        xmlhttp.send();
    }
    
    

}



/*function for edit feature */

function EditUsettings(UserId){
var SelectedPhone =$("#PhoneSelect").val()
var SelectedAdd =$("#AddressSelet").val()

  

    if (document.getElementById("UinfoEditbutton").innerHTML == "Save") {
        var Cdrop = document.getElementById('Country_drop');
        Cdrop.style.visibility = 'hidden';
        var UCoun= document.getElementById('UserCountry');
        UCoun.style.visibility = 'visible';
        $("#PhoneSelect").show(500);
        $("#AddressSelet").show(500);


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
        ccont12 = document.getElementById("udob").value;
     	ccont13 = document.getElementById("Country_drop").value ;
		//alert(ccont13);
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
        xmlhttp.open("GET","../UserSettings/UserSettinsProc.php?UserId=" + UserId + "&FN=" + ccont1 + "&MN=" + ccont2 + "&LN=" + ccont3 + "&DN=" + ccont4 + "&UP=" + ccont5 + "&UCI=" + ccont6 + "&UTI=" + ccont7 + "&UA="  + ccont8 + "&UC=" + ccont9 + "&US=" + ccont10 + "&UZip=" + ccont11 +  "&PhNum=" + SelectedPhone + "&AddNum=" + SelectedAdd + "&udob=" + ccont12 +"&CountryDrop=" + ccont13,true);
        xmlhttp.send();
		}
	
    
    if (document.getElementById("UinfoEditbutton").innerHTML == "Edit"){
        var Cdrop = document.getElementById('Country_drop');
        Cdrop.style.visibility = 'visible';
        var UCoun= document.getElementById('UserCountry');
        UCoun.style.visibility = 'hidden';
        $("#PhoneSelect").hide(500);
        $("#AddressSelet").hide(500);
        
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
}


/* function for hideing and showing content in UserSettings*/
function hideUserInfo(){
    if ($(".UserPart_infol").is(":visible")) {
        $(".UserPart_infol").hide(500);
        $(".UserPart_infor").hide(500);
        document.getElementById('UInfominus').src="../friends/images/+.png"
    } else{
        $(".UserPart_infol").show(500);
        $(".UserPart_infor").show(500);
        document.getElementById('UInfominus').src="../friends/images/-.png"
    }
}
function Hide_Socia_Keyword(){
	if ($("#mainbodySocialStreamUser").is(":visible")) {   
        $("#mainbodySocialStreamUser").hide(500);
        document.getElementById('SociKeywordMinus').src="../friends/images/+.png"

    } else{
        $("#mainbodySocialStreamUser").show(500);
        document.getElementById('SociKeywordMinus').src="../friends/images/-.png"
    }
}
function HideEmailInfo(){
    if ($(".EmialUserPart_infol").is(":visible")) {   
        $(".EmialUserPart_infol").hide(500);
        document.getElementById('EmailInfominus').src="../friends/images/+.png"

    } else{
        $(".EmialUserPart_infol").show(500);
        document.getElementById('EmailInfominus').src="../friends/images/-.png"
    }
}

function HideNSettings(){
    if ($("#NotificationBody").is(":visible")) {   
        $("#NotificationBody").hide(500);
        $("#CancelAndSaveButton3").hide(500);
        document.getElementById('NSettings').src="../friends/images/+.png"
        
    } else{
        $("#NotificationBody").show(500);
        $("#CancelAndSaveButton3").show(500);
        document.getElementById('NSettings').src="../friends/images/-.png"
    }
}

function HideUSettings(){
        if ($(".UAinfopart_infotext").is(":visible")) {   
        $(".UAinfopart_infotext").hide(500);
        $(".UAinfopart_infotext2").hide(500);
        document.getElementById('USettings').src="../friends/images/+.png"
    } else{
        $(".UAinfopart_infotext").show(500);
        $(".UAinfopart_infotext2").show(500);
        document.getElementById('USettings').src="../friends/images/-.png"
    }
}

function Hide_Social_Keyword(){
	if ($("#mainbodySocialStreamUser").is(":visible")) {   
        $("#mainbodySocialStreamUser").hide(500);
        document.getElementById('SociKeywordMinus').src="images/+.png"

    } else{
        $("#mainbodySocialStreamUser").show(500);
        document.getElementById('SociKeywordMinus').src="images/-.png"
    }
}

/*End of function for hideing and showing content in UserSettings*/

function upgradeacc(uid){
	//alert('function is called ');
	//alert (uid)
	cv = document.getElementById('accountsel').value;
	currentAccType = document.getElementById('AccountType').innerHTML;
	//alert(cv);
    if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById('AccountType').innerHTML = x.responseText;
		}
		}
		x.open("GET", "../UserSettings/UserSettinsProc.php?acctype=" + cv + "&currentacctype=" + currentAccType , true);
		x.send(null);
	}
	
}

