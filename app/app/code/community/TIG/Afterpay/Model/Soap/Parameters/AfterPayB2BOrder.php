<?php 
class TIG_Afterpay_Model_Soap_Parameters_AfterPayB2BOrder extends TIG_Afterpay_Model_Soap_Parameters_AfterPayOrder
{
    public $b2bbilltoAddress; //extends standard address object
    public $b2bshiptoAddress; //extends standard address object
    public $company; //contains info about the company
    public $costcenter;
}