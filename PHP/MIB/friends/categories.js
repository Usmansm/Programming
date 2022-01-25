function showCat_depricated(catId){
   // alert('hey');
    $('#fl').html('<img src="../img/loader.gif" width="480px"/>');
    //alert('calling');
    $.ajax({
      type: "POST",
	  url: "../php/class/categories.php",
	  data: {type:'showCat', catId:catId},
	  success: function(output) {
		 $('#fl').html(output);
	  }
    });
        
}