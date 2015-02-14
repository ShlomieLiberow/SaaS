<?php 
class TIG_Afterpay_Model_Observer_Config extends Mage_Core_Model_Abstract
{
    /**
     * This will check if the saved tax classes are not stored in more than 1 field
     */
    public function admin_system_config_section_save_after(Varien_Event_Observer $observer) 
    {
        $this->_checkVatClassesAllowed();
        $this->_saveData($observer);
        
        return $this;
    }
    
    protected function _checkVatClassesAllowed()
    {
        $postArray = Mage::getSingleton('core/app')->getRequest()->getPost();
        
        if (!array_key_exists('afterpay_afterpay_tax', $postArray['config_state'])) {
            return $this;
        }
        
        $highVat = $postArray['groups']['afterpay_tax']['fields']['high']['value'];
        $lowVat  = $postArray['groups']['afterpay_tax']['fields']['low']['value'];
        $zeroVat = $postArray['groups']['afterpay_tax']['fields']['zero']['value'];
        $noVat   = $postArray['groups']['afterpay_tax']['fields']['no']['value'];
        
        foreach ($highVat as $classId) {
            if ($classId === '') {
                continue;
            }
            
            if (in_array($classId, $lowVat) || in_array($classId, $zeroVat) || in_array($classId, $noVat)) {
                Mage::throwException('Tax classes may not be selected for more than 1 VAT group');
            }
        }
        
        foreach ($lowVat as $classId) {
            if ($classId === '') {
                continue;
            }
            if (in_array($classId, $highVat) || in_array($classId, $zeroVat) || in_array($classId, $noVat)) {
                Mage::throwException('Tax classes may not be selected for more than 1 VAT group');
            }
        }
        
        foreach ($zeroVat as $classId) {
            if ($classId === '') {
                continue;
            }
            if (in_array($classId, $lowVat) || in_array($classId, $highVat) || in_array($classId, $noVat)) {
                Mage::throwException('Tax classes may not be selected for more than 1 VAT group');
            }
        }
        
        foreach ($noVat as $classId) {
            if ($classId === '') {
                continue;
            }
            if (in_array($classId, $lowVat) || in_array($classId, $zeroVat) || in_array($classId, $highVat)) {
                Mage::throwException('Tax classes may not be selected for more than 1 VAT group');
            }
        }
    }
    
    protected function _saveData(Varien_Event_Observer $observer) 
    {
        //get all activated payment methods
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        foreach ($payments as $payment) {
            $code = $payment->getCode();
            $isAfterpay = strpos($code, 'portfolio');
            if ($isAfterpay !== false) {
                $this->_saveDataForPayment($code, $payment);
            }
        }
    }
    
    protected function _saveDataForPayment($code, $payment)
    {
        foreach(Mage::app()->getStores() as $eachStore => $storeVal)
        {
            $sortOrder = Mage::getStoreConfig('afterpay/afterpay_' . $code . '/sort_order', Mage::app()->getStore($eachStore)->getId());
            
            if ($sortOrder) {
                //set the sort_order as the new path
                Mage::getModel('core/config')->saveConfig('payment/' . $code . '/sort_order', $sortOrder, 'stores', Mage::app()->getStore($eachStore)->getId());
            }
            
            //saving title is purely to prevent conflicts with modules that look at payment/payment_code/title, rather than using the
            //payment method's getTitle() method
            $title = Mage::getStoreConfig('afterpay/afterpay_' . $code . '/portfolio_label', Mage::app()->getStore($eachStore)->getId());
            
            if ($title) {
                //set the title as the new path
                Mage::getModel('core/config')->saveConfig('payment/' . $code . '/title', $title, 'stores', Mage::app()->getStore($eachStore)->getId());
            }
        }
    }
}