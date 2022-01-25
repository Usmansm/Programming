ccq = 0;
clsel = "a"
isonfd = 0
var s;
function onpageloaded(){
  //  document.getElementById("friendsearch_input").focus();
  //  DENI: line above was breaking some modals in some casese so I put it in comment for what you was useing this line ? 
}
window.onload=onpageloaded()
function displaysmallnotification(utext){
	if(!utext){
		utext = "Intercepted!"
	}
	document.getElementById("smallnotification").style.display="inline"
	document.getElementById("smallnotificationtext").innerHTML=utext
	setTimeout(function(){document.getElementById("smallnotification").style.display="none"},9000);
}
function catedit(catid){
	$('#modalTitleText').html('Edit Category');
    $("#saveButton").show();
    $("#saveButton").attr("onclick", "editcat("+ catid +")");
	//$('#modalButtons').html('<div class="cat_right" ></div>');
	$('#modalText').html('<table border="0">            <tr>         	<form>            <td><label for="catTitle">Title:</label></td>            <td><input id="catTitle" type="text" placeholder="Category Title" name="catTitle"/></td></tr><tr>            <td style="vertical-align:top;">Description:</td>            <td><textarea cols="30" id="catDesc" rows="8" name="catDesc" placeholder="Category Description"/></td>            </form>            </tr>            </table>');
	overlay();
}
function editcat(catid){
	catname = document.getElementById("catTitle")
	catdesc = document.getElementById("catDesc")
}
function disevnsmod(){
displaysmallnotification("<img src='images/evnico.png' /> Evernote integration successful!")
      $('#modalTitleText').html('Successfuly connected with Evernote');
	  $('#modalButtons').html('');
	$('#modalText').html('We have successfuly connected your Myiceberg account with your Evernote account!<br />We will now sync your tags and categories.');
	overlay();

}
function hidesmallnotification(){
	document.getElementById("smallnotification").style.display="none"
}
function growcontent(or_shrink){
	if(!or_shrink){
		or_shrink = false
	}
	if(or_shrink != true){
	document.getElementById("friendslist").style.width="19%"
	document.getElementById("bigcontent").style.display="inline"
	}
	else{
	document.getElementById("bigcontent").style.display="none"
	document.getElementById("friendslist").style.width="74%"
	}
}
function getOffset(el){
    var _x = 0;
    var _y = 0;
    while(el && !isNaN(el.offsetLeft) && !isNaN(el.offsetTop)){
        _x += el.offsetLeft - el.scrollLeft;
        _y += el.offsetTop - el.scrollTop;
        el = el.offsetParent;
    }
    return { top: _y, left: _x };
}
function position_searchfilter_box(){
	buttonx = getOffset(document.getElementById('friendsearchinput')).left;
	buttony = getOffset(document.getElementById('friendsearchinput')).top;
	bwidth = 27;
	bheight = 33;
	fsinputx = getOffset(document.getElementById('friendsearch_button')).left;
	fsinputwidth = fsinputx - buttonx + bwidth - 2;
	document.getElementById("searchfilter_box").style.width=fsinputwidth+"px"
	newwidth = fsinputwidth + bwidth;
	newboxtop = buttony + bheight;
	newboxleft = buttonx + bwidth;
	newboxleft = newboxleft - bwidth;
	document.getElementById("searchfilter_box").style.top=newboxtop+"px"
	document.getElementById("searchfilter_box").style.left=newboxleft+"px"
	document.getElementById("searchfilter_box").style.display="inline"
	document.getElementById("friendsearchinput").focus()
}
function position_verbubble(){
	buttonx = getOffset(document.getElementById('verbutton')).left;
	buttony = getOffset(document.getElementById('verbutton')).top;
	buttonlen = document.getElementById('verbutton').style.width
	buttony = buttony - 9
	buttonx = buttonlen - 5 + buttonx
	document.getElementById("verbubble").style.top=buttony+"px"
	document.getElementById("verbubble").style.left=buttonx+"px"
}
function update_verbubble(){
	if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			//alert(x.responseText+" --This alert will be removed shortly!--")
			document.getElementById("verbubble").innerHTML=x.responseText
			verr = document.getElementById("verbubble").innerHTML
			if(verr != "0"){
				document.getElementById("verbubble").style.display="inline"
			}
		}
		}
		x.open("GET", "ex_inc/verification_count.php", true);
		x.send(null);
		}
}
function change_fsbutton_class(tstate,fromclosebutton){
	tshow = document.getElementById("searchfilter_box").style.display
	if(tstate == 1 && tshow == "none"){
		document.getElementById("friendsearch_button").className="friendsearch_button2"
		document.getElementById("friendsearchinput").style.borderTop="1px solid #17458b"
		document.getElementById("friendsearch_button").style.borderTop="1px solid #17458b"
		position_searchfilter_box()
	}
	else{
		document.getElementById("friendsearch_button").className="friendsearch_button"
		document.getElementById("searchfilter_box").style.display="none"
			document.getElementById("friendsearchinput").style.borderTop="none"
		document.getElementById("friendsearch_button").style.borderTop="none"
		if(fromclosebutton == true){
		document.getElementById("friendsearchinput").focus()
		}
	}
}
function checkbox_toggle(source){
  checkboxes = document.getElementsByName('friend_checkbox');
  //cfid = 0
  for(var i in checkboxes){
    checkboxes[i].checked = source.checked;
   // toggle_friend_checkboximg(cfid)
   // cfid ++;
   }
}
function fbimport(){
	document.getElementById("body").style.overflow="hidden"
	document.getElementById("modtrans").style.display="inline"
	document.getElementById("fbimportbody").style.display="inline"
	if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("modtrans").style.display="none"
			document.getElementById("fbimportbody").style.display="none"
			document.getElementById("friend_list_large_hold").innerHTML="<img src='images/loadera.gif' />"
			remove_widget('get_started')
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("friend_list_large_hold").innerHTML=x.responseText
		}
		}
		x.open("GET", "ex_inc/f_list_l.php", true);
		x.send(null);
		}
		}
		}
		x.open("GET", "../php/facebookImportFriends.php", true);
		x.send(null);
		}
}
function liimport(){
	document.getElementById("body").style.overflow="hidden"
	document.getElementById("modtrans").style.display="inline"
	document.getElementById("liimportbody").style.display="inline"
	if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("modtrans").style.display="none"
			document.getElementById("liimportbody").style.display="none"
			displaysmallnotification("Linkedin import successful")
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("friendslist_body").innerHTML=x.responseText
		}
		}
		x.open("GET", "ex_inc/f_list_l.php", true);
		x.send(null);
		}
		}
		}
		x.open("GET", "../php/linkedinImportFriends.php", true);
		x.send(null);
		}
}
function toggle_friend_checkboximg(fid){
	csrc = document.getElementById(fid+"_friend_checkboximg").src
	if(csrc == appbase_url + "friends/images/friend_checkbox.png"){
		document.getElementById(fid+"_friend_checkboximg").src= appbase_url + "friends/images/friend_checkbox2.png"
	}
	else{
		document.getElementById(fid+"_friend_checkboximg").src= appbase_url + "friends/images/friend_checkbox.png"
	}
}
//Delete friend
function frienddelete(){
	//var friends = new Array();
	var friends;
	checkboxes = document.getElementsByName('friend_checkbox');
	  for(var i in checkboxes){
	  	if(checkboxes[i].checked == true){
	  		friends += ","+checkboxes[i].id
	  		//friends.push(checkboxes[i].id)
	  	}
   }
   if(friends != ""){
   	//alert("friends are selected")
   	$('#modalTitleText').css('margin-left', '30px');
   	$('#modalTitleText').html('Delete Selected From Category');
   	$('#modalText').html("Delete selected friends?");
   	$("#saveButton").show();
   	$("#saveButton").html("Ok");
    $("#saveButton").attr("onclick", "delaction('" + friends + "')");
   	overlay()
   }
   else{
   	alert("No friends selected")
   }

	  	

}

