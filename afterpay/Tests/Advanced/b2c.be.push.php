<?php

// Load dBug Library for showing result. Not necesary for production
require_once('../../Lib/dBug/dBug.php');

// Set push password
$password = 'test';

// Check if POST data is sent
if ($_POST) {
    $push['statuscode'] = $_POST['statusCode'];
    $push['signature'] = $_POST['signature'];
    $push['orderReference'] = $_POST['orderReference'];
    $push['portefeuilleId'] = $_POST['portefeuilleId'];
    $push['merchantId'] = $_POST['merchantId'];
    
    new dBug($push);
} else {
    echo 'No POST data available';
}

// Check if signature is ok
$signature = md5($push['merchantId'] . $push['portefeuilleId'] . $password . $push['orderReference'] . $push['statuscode']);

if ($signature == $push['signature']) {
    // If signature is correct process order to refering statuscode
    // A = Accepted
    // P = Pending
    // W = Rejected
    // V = Removed
    echo 'signature is correct - ';
    echo 'process order ' . $push['orderReference'] . ' to status: ' . $push['statuscode'];
} else {
    // Signature is incorrect
    echo 'signature is incorrect, check order and push again';
}