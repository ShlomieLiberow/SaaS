<?php 
class TIG_Afterpay_Model_Sources_PortfolioCountry extends Varien_Object
{
    public function toOptionArray()
    {
    	$array = array(
    		 array('value' => 'nlnl', 'label' => Mage::helper('afterpay')->__('Netherlands')),
    		 array('value' => 'benl', 'label' => Mage::helper('afterpay')->__('Belgium'))
    	);
    	return $array;
    }
}