<?php
//TC change testing any issues
?>
<?php

include 'config/config.php';
//var_dump($config);
$conn = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db']);
if(isset($_GET['SalesforceId']) && isset($_GET['pass'])){
    if($_GET['pass'] == $config['SFpass']){
        $SFid = $_GET['SalesforceId'];
        $query = "SELECT * FROM source_import WHERE sourceUid = '".$SFid."' AND sourceName = 'salesforce' ";
        $results = mysqli_query($conn, $query);
        $data = mysqli_fetch_assoc($results);
        $num = mysqli_num_rows($results);
        //var_dump($data);
        $msg = $data['userId'];
        $error = false;
        if($num == 0){
            $msg = 'SalesforceId not found';
            $error = true;
        }
        
    }
    else{
        $msg = 'Incorrect Password Supplied';
        $error = true;
    }
    $xml = new SimpleXMLElement('<xml/>');
    
    if(!$error){
        $track = $xml->addChild('user');
        $track->addChild('MIBId', $msg);
        $track->addChild('SFId', $SFid);
    }
    else {
       $track = $xml->addChild('error');
       $track->addChild('errorMsg', $msg);   
    }
    
    Header('Content-type: text/xml');
    print($xml->asXML());
    
}

?>