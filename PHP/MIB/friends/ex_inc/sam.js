/**
 * Calendar Script
 * Creates a calendar widget which can be used to select the date more easily than using just a text box
 * http://www.openjs.com/scripts/ui/calendar/
 *
 * Example:
 * <input type="text" name="date" id="date" />
 * <script type="text/javascript">
 *     	calendar.set("date");
 * </script>
 */
 

 function change(value) {
   
  if (value == "SettingsProfile") {
        $("#alphaFriends").hide();
        $("#category_hold").hide();
        $("#friend_list_large_hold").hide();
        $("#alphalist").hide();
        $("#fd").hide();
        $("#footerFrendsPage").hide();
        $("#UserSettingsPage").show();
        $('#UserSettingsPage').load( 'UserSettings.php');
    }
    else if(value == "Logout"){
        window.location.assign(appbase_url+"logout.php");
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
		
				$('#modalText').html('Please wait while we import you friends, this can take a couple minutes. <br><img src="../img/loader.gif" width="480px"/>');
				$.ajax({
					type: "POST",
					url: "../php/facebookImportFriends.php",
					data: { type: "facebook"},
					success: function(output) {
				
						$('#modalText').html('<center>Myiceberg has succesfully imported your Facebook friends!<br>'+output);
						$('#modalButtons').html('');
					}
				});
			}
			else{
	
				window.location = appbase_url+"logout.php";
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
					}
				});
			}
			else{
		
								window.location = appbase_url+"logout.php?redirect=php/linkedinLogin.php";
			}
		}
	  });
	}
}

function check5(importSource){
  // alert('importSource: '+importSource);
    if(importSource != false){
       $('#modalText').html('Please wait while we import you friends, this can take a couple minutes. <br><img src="../img/loader.gif" width="480px"/>');
    overlay();
   // alert('calling');
    $.ajax({
        			type: "GET",
					url: "../php/class/check.php",
                    data: {'import':importSource},
					success: function(output) {
                        alert(output);
                        if(output != 'true'){
                            window.location = output;
                        }
                        
						$('#modalText').html('<center>Myiceberg has succesfully imported your connections!<br>');
						$('#modalButtons').html('');
					}
		}); 
    }
}

function deleteFromCat(friends) {
var selected = [];
alert('go');
$("input:checkbox[name=delete]:checked").each(function()
{
  selected.push($(this).val());
  //alert($(this).val());
});
//alert(selected);
//alert(friends);

 $.ajax({
	  type: "POST",
	  url: "../php/categoriesManagement.php",
	  data: { type: "deleteFromCat", friendsIDs: friends, catId: selected},
	  success: function(output) {
	  }
	});
	
	$('#modalText').html('<img src="../img/loader.gif" width="480px"/>');
	setTimeout(function(){closeModal(); window.location(appbase_url +'friends');}, 1000);	
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
		//$('#modalButtons').html('<center><input type="button" class="cat_button" value="Save" onClick="processVerifyFriends()"/><div class="cat_right" ></div><input type="button" class="cat_button" value="Close" onClick="closeModal()"/><div class="cat_right" ></div></div><input type="button" class="cat_button" value="Nmr Friends" onClick="test2()"/><div class="cat_right" ></div></center> ');
		$('#modalText').load("../php/verifyFriends.php");
		modalWidth(1000);
		
		overlay();
        $('#saveButton').attr('onClick', 'verify()');
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

function promptNewUser(Userid)   { 
	$('#modalTitleText').html('Add a New Friend');
		//$('#modalButtons').html('Add a New Friend');
        $("#saveButton").show();
        $('#modalText').load("fi.php");
        $("#saveButton").attr("onclick", "Add_Friend_Manualy('" + Userid + "')");
}

function modalWidth(width){
	$('#modal').css('width', width);
	$('#modalButtons').css('width', width);
	$('#modalText').css('width', width-10);
	$('#modalTitleText').css('width', width);
}

window.setInterval(function(){
  $.ajax({
	  type: "POST",
	  url: "../config/config.php",
	  data: {type: 'offline-check'},
	  success: function(output) {
		if(output == 'offline'){
			window.location =appbase_url+"logout.php?type=maintenance";
	  	}
	  }});
}, 30000);
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
					}
				});
				}, 2000);
}//typing
function promptAddFamily(LFID){
	$('#modalTitleText').html('Add family Member');
    $("#saveButton").show();
    $("#saveButton").attr("onclick", "addfam('" + LFID + "')");
	$('#modalText').load('ex_inc/addfam.php');
	overlay();
}

function verify(){
    var val;
    var vals=new Array();
    //alert('called');
    $('input:radio').each(function() {
        //alert('found');
      if($(this).is(':checked')) {
        if($(this).attr('id') == 'single'){
            val = $(this).attr('value');
            //alert('Found single entry with value: '+val);
            $.ajax({
            	type: "POST",
    			url: "../php/manageFriends.php",
    			data: {val:val, type: 'single'},
    			success: function(output) {			
    				alert(output);
    			}
		    });
            //alert('nutin');
        }
        if($(this).attr('id') == 'multiple'){
            val = $(this).attr('value');
            //alert(val);//test
            $.ajax({
                type: "POST",
    			url: "../php/manageFriends.php",
    			data: {val:val, type: 'multiple'},
    			success: function(output) {			
    				//alert(output);
    			}
		    });
       // alert('done');
        }
        
      }
    });

        
    
    console.log(vals);
}

    
    