//--

function delaction(friends){
	//hide()
	close();
	   $("#saveButton").html("Save");
	    if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			raw_friend_reload()
		}
		}
		x.open("GET", "ex_inc/delete_friend.php?fs="+friends, true);
		x.send(null);
		}
}

function checkselaction(){
	cact = document.getElementById("friend_control_action").value
	if(cact == "delfriend"){
		frienddelete()
	}
}
function delfriend() {
  friends = [];
   $('body input[type=checkbox]').each(function () {
		if(this.checked){
			var attr = $(this).attr('id');
			if(attr){
				friends.push(attr);
			}
		}
	});
  if(friends.length <= 0) {
    alert('No friends selected');
  }
  else {
    var key = friends.toSource();

    key = key.split('["').join('');
    key = key.split('"]').join('');
    key = key.split('", "').join('|');
    alert("You are deleting")
    $('#modalTitleText').html('Delete friends');
    $('#modalButtons').html('CLOSE BUTTON');
	  $('#modalText').load('ex_inc/delFriend.php?friends=' + key);
    overlay();
  }  
}
function cancel_mod(which){
	if(which == "delfriend"){
		document.getElementById("modtrans").style.display="none"
		document.getElementById("delfriend").style.display="none"
	}
}
function remove_widget(widget_name){
	element = document.getElementById(widget_name+"_widget");
	element.parentNode.removeChild(element);
}
function show_cmenu(){
	document.getElementById("context_menu").style.left=window.event.clientX+"px"
	document.getElementById("context_menu").style.top=window.event.clientY+"px"
	document.getElementById("context_menu").style.display="inline"
}
function hide_cmenu(){
	document.getElementById("context_menu").style.display="none"
}
function raw_friend_reload(){
	/*if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	document.getElementById("friend_list_large_hold").innerHTML="<img src='images/loadera.gif' />"
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("friend_list_large_hold").innerHTML=x.responseText
		}
		}
		x.open("GET", "ex_inc/f_list_l.php", true);
		x.send(null);
		} */
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
		
}
function sel_letter(letter){
	if(letter != "clear"){
		document.getElementById("alpha_"+clsel).className="alphspan"
		document.getElementById("alpha_"+letter).className="alphaspan_selected"
		clsel = letter
	}
	else{
		document.getElementById("alpha_"+clsel).className="alphspan"
		clsel = "a"
	}
}
function getLetter(letter){
	sel_letter(letter);
	  $("#alphaFriends").html('');
	  $("#alphaFriends").load("ex_inc/alphaFriends.php?letter="+letter, function(data){ 
		  $('#friend_list_large_hold').animate({
			 scrollTop: 0
		  }, '10000');
	  });
}
function smallflist(ffid,noo,data){
if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	document.getElementById("friend_list_large_hold").innerHTML="<img src='images/loadera.gif' />"
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("friend_list_large_hold").innerHTML=x.responseText
			window.location.hash=ffid
		}
		}
		if(noo=="scroll"){   										// Checking Scrolling Effect
			
			x.open("GET", "ex_inc/f_list_s_scroll.php?limit="+data, true);
			
		}else{
			if(noo=="alpha"){												// Sort as per alphabet
				
				x.open("GET", "ex_inc/f_list_s_alpha.php?alpha="+data, true);
			}else{
					if(noo=="page2"){												//Page 2 data
					x.open("GET", "ex_inc/f_list_s.php", true);
					}else{
						
							x.open("GET", "ex_inc/f_list_s.php", true);
						
					}
			}
		}
		x.send(null);
		}
}
function getCat(catId){
	  $("#alphaFriends").html('');
	  alert('selected');
	  $("#alphaFriends").load("ex_inc/catDisplay.php?catId="+catId, function(){ 
		  $('#friend_list_large_hold').animate({
			 scrollTop: 0
		  }, '10000');
	  });
	  selectAddToCat2(catId);
}
function overlay(){
	document.getElementById("overlay").style.visibility = 'visible';
}

