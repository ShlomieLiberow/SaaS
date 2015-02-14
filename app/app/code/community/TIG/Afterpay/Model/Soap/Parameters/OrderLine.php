<?php 
class TIG_Afterpay_Model_Soap_Parameters_OrderLine
{
    public $articleDescription;
    public $articleID;
    public $netunitprice; //item price excl. VAT in euroents with 2 decimals
    public $quantity; //integer. Max value is 2147483647
    public $unitprice; //item price incl. VAT in eurocents
    public $vatcategory; //VAT category. 1: high VAT, 2: low VAT, 3:0 VAT, no VAT, 5: middle VAT, 6: no VAT (awaiting ruling)
}