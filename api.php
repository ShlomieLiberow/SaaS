<?php
require_once("afterpay/lib/Afterpay/Afterpay.php");

ini_set('error_reporting', E_STRICT);
//process();


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
    $price = 1000; // in cents
    $tax_category = 1; // 1 = high, 2 = low, 3, zero, 4 no tax
    $Afterpay->create_order_line( $sku, $name, $qty, $price, $tax_category );



    $sku = 'sample2';
    $name = 'Sample Name 2';
    $qty = 1;
    $price = 30; // in cents
    $tax_category = 1; // 1 = high, 2 = low, 3, zero, 4 no tax
//    $Afterpay->create_order_line( $sku, $name, $qty, $price, $tax_category );

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
//    echo $result->return->statusCode;

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
        $invLine[$i]["unitprice"] = 200;
        $invLine[$i]["vatcategory"] =1;
        $invLine[$i]["articleID"] = "articleID " . $i;

        $Afterpay->create_order_line( "sku", "abc", 1, 200, 1 );
    }
    // Capture
    //

    $tid = array();
    for($i = 0; $i<5; $i++) {
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


//        echo "<p/>";

        //var_dump($result);
        $tid[$i] = $result->return->transactionId;




    }
  //  var_dump($tid);

  echo "<li class='row'><span class='quantity col-md-1'>1</span><span class='itemName col-md-5'>AfterPay ";
  echo json_encode($tid[0]);
  echo "</span><span class='itemName col-md-3'>25/02/2015</span><span class='popbtn'><a class='arrow'></a></span><span class='price col-md-2'>£200.00</span></li>";

  echo "<li class='row'><span class='quantity col-md-1'>1</span><span class='itemName col-md-5'>AfterPay ";
  echo json_encode($tid[1]);
  echo "</span><span class='itemName col-md-3'>05/03/2015</span><span class='popbtn'><a class='arrow'></a></span><span class='price col-md-2'>£200.00</span></li>";

  echo "<li class='row'><span class='quantity col-md-1'>1</span><span class='itemName col-md-5'>AfterPay ";
  echo json_encode($tid[2]);
  echo "</span><span class='itemName col-md-3'>15/03/2015</span><span class='popbtn'><a class='arrow'></a></span><span class='price col-md-2'>£200.00</span></li>";

  echo "<li class='row'><span class='quantity col-md-1'>1</span><span class='itemName col-md-5'>AfterPay ";
  echo json_encode($tid[3]);
  echo "</span><span class='itemName col-md-3'>25/03/2015</span><span class='popbtn'><a class='arrow'></a></span><span class='price col-md-2'>£200.00</span></li>";

  echo "<li class='row'><span class='quantity col-md-1'>1</span><span class='itemName col-md-5'>AfterPay ";
  echo json_encode($tid[4]);
  echo "</span><span class='itemName col-md-3'>05/04/2015</span><span class='popbtn'><a class='arrow'></a></span><span class='price col-md-2'>£200.00</span></li>";



  }

?>
  <!DOCTYPE html>
  <html>
    <head>
      <title>Shopping Cart</title>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
      <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css"/>
      <link rel="stylesheet" type="text/css" href="assets/css/custom.css"/>
    </head>

    <body>

      <nav class="navbar" >
        <div class="container">
          <a class="navbar-brand" href="file:///Users/hoonio/Developer/SaaS/index.html">SaaS</a>
          <div class="navbar-right">
            <div class="container minicart"></div>
          </div>
        </div>
      </nav>

      <div class="container text-center" style="margin-top: 100px;">

        <div class="col-md-12 text-left">
          <ul>
            <li class="row list-inline columnCaptions ">
              <span class="col-md-1">QTY</span>
              <span class="col-md-4">ITEM</span>
              <span class="col-md-4">Scheduled Payment</span>
              <span class="col-md-2">Price</span>
              <span class="col-md-1"></span>
            </li>

<?php process();?>

          <li class="row totals">
            <span class="itemName">Total:</span>
            <span class="price">£1000.00</span>
            <span class="order">  <a class="text-center" href="#">Confirm</a></span>
          </li>

          </ul>
        </div>

      </div>

      <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
      <script src="assets/js/bootstrap.min.js"></script>
      <script src="assets/js/customjs.js"></script>

    </body>
  </html>
