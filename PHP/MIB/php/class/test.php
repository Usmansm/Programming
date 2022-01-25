<?php
include_once('email.class.php');
include_once('sanitize.class.php');
include('../../config/config.php');

$email = new email;
$firstName = 'Usman';
$link = 'http://www.google.com';
$header = array(
	'From'      => 'tcleveland@myiceberg.com',
	'Reply-to'  => 'tcleveland@myiceberg.com'
 );
$mail_body = array(
	'text/plain' => 
	'Myiceberg Registration Confirmation Email

	Welcome to Myiceberg '.$firstName.',
	
	Congratulations on your recent registration with Myiceberg.
	
	To complete the registration, please click this link:
	'.$link.'  
	Thank you joining Myiceberg,
	
	The MIB Team',
	
	'text/html' => 
	'<div id="backgorund">
		<div id="title"> Myiceberg Registration Confirmation Email </div>
		 
			<div id="messsge">
				 <img alt="Logoupperright-original" src="http://assets.postageapp.com/000/002/002/logoUpperRight-original.jpg" id="logo"/>
			  <br>
			  <br>
				<div id="subject"> Welcome to Myiceberg '.$firstName.', </div>
				
				<div id="messsge2">Congratulations on your recent registration with Myiceberg.<br>
					The final step in completing your registration is activating your email by clicking the button below.</div>
			  <a href="'.$link.'"><img alt="Buttonforregistrationemail-original" src="http://assets.postageapp.com/000/002/003/buttonForRegistrationEmail-original.png" id="RegistrationButton"/></a>
				<div id="messsge3">You can also copy and paste this link to your browser instead:<br> '.$link.'  </div>
				
				
				<br />
			  <br>
				<div id="footer">Thank you joining Myiceberg,<br />The MIB Team</div>
			</div>
		</div>'
 );
echo 4;

$data = $email->send($_GET['email'], 'Myiceberg Confirmation Email', $mail_body, $header);
//var_dump($data);


?>