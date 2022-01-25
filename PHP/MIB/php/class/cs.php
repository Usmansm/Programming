<?php
//echo 'SUCCES!';
session_start();

$_SESSION['error'] = $_POST;
    require_once('../../config/config.php');
    require_once('friend.class.php');
    require_once('fb.class.php');
	require_once('li.class.php');
    require_once('import.class.php');
	require_once('default.class.php');
	require_once('sanitize.class.php');
	require_once('user.class.php');
	require_once('verify.class.php');
	require_once('urlCreator.class.php');
	require_once('login.class.php');
	require_once('detail.class.php');
	require_once("../../lib/facebooksdk/src/facebook.php");
    require_once('cookie.class.php');
    require_once('email.class.php');
    $friend = new friend;
    $verify = new verify;
    $data = array();
	
	$import = new import;
	$json = $_POST['json'];

//$json = '[{"address":[],"phone":[{"number":"12345","type":"mobile"},{"number":"33453","type":"work"}],"last_name":"Butt","email":[{"address":"alibutt2009@yahoo.com","type":null,"selected":true}],"first_name":"Ali"},{"address":[],"phone":[],"last_name":"Hanif","email":[{"address":"ahanif333@yahoo.com","type":null,"selected":true}],"first_name":"Aamer"}]';
$response = json_decode($json);

for($i=0 ; $i<sizeof($response) ; $i++)
{
	$random = rand(1,100);
$random1 = rand(2,50);
$random2 = rand(2,10);

$r = ($random + $random1) * $random2;
	$contact = $response[$i];
	$firstname[$i]= $contact->first_name;
	$data['FirstName']=$firstname[$i];
	$lastname[$i] = $contact->last_name;
	
	$data['LastName']=$lastname[$i];
	$email[$i] = $contact->email[0]->address;
	$data['Email']=$email[$i] ;
	$mobile = "";
	$office = "";
	$home = "";
	if($contact->phone != NULL && $contact->phone != '' && $contact->phone)
	{
		for($i1=0 ; $i1<sizeof($contact->phone) ; $i1++)
		{
			
		
		
			$check1=stristr($contact->phone[$i1]->type, "mobile");
			$check2=stristr($contact->phone[$i1]->type, "mobile phone");
			
			if($check1!=false || $check2!=false){
				$number=1;
			}
			$check3=stristr($contact->phone[$i1]->type, "Personal");
			$check4=stristr($contact->phone[$i1]->type, "Home");
			$check5=stristr($contact->phone[$i1]->type, "Home Phone");
			if($check3!=false || $check4!=false || $check5!=false){
				$number=2;
			}
			$check6=stristr($contact->phone[$i1]->type, "Work");
			$check7=stristr($contact->phone[$i1]->type, "Business Phone");
			$check15=stristr($contact->phone[$i1]->type, "BusinessTelephoneNumber");
			
			if($check6!=false || $check7!=false || $check15!=false ){
				$number=3;
			}
			$check8=stristr($contact->phone[$i1]->type, "Work_fax");
			$check9=stristr($contact->phone[$i1]->type, "Home_fax");
			$check10=stristr($contact->phone[$i1]->type, "Home Fax");
			$check11=stristr($contact->phone[$i1]->type, "Home Phone 2");
			$check12=stristr($contact->phone[$i1]->type, "main");
			$check13=stristr($contact->phone[$i1]->type, "pager");
			$check14=stristr($contact->phone[$i1]->type, "car phone");
			if($check8!=false || $check9!=false || $check10!=false || $check11!=false || $check12!=false || $check13!=false || $check14!=false){
				$number=4;
			}
			
			
			
		
			if($number=='1' && $number!='4' )
			{
				$mobile = $contact->phone[$i1]->number;
			}
			else if($number==3 && $number!=4)
			{
				$office = $contact->phone[$i1]->number;
			}
			else if($number==2 && $number!=4)
			{
				$home = $contact->phone[$i1]->number;
			}
		
		}
	}
	$data["MobilePhone"]=$mobile ;
	$data["Phone"]=$office;
	$data["OtherPhone"]=$home;
	$address = "";
	/*if($contact->address != NULL && $contact->address != '' && $contact->address)
	{
		$address = $contact->address->formatted;
	}
	echo $address ;*/
	$street="";
	$city="";
	$region="";
	$country="";
	$address="";
	$postal_code="";
	$street=$contact->address[0]->street;
	$city=$contact->address[0]->city;
	$region=$contact->address[0]->region;
	$country=$contact->address[0]->country;
	$postal_code=$contact->address[0]->postal_code;
	$address = $contact->address[0]->formatted;
	// Office part
	$street1="";
	$city1="";
	$region1="";
	$country1="";
	$address1="";
	$postal_code1="";
	$street1=$contact->address[1]->street;
	$city1=$contact->address[1]->city;
	$region1=$contact->address[1]->region;
	$country1=$contact->address[1]->country;
	$postal_code1=$contact->address[1]->postal_code;
	$address1 = $contact->address[1]->formatted;
	
	
	
	$data["street"]=$street;
	$data["region"]=$region;
	$data["country"]=$country;
	$data["city"]=$city;
	$data["postal_code"]=$postal_code;
	
		$data["street1"]=$street1;
	$data["region1"]=$region1;
	$data["country1"]=$country1;
	$data["city1"]=$city1;
	$data["postal_code1"]=$postal_code1;
	
	
	
	
	
	$data['type']=$_POST['source'];
	$data["MailingState"]=$street;
	$data["MailingState1"]=$street1;
	$data['id'] = md5($data['Email']);
	if($firstname[$i]!='' AND $lastname[$i]!='')
	{
	$import->addCS($data);
	}
	echo "Done";
	
}
	/*if(isset($_POST['firstName']) && isset($_POST['lastName'])){
    $data['firstName'] = $_POST['firstName'];
    $data['middleName'] = '';
    $data['lastName'] = $_POST['lastName'];
    $data['email'] = $_POST['email'];
    $known = $verify->checkImport($data['firstName'], false, $data['lastName']);
    if(!$known){
        $data['verified'] = 'verified';
    }
    else {
        $data['verified'] = 'unverified';
    }
    $data['accountType'] = 'temp';
    $data['sourceName'] = $_POST['source'];
	if(isset($_POST['phone'][0]['number'])){
		$data['phone'] = $_POST['phone'][0]['number'];
	}
	else {
		$data['phone'] = '';
	}
	if(isset($_POST['address']['region'])){
		$data['region'] = $_POST['address']['region'];
	}
	else {
		$data['region'] = '';
	}
	
	if(isset($_POST['address']['city'])){
		$data['city'] = $_POST['address']['city'];
	}
	else {
		$data['city'] = '';
	}

	if(isset($_POST['address']['postal_code'])){
		$data['postal_code'] = $_POST['address']['postal_code'];
	}
	else {
		$data['postal_code'] = '';
	}
	
	if(isset($_POST['address']['street'])){
		$data['street'] = $_POST['address']['street'];
	}
	else {
		$data['street'] = '';
	}
	
	if(isset($_POST['address']['type'])){
		$data['type'] = $_POST['address']['type'];
	}
	else {
		$data['type'] = '';
	}
	
	
	if(isset($_POST))
    $fullname = $data['firstName'].$data['lastName'];
	$_SESSION[] = $data;
    if($fullname != ''){
        $user =  $friend->addEmail($data);
		//var_dump($data);
    }
	}
	*/

	//echo $response;
	
	//var_dump($_POST);

  
	
	
    

?>