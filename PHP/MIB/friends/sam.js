
 //kk
var fals;

 function change(value) {
   
	if (value == "SettingsProfile" ){
			$("#alphaFriends").hide();
			$("#category_hold").hide();
			$("#friend_list_large_hold").hide();
			$("#alphalist").hide();
			$("#fd").hide();
			$("#footerFrendsPage").hide();
			$("#UserSettingsPage").show();
			$('#UserSettingsPage').load( '../UserSettings/UserSettings.php', function(){
				notemail();
			});

		
		
    }
    else if(value == "Logout"){
    	//alert("This is logout")
        window.location.assign("../logout.php")
     }
}

function promptDeleteFromCat(){
	
	friends = [];
	$('body input[type=checkbox]').each(function () {
		if(this.checked){
			var attr = $(this).attr('id');
			if(attr){
				friends.push(attr);
			}
		}
	});
	if (friends.length <= 0) {
		$('#modalTitleText').html('Delete Selected From Category');
        $('#modalTitleText').css('margin-left', '30px');
        $("#saveButton").show();
		//$('#modalButtons').html('<div class="cat_right" ></div>');
		$('#modalText').html('Please select your friends first');
		overlay();	
	}
	else {
		$('#modalTitleText').html('Delete Selected From Category');
        $('#modalTitleText').css('margin-left', '30px');
        $("#saveButton").show();
        $("#saveButton").attr("onclick", "deleteFromCat('" + friends + "')");
		//$('#modalButtons').html('<input type="button" class="cat_button" value="Cancel" onClick="closeModal()"/><input type="button" class="cat_button" value="Delete From Category" onClick="deleteFromCat(\''+friends+'\')" style="margin-left:10px;" /><div class="cat_right" ></div>');
		$('#modalText').load("../php/categoriesManagement.php", {type: 'catList2'});
		overlay();
	}
}

