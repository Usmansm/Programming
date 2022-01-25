
function  importFriends(source) {
	//alert(source);
	if(source == 'facebook'){
		$.ajax({
		type: "POST",
		url: "../php/importManagement.php",
		data: { type: "facebook"},
		success: function(output) {
			//alert(output);
			if(output == 'true'){
				alert('redirect2');
				$('#modalText').html('Please wait while we import you friends, this can take a couple minutes. <br><img src="../img/loader.gif" width="480px"/>');
				$.ajax({
					type: "POST",
					url: "../php/facebookImportFriends.php",
					data: { type: "facebook"},
					success: function(output) {
						//alert('finished');
						closeModal();
						location.reload();
					}
				});
			}
			else{
				//alert('redirect');
				window.location = 'http://ec2-72-44-38-246.compute-1.amazonaws.com/dev/logout.php';
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
			//alert(output);
			if(output == 'import'){
				//alert('yeah');
				window.location = 'http://ec2-72-44-38-246.compute-1.amazonaws.com/dev/php/linkedinImportFriends.php';
			}
			else{
				//alert('going to logout');
				window.location = 'http://ec2-72-44-38-246.compute-1.amazonaws.com/dev/logout.php?redirect=php/linkedinLogin.php';
			}
		}
	  });
	}
}

function deleteFromCat() {
	$.ajax({
	  type: "POST",
	  url: "../php/categoriesManagement.php",
	  data: { type: "deleteFromCat", friends: friends, catId: selectedCat},
	  success: function(output) {
		 // alert(output);
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