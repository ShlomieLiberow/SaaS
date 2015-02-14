<?php 
class TIG_Afterpay_Model_Sources_PortfolioArea extends Varien_Object
{
    public function toOptionArray()
    {
    	$array = array(
    		 array('value' => 'frontend', 'label' => Mage::helper('afterpay')->__('Frontend')),
    		 array('value' => 'backend', 'label' => Mage::helper('afterpay')->__('Backend')),
    		 array('value' => 'both', 'label' => Mage::helper('afterpay')->__('Both')),
    	);
    	return $array;
    }
}