function close() {
    document.getElementById("overlay").style.visibility = 'hidden';
    $("#saveButton").hide();
    $('#modalTitleText').css('margin-left', '30%');
    modalWidth(500);
}

function closeModal() {
    document.getElementById("overlay").style.visibility = 'hidden';
    $("#saveButton").hide();
    $('#modalTitleText').css('margin-left', '30%');
    modalWidth(500);
}

var friends = new Array(); 

function promptAddToCat(){

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
		$('#modalTitleText').html('Add Selected To Category');
		//$('#modalButtons').html('<input type="button" class="cat_button" value="Close" onClick="closeModal()"/><div class="cat_right" ></div>');
        //$("#saveButton").show();
        $('#modalTitleText').css('margin-left', '30px');
		$('#modalText').html('Please select your friends first');
		overlay();	
	}
	else {
		$('#modalTitleText').html('Add Selected To Category');
	//	$('#modalButtons').html('<center style="margin-top:5px;"><input type="button" class="cat_button" value="Cancel" onClick="closeModal()"/><div class="cat_right" ></div><input type="button" class="cat_button" value="Add To Category" onClick="addToCat('+friends+')" style="margin-left:10px;" /><div class="cat_right" ></div></center>');
		$("#saveButton").show();
        $("#saveButton").attr("onclick", "addToCat('" + friends + "')");
        $('#modalTitleText').css('margin-left', '30px');
		$('#modalText').load("../php/categoriesManagement.php", {type: 'catList'});
		overlay();
	}
}

