<?php 
class TIG_Afterpay_Model_Sources_CaptureMode extends Varien_Object
{
    public function toOptionArray()
    {
    	$array = array(
    		 array('value' => '0', 'label' => Mage::helper('afterpay')->__('Disabled')),
    		 array('value' => '1', 'label' => Mage::helper('afterpay')->__('Manual')),
    		 array('value' => '2', 'label' => Mage::helper('afterpay')->__('Automatic')),
    	);
    	return $array;
    }
}