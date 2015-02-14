<?php 
class TIG_Afterpay_Model_Sources_DebugMode extends Varien_Object
{
    public function toOptionArray()
    {
    	$array = array(
    		 array('value' => 'no', 'label' => Mage::helper('afterpay')->__('No')),
    		 array('value' => 'email', 'label' => Mage::helper('afterpay')->__('E-mail')),
    		 array('value' => 'log', 'label' => Mage::helper('afterpay')->__('Log')),
    	);
    	return $array;
    }
}