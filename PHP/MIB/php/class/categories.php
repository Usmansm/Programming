<?php

session_start();
include '../../config/config.php';
require "categories.class.php";
$categories = new categories;
if(isset($_GET['type'])){
	if($_GET['type'] == 'debug'){
		echo 'Debug ready!';
		$categories->deleteFromCat($_GET['catId'], $_GET['friends']);
	}
}
$_SESSION['calling'] = 'step1';
if(isset($_GET['type'])){
    if($_GET['type'] == 'showCat'){
        if(is_numeric($_GET['catId'])){
            $catId = $_GET['catId'];
            echo $categories->showCat($catId);
        }
        elseif(in_array($_GET['catId'], $config['sources'])){
            $source = $_GET['catId'];
            $public = array('facebook', 'linkedin');
            $private_cs = array('yahoo', 'gmail', 'windowslive', 'aol', 'plaxo', 'outlook', 'addressbook');
            $private_sf = 'salesforce';
            if(in_array($source, $public)){
                $type = '';
            }
            if(in_array($source, $private_cs)) {
                $type = 'source_import_cs';
            }
            if($source == $private_sf){
                $type = 'source_import_sf';
            }
            
            echo $categories->showSource($source, $type);
        }
        
    }
	if($_GET['type'] == 'addCat'){
		$categories->addCategory($_GET['catName'], $_GET['catDescription']);
	}
	
	if($_GET['type'] == 'deleteFromCat'){
		//echo '3';
		echo $categories->deleteFromCat($_GET['catId'], $_GET['friendsIDs']);
	}
	
}
if(isset($_GET['type'])){
	if($_GET['type'] == 'addToCat'){
		$_SESSION['gfi'] = true;
		$_SESSION['get'] = $_GET;
		$categories->addToCat($_GET['catId'], $_GET['friends']);
	}
}
?>