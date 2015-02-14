<?php 
class TIG_Afterpay_Model_Sources_PortfolioType extends Varien_Object
{
    public function toOptionArray()
    {
    	$array = array(
    		 array('value' => 'B2C', 'label' => Mage::helper('afterpay')->__('B2C')),
    		 array('value' => 'B2B', 'label' => Mage::helper('afterpay')->__('B2B')),
    	);
    	return $array;
    }
}