<?php
include_once('php/class/email.class.php');
include_once('php/class/sanitize.class.php');
include('config/config.php');
function mail_registration($to)
{
 
 $email = new email;
      
        
         $sanitize = new sanitize;
       
         $header = array(
            'From'      => 'tcleveland@myiceberg.com',
            'Reply-to'  => 'tcleveland@myiceberg.com'
         );
         
         $mail_body = array(
            'text/plain' => 
            'Myiceberg Registration Confirmation Email

            Welcome to Myiceberg ,
            
            Congratulations on your recent registration with Myiceberg.
            
            To complete the registration, please click this link:
            
            Thank you joining Myiceberg,
            
            The MIB Team',
            
            'text/html' => 
            '<div id="backgorund">
                <div id="title"> Myiceberg Registration Confirmation Email </div>
                 
                    <div id="messsge">
                         <img alt="Logoupperright-original" src="http://assets.postageapp.com/000/002/002/logoUpperRight-original.jpg" id="logo"/>
                      <br>
                      <br>
                        <div id="subject"> Welcome to Myiceberg , </div>
                        
                        <div id="messsge2">Congratulations on your recent registration with Myiceberg.<br>
                            The final step in completing your registration is verifying your email by clicking the button below.</div>
                      <a href=""><img alt="Buttonforregistrationemail-original" src="http://assets.postageapp.com/000/002/003/buttonForRegistrationEmail-original.png" id="RegistrationButton"/></a>
                        <div id="messsge3">You can also copy and paste this link into your browser instead:<br>   </div>
                        
                        
                        <br />
                      <br>
                        <div id="footer">Thank you registering with Myiceberg,<br />The MIB Team</div>
                    </div>
                </div>'
         );
        echo 4;
        $data = $email->send($to, 'Myiceberg Confirmation Email', $mail_body, $header);
        print_r($data);
		print_r($config['postage_key']);
        echo 5;
		
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Get info</title>
</head>

<body>
<table width="389" border="0" cellspacing="3">
  <tr>
    <td width="101"><div class="id_container">ID</div></td>
    <td width="275"><div class="email_container">EMAIL</div></td>
  </tr>
</table>


<table width="389" border="0" cellspacing="3">
  <tr>
    <td width="101">
    <div class="id_container">
    
    </div>
    </td>
    <td width="275"><div class="email_container">
    <?php 
		
		
		
		$to="eagle.usmans@gmail.com";
		
		
		$r=mail_registration($to);
		//var_dump($r);
	?>
    </div>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>