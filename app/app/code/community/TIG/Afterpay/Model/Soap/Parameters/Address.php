<?php
class TIG_Afterpay_Model_Soap_Parameters_Address
{
    public $city;
    public $housenumber;
    public $housenumberAddition;
    public $isoCountryCode; //2-letter iso country code (NL, DE, BE...)
    public $postalcode; //CCCCLL syntax //first number can not be 0
    public $region;
    public $streetname;
}