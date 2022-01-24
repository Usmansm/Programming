
    	<?php
		include "../config.php"; 
		$mysql = new mysqli($config['host'], $config['user'], $config['pass'], $config['dbmvc']) or die ('error');
			//include the S3 class
			if (!class_exists('S3'))require_once('S3.php');
			
			//AWS access info
			if (!defined('awsAccessKey')) define('awsAccessKey', 'AKIAI65DEDMAFPLL4W6A');
			if (!defined('awsSecretKey')) define('awsSecretKey', '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');
			
			//instantiate the class
			$s3 = new S3(awsAccessKey, awsSecretKey);
			//echo realpath(dirname(__FILE__));
			//check whether a form was submitted
			if(isset($_POST['Submit']) OR 1==1){
			
				$fbquery = "SELECT * FROM source_import WHERE sourceName='facebook' AND sourceProfilePicture LIKE  '%https://graph%'";
$res = $mysql->query($fbquery);
while($fbdata = $res->fetch_assoc()){
//if(true){
$m=1;
echo ' I am in';
		//if(file_exists($fbdata["sourceUid"].".png")){
		//unlink($fbdata["sourceUid"].".png");
		//		}
	//$fbdata["sourceUid"] = "100003618232019";
    
	//$a=file_get_contents("http://graph.facebook.com/".$fbdata["sourceUid"]."/?fields=picture.width(115).height(100)");
	//$a=file_get_contents("http://graph.facebook.com/".$fbdata["sourceUid"]."/picture?width=115&height=100"); 
	//$a=json_decode($a);
	
	//copy($a->picture->data->url,"".$fbdata["sourceUid"].".png");
	  copy("http://graph.facebook.com/".$fbdata["sourceUid"]."/picture?width=115&height=100","".$fbdata["sourceUid"].".png");
	$picquery = "UPDATE source_import SET sourceProfilePicture='http://mib-bucket.s3.amazonaws.com/dev/". $fbdata["sourceUid"] .".png' WHERE sourceId='". $fbdata["sourceId"] ."'";
   $picres = $mysql->query($picquery) or die("ERROR"); 

	
				//retreive post variables
				//create a new bucket
				$s3->putBucket("mib-bucket", S3::ACL_PUBLIC_READ);
				$fileTempName=realpath(dirname(__FILE__))."/".$fbdata["sourceUid"].".png";
				$fileName=$fbdata["sourceUid"].".png";
				//move the file
				if ($s3->putObjectFile($fileTempName, "mib-bucket", "dev/".$fileName, S3::ACL_PUBLIC_READ)) {
					echo "<strong>We successfully uploaded your file.</strong>";
				}else{
					echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				}
	unlink($fileName);
}


$fbquery = "SELECT * FROM source_import WHERE sourceName='linkedin' AND sourceProfilePicture IS NOT NULL";
$res = $mysql->query($fbquery);
while($lidata = $res->fetch_assoc()){
//if(true){
//echo 'linkedin';
$m=1;
//echo "\n";
	//http://m.c.lnkd.licdn.com
	//echo substr($lidata["sourceProfilePicture"], 0, 23);
	
    if(substr($lidata["sourceProfilePicture"], 0, 23) == "https://media.licdn.com"){
	//echo '1';
	copy($lidata["sourceProfilePicture"],"".$lidata["sourceUid"].".jpg");
	$picquery = "UPDATE source_import SET sourceProfilePicture='http://mib-bucket.s3.amazonaws.com/dev/". $lidata["sourceUid"] .".jpg' WHERE sourceId='". $lidata["sourceId"] ."'";
   $picres = $mysql->query($picquery);
	
				//retreive post variables
				//create a new bucket
				$s3->putBucket("mib-bucket", S3::ACL_PUBLIC_READ);
				$fileTempName=realpath(dirname(__FILE__))."/".$lidata["sourceUid"].".jpg";
				$fileName=$lidata["sourceUid"].".jpg";
				//move the file
				if ($s3->putObjectFile($fileTempName, "mib-bucket", "dev/".$fileName, S3::ACL_PUBLIC_READ)) {
					//echo "<strong>We successfully uploaded your file.</strong>";
				}else{
					//echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				}
	unlink($fileName);
	}
}









mysqli_close($mysql);
				
			}
			
			//QA side
			$mysql = new mysqli($config['host_qa'], $config['user_qa'], $config['pass_qa'], $config['dbmvcqa']) or die ('error');
			if(isset($_POST['Submit']) OR 1==1){
			
				$fbquery = "SELECT * FROM source_import WHERE sourceName='facebook' AND sourceProfilePicture LIKE  '%https://graph%'";
$res = $mysql->query($fbquery);
while($fbdata = $res->fetch_assoc()){
//if(true){
$m=1;
		//if(file_exists($fbdata["sourceUid"].".png")){
		//unlink($fbdata["sourceUid"].".png");
		//		}
	//$fbdata["sourceUid"] = "100003618232019";
    
	 copy("http://graph.facebook.com/".$fbdata["sourceUid"]."/picture?width=115&height=100","".$fbdata["sourceUid"].".png");
	$picquery = "UPDATE source_import SET sourceProfilePicture='http://mib-bucket.s3.amazonaws.com/dev/". $fbdata["sourceUid"] .".png' WHERE sourceId='". $fbdata["sourceId"] ."'";
   $picres = $mysql->query($picquery) or die("ERROR"); 

	
				//retreive post variables
				//create a new bucket
				$s3->putBucket("mib-bucket", S3::ACL_PUBLIC_READ);
				$fileTempName=realpath(dirname(__FILE__))."/".$fbdata["sourceUid"].".png";
				$fileName=$fbdata["sourceUid"].".png";
				//move the file
				if ($s3->putObjectFile($fileTempName, "mib-bucket", "qa/".$fileName, S3::ACL_PUBLIC_READ)) {
					echo "<strong>We successfully uploaded your file.</strong>";
				}else{
					echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				}
	unlink($fileName);
}


$fbquery = "SELECT * FROM source_import WHERE sourceName='linkedin' AND sourceProfilePicture IS NOT NULL";
$res = $mysql->query($fbquery);
while($lidata = $res->fetch_assoc()){
//if(true){
//echo 'linkedin';
$m=1;
//echo "\n";
	//http://m.c.lnkd.licdn.com
	//echo substr($lidata["sourceProfilePicture"], 0, 23);
	
    if(substr($lidata["sourceProfilePicture"], 0, 23) == "https://media.licdn.com"){
	//echo '1';
	copy($lidata["sourceProfilePicture"],"".$lidata["sourceUid"].".jpg");
	$picquery = "UPDATE source_import SET sourceProfilePicture='https://mib-bucket.s3.amazonaws.com/qa/". $lidata["sourceUid"] .".jpg' WHERE sourceId='". $lidata["sourceId"] ."'";
   $picres = $mysql->query($picquery);
	
				//retreive post variables
				//create a new bucket
				$s3->putBucket("mib-bucket", S3::ACL_PUBLIC_READ);
				$fileTempName=realpath(dirname(__FILE__))."/".$lidata["sourceUid"].".jpg";
				$fileName=$lidata["sourceUid"].".jpg";
				//move the file
				if ($s3->putObjectFile($fileTempName, "mib-bucket", "qa/".$fileName, S3::ACL_PUBLIC_READ)) {
					//echo "<strong>We successfully uploaded your file.</strong>";
				}else{
					//echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				}
	unlink($fileName);
	}
}










mysqli_close($mysql);
				
			}
			
		?>

<?php
	// Get the contents of our bucket
//	$contents = $s3->getBucket("mib-bucket");
//	foreach ($contents as $file){
	
		//$fname = $file['name'];
		//$furl = "http://mib-bucket.s3.amazonaws.com/".$fname;
		
		//output a link to the file
		//echo "<a href=\"$furl\">$fname</a><br />";
	//}
?>
