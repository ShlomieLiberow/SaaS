<?php
class TIG_Afterpay_CheckoutController extends Mage_Core_Controller_Front_Action
{
	public function checkoutAction()
	{
	    try {
            $request = Mage::getModel('afterpay/request_abstract');
            $response  = $request->sendRequest();
	    } catch (Exception $e) {
	        $response = false;
	        Mage::getSingleton('core/session')->addError(
                Mage::helper('afterpay')->__($e->getMessage())
            );
	    }
	    
		
		if (is_array($response)) {
			if($response['redirect'] === true) {
				$this->_redirectUrl($response['redirecturl']);
			} else {
				if ($response['response'] === true) {
					$successRedirectConfig = Mage::getStoreConfig('afterpay/afterpay_general/success_redirect', Mage::app()->getStore()->getId());
					$redirectUrl = $successRedirectConfig ? $successRedirectConfig : 'checkout/onepage/success';
				} else {
					$failureRedirectConfig = Mage::getStoreConfig('afterpay/afterpay_general/failure_redirect', Mage::app()->getStore()->getId());
					$redirectUrl = $failureRedirectConfig ? $failureRedirectConfig : 'checkout/onepage/';
				}
				
				$this->_redirect($redirectUrl);
			}
		} else {
			if ($response === true) {
				$successRedirectConfig = Mage::getStoreConfig('afterpay/afterpay_general/success_redirect', Mage::app()->getStore()->getId());
				$redirectUrl = $successRedirectConfig ? $successRedirectConfig : 'checkout/onepage/success';
			} else {
				$failureRedirectConfig = Mage::getStoreConfig('afterpay/afterpay_general/failure_redirect', Mage::app()->getStore()->getId());
				$redirectUrl = $failureRedirectConfig ? $failureRedirectConfig : 'checkout/onepage/';
			}
			
			$this->_redirect($redirectUrl);
		}
	}
}