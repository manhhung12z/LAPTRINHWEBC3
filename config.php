<?php
require 'vendor/autoload.php';

define('PAYPAL_CLIENT_ID', 'YOUR_CLIENT_ID');
define('PAYPAL_SECRET', 'YOUR_SECRET_KEY');
define('PAYPAL_MODE', 'sandbox'); // test mode

$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(PAYPAL_CLIENT_ID, PAYPAL_SECRET)
);

$apiContext->setConfig([
    'mode' => PAYPAL_MODE,
    'http.ConnectionTimeOut' => 30,
    'log.LogEnabled' => false
]);
?>
