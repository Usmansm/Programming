<?php
session_start();
require_once "../../config/config.php";
require_once "../../contacts/contacts.class.php";

$base = new mib_contacts;

$base->csv_create_form();

@mysqli_close($_SESSION["mysql"]);
?>