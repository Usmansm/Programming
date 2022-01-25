// JavaScript Document
jQuery(
  function($)
  {		
  /*----------------------------------------------Getting Result per Alphabet---------------------------------------------------*/
  $('.alphspan').click(function(){
  			$("#alpha").val(1); 						// Deactivate scroll effect
  			$("#loadingImage").css("display","block");  // Load loading Image
			
			
  			alpha=$(this).html();
			
			if($("#pageNo").val()==2){					//Checking Page
				
				smallflist($("#userDisplayId").val(),'alpha',alpha);
				$("#loadingImage").css("display","none");	
			}
			if($("#pageNo").val()!=2){
			
  			datastring="act=&catid=&alpha="+alpha; //Data to be send
								 $.ajax({
				
									type:"GET",
									url:"ex_inc/frnd_listing_alpha.php",
									data:datastring,
									cache: false,
									success:function(response){
									$("#loadingImage").css("display","none");	 // Hide loading Image	
									if($("#category").val()!=''){
									  $("#friend_list_large_hold").html(response);
									  }else{
										$("#fl").html(response);
									  }	
										
									
									},
									fail:function(error){
										alert(error);
										}
								});
  				}
  		});
	/*---------------------------------------------------------------------------------------------------------------------------*/
	
	/*----------------------------------------------Getting Result On the Load Of Body---------------------------------------------------*/
		
								limit=$("#counter").attr('name');
								
									
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
								 	
								  
	/*---------------------------------------------------------------------------------------------------------------------------------*/	
	
/*	
jQuery(function($){
  $('a[href^="mailto:"]').on('click', function(e){
    var email = $(this).attr('href').replace('mailto:', '');
    // submit action to server here.
	alert(email);
  });
});
*/
	
	/*----------------------------------------------Getting Result A-Z-------------------------------------------------------*/						
    
                            $('.alpha_options').click(function(){
								  $("#loadingImage").css("display","block");
								  $("#alpha").val(0); 	 // Activate scroll effect  
								  $("#counter").attr('name','0')
                                  int=$("#counter").attr('name');
								  if($("#pageNo").val()==2){
										smallflist($("#userDisplayId").val(),'page2');
										$("#loadingImage").css("display","none");
											
									}
								if($("#pageNo").val()!=2){
								  
									
								   datastring="firstLimit="+int+"&act=&catid=";
									 $.ajax({
					
										type:"GET",
										url:"ex_inc/frnd_listing.php",
										data:datastring,
										cache: false,
										success:function(response){
										$("#loadingImage").css("display","none");			
										if($("#category").val()!=''){
										  $("#friend_list_large_hold").html(response);
										  }else{
											$("#fl").html(response);
										  }	
											
										
										},
										fail:function(error){
											alert(error);
											}
									});
								  
                                }
                            });
	/*------------------------------------------------------------------------------------------------------------------------------------*/
	
	
	
	
	/*----------------------------------------------Scrolling Effect----------------------------------------------------------------------*/
	$('#friend_list_large_hold').scroll( function()
                              {
							  
							  
							  				
                                if($(this).scrollTop() + 
                                   $(this).innerHeight()              //Scrolling Condition
                                   >= $(this)[0].scrollHeight)
                                {
								
								

								if($("#alpha").val()==0){    // Checking Alphabet condition before scrolling
								
								
								$("#loadingImage").css("display","block");
								
                                  int=$("#counter").attr('name');
								 int=parseInt(int);
								  limit=int+25;
								  
								  if($("#pageNo").val()==2){
								 
								  
									
									
									
								  		limit=int+13;
									
										 $("#counter").attr('name',limit);
										
										datastring="firstLimit="+limit+"&act=&catid=";
								 $.ajax({
				
									type:"GET",
									url:"ex_inc/frnd_listing_page2.php",
									data:datastring,
									cache: false,
									success:function(response){
									
									
													
									$("#friend_list_large_hold").append(response);
										
									$("#loadingImage").css("display","none");
									},
									fail:function(error){
										alert(error);
										}
							});
								  
								  
										
									
									
									
								  
								
								 
                                   
								  }
								  if($("#pageNo").val()!=2){
								  if($("#category").val()!=''){
								  catg=$("#category").val();
								  act="cat";
								  }else{
								  catg="";
								  act="";
								  }
								  
								 datastring="firstLimit="+limit+"&act="+act+"&catid="+catg+"&scroll=yes";
								
								 $.ajax({
				
									type:"GET",
									url:"ex_inc/frnd_listing.php",
									data:datastring,
									cache: false,
									success:function(response){
									if(response=="You don't have any friends yet!"){
										$("#alpha").val(1);
									}
									$("#loadingImage").css("display","none");
									 if($("#category").val()!=''){
									  $("#friend_list_large_hold").append(response);
									  
									  }else{
										$("#fl").append(response);
										
									  }				
									
										
									
									},
									fail:function(error){
										alert(error);
										}
							});
								  $("#counter").attr('name',limit);
                                }
								
								}
								 
								 }
								 
                              });

  }
);
