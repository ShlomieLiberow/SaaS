<?php 
class TIG_Afterpay_Model_Sources_AcceptRiskOrders extends Varien_Object
{
    public function toOptionArray()
    {
    	$array = array(
    		 array('value' => '2', 'label' => Mage::helper('afterpay')->__('Yes')),
    		 array('value' => '1', 'label' => Mage::helper('afterpay')->__('Manual')),
    		 array('value' => '0', 'label' => Mage::helper('afterpay')->__('No')),
    	);
    	return $array;
    }
}