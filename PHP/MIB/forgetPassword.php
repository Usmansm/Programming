<?php
require_once('config/config.php');

    require_once('php/class/friend.class.php');
	require_once('php/class/fb.class.php');
	require_once('php/class/li.class.php');
    require_once('php/class/import.class.php');
	require_once('php/class/default.class.php');
	require_once('php/class/urlCreator.class.php');
	require_once('php/class/email.class.php');
	require_once('php/class/cookie.class.php');

       
        $email = new email;
		
        
		 global $config;
		//echo 'user initiated';
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']) or die($mysqli->connect_error);
		// To check if the user exist Already
		echo $_POST['email'];
		$result = $mysqli->query('SELECT userId FROM user_email WHERE emailAddr = "'.$_POST['email'].'"') or die ($mysqli->error);
		$dataUser = $result->fetch_assoc();
		
		// error is in lines 27-28-29-30 somewhere there  ...
         $urlCreator = new urlCreator;
         $cookie = new cookie;
         $hash = $cookie->hashString(15);
         $link = $urlCreator->forgotPassword($dataUser['userId'], $hash);

			
         $mail_body = array(
            'text/plain' => 
            'Myiceberg Registration Confirmation Email

            
            
            
            
            To change your password, please click this link:
            '.$link.'  
            Thank you for registering with Myiceberg,
            
            The MIB Team',
            
            'text/html' => 
            '<div id="backgorund">
                <div id="title"> Myiceberg Change Password Email </div>
                 
                    <div id="messsge">
                         <img alt="Logoupperright-original" src="http://assets.postageapp.com/000/002/002/logoUpperRight-original.jpg" id="logo"/>
                      <br>
                      <br>
                       
                        
                        
                        <div id="messsge3">You can copy and paste this link into your browser to change your password:<br> '.$link.'  </div>
                        
                        
                        <br />
                      <br>
                        <div id="footer">Thank you registering with Myiceberg,<br />The MIB Team</div>
                    </div>
                </div>'
         );
		//echo "here we are requesting data <br />";
		$data= $email->send($_POST['email'], 'Myiceberg Change Password', $mail_body,$config["headerMail"]);
		//echo  $config["headerMail"];
		print_r($data);

?>