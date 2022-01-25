<?php
include('config.php');
$sql_imr = "SELECT count(*)+266 FROM `votes` WHERE comment='imr'";
$res_imri=mysql_query($sql_imr) or die(mysql_error());
$res_imr=mysql_fetch_array($res_imri);
 
$sql_naw = "SELECT count(*)+111 FROM `votes` WHERE comment='naw'";
$res_nawi=mysql_query($sql_naw) or die(mysql_error());
$res_naw=mysql_fetch_array($res_nawi);

$sql_kut = "SELECT count(*)+14 FROM `votes` WHERE comment='kut'";
$res_kuti=mysql_query($sql_kut) or die(mysql_error());
$res_kut=mysql_fetch_array($res_kuti);

$sql_mus = "SELECT count(*)+134 FROM `votes` WHERE comment='mus'";
$res_musi=mysql_query($sql_mus) or die(mysql_error());
$res_mus=mysql_fetch_array($res_musi);

$sql_cha = "SELECT count(*)+251 FROM `votes` WHERE comment='cha'";
$res_chai=mysql_query($sql_cha) or die(mysql_error());
$res_cha=mysql_fetch_array($res_chai);

$sql_all = "SELECT count(*) FROM `votes` ";
$res_alli=mysql_query($sql_all) or die(mysql_error());
$res_all=mysql_num_rows($res_alli)+776;

//Voting
$sql_com = "SELECT * FROM complain ";
$res_com=mysql_query($sql_com) or die(mysql_error());
$comcount=mysql_num_rows($res_com);
// Complains
$sql_don = "SELECT * FROM comments ";
$res_don=mysql_query($sql_don) or die(mysql_error());
$doncount=mysql_num_rows($res_don);

?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Smart Mindx-Jaago Pakisan</title>
<link rel="icon" href="images/favicon.gif" type="image/x-icon"/>
 <!--[if lt IE 9]>
 <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
<link rel="shortcut icon" href="images/favicon.gif" type="image/x-icon"/> 

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.tinyscrollbar.min.js"></script>
<link href='http://fonts.googleapis.com/css?family=IM+Fell+DW+Pica+SC' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/themes/vader/jquery-ui.css" />

	<script type="text/javascript" src="js/flipcounter.js"></script>
	<!-- Style sheet for the counter, REQUIRED -->
	<link rel="stylesheet" type="text/css" href="css/counter.css" />

<link rel="stylesheet" href="css/website.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="css/demo.css" />

<link rel="stylesheet" type="text/css" href="css/styles.css"/>

<script type="text/javascript">
		$(document).ready(function(){
			$('#scrollbar1').tinyscrollbar();	
			
			$('#scrollbar2').tinyscrollbar();	
		});
		
		function tick2(){
		$('#ticker_02 li:first').slideUp( function () { $(this).appendTo($('#ticker_02')).slideDown(); });
	}
	setInterval(function(){ tick2 () }, 3000);
	
	function tick3(){
		$('#ticker_03 li:first').slideUp( function () { $(this).appendTo($('#ticker_03')).slideDown(); });
	}
	setInterval(function(){ tick3 () }, 3000);
	</script>	
