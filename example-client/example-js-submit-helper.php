<?php


/*
Wrapper around the API which submits URLs against an anonymous
account.
*/


$API = "https://api.bowdlerize.co.uk/1.2"; // proper URL
$USER = 'anonweb@blocked.org.uk';
$SECRET = 'SECRETKEY';

function createSignatureHash($message, $secret) {
	/* Use hmac functions to return signature for message string */
	return hash_hmac('sha512', $message, $secret);
}


$data = array('email' => $USER, 'url' => $_POST['url'], 'additional_data' => http_build_query($_POST));
$data['signature'] = createSignatureHash($_POST['url'], $SECRET);

$content = http_build_query($data);

// build the request
$options = array(
	'http' => array(
		'header' => "Content-type: application/x-www-form-urlencoded\r\n",
		'method' => 'POST',
		'content' => $content,
	)
);

// send it
$ctx = stream_context_create($options);
$result = file_get_contents("$API/submit/url", false, $ctx);


print $result;