calendar = {
	month_names: ["January","February","March","April","May","June","July","Augest","September","October","November","December"],
	weekdays: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
	month_days: [31,28,31,30,31,30,31,31,30,31,30,31],
	//Get today's date - year, month, day and date
	today : new Date(),
	opt : {},
	data: [],

	//Functions
	/// Used to create HTML in a optimized way.
	wrt:function(txt) {
		this.data.push(txt);
	},
	
	/* Inspired by http://www.quirksmode.org/dom/getstyles.html */
	getStyle: function(ele, property){
		if (ele.currentStyle) {
			var alt_property_name = property.replace(/\-(\w)/g,function(m,c){return c.toUpperCase();});//background-color becomes backgroundColor
			var value = ele.currentStyle[property]||ele.currentStyle[alt_property_name];
		
		} else if (window.getComputedStyle) {
			property = property.replace(/([A-Z])/g,"-$1").toLowerCase();//backgroundColor becomes background-color

			var value = document.defaultView.getComputedStyle(ele,null).getPropertyValue(property);
		}
		
		//Some properties are special cases
		if(property == "opacity" && ele.filter) value = (parseFloat( ele.filter.match(/opacity\=([^)]*)/)[1] ) / 100);
		else if(property == "width" && isNaN(value)) value = ele.clientWidth || ele.offsetWidth;
		else if(property == "height" && isNaN(value)) value = ele.clientHeight || ele.offsetHeight;
		return value;
	},
	getPosition:function(ele) {
		var x = 0;
		var y = 0;
		while (ele) {
			x += ele.offsetLeft;
			y += ele.offsetTop;
			ele = ele.offsetParent;
		}
		if (navigator.userAgent.indexOf("Mac") != -1 && typeof document.body.leftMargin != "undefined") {
			x += document.body.leftMargin;
			offsetTop += document.body.topMargin;
		}    
	
		var xy = new Array(x,y);
		return xy;
	},
	/// Called when the user clicks on a date in the calendar.
	selectDate:function(year,month,day) {
		var ths = _calendar_active_instance;
		document.getElementById(ths.opt["input"]).value = year + "-" + month + "-" + day; // Date format is :HARDCODE:
		ths.hideCalendar();
	},
	/// Creates a calendar with the date given in the argument as the selected date.
	makeCalendar:function(year, month, day) {
		year = parseInt(year);
		month= parseInt(month);
		day	 = parseInt(day);
		
		//Display the table
		var next_month = month+1;
		var next_month_year = year;
		if(next_month>=12) {
			next_month = 0;
			next_month_year++;
		}
		
		var previous_month = month-1;
		var previous_month_year = year;
		if(previous_month< 0) {
			previous_month = 11;
			previous_month_year--;
		}
		
		this.wrt("<table>");
		this.wrt("<tr><th><a href='javascript:calendar.makeCalendar("+(previous_month_year)+","+(previous_month)+");' title='"+this.month_names[previous_month]+" "+(previous_month_year)+"'>&lt;</a></th>");
		this.wrt("<th colspan='5' class='calendar-title'><select name='calendar-month' class='calendar-month' onChange='calendar.makeCalendar("+year+",this.value);'>");
		for(var i in this.month_names) {
			this.wrt("<option value='"+i+"'");
			if(i == month) this.wrt(" selected='selected'");
			this.wrt(">"+this.month_names[i]+"</option>");
		}
		this.wrt("</select>");
		this.wrt("<select name='calendar-year' class='calendar-year' onChange='calendar.makeCalendar(this.value, "+month+");'>");
		var current_year = this.today.getYear();
		if(current_year < 1900) current_year += 1900;
		
		for(var i=current_year-70; i<current_year+10; i++) {
			this.wrt("<option value='"+i+"'")
			if(i == year) this.wrt(" selected='selected'");
			this.wrt(">"+i+"</option>");
		}
		this.wrt("</select></th>");
		this.wrt("<th><a href='javascript:calendar.makeCalendar("+(next_month_year)+","+(next_month)+");' title='"+this.month_names[next_month]+" "+(next_month_year)+"'>&gt;</a></th></tr>");
		this.wrt("<tr class='header'>");
		for(var weekday=0; weekday<7; weekday++) this.wrt("<td>"+this.weekdays[weekday]+"</td>");
		this.wrt("</tr>");
		
		//Get the first day of this month
		var first_day = new Date(year,month,1);
		var start_day = first_day.getDay();
		
		var d = 1;
		var flag = 0;
		
		//Leap year support
		if(year % 4 == 0) this.month_days[1] = 29;
		else this.month_days[1] = 28;
		
		var days_in_this_month = this.month_days[month];

		//Create the calender
		for(var i=0;i<=5;i++) {
			if(w >= days_in_this_month) break;
			this.wrt("<tr>");
			for(var j=0;j<7;j++) {
				if(d > days_in_this_month) flag=0; //If the days has overshooted the number of days in this month, stop writing
				else if(j >= start_day && !flag) flag=1;//If the first day of this month has come, start the date writing

				if(flag) {
					var w = d, mon = month+1;
					if(w < 10)	w	= "0" + w;
					if(mon < 10)mon = "0" + mon;

					//Is it today?
					var class_name = '';
					var yea = this.today.getYear();
					if(yea < 1900) yea += 1900;

					if(yea == year && this.today.getMonth() == month && this.today.getDate() == d) class_name = " today";
					if(day == d) class_name += " selected";
					
					class_name += " " + this.weekdays[j].toLowerCase();

					this.wrt("<td class='days"+class_name+"'><a href='javascript:calendar.selectDate(\""+year+"\",\""+mon+"\",\""+w+"\")'>"+w+"</a></td>");
					d++;
				} else {
					this.wrt("<td class='days'>&nbsp;</td>");
				}
			}
			this.wrt("</tr>");
		}
		this.wrt("</table>");
		this.wrt("<input type='button' value='Cancel' class='calendar-cancel' onclick='calendar.hideCalendar();' />");

		document.getElementById(this.opt['calendar']).innerHTML = this.data.join("");
		this.data = [];
	},
	
	/// Display the calendar - if a date exists in the input box, that will be selected in the calendar.
	showCalendar: function() {
		var input = document.getElementById(this.opt['input']);
		
		//Position the div in the correct location...
		var div = document.getElementById(this.opt['calendar']);
		var xy = this.getPosition(input);
		var width = parseInt(this.getStyle(input,'width'));
		div.style.left=(xy[0]+width+10)+"px";
		div.style.top=xy[1]+"px";

		// Show the calendar with the date in the input as the selected date
		var existing_date = new Date();
		var date_in_input = input.value;
		if(date_in_input) {
			var selected_date = false;
			var date_parts = date_in_input.split("-");
			if(date_parts.length == 3) {
				date_parts[1]--; //Month starts with 0
				selected_date = new Date(date_parts[0], date_parts[1], date_parts[2]);
			}
			if(selected_date && !isNaN(selected_date.getYear())) { //Valid date.
				existing_date = selected_date;
			}
		}
		
		var the_year = existing_date.getYear();
		if(the_year < 1900) the_year += 1900;
		this.makeCalendar(the_year, existing_date.getMonth(), existing_date.getDate());
		document.getElementById(this.opt['calendar']).style.display = "block";
		_calendar_active_instance = this;
	},
	
	/// Hides the currently show calendar.
	hideCalendar: function(instance) {
		var active_calendar_id = "";
		if(instance) active_calendar_id = instance.opt['calendar'];
		else active_calendar_id = _calendar_active_instance.opt['calendar'];
		
		if(active_calendar_id) document.getElementById(active_calendar_id).style.display = "none";
		_calendar_active_instance = {};
	},
	
	/// Setup a text input box to be a calendar box.
	set: function(input_id) {
		var input = document.getElementById(input_id);
		if(!input) return; //If the input field is not there, exit.
		
		if(!this.opt['calendar']) this.init();
		
		var ths = this;
		input.onclick=function(){
			ths.opt['input'] = this.id;
			ths.showCalendar();
		};
	},
	
	/// Will be called once when the first input is set.
	init: function() {
		if(!this.opt['calendar'] || !document.getElementById(this.opt['calendar'])) {
			var div = document.createElement('div');
			if(!this.opt['calendar']) this.opt['calendar'] = 'calender_div_'+ Math.round(Math.random() * 100);

			div.setAttribute('id',this.opt['calendar']);
			div.className="calendar-box";

			document.getElementsByTagName("body")[0].insertBefore(div,document.getElementsByTagName("body")[0].firstChild);
		}
	}
}


