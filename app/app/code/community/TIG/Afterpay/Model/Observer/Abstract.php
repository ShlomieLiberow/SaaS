<?php
class TIG_Afterpay_Model_Observer_Abstract extends TIG_Afterpay_Model_Abstract 
{  
    protected $_order;
    protected $_bilingInfo;
    
    protected function _construct()
    {
        $this->_loadLastOrder();
        $this->_setOrderBillingInfo();
    }
    
    /**
     * Each payment method has it's own observer. When one of thos observers is called, this checks if it's
     * payment method is being used and therefore, if this observer needs to do anything.
     * 
     * @param unknown_type $observer
     */
    protected function _isChosenMethod($observer)
    {
        $ret = false;
        
        $chosenMethod = $observer->getOrder()->getPayment()->getMethod();
        
        if ($chosenMethod === $this->_code) {
            $ret = true;
        }
        return $ret;
    }
}