function eteFromCat(){
	
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

var selectedCat2 = '';
function selectAddToCat2(catId){
	$('#'+selectedCat2+'2').css('background-color', '#FFF');
	selectedCat2 = catId;
	$('#'+catId+'2').css('background-color', '#a0c5eb');
}
function addingToCatError(textForModal){
	$('#modalTitleText').html('Error');
	$('#modalText').load(textForModal);
	overlay();
}
function addToCat() {
var reloadcatID = 1;  // category to be loaded
	$("#modalText :checkbox:checked").each(function(o){
		
  var chkID = $(this).attr("value");
  reloadcatID=chkID;
  var fid = friends.join(",");
  
 $.ajax({
	  type: "GET",
	  async: false,
	  url: "../php/class/categories.php",
	  data: { type: "addToCat", friends: fid, catId: chkID},
	  success: function(output) {
		//alert(output);
		//Disp_News_Categories();
	  }
	});

});
	$('#modalText').html('<img src="../img/loader.gif" width="480px"/>');
	//setTimeout(function(){closeModal(); location.reload();}, 1000);
	setTimeout(function(){closeModal(); showCat(reloadcatID); makeblue(reloadcatID+'cat');}, 1000);
//alert(reloadcatID);

$.ajax({
	  type: "POST",
	 
	  url: "ex_inc/categories_show.php",
	 
	  success: function(output) {
		  
		//alert(output);
		//Disp_News_Categories();
		$("#category_hold").html(output);
		
	  }
	});

}

function deleteFromCat(friends) {
	//alert ('test11');
	$("#modalText :checkbox:checked").each(function(o){	
	var chkID = $(this).attr("value");
	
	//alert (chkID);
 $.ajax({
	  type: "GET",
	  async: false,
	  url: "../php/class/categories.php",
	  data: { type: "deleteFromCat", friendsIDs: friends, catId: chkID},
	  success: function(output) {
	    //alert(output);
		//alert('something');
	  }
	});
});
	$('#modalText').html('<img src="../img/loader.gif" width="480px"/>');
	setTimeout(function(){closeModal(); location.reload();}, 1000);		
}

function promptDeleteCat(catId,catName){
	$('#modalTitleText').html('Delete Category');
	$("#saveButton").show();
    $("#saveButton").attr("onclick", "deleteCat("+catId+")");
	$('#modalText').html('Are you sure you want to delete the category \"'+ catName +'\"');
	overlay();
}

function deleteCat(catId){
	$.ajax({
	  type: "POST",
	  url: "../php/categoriesManagement.php",
	  data: { type: "removeCat", cat: catId},
	  success: function(output) {
	  	close()
	  	raw_cat_reload()
	  }
	});
//	hideModalError();
	//$('#modalText').html('<img src="../img/loader.gif" width="480px"/>');
	//setTimeout(function(){close(); location.reload();}, 1000);	
}

function promptAddCat(){
	$('#modalTitleText').html('Add Category');
    $("#saveButton").show();
    $("#saveButton").attr("onclick", "checkAddCat()");
	//$('#modalButtons').html('<div class="cat_right" ></div>');
	$('#modalText').html('<table border="0">            <tr>         	<form>            <td><label for="catTitle">Title:</label></td>            <td><input id="catTitle" type="text" placeholder="Category Title" name="catTitle"/></td></tr><tr>            <td style="vertical-align:top;">Description:</td>            <td><textarea cols="30" id="catDesc" rows="8" name="catDesc" placeholder="Category Description"/></td>            </form>            </tr>            </table>');
	overlay();
}

function promptEditCat(catId){
	$('#modalTitleText').html('Edit Category');
	$("#saveButton").show();
    $("#saveButton").attr("onclick", "checkEditCat("+catId+")");
	$('#modalText').load("../php/categoriesManagement.php", {type: 'catEditData', catId: catId});

	overlay();
}

function checkEditCat(catId){
	var catTitle = $('#catTitle').val();
	var catDesc = $('#catDesc').val();	
	if(catTitle != '' || catDesc != ''){
		editCat(catDesc, catTitle, catId);
		close();
		raw_cat_reload()
	}
	else {
		showModalError('Please fill in all the fields');			
	}			
}

function checkAddCat(){
    //alert('function is called');
    //alert('function is called');
	var catTitle = $('#catTitle').val();
	var catDesc = $('#catDesc').val();	
	if(catTitle != '' || catDesc != ''){
		addCat(catDesc, catTitle);
	}
	else {
		showModalError('Please fill in all the fields');			
	}			
}

function showModalError(error){
	$('#modalError').html('Please fill in all the fields');
	$('#modalError').attr('class', 'friend_div');
	$('#modalError').fadeIn();
}

function hideModalError(){
	$('#modalError').html('');
	$('#modalError').removeAttr('class');
	$('#modalError').hide();
}


function addCat(catDesc, catTitle){
	$.ajax({
	  async: false,
	  type: "GET",
	  url: "../php/class/categories.php",
	  data: { type: "addCat", catDescription: catDesc, catName: catTitle},
	  success: function(output) {
	      
	  }
	  
	});
	$('#modalText').html('<img src="../img/loader.gif" width="480px"/>');
	setTimeout(function(){closeModal(); location.reload();}, 1000);

}
function editCat(catDesc, catTitle, catId){
	$.ajax({
	  type: "POST",
	  url: "../php/categoriesManagement.php",
	  data: { type: "editCat", catDesc: catDesc, catTitle: catTitle, catId: catId},
	  success: function(output) {
	  }
	});

}

function promptCloneCat(catId){
	$('#modalTitleText').html('Clone Category');
		$("#saveButton").show();
    $("#saveButton").attr("onclick", "checkCloneCat("+catId+")");

	$('#modalText').load("../php/categoriesManagement.php", {type: 'catEditData', catId: catId});
	overlay();
}

function checkCloneCat(catId){
	var catTitle = $('#catTitle').val();
	var catDesc = $('#catDesc').val();	
	if(catTitle != '' || catDesc != ''){
		cloneCat(catDesc, catTitle, catId);
		close()
		raw_cat_reload()
	}
	else {
		showModalError('Please fill in all the fields');			
	}			
}
function debugoutput(dout){
	document.getElementById("debug_displayout").innerHTML=dout
	document.getElementById("debug_display").style.display="inline"
}
function hide_debug(){
	document.getElementById("debug_display").style.display="none"
}
function cloneCat(catDesc, catTitle, catId){
	$.ajax({
	  type: "POST",
	  url: "../php/categoriesManagement.php",
	  data: { type: "cloneCat", catDesc: catDesc, catTitle: catTitle, catId: catId},
	  success: function(output) {
	  }
	});
	$('#modalText').html('<img src="../img/loader.gif" width="480px"/>');
	//setTimeout(function(){closeModal(); location.reload();}, 1000);
}

function promptImportFriends (){
	$('#modalTitleText').html('Import Friends');
	$('#modalButtons').html('');
	//var modalcontent = document.getElementById('importFirendsModal').innerHTML
	//$('#modalText').html(modalcontent);
	//alert('functionis called');

	//overlay();
	$.ajax({
	  type: "GET",
	  url: "../php/verifyFriends.php",
	  data: { type: "nmr"},
	  success: function(output) {
		  if(output > 0){
			  ImportFriendsError();
		  }
		  else {
				$('#modalText').load("ex_inc/sources.php");
				//$('#modalText').html(modalcontent);
				//	alert('functionis called');

				if($('#modalText').html() == ''){
				  $('#modalText').html('No friends left to verify.');
			  }
	overlay();
		  }
	  }
	});
	
}

function promptGetingStarted(){
	$('#modalTitleText').html(' Getting started');
	$('#modalText').load('ex_inc/GetingStarted.php');
	modalWidth(700);
	overlay();
}



function ImportFriendsError (){
	$('#modalTitleText').html('Import Friends Warning');
	$('#modalButtons').html('');
	$('#modalText').html('<span style="color:red;">You have friends who are currently not verified.  You must verify these friends before you can import any more friends</span>');
	overlay();
}



function get_friend_detail(ffid,noo,data){
if(noo!=1)
{
//alert("Alert called with no argument"+ffid);
}
if(noo=="page2"){
	$("#pageNo").val(2);
	$("#alpha").val(0);
	$("#counter").attr('name',0);			// Reset All the datas
	$("#userDisplayId").val(ffid);
	
}
	$("#fd").attr("style", "display: block")
	document.getElementById("fd").innerHTML="<img src='images/loadera.gif' />"
	document.getElementById("friend_list_large_hold").style.width="132px"
		if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("fd").innerHTML=x.responseText
			if(noo != 1){
			smallflist(ffid,noo,data)
     isonfd = 1
    }
		}
		}
		x.open("GET", "ex_inc/friend_detail.php?fid="+ffid, true);
		x.send(null);
		}
}
function clearfs(){
	document.getElementById("fl").innerHTML=""
}


