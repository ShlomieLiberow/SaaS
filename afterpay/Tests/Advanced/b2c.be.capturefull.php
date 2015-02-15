<?php

// Load AfterPay Library
require_once('../../Lib/Afterpay/Afterpay.php');

// Load dBug Library for showing result. Not necesary for production
require_once('../../Lib/dBug/dBug.php');

// Create new AfterPay Object
$Afterpay = new Afterpay();

$Afterpay->set_ordermanagement('capture_full');

// Set up the additional information
$aporder['invoicenumber'] = 'INVOICE123456';
$aporder['ordernumber'] = 'ORDER1234567';

// Create the order object for order management (OM)
$Afterpay->set_order( $aporder, 'OM' );

// Set country to 'BE' for Belgium
$Afterpay->country = 'BE';

// Set up the AfterPay credentials and sent the order
$authorisation['merchantid'] = '';
$authorisation['portfolioid'] = '';
$authorisation['password'] = '';
$modus = 'test'; // for production set to 'live'

// Show request in debug
new dBug(array('AfterPay Request' => $Afterpay));

$Afterpay->do_request( $authorisation, $modus );

// Show result in debug
new dBug(array('AfterPay Result' => $Afterpay->order_result));