</head>
<body>
   <div class="bg">
    <!--start container-->
    <div id="container">
    <!--start header-->
    <header>
      <!--start logo-->
      <a href="#" id="logo"><img src="images/logo.png" width="180" height="143" alt="logo"/></a>    
      <!--end logo-->
      
      <!--end header-->
	</header>
   <!--start intro-->
   <section id="intro">
      <hgroup>
      
      <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"Replacing I by WE- Jaago Pakistan"<span></span></h1>
      <h2 style="color:#000">A small step to make changes in the Society.Please <a href="https://play.google.com/store/apps/details?id=com.smartmindx.apportunity#?t=W251bGwsMSwxLDIxMiwiY29tLnNtYXJ0bWluZHguYXBwb3J0dW5pdHkiXQ.." target="_blank">click here</a> to download the App.This App is one step to <b>KHAMOSHI KA BOYCOTT</b></h2>
      </hgroup>
   </section>
   <!--end intro-->
   <!--start holder-->
   <div class="holder_content">
   
    <section class="group1">
         <h3><b style="color:#000">Votes</b></h3>
         <div><div style="float:left"><a href="#"><img src="images/zar.jpg" width="80" height="60" alt="picture1"/> </a></div> <div id="flip-counter" class="flip-counter" style="float:right;position:relative; top:-72px;"></div></div>
              
         <div style="position:absolute ; top:136px;"><div style="float:left"><a href="#"><img src="images/imran.jpg" width="80" height="60" alt="picture1"/> </a></div> <div id="flip-counter1" class="flip-counter" style="position:relative;left:111px;top:-73px;float:right;"></div></div> 
         
          <div style="position:absolute ; top:232px;"><div style="float:left"><a href="#"><img src="images/nawaz.jpg" width="80" height="60" alt="picture1"/> </a></div> <div id="flip-counter2" class="flip-counter" style="position:relative;float:right;left:111px;top:-73px"></div></div>
          
         <div style="position:absolute ; top:328px;"><div style="float:left"><a href="#"><img src="images/musharaf.jpg" width="80" height="60" alt="picture1"/> </a></div> <div id="flip-counter3" class="flip-counter" style="position:relative;float:right;left:111px;top:-73px"></div></div>
           
           <div style="position:absolute ; top:424px;"><div style="float:left"><a href="#"><img src="images/revo.jpg" width="80" height="60" alt="picture1"/> </a></div> <div id="flip-counter4" class="flip-counter" style="position:relative;float:right;left:111px;top:-73px"></div></div>
         
         
      
        
         
        
       <!--  <a href="#"><span class="button">Read more</span></a>   -->
   	</section>
    
     
   <div id="scrollbar1" class="group2">
    <h3><b style="color:#000">Complains</b></h3>
   <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
		<div class="viewport">
			 <div class="overview">
        
         <ul id="ticker_02" class="ticker1">
   
             <?php
			 while ($res_comi=mysql_fetch_array($res_com))
			 {
				 ?>
                 <li><p><b> <?php echo $res_comi['comment'];?></p></li>
                 <?php
			 }
			 ?>
				
                </ul>        		
        </div>
		</div>
	</div>
      <div id="scrollbar2" class="group3">
    <h3><b style="color:#000">Donations</b></h3>
   <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
		<div class="viewport">
			 <div class="overview">
        
         <ul id="ticker_03" class="ticker1">
 <?php
			 while ($res_doni=mysql_fetch_array($res_don))
			 {
				 ?>
                 <li><p ><b> <?php echo $res_doni['comment'];?></p></li>
                 <?php
			 }
			 ?>
				
                
             
				
                </ul>        		
        </div>
		</div>
	</div>
     
	</div>
    
    
	<!--end holder-->
   <!--start holder-->
   <div class="holder_content" style="margin-top:60px;">
    <div id="flip-counter5" class="flip-counter" style="position:absolute ; left:125px;"></div>
    
    <div id="flip-counter6" class="flip-counter" style="position:absolute ; left:475px;"></div>
    
    <div id="flip-counter7" class="flip-counter" style="position:absolute ; left:805px;"></div>
      <section class="group4">
         
         <article>
        <br />
         <h4>Total Votes :</h4>
          <br />
          <h4>Total Complains :</h4>
           <br />
           <h4>Total Donations :</h4>
         
         
         </article> 
       </section>
   </div>
   <!--end holder-->
   </div>
   <!--end container-->
   <!--start footer-->
   <footer>
      <div class="container">  
         <div id="FooterTwo"> © 2012 <a href="http://www.smartmindx.com" target="_blank">SmartMindx</a> </div>
         <div id="FooterTree"> Powered by <a href="http://www.smartmindx.com" target="_blank">SmartMindx</a>  </div> 
      </div>
   </footer>
   <!--end footer-->
   </div>
   <!--end bg-->
   <!-- Free template distributed by http://freehtml5templates.com -->
   
   <script type="text/javascript">
	//<![CDATA[

	$(function(){
		
		// Initialize a new counter
		var myCounter = new flipCounter('flip-counter', {value:<?php echo $res_kut[0];?>, inc:1, pace:600, auto:false});
		
		var myCounter = new flipCounter('flip-counter1', {value:<?php echo $res_imr[0];?>, inc:1, pace:600, auto:false});
		
		var myCounter = new flipCounter('flip-counter2', {value:<?php echo $res_naw[0];?>, inc:1, pace:600, auto:false});
		
		var myCounter = new flipCounter('flip-counter3', {value:<?php echo $res_mus[0];?>, inc:1, pace:600, auto:false});

		var myCounter = new flipCounter('flip-counter4', {value:<?php echo $res_cha[0];?>, inc:1, pace:600, auto:false});
		
		var myCounter = new flipCounter('flip-counter5', {value:<?php echo $res_all;?>, inc:1, pace:600, auto:false});
		
		var myCounter = new flipCounter('flip-counter6', {value:<?php echo $comcount;?>, inc:1, pace:600, auto:false});
		
		var myCounter = new flipCounter('flip-counter7', {value:<?php echo $doncount;?>, inc:1, pace:600, auto:false});



		/**
		 * Demo controls
		 */
		
		var smartInc = 0;
		
		
		
		// Auto-increment
		$("#auto_toggle").buttonset();
		$("input[name=auto]").change(function(){
			if ($("#auto1:checked").length == 1){
				$("#counter_step").button({disabled: true});
				$(".auto_off_controls").hide();
				$(".auto_on_controls").show();
				
				myCounter.setPace($("#pace_slider").slider("value"));
				myCounter.setIncrement($("#inc_slider").slider("value"));
				myCounter.setAuto(true);
			}
			else{
				$("#counter_step").button({disabled: false});
				$(".auto_off_controls").show();
				$(".auto_on_controls").hide();
				$("#add_sub").buttonset();
				$("#set_val, #inc_to, #smart").button();
				myCounter.setAuto(false).stop();
			}
		});
		$("#counter_step").button({disabled: true});
		$("#counter_step").button().click(function(){
			myCounter.step();
			return false;
		});
		
		// Addition/Subtraction
		$("#add").click(function(){
			myCounter.add(567);
			return false;
		});
		$("#sub").click(function(){
			myCounter.subtract(567);
			return false;
		});
		
		// Set value
		$("#set_val").click(function(){
			myCounter.setValue(12345);
			return false;
		});
		
		// Increment to
		$("#inc_to").click(function(){
			myCounter.incrementTo(12345);
			return false;
		});
		
		// Get value
		$("#smart").click(function(){
			var steps = [12345, 17, 4, 533];

			if (smartInc < 4) runTest();
			
			function runTest(){
				var newVal = myCounter.getValue() + steps[smartInc];
				myCounter.incrementTo(newVal, 10, 600);
				smartInc++;
				if (smartInc < 4) setTimeout(runTest, 10000);
			}
			$(this).button("disable");
			return false;
		});
		
		// Expand help
		$("a.expand").click(function(){
			$(this).parent().children(".toggle").slideToggle(200);
			return false;
		});

	});

	//]]>
	</script>
  </body>
</html>
