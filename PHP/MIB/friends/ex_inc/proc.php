<?php
/*
 * Corey
 * File to process external actions
 *
 */
 session_start();
 include "../../config/config.php";
 $mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
 if($_GET["a"] == "delevn"){
     //Delete evernote post
     $tq = "DELETE FROM evn_notes_cat WHERE evnNoteGuid='". $_GET["pid"] ."'";
     $mysql->query($tq);
     $tq = "DELETE FROM evn_note_detail WHERE evnNoteGuid='". $_GET["pid"] ."'";
     $mysql->query($tq);
     $tq = "DELETE FROM user_frnd_evernote WHERE evnNoteGuid='". $_GET["pid"] ."' AND userId='". $_SESSION["userId"] ."'" OR die("deathll");
     $mysql->query($tq);
 }
 
 if($_GET["a"] == "delevnfrnd"){
     $_SESSION["dd"] = "here";
     $tq = "DELETE FROM user_frnd_evernote WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $_GET["fid"] ."' AND evnNoteGuid='". $_GET["pid"] ."'";
     $mysql->query($tq);
     $tsel = "SELECT * FROM user_frnd_evernote WHERE evnNoteGuid='". $_GET["pid"] ."'";
     $tres = $mysql->query($tsel);
     $td = $tres->fetch_assoc();
     if($td["id"] == ""){
         $new= "DELETE FROM evn_note_detail WHERE evnNoteGuid='". $_GET["pid"] ."'";
         $tt = $mysql->query($newq);
     }
 }
 
  if($_GET["a"] == "delfb"){
     //Delete facebook post
     $tq = "DELETE FROM user_frnd_fbpost WHERE userId='". $_SESSION["userId"] ."' AND friendId='". $_GET["fid"] ."' AND fbpostId='". $_GET["pid"] ."'";
     $mysql->query($tq);
 }
?>