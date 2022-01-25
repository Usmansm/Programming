
function  importFriends(source) {

	if(source == 'facebook'){
		$.ajax({
		type: "POST",
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
				
						closeModal();
						location.reload();
					}
				});
			}
			else{
	
				window.location =  appbase_url + 'logout.php';
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
				
						closeModal();
						location.reload();
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

function deleteFromCat() {
	//alert('JS function deleteFromCat is called');
	$.ajax({
	  type: "POST",
	  url: "../php/categoriesManagement.php",
	  data: { type: "deleteFromCat", friends: friends, catId: selectedCat3},
	  success: function(output) {
	  //alert(output);
	
	  }
	});
	
	$('#modalText').html('<img src="../img/loader.gif" width="480px"/>');
	setTimeout(function(){closeModal(); location.reload();}, 1000);	
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
		$('#modalButtons').html('<input type="button" class="cat_button" value="Close" onClick="closeModal()"/><div class="cat_right" ></div>');
		$('#modalText').html('Please select your friends first');
		overlay();	
	}
	else {
		$('#modalTitleText').html('Delete Selected From Category');
		$('#modalButtons').html('<input type="button" class="cat_button" value="Cancel" onClick="closeModal()"/><input type="button" class="cat_button" value="Delete From Category" onClick="deleteFromCat('+friends+')" style="margin-left:10px;" /><div class="cat_right" ></div>');
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
	//alert(nmrVerify);
	var key = 0;
	var value = '';
	while(nmrVerify >= key){
		value = $('input[name='+key+']:checked').val();
		if(value != 'undefined'){
		//alert(value);
		$.ajax({
	  type: "POST",
	  url: "../php/manageFriends.php",
	  data: { data: value},
	  success: function(output) {
		//alert(output);
	  }
	});		
	key++;
}
	}
	closeModal();
		
}
var html = '';
function promptVerifyFriends(){
	//alert('yup');
		$('#modalTitleText').html('Verify your friends');
		$('#modalButtons').html('<center><input type="button" class="cat_button" value="Save" onClick="processVerifyFriends()"/><div class="cat_right" ></div><input type="button" class="cat_button" value="Close" onClick="closeModal()"/><div class="cat_right" ></div></center> ');
		$('#modalText').load("../php/verifyFriends.php");
		modalWidth(1000);
		html = $('#modalText').html();
		if(html == ''){
			modalWidth(500);
			$('#modalButtons').html('<center><input type="button" class="cat_button" value="Close" onClick="closeModal()"/><div class="cat_right" ></div></center> ');
			$('#modalText').html("You don't have any friends to verify at this moment");
		}
		
		overlay();
}
function loadDevModal (){
	$('#modalText').load("../php/devModal.php");
}
function promptDevModal() {
	$('#modalTitleText').html('Dev');
		$('#modalButtons').html('<input type="button" class="cat_button" value="Refresh" onClick="loadDevModal()"/><div class="cat_right" ></div><input type="button" class="cat_button" value="Test1" onClick="modalWidth(500)"/><div class="cat_right" ></div><input type="button" class="cat_button" value="Test2" onClick="modalWidth(800)"/><div class="cat_right" ></div>');
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

function promptNewUser() {
	//alert('yup');
	$('#modalTitleText').html('Add New MIB Friend Manually');
		$('#modalButtons').html('');
		$('#modalText').load("fi.php");
}

function modalWidth(width){
	$('#modal').css('width', width);
	$('#modalButtons').css('width', width);
	$('#modalText').css('width', width-10);
	$('#modalTitleText').css('width', width);
}


