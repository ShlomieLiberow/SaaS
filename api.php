<?php
require_once("afterpay/lib/Afterpay/Afterpay.php");


process();


function process2(){

    $merchantId = "300005645";
    $portfolioId = "1";
    $password ="17fe96fdff";

    $orderInfo = "";
    $Afterpay = new Afterpay();
    //  $Afterpay->set_ordermanagement('capture_partial');

    $authorisation['merchantid'] =$merchantId;
    $authorisation['portfolioid'] = $portfolioId;
    $authorisation['password'] = $password;
    $modus = 'test'; // or 'live' for production


    $sku = 'PRODUCT1';
    $name = 'Product name 1';
    $qty = 3;
    $price = 3000; // in cents
    $tax_category = 1; // 1 = high, 2 = low, 3, zero, 4 no tax
    $Afterpay->create_order_line( $sku, $name, $qty, $price, $tax_category );


/*
    $sku = 'sample2';
    $name = 'Sample Name 2';
    $qty = 1;
    $price = 30; // in cents
    $tax_category = 1; // 1 = high, 2 = low, 3, zero, 4 no tax
*/
//   $Afterpay->create_order_line( $sku, $name, $qty, $price, $tax_category );

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


    $aporder['billto_address']['isocountrycode'] = 'NL';
    $aporder['billto_address']['postalcode'] = '8441ER';
    $aporder['billto_address']['referenceperson']['dob'] = '1980-12-12T00:00:00';
    $aporder['billto_address']['referenceperson']['email'] = 'test@afterpay.nl';
    $aporder['billto_address']['referenceperson']['gender'] = 'M';
    $aporder['billto_address']['referenceperson']['initials'] = 'A';
    $aporder['billto_address']['referenceperson']['isolanguage'] = 'NL';
    $aporder['billto_address']['referenceperson']['lastname'] = 'de Tester';
    $aporder['billto_address']['referenceperson']['phonenumber'] = '0513744112';
    $aporder['billto_address']['streetname'] =  'KR Poststraat';

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



    $orderNo= "order".substr(md5(time()),0,5);
    $aporder['ordernumber'] = $orderNo;
    $aporder["invoicenumber"] = $orderNo;
    $aporder['bankaccountnumber'] = "";
    $aporder['currency'] = "GBP";
    $aporder['ipaddress'] = "8.8.8.8";

    $Afterpay->set_ordermanagement('capture_partial');
    $Afterpay->set_order( $aporder, 'OM' );

    $Afterpay->do_request( $authorisation, $modus );

    $result =$Afterpay->order_result;

    var_dump($result);
    //echo $result->return->statusCode;

}


function process(){

    $merchantId = "300005645";
    $portfolioId = "1";
    $password ="17fe96fdff";

    $orderInfo = "";
    $Afterpay = new Afterpay();
  //  $Afterpay->set_ordermanagement('capture_partial');

    $authorisation['merchantid'] =$merchantId;
    $authorisation['portfolioid'] = $portfolioId;
    $authorisation['password'] = $password;
    $modus = 'test'; // or 'live' for production


    $sku = 'PRODUCT1';
    $name = 'Product name 1';
    $qty = 1;
    $price = 3000; // in cents
    $tax_category = 1; // 1 = high, 2 = low, 3, zero, 4 no tax
    $Afterpay->create_order_line( $sku, $name, $qty, $price, $tax_category );



    $sku = 'sample2';
    $name = 'Sample Name 2';
    $qty = 1;
    $price = 30; // in cents
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


    $aporder['billto_address']['isocountrycode'] = 'NL';
    $aporder['billto_address']['postalcode'] = '8441ER';
    $aporder['billto_address']['referenceperson']['dob'] = '1980-12-12T00:00:00';
    $aporder['billto_address']['referenceperson']['email'] = 'test@afterpay.nl';
    $aporder['billto_address']['referenceperson']['gender'] = 'M';
    $aporder['billto_address']['referenceperson']['initials'] = 'A';
    $aporder['billto_address']['referenceperson']['isolanguage'] = 'NL';
    $aporder['billto_address']['referenceperson']['lastname'] = 'de Tester';
    $aporder['billto_address']['referenceperson']['phonenumber'] = '0513744112';
    $aporder['billto_address']['streetname'] =  'KR Poststraat';

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



        $orderNo= "order".substr(md5(time()),0,5);
    $aporder['ordernumber'] = $orderNo;
    $aporder["invoicenumber"] = $orderNo;
     $aporder['bankaccountnumber'] = "";
   $aporder['currency'] = "GBP";
   $aporder['ipaddress'] = "8.8.8.8";

    $Afterpay->set_order( $aporder, 'B2C' );

   $Afterpay->do_request( $authorisation, $modus );


    $result =$Afterpay->order_result;

    //var_dump($result);
    echo $result->return->statusCode;

    $Afterpay = new Afterpay();
    $invLine = array();


  $Afterpay->set_ordermanagement('capture_partial');

    //$tranId = $result->return->statusCode;

    $tranKey["Ordernumber"] =$orderNo;
    //$orderNo =  $orderNo ;
  //  $Afterpay->set_order( $aporder, 'OM' );

    for($i = 0; $i<1; $i++){
        $invLine[$i]["articleDescription"] = "item " . $i;
        $invLine[$i]["articleID"] = "articleID " . $i;

        $invLine[$i]["quantity"] = "1 ";
        $invLine[$i]["unitprice"] = 1000;
        $invLine[$i]["vatcategory"] =1;
        $invLine[$i]["articleID"] = "articleID " . $i;

        $Afterpay->create_order_line( "sku", "abc", 1, 1000, 1 );
    }
    // Capture
    //
    for($i = 0; $i<3; $i++) {
        $cap["Invoicelines"] = $invLine;
        $cap["invoicenumber"] = $orderNo.$i;
        $cap["ordernumber"] = $orderNo;


        $cap["Capturedelaydays"] = 14 *1;
        $cap["Transactionkey"] = $tranKey;
        $Afterpay->set_order( $cap, 'OM' );

     //   var_dump($authorisation);
       // $Afterpay->set_order( $cap, 'OM' );
        $Afterpay->do_request( $authorisation, $modus );

        $result =$Afterpay->order_result;



    }
    var_dump($Afterpay);






 //   echo $orderNo;


    //var_dump();
}

/// ORDER

// Set up the additional information
//   $aporder['ordernumber'] = 'ORDER123';
//  $aporder['bankaccountnumber'] = '12345'; // or IBAN 'NL32INGB0000012345';
// $aporder['currency'] = 'EUR';
//$aporder['ipaddress'] = "5.79.64.0";//$_SERVER['REMOTE_ADDR'];

//       "captureFull'
// Create the order object for B2C or B2B
// var_dump($Afterpay);


// Invoice Lines


//echo $aporder->getInvoicenumber;

// var_dump($aporder);
// Capture Object

//Capture Object End

//var_dump($Afterpay->order_result);