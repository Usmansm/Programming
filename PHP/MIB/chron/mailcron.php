<?php
include "../config/config.php";
include "../php/class/email.class.php";
include "../php/class/sanitize.class.php";

	$mysqli = new mysqli($config["host"], $config["user"], $config["pass"], $config["db"]);
	$emailAdd;
	//$terms;
$getuserQuery="select * from fb_notify_temp where uidComplete=0";
$result=$mysqli->query($getuserQuery);
$email = new email;
	$mail_body = array(
									'text/html' => 
									''
								 );
	while($data = $result->fetch_assoc())
	{ //Got all Users with incomplete status
	//print_r($data);

	 //Get All Terms for users with the ID
	  $termsQuery="select * from user_monitor_terms where userId=".$data['userId'];
	 // echo $termsQuery;
	   $resultterms=$mysqli->query($termsQuery);
	  while($term = $resultterms->fetch_assoc()){
      $terms[]= $term['termName'];
 }
	 //print_r($terms);
	  
	  //Get his email First
	 $emailQuery="select * from user_email where userId=".$data['userId']." and NotificationEmail=1";
	// echo  $emailQuery;
	 $resultEmail=$mysqli->query($emailQuery);
	 $dataEmail = $resultEmail->fetch_assoc();
	 $emailAdd=$dataEmail['emailAddr'];
	// echo 'The email is '.$emailAdd;
				$getuserfrndQuery="select * from user_frnd_fbpost where processed=0 AND userId=".$data['userId'];
			    $resultfrnd=$mysqli->query($getuserfrndQuery);
				$subject="Your Myiceberg alerts for ".date('d M');
				while($datafrnd = $resultfrnd->fetch_assoc())
				{ //Got all Users and assosiated Posts with them
				print_r($datafrnd);
							$getfbpostQuery="select * from fb_stream where fbPostid='".$datafrnd['fbPostid']."'";
						//	echo $getfbpostQuery;
							$resultfb=$mysqli->query($getfbpostQuery);
						
							while($datafb= $resultfb->fetch_assoc())
							{ //Got all FBPOSTS for that particular User
							 //Now just just send Email
								$datafb['fbMessage']=get_post_underline($datafb['fbMessage'],$terms);
								//var_dump($datafb['fbMessage']);
									//Check if there is Target or Not
									if($datafb['targetName']!='')
									{
									 $mail_body['text/html'] .=
									''.$datafb['actorName'].' posted to '.$datafb['targetName'].' on ' .date('l jS \of F Y h:i:s A',$datafrnd["FbCreatedtime"]).' '.$datafb['fbMessage'].
									' <a href="'.$datafb['fbPermalink'].'">See Full Post</a> <br> <br> '
								 ;
									}
									else
									{
									// Check is there is no Target
									 $mail_body['text/html'] .=
									''.$datafb['actorName'].' posted on ' .date('l jS \of F Y h:i:s A',$datafrnd["FbCreatedtime"]).' '.$datafb['fbMessage'].
									' <a href="'.$datafb['fbPermalink'].'">See Full Post</a> <br> <br> '
								 ;
									}
													
									
										
							}
						//Update usrfrn_fbpost
							$updateuserfrndQuery="Update user_frnd_fbpost set processed=1 where fbPostid='".$datafrnd['fbPostid']."' AND userId=".$data['userId'];
			    $mysqli->query($updateuserfrndQuery);
				}
				//$emailAdd="zain.shah120@gmail.com";
					$sentEmail = $email->send( $emailAdd, $subject, $mail_body, $config["headerMail"]);
					print_r($sentEmail);	
									print_r($data);
									$upuserQuery="delete from fb_notify_temp where userId=".$data['userId'];
$mysqli->query($upuserQuery);
	}
	
	function get_post_underline($post,$terms)
	{
		//echo 'The terms are';
		
	//	print_r($terms);
		
		foreach($terms as $term)
		{
		//echo "$term \n";
		//echo $post;
		//$replace='<span class="fb_term" >'.$term.'</span>';
		$replace='<span style="font-weight:bold; border-bottom: 1px solid #999999;" >'.$term.'</span>';
		$post = str_ireplace($term, $replace, $post,$count);
		//$insmessage = str_ireplace($term, "Pakistan", $post,$count);
		//echo $post;
		//echo $count;
		
		//var_dump($post);
		}
	return $post;
	
	}
?>