function  importFriends(source) {

	if(source == 'facebook'){
		$.ajax({
		type: "GET",
		url: "../php/importManagement.php",
		data: { type: "facebook"},
		success: function(output) {
	
			if(output == 'true'){
		
				$('#modalText').html('Please wait while we import your friends, this may take a couple minutes. <br><img src="../img/loader.gif" width="480px"/>');
				$.ajax({
					type: "POST",
					url: "../php/facebookImportFriends.php",
					data: { type: "facebook"},
					success: function(output) {
				
						$('#modalText').html('<center>Myiceberg has succesfully imported your Facebook friends!<br>'+output);
						$('#modalButtons').html('');
						raw_friend_reload()
						raw_cat_reload()
						//alert("fffffff")
					}
				});
			}
			else{
	
				window.location = appbase_url + 'logout.php';
			}
		}
	  });
	}
	if(source == 'linkedin'){
		$.ajax({
		type: "POST",
		url: "../php/importManagement.php",
		data: { type: "linkedin"},
		success: function(output) {
			if(output == 'import'){
		$('#modalText').html('Please wait while we import you friends, this can take a couple minutes. <br><img src="../img/loader.gif" width="480px"/>');
				$.ajax({
					type: "POST",
					url: "../php/linkedinImportFriends.php",
					data: { type: "linkedin"},
					success: function(output) {
						$('#modalText').html('<center>Myiceberg has succesfully imported your Linkedin connections!<br>'+output);
						$('#modalButtons').html('');
						//alert("fffffff")
						raw_friend_reload()
						raw_cat_reload()
					}
				});
			}
			else{
		
				window.location = appbase_url + 'logout.php?redirect=php/linkedinLogin.php';
			}
		}
	  });
	}
}
/*function for displaying modal afer friends ar e imported 
or at least I am guesint that this function is for that Deni.
*/
function check5(importSource){
   //alert('importSource: '+importSource);
    if(importSource != false){
       $('#modalText').html('Please wait while we import your friends, this can take a couple minutes. <br><img src="../img/loader.gif" width="480px"/>');
    overlay();
   // alert('calling');
    $.ajax({
        			type: "GET",
					url: "../php/class/check.php",
                    data: {'import':importSource},
					success: function(output) {
					//alert(output);
					if(output=='multiple')
					{
						$('#modalText').html('<center>We noticed you have mulitple MIB Accounts. Please contact help desk<br>');
						//alert(output);
						$('#modalButtons').html('');
						$('#saveButton').attr('href', '');
					}
 
                       else if(output != 'true'){
                            window.location = output;
							//alert(output);
                        }
						else {
							                      
						$('#modalText').html('<center>Myiceberg has successfully imported your connections!<br>');
						//alert(output);
						$('#modalButtons').html('');
						$('#saveButton').attr('href', '');
						limit=0;
									
								 datastring="firstLimit="+limit+"&act=&catid=";
								 $.ajax({
				
									type:"GET",
									url:"ex_inc/frnd_listing.php",
									data:datastring,
									cache: false,
									success:function(response){
													
									$("#fl").html(response);
										
									
									},
									fail:function(error){
										alert(error);
										}
									});
						raw_friend_reload()
						raw_cat_reload()
						}
					}
		}); 
    }
}


