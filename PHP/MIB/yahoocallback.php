<?php
session_start();
$returnvars = "";
foreach($_GET as $key => $val){
    $returnvars .= "&".$key."=".$val;
}
header("Location: ".$_SESSION["return_url"]."?callback=true".$returnvars);
?>