function Load_News_Page(Uid){
	
	  raw_cat_reload("news")
    $("#alphaFriends").show();
    $("#category_hold").show();
    $("#friend_list_large_hold").show();
    $("#alphalist").show();
    $("#fd").show();
    $("#UserSettingsPage").hide();
    
  	document.getElementById("friend_list_large_hold").style.width="125px";
   smallflist(Uid)
  $('#fd').load( '../Users/News_index.php'); 
  
}