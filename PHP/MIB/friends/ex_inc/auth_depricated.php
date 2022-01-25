<?php
    session_start();
	include_once "../config/config.php";
	
    $config['callback_url']         =   'http://ec2-54-243-154-131.compute-1.amazonaws.com/MIBWORKING/dev/friends/ex_inc/demo.php';
    $config['linkedin_access']      =   '82rbu6tsvmus';
    $config['linkedin_secret']      =   '6R7hIoANfG2H0a8m';

    include_once "linkedin.php";
	include_once "";

    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url'] );
    //$linkedin->debug = true;

    # Now we retrieve a request token. It will be set as $linkedin->request_token
    $linkedin->getRequestToken();
    $_SESSION['requestToken'] = serialize($linkedin->request_token);
  
    # With a request token in hand, we can generate an authorization URL, which we'll direct the user to
    //echo "Authorization URL: " . $linkedin->generateAuthorizeUrl() . "\n\n";
    header("Location: " . $linkedin->generateAuthorizeUrl());
?>
