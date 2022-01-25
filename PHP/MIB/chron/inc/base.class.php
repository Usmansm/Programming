<?php
class fb_server{
	
    public function rlog($log_text){
    //File date format = mm_dd_yyyy
        $filetime = date("m_d_o",time());
        $timestamp = "[". date("l jS \of F Y h:i:s A",time()) ."]";
        $string = $timestamp ." ". $log_text."\n-\n";
        $fhandle = fopen("logs/". $filetime .".txt","a");
        fwrite($fhandle,$string);
        fclose($fhandle);
    }
	
	public function call_email_service(){
		header("Location: ". $_SESSION["config"]["root"] ."chron/mailcron.php");
	}
    
	public function displayoutput($str){
	    $this->rlog($str);
		//$ctime = date("Y-m-d H:i:s",time());
		//echo "[". $ctime ."] ".$str."<br />";
	}
    
    public function fin($uid){
        echo "harvested for uid ".$uid;
		$user_fin_query = "INSERT INTO fb_notify_temp(userId,uidComplete) VALUES('". $uid ."', '0')";
		$_SESSION["mysql"]->query($user_fin_query);
    }
	
	public function get_terms($uid){
		
		$terms = array();
		$query = "SELECT * FROM user_monitor_terms WHERE userId='". $uid ."'";
		$res = $_SESSION["mysql"]->query($query);
		while($data = $res->fetch_assoc()){
			array_push($terms,$data["termName"]);
			$this->displayoutput("Found term \"". $data["termName"] ."\"");
		}
		return $terms;
	}
	
	public function getfbtoken($uid){
		$query = "SELECT * FROM user_external_accnt WHERE userId = '".$uid."' AND authProvider = 'facebook'";
		$res = $_SESSION["mysql"]->query($query);
		$data = $res->fetch_assoc();
		return $data["authAccesstoken"];
	}
	
	public function getfbuid($uid){
		$query = "SELECT * FROM user_external_accnt WHERE userId = '".$uid."' AND authProvider = 'facebook'";
		$res = $_SESSION["mysql"]->query($query);
		$data = $res->fetch_assoc();
		return $data["externalAcctuid"];
	}
	
	
	
