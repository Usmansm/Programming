<?php
ini_set('max_execution_time', 0);
	require_once('bc-mapi.php');
	if (!class_exists('S3'))require_once('S3.php');
	
	//AWS access info
			if (!defined('awsAccessKey')) define('awsAccessKey', 'AKIAJRJ5XYZL2LQUYRGA');  
			if (!defined('awsSecretKey')) define('awsSecretKey', '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');
			$s3 = new S3(awsAccessKey, awsSecretKey);
			//instantiate the class


header("Access-Control-Allow-Origin: *");

if ($_POST["requestBody"]) {
	$data = json_decode($_POST["requestBody"]);
} else {
	$data = array();
}
// get request type or default to GET
if ($_POST["requestType"]) {
	$method = $_POST["requestType"];
} else {
	$method = "GET";
}
// get the URL and authorization info from the form data
$request = $_POST["url"];
$readToken = 'oiskAONgOf5BQzL-ryIutCVCzfxwo3m8MY-slPFnhOSK54tbD1y5sQ..';
$url1 ='api.brightcove.com/services/library?command=find_all_videos&page_size=10&video_fields=id%2Cname%2Crenditions&media_delivery=default&sort_by=PUBLISH_DATE&sort_order=ASC&page_number=0&get_item_count=true&callback=BCL.onSearchResponse&token=';
$url2 ='api.brightcove.com/services/library?command=search_videos&all=tag:cmi_working&callback=BCLS.secondaryCallResponse&page_size=25&sort_by=CREATION_DATE&get_item_count=false&video_fields=id%2Cname%2CcreationDate%2Crenditions&media_delivery=http&token=';

$request = $url2.$readToken;
//send the http request
$ch = curl_init($request);
curl_setopt_array($ch, array(
		CURLOPT_CUSTOMREQUEST  => $method,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_SSL_VERIFYPEER => FALSE,
		CURLOPT_HTTPHEADER     => array(),
		CURLOPT_POSTFIELDS => json_encode($data)
	));
$response = curl_exec($ch);
curl_close($ch);
// Check for errors
if ($response === FALSE) {
	echo "Error: "+$response;
	die(curl_error($ch));
}
echo $response;
$response = substr($response, 27);
$response = substr($response, 0 , -2);
// Decode the response
 $responseData = json_decode($response, TRUE);
// return the response to the AJAX caller
foreach ( $responseData['items'] as $item) {
// 1 . Get Token

$client_id     = "afc02b2d-dcc5-4a8d-a07a-7c33491e215d";
$client_secret = "s7hBQM9IPbqW0pycRZKq_BHRTx7hm8msUAtIjTHnWetSkfnmnHr3FgIx1MILMJzZfw3mqupOkiiFylsfEBewbQ";
$auth_string   = "{$client_id}:{$client_secret}";
$request       = "https://oauth.brightcove.com/v3/access_token?grant_type=client_credentials";
$ch            = curl_init($request);
curl_setopt_array($ch, array(
		CURLOPT_POST           => TRUE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_SSL_VERIFYPEER => FALSE,
		CURLOPT_USERPWD        => $auth_string,
		CURLOPT_HTTPHEADER     => array(
			'Content-type: application/x-www-form-urlencoded',
		),
		CURLOPT_POSTFIELDS => $data
	));
$response = curl_exec($ch);
curl_close($ch);
// Check for errors
if ($response === FALSE) {
	die(curl_error($ch));
}
// Decode the response
$responseData = json_decode($response, TRUE);
$access_token = $responseData["access_token"];

echo $access_token;

//2. Process the Video



		$fname = $item['id'].".mp4";
		
		echo 'Video : '.$fname;
		
		
		
	
	$request ='{
    "master": {
        "url":"https://videos-bri.s3.amazonaws.com/'.$fname.'"
    },
    "profile": "high-resolution",
    "capture-images": false,
    "text_tracks": [
        {
            "url": "https://videos-bri.s3.amazonaws.com/'.$item['id'].'.vtt",
            "srclang": "en",
            "kind": "captions",
            "label": "EN",
            "default": true
        }
    ]
}';
	
	echo $request;
	
$url 		   = "https://ingest.api.brightcove.com/v1/accounts/4377450450001/videos/".$item['id']."/ingest-requests";	
$ch            = curl_init($url);
curl_setopt_array($ch, array(
		CURLOPT_POST           => TRUE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_SSL_VERIFYPEER => FALSE,
		CURLOPT_USERPWD        => $auth_string,
		CURLOPT_HTTPHEADER     => array("Authorization: Bearer $access_token","Content-type: application/json"),
		CURLOPT_POSTFIELDS => $request
	));
$response = curl_exec($ch);
print_r($response);
// Check for errors
if ($response === FALSE) {
	die(curl_error($ch));
}
	
				 $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				 
				 echo 'STATUS IS '. $status;
curl_close($ch);
			
				$bc = new BCMAPI( 
		'oiskAONgOf5BQzL-ryIutCVCzfxwo3m8MY-slPFnhOSK54tbD1y5sQ..',
		'oiskAONgOf5BQzL-ryIutCVCzfxwo3m8b97JBP4sihNEOs-mZ6o-Bw..'
	);
	$new_tags[] ='cmi_complete';
# Create new meta data
		$new_meta = array(
			'id' => $item['id'], 
			'tags' => $new_tags
		);
		
		# Send changes to API
		$bc->update('video', $new_meta);
				
				
			
	break;
}

?>