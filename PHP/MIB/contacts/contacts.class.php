<?php
/*
 * MIB Contact management system
 * Corey Masslock
 * Thu Oct 03 2013 16:34:34 GMT-0500
 * This system currentsly supports:
 *      Gmail:
 *          Requesting/Updating/Saving auth token and other user data
 *          Getting contacts from a users gmail account
 *          Sorting gmail contacts into useable array
 * 
 *      Outlook/CSV:
 *          Import form to upload a CSV file
 *          Importing/storage management of CSv file
 *          
 */
 
 //Declare variables that will be used globaly throughout this system
 $_SESSION["config"] = $config;
 unset($_SESSION["mysql"]);
 $_SESSION["mysql"] = new mysqli($_SESSION['config']['host'], $_SESSION['config']['user'], $_SESSION['config']['pass'], $_SESSION['config']['db']);

 class mib_contacts{
     
     
     public function gmail_check_internal_token(){
         global $gclient;
         $query = "SELECT * FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."' AND authProvider='gmail'";
         $result = $_SESSION["mysql"]->query($query);
         $num = $result->num_rows;
         if($num > 0){
             $dat = $result->fetch_assoc();
             $gclient->setAccessToken($dat["authAccesstoken"]);
             $_SESSION["gmail_token"] = $dat["authAccesstoken"];
             return true;
         }
         else{
             return false;
         }  
     }
     
     public function gmail_request_oauth(){
         global $gclient;
         $auth = $gclient->createAuthUrl();
         return $auth;
     }
     
     public function gmail_process_code(){
         global $gclient;
         $gclient->authenticate();
         $token = $gclient->getAccessToken();
         $itoken = $_SESSION["mysql"]->real_escape_string($token);
         if($_SESSION["mysql"]->query("DELETE FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."' AND authProvider='gmail'")){
         }
         if($_SESSION["mysql"]->query("INSERT INTO user_external_accnt(userId,authProvider,authAccesstoken) VALUES('". $_SESSION["userId"] ."', 'gmail', '". $itoken ."') ")){
         }
        else{
        }
         return true;
     }
     
     public function gmail_display_contacts($list){
         foreach($list as $contact){
             echo "<div class='contact_box' >";
             echo "<span><b>".$contact["fname"]. " ". $contact["lname"]."</b></span><br />";
             echo "Primary email: ".$contact["primary_email"]."<br />";
             echo "</div>";
        }
     }   
    
    public function gmail_retrieve_all(){
        global $gclient;
        //Retrieve a raw list of the users contact book
        $req = new Google_HttpRequest("https://www.google.com/m8/feeds/contacts/default/full?max-results=10000");
        $val = $gclient->getIo()->authenticatedRequest($req);
        $search=array('<gd:','</gd:');
        $replace=array('<gd','</gd');
        $response = json_encode(simplexml_load_string(str_replace($search, $replace,$val->getResponseBody())));

        return $response;
    }
    
    public function gmail_get_all_sorted(){
        $contacts_json = $this->gmail_retrieve_all();
        $sorted_contacts = array();
        $current_record = 0;
        $contacts = json_decode(($contacts_json), true);//
		//print_r( $contacts);
        //For each "contact entry" we attempt to push it into the sorted_contacts var with the following data for now: fname, lname, email
        foreach($contacts["entry"] as $contact_entry){
            foreach($contact_entry as $key => $val){
			//print_r($val);
                if($key == "title"){
                    //Break down the name (saved a single string like "Corey Masslock")
                    if($key != "" && ! is_array($val)){
                    $names = explode(" ",$val);
                    $contact_fname = $names[0];
                    $contact_lname = $names[1];
                    }
                    else{
                        $contact_fname = "N/A";
                        $contact_lname = "N/A";
                    }
                }
                
                if($key == "gdemail"){
                    foreach($val as $email_record => $email_val){
                        if($email_val["primary"] == "true"){
                            $contact_primary_email = $email_val["address"];
                        }
                    }
                }
				if($key=="gdpostalAddress")
				{
				echo 'Address';
				}
            }
            $sorted_contacts[$current_record] = array(
                "fname" => $contact_fname,
                "lname" => $contact_lname,
                "primary_email" => $contact_primary_email
            );
            $current_record++;
        }
        return $sorted_contacts;
    }

    
    public function gmail_disconnect(){
        global $gclient;
        unset($_SESSION["token"]);
        unset($_SESSION["gmail_token"]);
        $disq = "DELETE FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."' AND authProvider='gmail'";
        $_SESSION["mysql"]->query($disq);
        //$gclient->revokeToken();
    }
    
    
    public function csv_create_form($custom_action = false){
        if($custom_action == false){
            $action = $_SESSION["config"]["root"]."csv/prc.php";
        }
        else{
            $action = $custom_action;
        }
        echo "<div id='csvim'><form enctype='multipart/form-data' action='". $action ."' method='POST' >\n";
        echo "<input type='hidden' name='MAX_FILE_SIZE' value='100000000' />\n";
        echo "<input type='hidden' name='act' value='import_csv' />\n";
        echo "Please select your CSV file: <br />\n<input name='csv_import' type='file' /><br /><br />\n";
        echo "<input type='submit' value='Import contacts' onclick='displayImportFromCSVProfress()' /></form></div>\n";
        echo "<div id='csvimpen' style='display: none;' >Please wait while we import, this can take a couple minutes. <br><img src='../img/loader.gif' width='480px'/></div>";
    }
	
	public function csv_create_user($mail){
		$newq = "INSERT INTO users(userStatus,accountType,email) VALUES('temp','personal','". $mail ."')";
		$_SESSION["mysql"]->query($newq);
		return $_SESSION["mysql"]->insert_id;
		
	}
	
    
    public function csv_raw_content(){
        $file = $_FILES["csv_import"]["tmp_name"];
        $raw = file_get_contents($file);
        return $raw;
    }
    
	function csv_check_mail($mail){
		$mailq = "SELECT * FROM user_email WHERE emailAddr=LOWER('". strtolower($mail) ."')";
		
		$res = $_SESSION["mysql"]->query($mailq);
		$num = $res->num_rows;
		if($num > 0){
			$dat = $res->fetch_assoc();
			return $dat;
		}
		else{
			return "none";
		}
	}
    
    public function csv_check_integrity(){
        $file = $_FILES["csv_import"]["tmp_name"];
        $fsize = filesize($file);
        if($fsize <= 100000000){
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
           // $ftype = finfo_file($finfo,$file);
           $ftype = "text/plain";
            if($ftype == "text/plain" || $ftype == "text/csv"){
                return "good";
            }//hh
            else{return 2;}
        }
        else{return 1;}
        
    }

    
    public function yahoo_check_internal_token(){
        $query = "SELECT * FROM user_external_accnt WHERE userId='". $_SESSION["userId"] ."' AND authProvider='yahoo'";
        $result = $_SESSION["mysql"]->query($query);
        $num = $result->num_rows;
        if($num > 0){
            $dat = $result->fetch_assoc();
            $_SESSION["yahoo_token"] = $dat["authAccesstoken"];
            return true;
        }
        else{
            return false;
        }
    }
    
    public function yahoo_request_auth(){
        $session = YahooSession::requireSession(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID);
    }
    
 }

?>