	public function harvest($uid){
		 unset($targetname);
        unset($post);
		$this->displayoutput("Starting harvest for user ".$uid);
		$terms = $this->get_terms($uid);
		$fbs = array();
    	$fbs["appId"] = $_SESSION["config"]["facebook_appId"];
    	$fbs["secret"] = $_SESSION["config"]["facebook_secret"];
		$fbtoken = $this->getfbtoken($uid);
		$fbuid = $this->getfbuid($uid);
		if($fbtoken != "" && $fbuid != ""){
		$fbs["accessToken"] = $fbtoken;
		$fbs["user"] = $fbuid;
    	$facebook = new Facebook($fbs);
		$this->displayoutput("Get FB token ".$fbtoken);
		$this->displayoutput("Got FB UID ".$fbuid);
		foreach($terms as $term){
			$this->displayoutput("Sending term ".$term." to facebook...");
			$fql = "SELECT post_id, source_id, actor_id, permalink, target_id, created_time, message, updated_time, attribution FROM stream where message != '' and strpos(lower(message),lower('" . $term . "')) >=0 and source_id IN (SELECT uid2 FROM friend WHERE uid1 = '" . $fbuid . "')LIMIT 200";
			$param = array('method' => 'fql.query', 'query' => $fql, 'callback' => '', 'access_token' => $fbtoken);
			$fqlResult = $facebook -> api($param);
			$numposts = count($fqlResult);
			$this->displayoutput("Found a total of ".$numposts." matching the term");
			if($numposts >= 1){
			foreach($fqlResult as $post){
				$this->displayoutput("Found the following post: ".$post["message"]);
				$this->displayoutput("Checking if the post ". $post["message_id"] ." exists in the DB...");
				//$query = "SELECT * FROM fb_stream WHERE fbPostid='". $post["message_id"] ."'";
				$query = "SELECT * FROM fb_stream WHERE fbPostid='". $post["post_id"]  ."'";
				$res = $_SESSION["mysql"]->query($query);
				//$data = $res->fetch_assoc();
				$count = $res->num_rows;
				if($count < 1){
					$this->displayoutput("Post is new");
					if($post["actor_id"] != ""){
						$this->displayoutput("Checking facebook for actor id '". $post["actor_id"] ."'");
						$sfql = "SELECT first_name, last_name FROM user where uid='". $post["actor_id"] ."'";
						$sparam = array('method' => 'fql.query', 'query' => $sfql, 'callback' => '', 'access_token' => $fbtoken);
						$sfqlResult = $facebook -> api($sparam);
						$actorname = $sfqlResult[0]["first_name"]." ".$sfqlResult[0]["last_name"];
						$this->displayoutput("Result: ".$actorname);
				}
                   // $this->displayoutput("target id: ".$post["target_id"]);
                   //var_dump($post["target_id"]);
				if($post["target_id"] != "null" && $post["target_id"] != "" ){
						$this->displayoutput("Checking facebook for target id '". $post["target_id"] ."'");
						$sfql = "SELECT first_name, last_name FROM user where uid='". $post["target_id"] ."'";
						$sparam = array('method' => 'fql.query', 'query' => $sfql, 'callback' => '', 'access_token' => $fbtoken);
						$sfqlResult = $facebook -> api($sparam);
						$targetname = $sfqlResult[0]["first_name"]." ".$sfqlResult[0]["last_name"];
						$this->displayoutput("Result: ".$actorname);
						$this->displayoutput("Looking up target id '". $post["target_id"] ."' in our system");
                $lq = "SELECT * FROM source_import WHERE sourceUid='". $post["target_id"] ."'";
                $lres = $_SESSION["mysql"]->query($lq);
                $lrow = $lres->fetch_assoc();
                if($lrow["sourceId"] == ""){
                    $this->displayoutput("No user with that target id exists in our system.");
                }
                else{
                    $this->displayoutput("Found a user with that target id in our system checking if that user is a friend");
                    $selq = "SELECT * FROM user_friend_detail WHERE userId='". $uid ."' AND friendId='". $lrow["userId"] ."'";
                    $selres = $_SESSION["mysql"]->query($selq);
                    $fdata = $selres->fetch_assoc();
                    if($fdata != ""){
                        $this->displayoutput("User is a friend of the post source. Creating a record in user_frnd_fbpost");
                        $insq = "INSERT INTO user_frnd_fbpost(userId, friendId, fbPostid, FbCreatedtime) VALUES('". $uid ."', '". $lrow["userId"] ."', '". $post["post_id"] ."', '". $post["created_time"] ."')";
                        $inres = $_SESSION["mysql"]->query($insq);
                    }
                    else{
                        $this->displayoutput("That user is not a friend of the post source<br />");
                    }
                }
				}
				//$insmessage = str_ireplace($term, "<span class=fb_term >".$term."</span>", $post["message"]);
				$insmessage =$post["message"];
				$insmessage = $_SESSION["mysql"]->real_escape_string($insmessage);
				if($targetname != "" && $targetname != "null" && $post["target_id"] != "" && $post["target_id"] != "null"){
				$inq = "INSERT INTO fb_stream(fbPostid, actorId, actorName, targetName, fbPermalink, fbMessage, fbCreatedtime, fbSourceid) VALUES('". $post["post_id"] ."', '". $post["actor_id"] ."', '". $actorname ."', '". $targetname ."', '". $post["permalink"] ."', '". $insmessage ."', '". $post["created_time"] ."', '". $post["source_id"] ."')";
				}
				else{
				$inq = "INSERT INTO fb_stream(fbPostid, actorId, actorName, fbPermalink, fbMessage, fbCreatedtime, fbTargetid, fbSourceid) VALUES('". $post["post_id"] ."', '". $post["actor_id"] ."', '". $actorname ."', '". $post["permalink"] ."', '". $insmessage ."', '". $post["created_time"] ."', '". $post["target_id"] ."', '". $post["source_id"] ."')";
				}
				$res = $_SESSION["mysql"]->query($inq);
				$this->displayoutput("Looking up actor id '". $post["actor_id"] ."' in our system");
				$lq = "SELECT * FROM source_import WHERE sourceUid='". $post["actor_id"] ."'";
				$lres = $_SESSION["mysql"]->query($lq);
				$lrow = $lres->fetch_assoc();
				if($lrow["sourceId"] == ""){
					$this->displayoutput("No user with that actor id exists in our system.");
				}
				else{
					$this->displayoutput("Found a user with that actor id in our system checking if that user is a friend");
					$selq = "SELECT * FROM user_friend_detail WHERE userId='". $uid ."' AND friendId='". $lrow["userId"] ."'";
					$selres = $_SESSION["mysql"]->query($selq);
					$fdata = $selres->fetch_assoc();
					if($fdata != ""){
						$this->displayoutput("User is a friend of the post source. Creating a record in user_frnd_fbpost");
						$insq = "INSERT INTO user_frnd_fbpost(userId, friendId, fbPostid, FbCreatedtime) VALUES('". $uid ."', '". $lrow["userId"] ."', '". $post["post_id"] ."', '". $post["created_time"] ."')";
						$inres = $_SESSION["mysql"]->query($insq);
					}
					else{
						$this->displayoutput("That user is not a friend of the post source<br />");
					}
				}
				}
				else{
					$this->displayoutput("Post already exists, moving on");
				}
			}
			}
		}
		}
        $this->fin($uid);
	}
	
	public function startup_harvest(){
		$this->displayoutput("Started harvest at".time());
		$harvest = $this->get_harvest_users();
		foreach($harvest as $user){
			$this->harvest($user);
			
		}
	}
	
	public function get_harvest_users(){
		
		$users = array();
		$query = "SELECT * FROM users WHERE userStatus='active' AND accountType!='personal'";
		$res = $_SESSION["mysql"]->query($query);
		while($data = $res->fetch_assoc()){
			array_push($users,$data["userId"]);
		}
		$total = count($users);
		$this->displayoutput("Found a total of ".$total." non-personal users");
		return $users;
	}
}
?>