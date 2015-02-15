<<?php
require_once("Afterpay/Afterpay.php");


 $merchantId = "300005645";
 $portfolioId = "1";
 $password ="17fe96fdff";


$Afterpay = new Afterpay();
$authorisation['merchantid'] =$merchantId;
$authorisation['portfolioid'] = $portfolioId;
$authorisation['password'] = $password;
$modus = 'test'; // or 'live' for production

/// ORDER

$sku = 'PRODUCT1';
$name = 'Product name 1';
$qty = 5;
$price = 5000; // in cents
$tax_category = 1; // 1 = high, 2 = low, 3, zero, 4 no tax
$Afterpay->create_order_line( $sku, $name, $qty, $price, $tax_category );

//
// Set up the bill to address
$aporder['billtoaddress']['city'] = 'Heerenveen';
$aporder['billtoaddress']['housenumber'] = '90';
$aporder['billtoaddress']['isocountrycode'] = 'NL';
$aporder['billtoaddress']['postalcode'] = '8441ER';
$aporder['billtoaddress']['referenceperson']['dob'] = '1980-12-12T00:00:00';
$aporder['billtoaddress']['referenceperson']['email'] = 'test@afterpay.nl';
$aporder['billtoaddress']['referenceperson']['gender'] = 'M';
$aporder['billtoaddress']['referenceperson']['initials'] = 'A';
$aporder['billtoaddress']['referenceperson']['isolanguage'] = 'NL';
$aporder['billtoaddress']['referenceperson']['lastname'] = 'de Tester';
$aporder['billtoaddress']['referenceperson']['phonenumber'] = '0513744112';
$aporder['billtoaddress']['streetname'] =  'KR Poststraat';

// Set up the ship to address
$aporder['shiptoaddress']['city'] = 'Heerenveen';
$aporder['shiptoaddress']['housenumber'] = '90';
$aporder['shiptoaddress']['isocountrycode'] = 'NL';
$aporder['shiptoaddress']['postalcode'] = '8441ER';
$aporder['shiptoaddress']['referenceperson']['dob'] = '1980-12-12T00:00:00';
$aporder['shiptoaddress']['referenceperson']['email'] = 'test@afterpay.nl';
$aporder['shiptoaddress']['referenceperson']['gender'] = 'M';
$aporder['shiptoaddress']['referenceperson']['initials'] = 'A';
$aporder['shiptoaddress']['referenceperson']['isolanguage'] = 'NL';
$aporder['shiptoaddress']['referenceperson']['lastname'] = 'de Tester';
$aporder['shiptoaddress']['referenceperson']['phonenumber'] = '0513744112';
$aporder['shiptoaddress']['streetname'] =  'KR Poststraat';

// Set up the additional information
//   $aporder['ordernumber'] = 'ORDER123';
//  $aporder['bankaccountnumber'] = '12345'; // or IBAN 'NL32INGB0000012345';
// $aporder['currency'] = 'EUR';
//$aporder['ipaddress'] = "5.79.64.0";//$_SERVER['REMOTE_ADDR'];

//       "captureFull'
// Create the order object for B2C or B2B
$Afterpay->set_order( $aporder, 'B2C' );

$Afterpay->do_request( $authorisation, $modus );
// var_dump($Afterpay);


// Invoice Lines


echo $aporder->getInvoicenumber;

// var_dump($aporder);
// Capture Object

//Capture Object End

var_dump($Afterpay->order_result);