function letter(wh){
	window.location.hash=wh
	window.location.hash="top"
}


/* this function was used for loading Freind Large List page but now we are not useing it any more.. 
function tofriends(wh){
document.getElementById("friend_list_large_hold").innerHTML=""

	document.getElementById("friend_list_large_hold").style.width="52%"
	if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
 // document.getElementById("sfl").innerHTML=""
 document.getElementById("fd").innerHTML=""
			document.getElementById("friend_list_large_hold").innerHTML=x.responseText
     	letter(wh)
      isonfd = 0
			//sel_letter("clear")
		}
		}
		x.open("GET", "ex_inc/f_list_l.php", true);
		x.send(null);
		}
}
*/
function fdcollapse(pid, eid){
    cdis = document.getElementById(eid).style.display
    if(cdis == "inline"){
      document.getElementById(eid).style.display="none"
      document.getElementById(pid).src="images/+.png"
    }
    else{
      document.getElementById(eid).style.display="inline"
      document.getElementById(pid).src="images/-.png"
    }
}


function simpleajax(ext_file,get_vars){
    if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			return x.responseText;
		}
		}
		x.open("GET", ext_file+"?"+get_vars, true);
		x.send(null);
		}
  
}

function note_char_count(tboxname,dname){
  currcon = document.getElementById(tboxname).value
  maxchars = 300
  lof = currcon.length
  charsleft = maxchars - lof
  if(charsleft > 0){
    fcolor = "<font color=green >"
  }
  else{
    fcolor = "<font color=red >"
  
}
  distext = "Remaining characters: "+ fcolor + charsleft + "</font>"
  document.getElementById(dname).innerHTML=distext
}
function evns(){
	Load_News_Page()
	/*
    if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("apphold").innerHTML=x.responseText;
      */
     disevnsmod()
/*		}
		}
		x.open("GET", "UserSettings.php", true);
		x.send(null);
		}*/
}
function del_evn_post(pid, eid, epid){
    document.getElementById(pid).src="images/blue_loader.gif"
    if(document.getElementById){
    	var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			//x.responseText;
            document.getElementById(eid).style.display="none"
            raw_cat_reload("news")
            displaysmallnotification("<img src='images/check.png' /> Post deleted!!")
		}
		}
		x.open("GET", "ex_inc/proc.php?a=delevn&pid="+epid, true);
		x.send(null);
		}
}
function del_evn_frnd_post(pid, eid, epid, fid){
    document.getElementById(pid).src="images/blue_loader.gif"
    if(document.getElementById){
        var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			//x.responseText;
            document.getElementById(eid).style.display="none"
            displaysmallnotification("<img src='images/check.png' /> Post deleted!!")
		}
		}
		x.open("GET", "ex_inc/proc.php?a=delevnfrnd&pid="+epid+"&fid="+fid, true);
		x.send(null);
		}
}
function removefbpost(pid,rfid){
    document.getElementById("trash_"+pid).src="images/blue_loader.gif"
    if(document.getElementById){
    	var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			//x.responseText;
            document.getElementById("post_"+pid).style.display="none"
            displaysmallnotification("<img src='images/check.png' /> Post deleted!!")
		}
		}
		x.open("GET", "ex_inc/proc.php?a=delfb&pid="+pid+"&fid="+rfid, true);
		x.send(null);
		}
}
function raw_cat_reload(act){
	if(act == "news"){
		lvar = "&act=news"
	}
	else{
		lvar = ""
	}
if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("category_hold").innerHTML=x.responseText
		}
		}
		x.open("GET", "../php/class/categories.class.php?a=reload"+lvar, true);
		x.send(null);
		}
}

