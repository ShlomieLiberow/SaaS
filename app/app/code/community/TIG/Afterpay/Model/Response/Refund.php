<?php 
class TIG_Afterpay_Model_Response_Refund extends TIG_Afterpay_Model_Response_Abstract
{    
    protected function _construct()
    {
	    $this->setHelper(Mage::helper('afterpay'));
    }
    
    public function processResponse()
    {
        if ($this->_response === false) {
            $this->_debugEmail .= "An error occurred in building or sending the SOAP request.. \n";
            return $this->_error();
        }
        
        $this->_debugEmail .= "verifiying authenticity of the refund response... \n";
        $verified = $this->_verifyResponse();

        if ($verified !== true) {
            $this->_debugEmail .= "The authenticity of the refund response could NOT be verified. \n";
            return $this->_verifyError();
        }
        $this->_debugEmail .= "Verified as authentic! \n\n";
        
        $requiredAction = $this->_parseResponse();
        $this->_debugEmail .= 'Parsed response: ' . $requiredAction . "\n";
        
        $return = $this->_requiredAction($requiredAction);
        
        $this->sendDebugEmail();
        
        return $return;
    }
    
    protected function _verifyResponse()
    {
        $verified = false;
        
        //save response XML to string
        $responseDomDoc = $this->_responseXML;
        $responseString = $responseDomDoc->saveXML();

        $resultId = (int) $this->_response->return->resultId;
        if ($resultId !== 0) {
            $verified = true;
        } else {
            $verified = $this->_verifySignature();
        }
        
    	return $verified;
    }

    protected function _verifySignature()
    {
        $this->_debugEmail .= "verifying signature of the response...\n";
        $verified = false;

        $testMode = (bool) Mage::getStoreConfig('afterpay/afterpay_general/mode', Mage::app()->getStore()->getId());
        if (!$testMode) {
        	$method = $this->_order->getPayment()->getMethod();
        	$testMode = (bool) Mage::getStoreConfig('afterpay/afterpay_' . $method . '/mode', Mage::app()->getStore()->getId());
        }
        
        if ($testMode) {
        	$merchantId = Mage::getStoreConfig('afterpay/afterpay_general/test_merchant_id', Mage::app()->getStore()->getId());
        } else {
        	$merchantId = Mage::getStoreConfig('afterpay/afterpay_general/live_merchant_id', Mage::app()->getStore()->getId());
        }
        
        $checksum      = $this->_response->return->checksum;
        $totalAmount   = $this->_response->return->totalInvoicedAmount;
        $resultId      = $this->_response->return->resultId;
        $transactionId = $this->_response->return->transactionId;
        $orderId       = $this->_order->getIncrementId();
        
        $signatureString = $merchantId 
                         . '-'
        				 . $totalAmount 
        				 . '-'
        				 . $resultId 
        				 . '-'
        				 . $transactionId 
        				 . '-'
        				 . $this->_order->getIncrementId();

        $this->_debugEmail .= "\nSignature string: {$signatureString}\n";
        $signature = MD5($signatureString);
        $this->_debugEmail .= "signature: {$signature}\n";
        
        if ($signature === $checksum) {
            $this->_debugEmail .= "Signature matches Afterpay's checksum!\n";
            $verified = true;
        }
        
        return $verified;
    }

    protected function _accept()
    {
        $this->_debugEmail .= "The response indicates a successful refund request. \n";
        
        $this->_order->addStatusHistoryComment($this->_helper->__('This order has been refunded by AfterPay'))->save();
        
        $this->_order->setStatus(Mage::getStoreConfig('afterpay/afterpay_refund/order_status_accepted', Mage::app()->getStore()->getId()));
		
		return true;
    }

    protected function _pending()
    {
        $this->_debugEmail .= "The response is neutral (not successful, not unsuccessful). \n";

		Mage::throwException($this->_helper->__('Unable to accept refund.'));
        
		return true;
    }

    protected function _validation()
    {
        $this->_debugEmail .= "The refund request generated a validation error. \n";

        $this->_order->addStatusHistoryComment($this->_helper->__('AfterPay refund attempt has failed'))->save();
        
        $this->_order->setStatus(Mage::getStoreConfig('afterpay/afterpay_refund/order_status_rejected', Mage::app()->getStore()->getId()))
                     ->save();
                   
        Mage::throwException($this->_helper->__('Unable to refund order.'));

        return false;
    }
    
    protected function _error()
    {
        $this->_debugEmail .= "The refund request generated an error. \n";

        $this->_order->addStatusHistoryComment($this->_helper->__('AfterPay refund attempt has failed'))->save();
        
        $this->_order->setStatus(Mage::getStoreConfig('afterpay/afterpay_refund/order_status_rejected', Mage::app()->getStore()->getId()))
                     ->save();
                         
        Mage::throwException($this->_helper->__('Unable to refund order.'));

        return false;
    }

    protected function _verifyError()
    {
        $this->_debugEmail .= "Could not verify authenticity of refund response";
        
        $this->_order->addStatusHistoryComment($this->_helper->__('Could not verify the authenticity of the refund response.'))->save();
        
        $this->_order->setStatus(Mage::getStoreConfig('afterpay/afterpay_refund/order_status_rejected', Mage::app()->getStore()->getId()))
                     ->save();
        
        Mage::throwException($this->_helper->__('Could not verify the authenticity of the refund response.'));
        
        return false;
    }
}