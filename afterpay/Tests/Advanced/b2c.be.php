<?php

// Load AfterPay Library
require_once('../../Lib/Afterpay/Afterpay.php');

// Load dBug Library for showing result. Not necesary for production
require_once('../../Lib/dBug/dBug.php');

// Create new AfterPay Object
$Afterpay = new Afterpay();

// Set up the bill to address
$aporder['billtoaddress']['city'] = 'Antwerpen';
$aporder['billtoaddress']['housenumber'] = '1';
$aporder['billtoaddress']['isocountrycode'] = 'BE';
$aporder['billtoaddress']['postalcode'] = '2040';
$aporder['billtoaddress']['referenceperson']['dob'] = '1980-12-12T00:00:00';
$aporder['billtoaddress']['referenceperson']['email'] = 'test@afterpay.be';
$aporder['billtoaddress']['referenceperson']['gender'] = 'M';
$aporder['billtoaddress']['referenceperson']['initials'] = 'A';
$aporder['billtoaddress']['referenceperson']['isolanguage'] = 'NL';
$aporder['billtoaddress']['referenceperson']['lastname'] = 'de Tester';
$aporder['billtoaddress']['referenceperson']['phonenumber'] = '0513744112';
$aporder['billtoaddress']['streetname'] =  'Teststraat';

// Set up the ship to address
$aporder['shiptoaddress']['city'] = 'Antwerpen';
$aporder['shiptoaddress']['housenumber'] = '1';
$aporder['shiptoaddress']['isocountrycode'] = 'BE';
$aporder['shiptoaddress']['postalcode'] = '2050';
$aporder['shiptoaddress']['referenceperson']['dob'] = '1980-12-12T00:00:00';
$aporder['shiptoaddress']['referenceperson']['email'] = 'test@afterpay.be';
$aporder['shiptoaddress']['referenceperson']['gender'] = 'M';
$aporder['shiptoaddress']['referenceperson']['initials'] = 'A';
$aporder['shiptoaddress']['referenceperson']['isolanguage'] = 'NL';
$aporder['shiptoaddress']['referenceperson']['lastname'] = 'de Tester';
$aporder['shiptoaddress']['referenceperson']['phonenumber'] = '0513744112';
$aporder['shiptoaddress']['streetname'] =  'Teststraat';

// Set up the additional information
$aporder['ordernumber'] = 'ORDER1234567';
$aporder['bankaccountnumber'] = '';
$aporder['currency'] = 'EUR';
$aporder['ipaddress'] = $_SERVER['REMOTE_ADDR'];

$sku = 'PRODUCT1';
$name = 'Product name 1';
$qty = 3;
$price = 3000; // in cents
$tax_category = 1; // 1 = high, 2 = low, 3, zero, 4 no tax
$Afterpay->create_order_line( $sku, $name, $qty, $price, $tax_category );

// Create the order object for B2C or B2B
$Afterpay->set_order( $aporder, 'B2C' );

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