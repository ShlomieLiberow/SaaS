<?php

// Load AfterPay Library
require_once('../../Lib/Afterpay/Afterpay.php');

// Load dBug Library for showing result. Not necesary for production
require_once('../../Lib/dBug/dBug.php');

// Create new AfterPay Object
$Afterpay = new Afterpay();

$Afterpay->set_ordermanagement('refund_partial');

// Set up the additional information
$aporder['invoicenumber'] = '1';
$aporder['ordernumber'] = 'ORDER1234567-04';
$aporder['creditinvoicenumber'] = 'ORDER1234567-04';

// Set refund line
$sku = 'PRODUCT1';
$name = 'Product name 1';
$qty = 1;
$price = -3000; // in cents, make sure it's a negative value
$tax_category = 1; // 1 = high, 2 = low, 3, zero, 4 no tax
$Afterpay->create_order_line( $sku, $name, $qty, $price, $tax_category );

// Create the order object for order management (OM)
$Afterpay->set_order( $aporder, 'OM' );

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