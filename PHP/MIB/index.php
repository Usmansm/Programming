<?php
  //TC change
?>
<?php
  session_start();
  include("config/config.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <!-- Meta Data -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  
  <!-- CSS Stylesheets -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link type="text/css" href=" friends/ex_inc/jquery.datepick.css" rel="stylesheet">
	<script type="text/javascript" src="friends/ex_inc/jquery.datepick.js"></script>
  
 
  
	<!-- jQuery/JavaScript External Scripts -->
	<script type="text/javascript" src="index.js"></script>

	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
  
  <!-- Page Title -->
  <title>Myiceberg - Login</title>
</head>

<body>

<?php
if(isset($_SESSION['userId'])){
	echo '<script type="text/javascript"> window.location = "'.$config['root'].'/friends" </script>';
}
require_once("lib/facebooksdk/src/facebook.php");

$facebook = new Facebook(array(
  'appId'  => $config['facebook_appId'],
  'secret' => $config['facebook_secret'],
));
if(isset($_GET['fbLogin'])){
    $params = array(
      	'scope' => $config['facebook_scope'],
  		'redirect_uri' => $config['root'].'php/class/fbLogin.php'
	);
	$link = $facebook->getLoginUrl($params);
        header('location: '.$link);
}
if(isset($_GET['liLogin'])){
    header('Location: '.$config['root'].'php/class/get.php?login=true');
}

$user = $facebook->getUser();

$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
$query = "SELECT * FROM source_import WHERE sourceUid = '".$user."'";
$results = mysqli_query($conn, $query);
if(mysqli_num_rows($results) >= 1){
	$link = '#';
	$click = 1;
}
else {
	$params = array(
  		'scope' => $config['facebook_scope'],
  		'redirect_uri' => $config['root'].'php/class/fbLogin.php'
	);
	$link = $facebook->getLoginUrl($params);
    
	$click = false;
}
?>

<div class="row-fluid" id="header">
<?php
$params = array( 'next' => $config['root'] );

//echo $facebook->getLogoutUrl($params); // $params is optional. 
?>
<?php
 global $config;
		//echo 'user initiated';
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']) or die($mysqli->connect_error);
		// To check if the user exist Already
		$result = $mysqli->query('SELECT * FROM user_verify_email WHERE userId = "'.$_GET["userId"].'" AND code = "'.$_GET["hash"].'"');
		if($result->num_rows > 0){
		$auth="true";
		}

?>
<form action="friends/index.php" method="POST">
	<span class="span4 offset7">
		<span class="row-fluid">
			<span class="span3" style="margin-top:10px;"><span class="LoginButton" onclick="login()">Login</span>
			<span class="" onclick="OpenForgotPasswordModal()" id="forgotPasswordText" style="
					color: grey;
					font-size: 12px;
					text-shadow: none;
					display: block;
					font-weight: bold;
					margin-top: 15px;
					cursor:pointer;">Forgot Password?</span>
				</span>
			</span>
				<span class="span9 offset3" id="loginForm">
				<span class="row">
					<span class="span4 "><input type="text" class="loginInput" id="email" placeholder="Email" name="email"></span>
				</span>
				
				<span class="row">
					<span class="span4"><input type="password" class="loginInput" id="password" placeholder="Password" name="password"></span>
					<!--<span class="span4 offset4" id="loginButton" onClick='login()'>Login</span>-->
				</span>
				</form>
				<!--<span class="row-fluid">
					<span class="" id="sourceLoginText">Or login with: </span>
					<span ><a href="<?php echo $link; ?>"><img src="img/login/fbTiny.png" alt="facebook" width="18px;" height="18px" <?php if($click){echo 
   'onClick="facebookLogin()"';} ?>/></a></span>
					<span ><a href="<?php echo $config['root']; ?>php/class/get.php?login=true"><img src="img/login/liTiny.png" alt="linkedin" width="18px;" height="18px" /></a></span>
				</span>-->
			</span>
		</span>
	</span>
</div>


<div class="row-fluid" id="content">

	<span class="span5" id="contentInfo">
		<span class="span9">
        <br>
        
			<div id="contentText2"><b>P</b>ersonal <b>R</b>elationship <b>M</b>anagement</div> 
			<div id="contentText3">A private platform that aggregates your contacts and content enabling scalable intimacy</div>
		</span>
		<span class="span9" id="contentText">
			<img src="img/login/MIBicon3.png" height="520" width="520"/>
		</span>
		<span class="span9">
			<div class="" id="footertext">"How you gather, manage and use information will determine whether you win or lose" - Bill Gates</div>
		</span>
	</span>

 <?php if(!isset($auth)){?> 
  <span class="span4 offset1" id="contentRegistration">
    <p id="registrationTitle">Register with Myiceberg</p>
    <p>
      <form id="registrationForm">
      <fieldset>
       <span class="row-fluid">
        <span class="span4 regLabel" id='Error'></span><br />
        </span>
        <span class="row-fluid">
        <span class="span4 regLabel"> </span><span class="span8 text-left"><input type="text" class="regInput" id="firstName" placeholder="First Name"></span><br />
        </span>
        <span class="row-fluid">
        <span class="span4 regLabel"></span><span class="span8 text-left"><input type="text" class="regInput" id="lastName" placeholder="Last Name"></span><br />
        </span>
        <span class="row-fluid">
        <span class="span4 regLabel"></span><span class="span8 text-left"><input type="text" class="regInput" id="email2" placeholder="Email"></span><br />
        </span>
        <span class="row-fluid">
        <span class="span4 regLabel"> </span><span class="span8 text-left"><input type="text" class="regInput" id="confirmEmail" placeholder="Confirm Email"></span><br />
        </span>
        <span class="row-fluid">
		<span class="span4 regLabel"></span><span class="span8 text-left"><input type="password" class="regInput" id="passwordReg" placeholder="Password"></span><br />
		</span>
		  <span class="row-fluid">
		<span class="span4 regLabel"></span><span class="span8 text-left"><input type="password" class="regInput" id="passcode" placeholder="PassCode"></span><br />
		</span>
        <span class="row-fluid">
        <span class="span4" id="captchaCode">
		
        </span>
        </span>
        <span class="row-fluid text-center" id="registerButton">
		<span style="margin-left: 67px; display: block; margin-bottom: 15px; text-align: center;">
			By clicking Join Now,
			<br>
			you agree to Myiceberg's
			<u><a href="<?php echo $config['root']; ?>footer_pages/TermsOfUse.php" style="color:white;">Terms Of Use</a></u>
			
		</span>
		<img class="" style="margin-left:230px;" src="img/login/joinButton.png" alt="Join Now!" onClick='register()' />
        </span>
      </fieldset>
      </form>
        
    </p>
  </span>
  <?php } else{?>
  <span class="span4 offset1" id="contentRegistration">
    <p id="registrationTitle">Change Your Password</p>
    <p>
      <form id="registrationForm">
      <fieldset>
       <span class="row-fluid">
        <span class="span4 regLabel" id='Error'></span><br />
        </span>
        <span class="row-fluid">
        <span class="regLabel" style="width:150px;">New Password: </span><span class="span8 text-left"><input type="text" class="regInput" id="newPassword" placeholder="New Password"></span><br />
        </span>
		<input type="hidden" id="userId" value="<?php echo $_GET['userId'];?>" />
        <span class="row-fluid">
		<span class="regLabel" style="width:150px;">Confirm Password: </span><span class="span8 text-left"><input type="text" class="regInput" id="rePassword" placeholder="Confirm Password"></span><br />
		</span>
        <span class="row-fluid">
        <span class="" id="captchaCode">
		
        </span>
        </span>
        <span class="row-fluid text-center" id="registerButton">
		<input type="button" value="Change" onclick="changePassword()" />
        </span>
      </fieldset>
      </form>
        
    </p>
  </span>
  <?php } ?> 
</div>

<div  id="footer2">
		<div class="" id="footerlogo"> <img src="img/logos/mib.png" /> </div>
		<div class="" id="footertext2"> <b><a href="https://www.google.com/">www.myiceberg.com</a></b> <br><a href="<?php echo $config['root']?>footer_pages/About_Us.php">About Us</a> | <a href="<?php echo $config['root']?>footer_pages/priceing.php">Pricing</a> | <a href="<?php echo $config['root']?>footer_pages/Privacy_Policy.php">Privacy Policy</a></div>
		<div class="" id="footertext3"> <b>2013 Myiceberg LLC. All Rights Reserved - v13.1</b>
</div>
<div id="overlay">
    <div id="modal">
        <p id="ModalTitleParagraph"><span id="modalTitleText"></span><a id="closeButton" href="javascript:close()">X</a></p>
        <center><div id="modalButtons"></div></center>
        <hr>
        <div id="modalError" class="friend_div"></div>
        <div id="modalText"></div>
    </div>
</div>

<script>


function facebookLogin(){
	window.location = "<?php echo $config['root']; ?>php/class/fbLogin.php";
}
</script>


<?php 
if (isset($_GET['verify']) and $_GET['verify'] == 'true'){
?>
	<script>
		ComfirmVerificationModal();
	</script>
<?php
}else if (isset($_GET['verify']) and  $_GET['verify'] =='false'){
?>
	<script>
		ErrorVerificationModal();
	</script>
<?php 
}
?>
</body>
</html>