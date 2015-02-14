<?php
class TIG_Afterpay_Model_Sources_StatusesNew extends Varien_Object
{
    const STATE = 'new';
    
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