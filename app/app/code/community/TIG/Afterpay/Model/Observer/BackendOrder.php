<?php 
class TIG_Afterpay_Model_Observer_BackendOrder extends Mage_Core_Model_Abstract
{
    public function checkout_submit_all_after(Varien_Event_Observer $observer)
    {
        $order = $observer->getOrder();
        $method = $order->getPayment()->getMethod();
        $allowedPaymentMethods = Mage::helper('afterpay')->getAfterPayPaymentMethods();
        
        if (!in_array($method, $allowedPaymentMethods)) {
            return $this;
        }
        
        try {
            $request = Mage::getModel('afterpay/request_backendOrder');
            $request->setOrder($order)
                    ->setMethod($method)
                    ->setAdditionalFields($order->getPayment()->getMethodInstance()->getInfoInstance()->getAdditionalInformation())
                    ->setTestMode((bool) Mage::getStoreConfig('afterpay/afterpay_general/mode', Mage::app()->getStore()->getId()))
                    ->setOrderBillingInfo()
		            ->setOrderShippingInfo();
                    
            $portfolioType = Mage::getStoreConfig('afterpay/afterpay_' . $method . '/portfolio_type', Mage::app()->getStore()->getId());
            if ($portfolioType == 'B2B') {
                $request->setIsB2B(true);
            }
            
            $response  = $request->sendRequest();
	    } catch (Exception $e) {
	        $response = false;
	        Mage::getSingleton('core/session')->addError(
                Mage::helper('afterpay')->__($e->getMessage())
            );
            Mage::throwException($e->getMessage());
	    }
	    
        return $this;
    }
}