function selectAddToCat3(catId){
	$('#'+selectedCat3+'3').css('background-color', '#FFF');
	selectedCat3 = catId;
	$('#'+catId+'3').css('background-color', '#a0c5eb');
}
selectedCat3 = '';

function processVerifyFriends() {
	//alert('Processing');
	
	$('input:radio:checked').each(function() {
   var value = $(this).val();
  // alert(value);	
   if(value != 'undefined'){
		
		$.ajax({
	 type: "POST",
	  url: "../php/manageFriends.php",
	 data: { data: value},
	 success: function(output) {
		 // alert(output);
	  }
	});		
}
});
	
	
	$('#modalButtons').html('<center><input type="button" class="cat_button" value="Close" onClick="closeModal()"/><div class="cat_right" ></div></center> ');
	$('#modalText').html("All the selected friends have been succesfully updated!");
		
}
var html = '';
function promptVerifyFriends(){
		$('#modalTitleText').html('Verify your friends');
        $("#saveButton").show();
        $("#modalText").html("We are currently loading your friends to be verified, this may take a few minutes.<br /><img src='images/loader.gif' />");
		//$('#modalButtons').html('<center><input type="button" class="cat_button" value="Save" onClick="processVerifyFriends()"/><div class="cat_right" ></div><input type="button" class="cat_button" value="Close" onClick="closeModal()"/><div class="cat_right" ></div></div><input type="button" class="cat_button" value="Nmr Friends" onClick="test2()"/><div class="cat_right" ></div></center> ');
		$('#modalText').load("../php/verifyFriends.php");
		modalWidth(1000);
		
		overlay();
        $('#saveButton').attr('onClick', 'verify()');
		$('#saveButton').removeAttr('href');
}
function loadDevModal (){
	$('#modalText').load("../php/devModal.php");
}
function promptDevModal() {
	$('#modalTitleText').html('Dev');
		$('#modalButtons').html('<input type="button" class="cat_button" value="Refresh" onClick="loadDevModal()"/><div class="cat_right" ></div><input type="button" class="cat_button" value="Test1" onClick="test2()"/><div class="cat_right" ></div><input type="button" class="cat_button" value="Test2" onClick=""/><div class="cat_right" ></div>');
		$('#modalText').load("../php/devModal.php");
		overlay();
}
var isCtrl = false;
document.onkeyup=function(e){
	if(e.which == 17) isCtrl=false;
}
document.onkeydown=function(e){
	if(e.which == 17) isCtrl=true;
	if(e.which == 81 && isCtrl == true) {
		promptDevModal();
		return false;
	}
}

