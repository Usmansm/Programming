<?php 


require "../../php/class/categories.class.php";
$categories = new categories;
echo $categories->getAllCategories();
?>