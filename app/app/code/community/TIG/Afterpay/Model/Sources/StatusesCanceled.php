<?php
class TIG_Afterpay_Model_Sources_StatusesCanceled extends Varien_Object
{
    const STATE = 'canceled';
    
    static public function toOptionArray()
    {
        $statuses = Mage::getSingleton('sales/order_config')->getStateStatuses(self::STATE);
         
        $options = array();
        foreach($statuses as $value => $label)
        {
            $options[] = array(
            	'value' => $value, 'label' => $label
            );
        }
        
        return $options;
    }
}