function promptAddFamily(LFID){
	$('#modalTitleText').html('Add family Member');
    $("#saveButton").show();
    $("#saveButton").attr("onclick", "addfam('" + LFID + "')");
	//var importFrModal = document.getElementById('addfamhold').innerHTML;
	//$('#modalText').html(importFrModal);
	$('#modalText').load("addfam.php");
	
	overlay();
}
	/*function for displaying modal adding user manulay*/
function promptNewUser(Userid){ 
		$('#modalTitleText').html('Add a New Friend');
		//$('#modalButtons').html('Add a New Friend');
		//alert(' we are here ');
        $("#saveButton").show();
		//var importFrModal = document.getElementById('importFirendManModal').innerHTML;
		//$('#modalText').html(importFrModal);
		//alert(' we are her4 ');
		$('#modalText').load("ex_inc/addFriendManModal.php");
        $("#saveButton").attr("onclick", "Add_Friend_Manualy('" + Userid + "')");
		//alert(' we are here3 ');
		//load("../ex_inc/addFriendManModal.php");
}


function ImportFromCSV(){
// this is function which I am calling for import form CSV ... |
// what file I need to call now ? 
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}else{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById("modalText").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","../csv/test/index.php",true);
		xmlhttp.send();

}

function MoreCSVModalInfo(){
	$('#modalTitleText').html('CSV Import Info');
	$("#saveButton").hide();
	$('#modalText').load("ex_inc/CSVMoreInfoModal.php");
}

	
function displayImportFromCSVProfress(){
	document.getElementById("csvim").style.display='none'
	document.getElementById("csvimpen").style.display='inline'
	//$('#modalTitleText').html('Importing from CSV');
	//$('#modalText').html('Please wait while we import, this can take a couple minutes. <br><img src="../img/loader.gif" width="480px"/>');
//	overlay();
	
	/*if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}else{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}`
	
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById("modalText").innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","../csv/test/index.php",true);
	xmlhttp.send();*/
	
}
function ImportingFromCSVDONE(){
	$('#modalTitleText').html('Importing done');
	$('#modalText').html('Importing form CSV is done');
	overlay();
}
	/*function for procesing adding freinds manyaly ...*/
function Add_Friend_Manualy(Uid){
	//alert('FUNCTION IS CALLED');
	fi_fname = document.getElementById("fi_fname").value + ",.,"
	fi_mname = document.getElementById("fi_mname").value + ",.,"
	fi_lname = document.getElementById("fi_lname").value + ",.,"
	fi_company = document.getElementById("fi_company").value + ",.,"
	fi_college = document.getElementById("fi_college").value
	//alert(fi_fname);
	//alert(fi_mname);
	  
	fi_email = document.getElementById("fi_email").value + ",.,"
	fi_phone = document.getElementById("fi_phone").value + ",.," 
	fi_bday = document.getElementById("fi_bday").value + ",.,"
	fi_title = document.getElementById("fi_title").value + ",.,"
	fi_school = document.getElementById("fi_school").value
	//alert(fi_email + ' ' + fi_phone + ' ' + fi_bday + ' ' + fi_title + ' ' + fi_school  )
	//alert(fi_email)
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
	//alert(fi_fname)
	//alert(fi_lname);
	//alert(fi_email);
	if ((fi_fname == ',.,') || (fi_lname == ',.,') || (fi_email == ',.,')  ){
	
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
					get_friend_detail(Uid);
				}
			} 
	x.open("GET", "ex_inc/add_Fr_Man_Proc.php?Uid="+ Uid + "&NameArray="+ fi_fname + fi_mname + fi_lname + fi_company + fi_college + "&EmailArray=" + fi_email + fi_phone + fi_bday + fi_title + fi_school +"&HomeAddArray=" + Home_Add + Home_City + Home_State + Home_Zip + Phone_DD +"&OfficeAddArray="+Office_Add + Office_City + Office_State + Office_Zip , false );
	x.send(null);
			//alert('sent');
			
			
		}
	
	}
  

}


