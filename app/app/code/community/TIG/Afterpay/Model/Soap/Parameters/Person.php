<?php 
class TIG_Afterpay_Model_Soap_Parameters_Person
{
    public $dateofbirth; //date time format
    public $emailaddress;
    public $gender; // M / V
    public $initials;
    public $isoLanguage; //iso language. Currently supported: NL, DE, NL-BE, FR-BE
    public $lastname;
    public $phonenumber1; //prefix is allowed. Length without prefix: 10 chars
    public $phonenumber2;
    public $prefix;
    public $title;
}