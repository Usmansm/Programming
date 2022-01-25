<?php
session_start();
include "../config/config.php";
$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
$authquery = "SELECT * FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."' AND authProvider='linkedin'";
$authres = $mysql->query($authquery);
$authdata = $authres->fetch_assoc();
?>
<form method="post" action="test.php" >
	Auth token:<br />
	<input type="text" name="authtoken" value="<?php echo $authdata["authAccesstoken"] ?>" /><br />
	Linkedin id1:
	</br/><input type="text" name="lid1" value="<?php echo $authdata["externalAcctuid"] ?>" /><br />
	Linkedin id2:<input type="text" name="lid2" /><br />
	<input type="submit" value="Submit" />
</form>