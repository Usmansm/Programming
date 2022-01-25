<?php
session_start();
  function fetch($resource, $params, $body = ''){ 
	$url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
	$context = stream_context_create(
		array('http' => 
		 array('method' => 'GET',
		 )
		)
	   );
	$response = file_get_contents($url, false, $context);
	return json_decode($response);
  }
  $people = 0;
  $start = 0;
  $stop = 20;
  $loop = TRUE;
  $count = 0;
  
  while($loop == TRUE){
  	  $params = array('oauth2_access_token' => $_POST["authtoken"], 'start' => $start, 'count' => $stop, 'format' => 'json');
  $data = fetch('/v1/people/'.$_POST["lid2"].':(relation-to-viewer:(connections))', $params);
  	$cc = count($data->relationToViewer->connections->values);
	echo "---CC  =  ". $cc ."--";
	if($cc != 0){
  foreach(@$data->relationToViewer->connections->values as $temp){

  	  	foreach(@$temp as $record){
  		//print_r($record);
  		echo $count.". ".$record->firstName." ".$record->lastName."<br />";
		$count++;
		echo "============START STOP : ". $start ." ".$stop." =========";
	}
	}
  }
	else{
			    	echo "CC IS 0";
  	       $loop = FALSE;
			echo "cc is < 20, setting loop to false, outcome: \$loop = ".$loop;
	}
  $start = $stop;
	$stop = $stop + 20;
  }
  echo "<br />Total: ".$count."";
?>