//Function to load news_cat_display.php to display only 1 category of notes
function shownewscat(nid){
	document.getElementById("NewsStream").innerHTML="<img src='images/loadera.gif' />"
	if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("NewsStream").innerHTML=x.responseText
		}
		}
		x.open("GET", "../Users/news_cat_display.php?ncid="+nid, true);
		x.send(null);
		}
}
function showCat(cid){
	if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	$("#category").val(cid);
	document.getElementById("friend_list_large_hold").innerHTML="<img src='images/loadera.gif' />"
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			document.getElementById("friend_list_large_hold").innerHTML=x.responseText
		}
		}
		x.open("GET", "ex_inc/f_list_l.php?act=cat&catid="+cid, true);
		x.send(null);
		}
}
function makeblue(tel){
	spawns = document.getElementsByName('cspann');
  //cfid = 0
  for(var i in spawns){
  	if(spawns[i].id != undefined){
    document.getElementById(spawns[i].id).style.background = "white";
   }
   // toggle_friend_checkboximg(cfid)
   // cfid ++;
   }
   document.getElementById(tel).style.background="#9cc6ff"
}

function evn_noterefresh(){
if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			Load_News_Page();
		}
		}
		x.open("GET", "ex_inc/evn_checkfornotes.php?re=0", true);
		x.send(null);
		}
}

