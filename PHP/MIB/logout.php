<?php
//TC was in the logout sam file HEYO1111O111O111now
//TC was in the logout but noooot really Tony_did this test file now
?>
<?php
session_start();
include("config/config.php");
//var_dump($_SESSION);
$user = $_SESSION['userId'];
session_destroy();
if($_GET['type'] == 'maintenance'){
	//echo"<script>alert('Maintenance, brb :p');</script>";
	echo '<script type="text/javascript"> window.location = "'.$config['root'].'" </script>';
}
if(isset($_GET['redirect'])){
	echo'redirecting.......';
	echo '<script type="text/javascript"> window.location = "'.$config['root'].''.$_GET["redirect"].'?userId='.$user.'&importLi=true" </script>';
}
else {
echo '<script type="text/javascript"> window.location = "'.$config['root'].'" </script>';
}
?>