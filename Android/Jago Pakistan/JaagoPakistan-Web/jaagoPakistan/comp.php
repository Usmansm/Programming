<?php

 include('config.php');

mysql_query("insert into complain (comment) values('".$_REQUEST['comment']."')") ;
      
$var=mysql_affected_rows();   
if($var>0)
print("pakistan");
else
print("games");  


    mysql_close();
?>