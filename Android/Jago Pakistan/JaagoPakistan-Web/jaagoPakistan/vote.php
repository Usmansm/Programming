<?php

 include('config.php');

mysql_query("insert into votes (id,comment) values('".$_REQUEST['id']."','".$_REQUEST['comment']."')") ;
      
$var=mysql_affected_rows();   
if($var>0)
print("pakistan");
else
print("games");  


    mysql_close();
?>