function evn_refresh(){
	document.getElementById("evn_load").src="images/evn_load.gif"
    if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}			
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {
			evn_noterefresh()
		}
		}
		x.open("GET", "ex_inc/evn_tagsync.php?re=0", false);
		x.send(null);
		}
}

function fbharvest(uid){
	$('#modalTitleText').html('Retrieveing your FB posts');
	$('#modalText').html('Please wait while we search your Facebook news feed for posts which meet your current key words. This may take a couple minutes.<br /><img src="../img/loader.gif" width="480px"/>');
	overlay();
	setTimeout("fbharvest2("+ uid +")",500)//commit comment
}
function fbharvest2(uid){
if(document.getElementById){
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}
	if(x) {
		x.onreadystatechange = function() {
		if(x.readyState == 4 && x.status == 200) {

			//alert("done")
			//alert(x.responseText)
			$('#modalText').html('Your Facebook news feed search is completed.');
			//hide()
		}
		}
		x.open("GET", "../chron/fb_chron_server.php?usr="+uid, true);
		x.send(null);
		}
}
function togopts(optid){
	cdisplay = document.getElementById(optid+"-opts").style.display
	if(cdisplay == "none"){
		document.getElementById(optid+"-opts").style.display="inline"
		document.getElementById(optid+"-img").src="images/-.png"
	}
	else{
		document.getElementById(optid+"-opts").style.display="none"
		document.getElementById(optid+"-img").src="images/+.png"
	}
}


function sameverifychecks(divid,thischeckid,difid){
   $('#'+ divid +' input[type=radio]').each(function () {
		if(this.id != thischeckid && this.id != difid && this.id.indexOf("dif") > -1){
			this.checked = true
		}
		else if(this.id != thischeckid && this.id != difid && this.id.indexOf("same") > -1){
			this.checked =false
			this.disabled=true
		}
	});
}

function difverifychecks(divid,thischeckid,difid){
   $('#'+ divid +' input[type=radio]').each(function () {
		this.disabled = false
	});
}
