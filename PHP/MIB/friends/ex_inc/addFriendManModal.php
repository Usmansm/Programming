	<?php 
	
	echo '
	<div id="importFirendManModal">
		<font color="red" >All fields marked with * must be filled out.</font>
		<div class="fi_modal_title">Personal information</div>
		
		<div class="fi_modal_right">
			<span class="fi_modal_formtext" >*Email</span><br>
			<input type="text" name="fi_email" id="fi_email" class="fi_input" /><br />
			<span class="fi_modal_formtext" >Phone</span>
			<select class="detail_drop" id="PhoneSel">
				<option value="Cell">Cell</option>
				<option value="Home">Home</option>
				<option value="Office">Office</option>
			</select>
			<script>
	$( "#fi_bday" ).datepicker({
      changeMonth: true,
      changeYear: true,
	  yearRange: "-113:+0" 
    });
  </script>
			<input type="text" name="fi_phone" id="fi_phone" class="fi_input" /><br />
			<span class="fi_modal_formtext" >Birthday</span><br>
			<input type="text" id="fi_bday"  /><br />
			<span class="fi_modal_formtext" >Title</span><br>
			<input type="text" name="fi_title" id="fi_title" class="fi_input" /><br />
			<span class="fi_modal_formtext" >High school</span><br>
			<input type="text" name="fi_school" id="fi_school" class="fi_input" /><br />
		</div>
	
		<div class="fi_modal_left">
			<span class="fi_modal_formtext" >*First name</span><br>
			<input type="text" name="fi_fname" id="fi_fname" class="fi_input" /><br />
			<span class="fi_modal_formtext" >Middle name</span><br>
			<input type="text" name="fi_mname" id="fi_mname" class="fi_input" /><br />
			<span class="fi_modal_formtext" >*Last Name</span><br>
			<input type="text" name="fi_lname" id="fi_lname" class="fi_input" /><br />
			<span class="fi_modal_formtext" >Company</span><br>
			<input type="text" name="fi_company" id="fi_company" class="fi_input" /><br />
			<span class="fi_modal_formtext" >College</span><br>
			<input type="text" name="fi_college" id="fi_college" class="fi_input" /><br />
		</div>

		<div class="fi_modal_title" style="margin-top: 20px;">Address information</div>

		<div class="fi_modal_right">
			<span class="fi_modal_formtext" >Home Add</span><br>
			<input type="text" name="HomeAddInput" id="HomeAddInput" class="fi_input" /><br />
		 <span class="fi_modal_formtext" >Home City</span><br>
			<input type="text" name="HomeAddInput" id="HomeCityInput" class="fi_input" /><br />
		 <span class="fi_modal_formtext" >Home State</span><br>
			<input type="text" name="HomeAddInput" id="HomeStateInput" class="fi_input" /><br />
		 <span class="fi_modal_formtext" >Home Zip</span><br>
			<input type="text" name="HomeAddInput" id="HomeZipInput" class="fi_input" /><br />
		</div>

		<div class="fi_modal_left">
			<span class="fi_modal_formtext" >Office Add</span><br>
			<input type="text" name="HomeAddInput" id="OfficeAddInput" class="fi_input" /><br />
		 <span class="fi_modal_formtext" >Office City</span><br>
			<input type="text" name="HomeAddInput" id="OfficeCityInput" class="fi_input" /><br />
		 <span class="fi_modal_formtext" >Office State</span><br>
			<input type="text" name="HomeAddInput" id="OfficeStateInput" class="fi_input" /><br />
		 <span class="fi_modal_formtext" >Office Zip</span><br>
			<input type="text" name="HomeAddInput" id="OfficeZipInput" class="fi_input" /><br />
		</div>
		
	</div> ';
	
	?>