function promptNewUserSF(Userid,frId)   { 
		overlay();	
		$('#modalTitleText').html('Add to SalesForce');
		//alert(frId);
		//$('#modalButtons').html('Add a New Friend');
        $("#saveButton").show();
        $('#modalText').load("add_contact_sf.php?frId="+frId);
        $("#saveButton").attr("onclick", "Add_Friend_Manualy_SF('" + Userid + "')");
}



function modalWidth(width){
	$('#modal').css('width', width);
	$('#modalButtons').css('width', width);
	$('#modalText').css('width', width-10);
	$('#modalTitleText').css('width', width);
}


function test2(){
$.ajax({
	  type: "POST",
	  url: "../php/verifyFriends.php",
	  data: {type: 'nmr'},
	  success: function(output) {
		//alert(output);
	  }});
}
function importFriendsLi() {
	window.setTimeout(function(){
	$('#modalTitleText').html('Import Friends');
	$('#modalButtons').html('');	
	$('#modalText').html('Please wait while we import you friends, this can take a couple minutes. <br><img src="../img/loader.gif" width="480px"/>');
	overlay();
				$.ajax({
					type: "POST",
					url: "../php/linkedinImportFriends.php",
					data: { type: "linkedin"},
					success: function(output) {
				
						$('#modalText').html('<center>Myiceberg has succesfully imported your Linkedin friends!<br>'+output);
						$('#modalButtons').html('');
						raw_friend_reload()
						raw_cat_reload()
					}
				});
				}, 2000);
}//typing

function addfam(lfid){
	//alert('function is called');
	ftype = document.getElementById("famtype").value
	ffname = document.getElementById("famfname").value
	flname = document.getElementById("famlname").value
	femail = document.getElementById("famemail").value
	fphone = document.getElementById("famphone").value
	fdob = document.getElementById("famdob").value
	fnotes = document.getElementById("famnotes").value
	
		document.getElementById("addfamhold").innerHTML = "<img src='images/loader.gif' />"
			if (document.getElementById) {
				var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
			}
			if (x) {
				x.onreadystatechange = function() {
					if (x.readyState == 4 && x.status == 200) {
						document.getElementById("addfamhold").innerHTML = x.responseText;
						close()
						get_friend_detail(lfid);
					}
				}
				x.open("GET", "ex_inc/addfamproc.php?fid="+ lfid +"&ftype="+ ftype + "&ffname="+ ffname + "&flname="+ flname + "&femail="+ femail + "&fphone="+ fphone +"&fdob="+ fdob +"&fnotes="+ fnotes, true);
				x.send(null);
			}
	
}
function verify(){
	var nn;
    var val;
    var fals;
    fals = "";
    $('#modalText').prepend('Please wait while we process your request. <br> This should not take more than one minute. <br><img src="../img/loader.gif" width="480px"/>');
	$("#modalText").scrollTop(0);
    $('input:radio').each(function() {
        //alert('founss');
      
        if($(this).attr('class') == '1way'){
            if(this.checked == true){
            val = $(this).attr('value');
            if(val != undefined){
            fals += ","+val
            }
           }
        }

		if($(this).attr('class') == 'rb' && this.checked == true){
			
			//alert('this part is called');
			if(this.checked == true){
            val = $(this).attr('value');
            if(val != undefined){
            fals += ","+val
            }
           }
       //     alert(val);
       /*
            $.ajax({
                type: "GET",
				async: false,
    			url: appbase_url + "php/manageFriends.php",
    			data: {val:val, type: 'multiple2'},
    			success: function(output) {			
    				//alert(output);
    			}
		    }); */
        
        
      }
    });
   // alert(fals)
      $.ajax({
                type: "GET",
				async: false,
    			url: "../php/combineFriends.php",
    			data: {val:val, f: fals},
    			success: function(output) {	
    				//alert(output)		
    			}
		    });
	location.reload();	
}
function mod_gr(){
		$('#modalTitleText').html('Verify first');
        $('#modalTitleText').css('margin-left', '30px');
        $('#modalText').html('Please verify this friend');
        overlay()
}

    
    
