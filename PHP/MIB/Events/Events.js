 function change(value) {
	if (value == "SettingsProfile" ){
		$("#UserSettingsPage").show();
		$('#UserSettingsPage').load( '../UserSettings/UserSettings.php');
	}
	else if(value == "Logout"){
		window.location.assign(( appbase_url + "logout.php");
	}
 }