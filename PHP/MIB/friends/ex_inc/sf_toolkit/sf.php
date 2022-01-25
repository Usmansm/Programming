<?php

require_once ('soapclient/SforcePartnerClient.php');
require_once ('soapclient/SforceHeaderOptions.php');

try{
	define("USERNAME","abdullah.abdn@gmail.com");
	define("PASS","Abdullahkhan123");
	define("SECURITY_TOKEN","gaZd084OvxHMn3V9K2GAtwkeE");
	
	require_once('soapclient/SforcePartnerClient.php');

	$client = new SforcePartnerClient();
	$client->createConnection("soapclient/partner.wsdl.xml");
	$result = $client->login(USERNAME,PASS.SECURITY_TOKEN);
} catch (exception $e)
{
    $error = '<pre>' . print_r($e, true) . '</pre>';
    echo $error; 
}
$query = "SELECT Id, FirstName, LastName, Phone from Contact";
$response = $client->query($query);

echo "Results of query '$query'<br/><br/>\n";
	foreach ($response->records as $record) {
    // Id is on the $record, but other fields are accessed via the fields object
	echo "<pre>";
		print_r($record);
	echo "</pre>";
}
?>