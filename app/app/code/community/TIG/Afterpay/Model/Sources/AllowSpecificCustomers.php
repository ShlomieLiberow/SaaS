<?php 
class TIG_Afterpay_Model_Sources_AllowSpecificCustomers extends Varien_Object
{
    public function toOptionArray()
    {
    	$array = array(
    		 array('value' => '0', 'label' => Mage::helper('afterpay')->__('All allowed client groups')),
    		 array('value' => '1', 'label' => Mage::helper('afterpay')->__('Specific client groups')),
    	);
    	return $array;
    }
}