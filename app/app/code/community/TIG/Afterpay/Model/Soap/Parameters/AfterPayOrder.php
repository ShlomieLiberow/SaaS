<?php 
class TIG_Afterpay_Model_Soap_Parameters_AfterPayOrder
{
    public $ordernumber;
    public $parentTransactionreference;
    public $bankID;
    public $bankaccountNumber;
    public $currency; //currently only EUR is allowed
    public $exchangeDate;
    public $exchangeRate; //example: 1CHF = 0,91383 EUR. Then value of this field will be 0,91383
    public $ipAddress; //IPv4, IPv6 ready in Q1/2013
    public $totalOrderAmount; //amount inc. tax in eurocents
    public $totalOrderNetAmount; //amount ex. tax in eurocents with 2 decimals //only enter if shop wants to invoice ex. VAT
    public $orderlines; //array of order lines
    public $extrafields; //array of extra info that does not fit within current webservice request
    public $shopdetails;
    public $shopper; //customr info
    public $person; //info of contact within company who places order on behalf of company
}