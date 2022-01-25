//TC update
ERRORdisplaying = 0 ;

function ErrorVerificationModal(){
	if (ERRORdisplaying == 0){
		$('#modalTitleText').html('ERROR');
		$("#modalText").html("The email was not found in Myiceberg;  please contact support@myiceberg.com and paste your email address into the body of the message.");
		
		overlay();	
		window.ERRORdisplaying = 1;
	}
}
function ComfirmVerificationModal(){
	$('#modalTitleText').html('Email verified');
	$("#modalText").html("Congratulations your email has been verified, please log in.");
	
	
	overlay();
}

function OpenForgotPasswordModal(){
	$('#modalTitleText').html('Forgot Password');
	$("#modalText").html("Please enter your email for instructions to change password or verify your email. <br> <input id='modalInputEmail' style='width: 355px;'></input> <button id='ResetPasswordButtonModal' onclick='forgetPassword();'>Reset Password</button>");
	
	overlay();

}

function login(){
	var email = $("input#email").val(); 
    var password = $("input#password").val();
	if(email === '' || password === '' ){
        alert('Please fill in all the required fields');
    }else{
		
		$.ajax({
        			type: "GET",
					url: "login.php",
					data: { 'email' : email, 'password' : password},
					success: function(output) {
						
				/*			$('#modalText').html('<center>Your password has been changed successfully</center>');
							$('#modalButtons').html('');
						overlay();
                       
						*/
						if(output=="success"){
							//location.href='friends/?manual=yes'
							location.href='friends/index.php?GettingStarted=true'
						}else{
							$('#modalText').html('<center>Login unsuccessful. Please try again, verify your email address or use forget password option</center>');
							$('#modalButtons').html('');
							overlay();
						}
						
                        
					}
				});
	}
   
}
function changePassword(){
	var password1 = $("input#newPassword").val(); 
    var password2 = $("input#rePassword").val();
	var id = $("input#userId").val();
	
	if(password1!=password2){
		alert("Both password didnt match");
	}else{
		$.ajax({
        			type: "GET",
					url: "changePassword.php",
					data: { 'user' : id, 'password' : password1},
					success: function(output) {
						
							$('#modalText').html('<center>Your password has been changed successfully</center>');
							$('#modalButtons').html('');
						overlay();
                       
						
						
                        
					}
				});  
	}
}
function forgetPassword(){
	
	
	if( $("#modalInputEmail").val()!=null)
	{
	var email = $("#modalInputEmail").val();
	}
	else if($("input#email2").val()!=null)
	{
	var email = $("input#email2").val();
	}

	if (document.getElementById) {
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	}
	if (x) {
		x.onreadystatechange = function() {
			if (x.readyState == 4 && x.status == 200) {
				document.getElementById("modalText").innerHTML = ('<center>An email has been sent to confirm the changing of your password</center>');

			}
		}
		x.open("POST", "forgetPassword.php", true);
		x.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		x.send("&email=" + email);

	}
		/*$.ajax({
        			type: "GET",
					url: "forgetPassword.php",
					data: { 'email' : email},
					complete: function(output) {
							$('#modalText').html(output  + '<center>An email has been sent to confirm the changing of your password</center>');
							$('#modalButtons').html('');
							$("#overlay").css('visibility','true');
                       
						
						
                        
					}
				});  */
}
function register(){
    
    var result = checkReg();
//	alert(result);
    if(result != true){
        $('#Error').html(result);
    }
    else {
        var firstName = $("input#firstName").val(); 
    var lastName = $("input#lastName").val(); 
    var email = $("input#email2").val();
      var passcode= $("input#passcode").val(); 
    var pass1 = $("input#passwordReg").val(); 
    var pass2 = $("input#confirmEmail").val(); 
      $.ajax({
        		type: "GET",
					url: "php/class/manualUser.php",
					data: {'firstName': firstName, 'lastName' : lastName,'passcode' : passcode, 'email' : email, 'pass1' : pass1, 'pass2' : pass2},
					success: function(output) {
						var n=output.match(/emailExist/gi);
						var m=output.match(/notVerified/gi);
						var k=output.match(/beta/gi);
						//alert(k);
						//var tempEmail=$("input#email2").val()
						
						if(k!=null){
							$('#modalText').html('<center>Please Enter Correct Passcode</center>');
							$('#modalButtons').html('');
							
						}
						
						else if(n!=null){
							$('#modalText').html('<center>Email entered is already associated with a active Myiceberg account. If you forgot your password please <a href="#" onclick="forgetPassword(); ">click here</a></center>');
							$('#modalButtons').html('');
							
						}else{ 
							if(m!=null){
								$('#modalText').html('<center>Thank You and Welcome to Myiceberg...Please check your inbox to verify your email and complete your registration</center>');
								$('#modalButtons').html('');
							}else{
								$('#modalText').html('<center>Thank You and Welcome to Myiceberg...Please check your inbox to verify your email and complete your registration</center>');
								$('#modalButtons').html('');
							}
						}
                       // alert(output);
						overlay();
                        
					}
				});  
    }
}

function checkReg(){
    var firstName = $("input#firstName").val(); 
    var lastName = $("input#lastName").val(); 
    var email = $("input#email2").val(); 
    var pass1 = $("input#passwordReg").val(); 
    //var email2 = $("input#confirmPasswordReg").val();  
	 var email1 = $("input#confirmEmail").val(); 
	  var passcode = $("input#passcode").val(); 
    var error = ''
	 if(passcode === ''){
        return 'Please fill in all the required fields';
    }
    if(firstName === ''){
        return 'Please fill in all the required fields';
    }
    if(lastName === ''){
        return 'Please fill in all the required fields';
    }
    if(pass1 === ''){
        return 'Please fill in all the required fields';
    }
    if(email1 === ''){
        return 'Please fill in all the required fields';
    }
    if(email != email1){
        return 'The Email do not match';
    }
    if (email !== "") {  // If something was entered
        if (!isValidEmailAddress(email)) {
            $("label#email_error").show(); //error message
            $("input#sc_email").focus();   //focus on email field
            return 'Please enter a valid email address';  
        }
        //return 'Please fill in all the required fields.';
    } 
    return true;
}
function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}

function overlay(){
	el = document.getElementById("overlay");
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";

}

function close() {
     document.getElementById("overlay").style.visibility = 'hidden';
}

function closeModal() {
     document.getElementById("overlay").style.visibility = 'hidden';
}