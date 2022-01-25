<?php
session_start();
echo "<a href=\"../logout.php\" >Logout</a><br />\n";
var_dump($_SESSION);
echo "<br /><br />";
echo <<<FORM
<form method="post" action="sesc.php" >
<input type="text" name="newuid" />
</form>
FORM;
?>