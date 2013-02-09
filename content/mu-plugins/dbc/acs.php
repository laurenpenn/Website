<?php

// Access ACS
$accessACS = "https://secure.accessacs.com/acscfwsv2/wsca.asmx?WSDL";
$secid = 'p24ze9aTUcRuyErAvA6ePAyAf';
$siteid = '92231';

// Show all the errors
error_reporting(E_ERROR);

// Create NuSoap client
include( trailingslashit( WP_CONTENT_DIR ) . 'mu-plugins/dbc/lib/nusoap.php' );
$client = new nusoap_client($accessACS, 'wsdl');

// Exit on an error
$err = $client->getError();
if ($err) {
	echo 'Constructor error:' . $err;
	exit();
}

// Get our token
$output = $client->call("getLoginToken", array('secid' => $secid, 'siteid' => $siteid));
print_r($output);
$token=$output['getLoginTokenResult'];
print "Our TOKEN:".$token;

// Do a quick test.
$output = $client->call("getStatAges", array('token' => $token));
print_r($output);

?>
