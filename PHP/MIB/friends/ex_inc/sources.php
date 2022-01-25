<?php
session_start();
require_once('../../config/config.php');
 $mysqli = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
 
echo '<br><div class="importSourceModal" onClick="check5(\'facebook\')"><img class="importSourceModalIcon" src="../img/logos/facebook.png"/> <span class="importSourceModalTitle">Import Friends From Facebook</span></div>';
echo '<div class="importSourceModal" onClick="check5(\'linkedin\')"><img class="importSourceModalIconRight" src="../img/logos/linkedin.png"/> <span class="importSourceModalTitleRight">Import Friends From LinkedIn</span></div><br><br>';
echo '<br><br><div class="importSourceModal" onClick="moveSF();"><img class="importSourceModalIcon" src="../img/logos/salesforce.png"/> <span class="importSourceModalTitle">Import Friends From Salesforce</span></div>';
//echo '<div class="importSourceModal" onClick="importFriends(\'google+\')"><img class="importSourceModalIconRight" src="../img/logos/google+.png"/> <span class="importSourceModalTitleRight">Import Friends From Google+</span></div><br><br><br>';
//echo '<div class="importSourceModal" onClick="importFriends(\'twitter\')"><img class="importSourceModalIcon" src="../img/logos/twitter.png"/> <span class="importSourceModalTitle">Import Friends From Twitter</span></div>';
//echo '<div class="importSourceModal" ><img class="importSourceModalIconRight" src="../img/logos/mail.png"/> <span class="importSourceModalTitleRight" onClick="cs()">Import From <br>Mail Client</span></div><br>';
echo '<div class="importSourceModal" onClick="ImportFromCSV()"><img class="importSourceModalIconRight" onClick="ImportFromCSV()" src="images/CSV.png"/> <span class="importSourceModalTitleRight" >Import From CSV <br/>
</span></div><br><span id="CSVMoreInfo" onClick="MoreCSVModalInfo()">More Info</span><br>';
//echo '<br><br><div class="importSourceModal" onClick="promptNewUser('.$_SESSION['userId'].')"><img class="importSourceModalIcon" src="../img/logos/mib.png"/> <span class="importSourceModalTitle">Add New Friend</span></div><br><br><br>';



  $result = mysqli_query($mysqli,"SELECT * FROM user_external_accnt WHERE userId = '".$_SESSION['userId']."' AND authProvider='salesforce' AND authAccesstoken!=''");
  
  $row = $result->fetch_assoc();
  if ($row>0){ 
//echo '<div class="importSourceModal" onClick="promptNewUserSF('.$_SESSION['userId'].')"><img class="importSourceModalIconRight" src="../img/logos/salesforce.png"/> <span class="importSourceModalTitleRight">Add Friend to Salesforce</span></div><br><br><br>';
}

//test
?>

<script>
function moveSF()
{
window.location.href="<?php echo $config['root'].'resttest/proxy_import.php'; ?>";
}
</script>