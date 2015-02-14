<?php 
class TIG_Afterpay_Model_Sources_TestLive extends Varien_Object
{
    public function toOptionArray()
    {
    	$array = array(
    		 array('value' => '1', 'label' => Mage::helper('afterpay')->__('Test')),
    		 array('value' => '0', 'label' => Mage::helper('afterpay')->__('Live')),
    	);
    	return $array;
    }
}