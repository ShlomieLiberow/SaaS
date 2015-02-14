<?php 
class TIG_Afterpay_Model_Soap_Parameters_Capture extends TIG_Afterpay_Model_Soap_Parameters_OrderManagement
{
    public $capturedelaydays; //amount of days AfterPay will wait to invoice the order
    public $shippingCompany; //name of company used to ship the order
    public $trackingnumber; //optional trackingnumber for the shipment
}