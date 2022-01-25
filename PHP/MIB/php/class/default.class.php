<?php
class convert {

	public function phpArrayToJsArray($array){
	}
	
}

class logout {
	
	public function logout($redirect = false){
		echo 'LOGGING OUT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br><BR><BR><BR>';
		$_SESSION['logged_in'] = false;
		$_SESSION['userId'] = false;
		session_destroy();
		if(isset($_COOKIE['keep_logged']) && $_COOKIE['keep_logged'] == true){
			setcookie("keep_logged", "", time()-3600);	
			setcookie("userId", "", time()-3600);
			setcookie("secretKey", "", time()-3600);
		}
	}
	
}

class notification {
	
	public function redirect($url){
		$_SESSION['notification'] = true;
		$_SESSION['notification_type'] = 'redirect';
		$_SESSION['notification_redirect_url'] = $url;
	}
	
	public function modal($type, $msg, $action = false, $url = false){
		$_SESSION['notification'] = true;
		$_SESSION['notification_type'] = $type;
		$_SESSION['notification_msg'] = $msg;
		if($action != false){
			$_SESSION['notification_action'] = $action;
		}
		if($action == 'redirect' && $url != false){
			$_SESSION['notification_redirect_url'] = $url;
		}
		
	}
	
	public function fatal($type, $error, $data = NULL, $url = NULL, $html = NULL){
		global $config;
		$email = new email();
		$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
		$mysqli->query('INSERT INTO errors (userId, type, error, data, html) VALUES ("'.$_SESSION['userId'].'", "'.$type.'", "'.$error.'", "'.$data.'" "'.$html.'")');
		$id = $mysqli->insert_id;
		$url = new urlCreator;
		$link = $url->createUrl('root', 'adminError.php?id='.$id);
		$email->alertAdmin($type, $error, $link);
		$this->modal('ok', 'OOPS, Something went terribly wrong!<br>An admin has been notified of this problem.<br> Please stand by while we fix the problem.<br> If you continue having problems, please contact us.');
	}
	
	public function inputAlert(){
		
	}
}


class sse {
	
	public function checkBrowser() {
		if($_SESSION['browser'] == 'ie' || $_SESSION['browser'] == 'unknown'){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function send($msg) {
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		echo $msg;
		flush();
	}
	
	
}


class error {
	
	public function fatal($msg, $logout = false){
		$logout = new logout;
		$logout->logout();
		$modal = new modal;
		$url = new urlCreator;
		$logoutUrl = $url->createUrl('logout', false);
		$modal->modal('ok', $msg, 'redirect', $logoutUrl);
	}
	
	public function alert($msg){
		
	}
}

?>