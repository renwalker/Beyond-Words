<?php

header('Content-type: text/json');

if (!array_key_exists('amount', $_POST))
{
	echo json_encode(array('success' => FALSE, 'error' => "Missing parameters"));
	return;
}

/*
$response = "responseEnvelope.timestamp=2013-09-04T09%3A45%3A49.378-07%3A00&responseEnvelope.ack=Success&responseEnvelope.correlationId=3dc49ee981f20&responseEnvelope.build=6941298&preapprovalKey=PA-74N29975KX928282B";
parse_str($response, $data);
$data['success'] = TRUE;
echo json_encode($data);
die();
*/

$maxNumberOfPayments = 5;

$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_URL, 'https://svcs.sandbox.paypal.com/AdaptivePayments/Preapproval');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'X-PAYPAL-SECURITY-USERID: dr-facilitator_api1.dr-php.com',
		'X-PAYPAL-SECURITY-PASSWORD: 1377823957',
		'X-PAYPAL-SECURITY-SIGNATURE: AsNTKlnqPTOpBcTCUcxBShJZG8UHAVoHO4tP0rJqjciF1QLT-uRjvwOR',
		'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
		'X-PAYPAL-RESPONSE-DATA-FORMAT: NV',
		'X-PAYPAL-APPLICATION-ID: APP-80W284485P519543T'
	));
curl_setopt($ch, CURLOPT_POSTFIELDS, 
		'returnUrl=http%3A%2F%2Fbw.adpearhosting.com%2F' .
		'&cancelUrl=http%3A%2F%2Fbw.adpearhosting.com%2F' .
		'&startingDate=2013-09-05T00%3A00%3A00Z' .
		'&endingDate=2013-10-05T00%3A00%3A00Z' .
		'&maxAmountPerPayment=' . $_POST['amount'] .
		'&maxNumberOfPayments=' . $maxNumberOfPayments .
		'&maxTotalAmountOfAllPayments=' . $_POST['amount'] .
		'&currencyCode=USD' .
		'&requestEnvelope.errorLanguage=en_US' .
		'&displayMaxTotalAmount=true' . 
		'&memo='
	);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

$response = curl_exec($ch);

parse_str($response, $data);

if (curl_errno($ch)) {
	$data['success'] = FALSE;
	$data['error'] = curl_error($ch);
}
else
{
	$data['success'] = TRUE;
}
echo json_encode($data);
curl_close($ch);

?>
