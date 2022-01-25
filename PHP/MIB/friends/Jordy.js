$(document).ready(function() {
	 //alert('Hi!');
  //Event handler for search feature.
  //18 june 2013.



  $("#friendsearchinput").keyup(function () {
    var key =  $('#friendsearchinput').val();

    key = key.split(' ').join('+');

    $('#friend_list_large_hold').load('ex_inc/SearchFriends.php?key='+ key);
  });


  
})