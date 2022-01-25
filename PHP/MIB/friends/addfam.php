<?php
if(@! $_SESSION){
    session_start();
}


$html = <<<HTML
<div id="addfamhold" >
<br />
<span class="addfam_title" >Family Member Type: </span>
<select name="famtype" id="famtype" onchange="checkfamtype()">
	<option value="spouse">Spouse</option>
    <option value="mother">Mother</option>
	<option value="father">Father</option>
	<option value="daughter">Daughter</option>
	<option value="son">Son</option>
    <option value="sister">Sister</option>
    <option value="brother">Brother</option>
    <option value="other">Other</option>
</select><br />
<br /><label for="famfname" ><span class="addfam_title" >First Name: </span></label><br />
<input type="text" id="famfname" /><br />
<br /><label for="famlname" ><span class="addfam_title" >Last Name: </span></label><br />
<input type="text" id="famlname" /><br />
<div id="addfam_spouseonly" style="display: inline;" >
<br /><label for="famemail" ><span class="addfam_title" >Email Address: </span></label><br />
<input type="text" id="famemail" /><br />
<br /><label for="famphone" ><span class="addfam_title" >Phone Number: </span></label><br />
<input type="text" id="famphone" /><br />
</div>
<br /><label for="famdob" ><span class="addfam_title" >Date of Birth: </span></label><br />
<input type="text" id="famdob" /><br />

	<script>
	$( "#famdob" ).datepicker({
      changeMonth: true,
      changeYear: true,
	  yearRange: "-113:+0" 
    });
  </script>
<br /><label for="famnotes" ><span class="addfam_title" >Notes: </span></label><br />
<textarea id="famnotes" ></textarea>
</div>
HTML;

echo $html;

?>