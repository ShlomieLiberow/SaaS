<?php
class TIG_Afterpay_Model_Response_Abstract extends TIG_Afterpay_Model_Abstract
{
    protected $_debugEmail = '';
    protected $_responseXML = '';
    protected $_response = null;
    protected $_customResponseProcessing = false;
    protected $_request;
    protected $_isRisk = false;

	public function setCurrentOrder($order)
    {
    	$this->_order = $order;
    	return $this;
    }

    public function getCurrentOrder()
    {
    	return $this->_order;
    }

    public function setDebugEmail($debugEmail)
    {
    	$this->_debugEmail = $debugEmail;
    	return $this;
    }

    public function getDebugEmail()
    {
    	return $this->_debugEmail;
    }

    public function setResponseXML($xml)
    {
        $this->_responseXML = $xml;
    	return $this;
    }

    public function getResponseXML()
    {
        return $this->_responseXML;
    }

    public function setResponse($response)
    {
        $this->_response = $response;
    	return $this;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    public function setRequest($request)
    {
        $this->_request = $request;
    	return $this;
    }

    public function getRequest()
    {
        return $this->_request;
    }

    public function setIsRisk($isRisk)
    {
        $this->_isRisk = $isRisk;
    	return $this;
    }

    public function getIsRisk()
    {
        return $this->_isRisk;
    }

    public function processResponse()
    {
        if (is_null($this->_response)) {
            Mage::throwException($this->_helper('No response was available'));
        }
        
        if ($this->_response === false) {
            $this->_debugEmail .= "An error occurred in building or sending the SOAP request.. \n";
            return $this->_error();
        }
        
        $this->_debugEmail .= "verifiying authenticity of the response... \n";
        $verified = $this->_verifyResponse();

        if ($verified !== true) {
            $this->_debugEmail .= "The authenticity of the response could NOT be verified. \n";
            return $this->_verifyError();
        }
        $this->_debugEmail .= "Verified as authentic! \n\n";
        
        $requiredAction = $this->_parseResponse();
        $this->_debugEmail .= 'Parsed response: ' . $requiredAction . "\n";

        $this->_debugEmail .= "Dispatching custom order processing event... \n";
        Mage::dispatchEvent(
        	'afterpay_response_custom_processing',
            array(
        		'model'         => $this,
                'order'         => $this->_order,
                'response'      => $this->_response,
            )
        );

        $return = $this->_requiredAction($requiredAction);
        
        $this->sendDebugEmail();
        
        return $return;
    }
    
    protected function _parseResponse()
    {
        if (array_key_exists($this->_response->return->resultId, $this->responseCodes)) {
            $response = $this->responseCodes[$this->_response->return->resultId];
        } else {
            $response = false;
        }
        
        switch ($response) {
            case self::AFTERPAY_SUCCESS:            $requiredAction = 'accept';
                                                    break;
            case self::AFTERPAY_ERROR:              $requiredAction = 'error';
                                                    break;
            case self::AFTERPAY_FAILED:             $requiredAction = 'failed';
                                                    break;
            case self::AFTERPAY_REJECTED:           $requiredAction = 'reject';
                                                    break;
            case self::AFTERPAY_PENDING_PAYMENT:    $requiredAction = 'pending';
                                                    break;
            case self::AFTERPAY_VALIDATION_ERROR:   $requiredAction = 'validation';
                                                    break;
            default:                                $requiredAction = 'pending';
        }
        
        return $requiredAction;
    }

    protected function _requiredAction($response)
    {
        try {
            $response = '_' . $response;
        } catch (Exception $e) {
            return $this->_error();
        }
        return $this->$response();
    }

    protected function _accept()
    {
        $this->_debugEmail .= "The response indicates a successful request. \n";
		if(!$this->_order->getEmailSent())
        {
        	$this->_order->sendNewOrderEmail();
        }
        
        $this->_storeAfterPayOrderReference();
        $this->_storeAfterPayTransactionId();
        
        if (
            array_key_exists($this->_response->return->statusCode, $this->responseCodes)
            && $this->responseCodes[$this->_response->return->statusCode] == self::AFTERPAY_ACCEPTED
            && $this->_order->canInvoice()
        ) {
            $this->_updateAndInvoice();
        }
        
		Mage::getSingleton('core/session')->addSuccess(
		    $this->_helper->__('Your order has been placed succesfully.')
		);
		
		return true;
    }

    protected function _failed()
    {
        $this->_debugEmail .= 'The transaction was unsucessful. \n';
        Mage::getSingleton('core/session')->addError(
            $this->_helper->__('Your order was unsuccesful. Please try again or choose another payment method.')
        );

        
        $this->_order->cancel()->save();
        $this->_debugEmail .= "The order has been cancelled. \n";
        $this->restoreQuote();
        $this->_debugEmail .= "The quote has been restored. \n";

        return false;
    }

    /**
     * Method to process an authorization request that has been rejected. If so configured, this method will attempt a second authorization
     * for the risk portfolio ID specified. In order to do so it will call TIG_Afterpay_Model_Request_Risk::sendRequest().
     * This code is almost identical to TIG_Afterpay_Model_Request_Abstract::sendRequest() except that it uses all variables defined in said
     * method, rather than redefine them.
     * 
     * If this also causes this method to be called, it will instead cancel the order.
     * 
     * @param boolean $isRisk
     */
    protected function _reject()
    {
        $this->_debugEmail .= 'The transaction was unsucessful. \n';
        
         if ($this->_isRisk === false) {
             $riskConfig = (int) Mage::getStoreConfig(
             	'afterpay/afterpay_' . $this->getOrder()->getPayment()->getMethod() . '/portfolio_accept_risk', 
                Mage::app()->getStore()->getId()
             );
             switch($riskConfig) {
                 case 1:    return $this->_manualRisk();
                 case 2:    return $this->_riskRequest();
                 case 0:    
                 default:   return $this->_rejectFinal();
             }
        } else {
            return $this->_rejectFinal();
        }
    }
    
    protected function _manualRisk()
    {
        $rejectMessage = 'This order is awaiting final approval.'
        			   . ' You will receive an update regarding the status of this order shortly.'
        			   . ' If you do not receive an update about the status of this order, please contact the webshop\'s owner.';
        
        Mage::getSingleton('core/session')->addNotice(
            $this->_helper->__($rejectMessage)
        );
        
        $pendingManualRiskOrderStatus = Mage::getStoreConfig(
        	'afterpay/afterpay_' . $this->_order->getPayment()->getMethod() . '/manual_risk_order_status_pending',
            Mage::app()->getStore()->getId()
        );
        
        $this->_order->setStatus($pendingManualRiskOrderStatus);
        $this->_order->addStatusHistoryComment(
        	$this->_helper->__('This order has been rejected. You can attempt to process the order using a risk portfolio by using the re-order button')
        );
        $this->_order->save();
        
        $this->_debugEmail .= "The order may be re-ordered using a risk portfolio \n";
        
        return true;
    }
    
    protected function _riskRequest()
    { 
        $riskRequest = Mage::getModel('afterpay/request_risk');
        $riskRequest->setVars($this->_request->getVars())
                    ->setIsB2B($this->_request->getIsB2B())
                    ->setMethod($this->_request->getMethod())
                    ->setTestmode($this->_request->getMethod());
        
        return $riskRequest->sendRequest();
    }

    public function _rejectFinal()
    {
        $rejectMessage = $this->_getRejectMessage();
        $rejectDescription = $this->_getRejectDescription();
        
        Mage::getSingleton('core/session')->addError(
            $this->_helper->__($rejectMessage)
        );
        
        $this->_order->addStatusHistoryComment($rejectDescription);
        
        $this->_order->cancel()->save();
        $this->_debugEmail .= "The order has been cancelled. \n";
        $this->restoreQuote();
        $this->_debugEmail .= "The quote has been restored. \n";

        return false;
    }
    
    protected function _error()
    {
        $this->_debugEmail .= "The transaction generated an error. \n";
        Mage::getSingleton('core/session')->addError(
            $this->_helper->__('A technical error has occurred. Please try again. If this problem persists, please contact the shop owner.')
        );

        $this->_order->cancel()->save();
        $this->_debugEmail .= "The order has been cancelled. \n";
        $this->restoreQuote();
        $this->_debugEmail .= "The quote has been restored. \n";

        return false;
    }

    protected function _pending()
    {
        $this->_debugEmail .= "The response is neutral (not successful, not unsuccessful). \n";

		Mage::getSingleton('core/session')->addSuccess(
		    $this->_helper->__(
		    	'Your order has been placed succesfully. You will recieve an e-mail containing further payment instructions shortly.'
		    )
		);
		
		if($this->_response->return->extrafields->nameField == 'redirectUrl')
		{
			$return = array(
				'response' => true,
				'redirect' => true,
				'redirecturl' => $this->_response->return->extrafields->valueField
			);
		} else {
			$return = array(
				'response' => true,
				'redirect' => false,
				'redirecturl' => ''
			);
		}

		return $return;
    }

    protected function _validation()
    {
        $this->_debugEmail .= "The response indicates a validation error. \n";

        //Mage::getBlockSingleton('core/messages')->setEscapeMessageFlag(false);
        Mage::getSingleton('core/session')->addError(
		    $this->_helper->__(
		    	'One or more fields you have entered appear to be incorrect. Please check all values entered and try again.'
		    )
		);
        
		if(!is_array($this->_response->return->failures))
		{
			$failures[] = $this->_response->return->failures;
			$this->_response->return->failures = $failures;
		}
		
        foreach($this->_response->return->failures as $failure) {
            if (isset($failure->suggestedvalue) && !empty($failure->suggestedvalue)) {
                $validationSuggestion = ucfirst($failure->suggestedvalue);		  
                Mage::getSingleton('core/session')->addError(
        		    $this->_helper->__($validationSuggestion)
        		);
            }
            $this->_debugEmail .= 'Failure: ' . var_export($failure, true) . "\n";
        }
        
        $this->_order->cancel()->save();
        $this->_debugEmail .= "The order has been cancelled. \n";
        $this->restoreQuote();
        $this->_debugEmail .= "The quote has been restored. \n";
        
		return false;
    }

    protected function _verifyError()
    {
        $this->_debugEmail .= "The transaction's authenticity was not verified. \n";
        Mage::getSingleton('core/session')->addNotice(
            $this->_helper->__('We are currently unable to retrieve the status of your transaction. If you do not recieve an e-mail regarding your order within 30 minutes, please contact the shop owner.')
        );
        
        $this->_debugEmail .= "The quote has been restored. \n";
        
        return false;
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
        $totalAmount   = round($this->_order->getBaseGrandTotal() * 100, 0);
        $resultId      = $this->_response->return->resultId;
        $transactionId = $this->_response->return->transactionId;
        $orderId       = $this->_order->getIncrementId();
       
        $orderId       = $this->_isRisk ? $orderId . '-R' : $orderId;
        
        $signatureString = $merchantId 
                         . '-'
        				 . $totalAmount 
        				 . '-'
        				 . $resultId 
        				 . '-'
        				 . $transactionId 
        				 . '-'
        				 . $orderId;

        $this->_debugEmail .= "\nSignature string: {$signatureString}\n";
        $signature = MD5($signatureString);
        $this->_debugEmail .= "signature: {$signature}\n";
        
        if ($signature === $checksum) {
            $this->_debugEmail .= "Signature matches Afterpay's checksum!\n";
            $verified = true;
        }
        
        return $verified;
    }
    
    public function _updateAndInvoice()
    {
        $this->_order->addStatusHistoryComment($this->_helper->__('This order has been accepted by AfterPay.'));
        $this->_order->save();
        
        try {
            $payment = $this->_order->getPayment();
            $this->_debugEmail .= "Attempting to capture order.\n";
            if (Mage::getStoreConfig('afterpay/afterpay_general/auto_invoice', Mage::app()->getStore()->getId())) {
                $payment->registerCaptureNotification($this->_order->getBaseGrandTotal());
                $this->_order->setStatus(Mage::getStoreConfig('afterpay/afterpay_' . $this->_order->getPayment()->getMethod() . '/order_status_accepted', Mage::app()->getStore()->getId()))->save();
            }
        } catch (Exception $e) {
            $this->_debugEmail .= 'capture has failed. Reason: ' . $e->getMessage() . "\n";
            $this->_order->addStatusHistoryComment($e->getMessage());
            $this->_order->save();
        }
        
        $this->_storeAfterPayInvoiceId();
        $this->_order->save();
    }
    
    protected function _storeAfterPayTransactionId()
    {
        $transactionId = $this->_response->return->transactionId;
        
        $this->_order->setAfterpayTransactionId($transactionId);
        $this->_order->save();
    }
    
    protected function _storeAfterPayOrderReference()
    {
        $orderReference = $this->_response->return->afterPayOrderReference;
        
        $this->_order->setAfterpayOrderReference($orderReference);
        $this->_order->save();
    }
    
    protected function _storeAFterPayInvoiceId()
    {
        foreach($this->_order->getInvoiceCollection() as $invoice)
	    {
	        $invoice->setTransactionId($this->_response->return->afterPayOrderReference)
	                ->save();
	    }
    }
    
    protected function _getRejectMessage()
    {
        if (isset($this->_response->return->rejectCode)) {
            $rejectCode = (int) $this->_response->return->rejectCode;
        } else {
            $rejectCode = 1;
        }
        
        $messageBlock = Mage::getBlockSingleton('afterpay/rejectMessages');
        $rejectMessage = $messageBlock->setRejectTemplate($rejectCode)->toHtml();
        
        return $rejectMessage;
    }
    
    protected function _getRejectDescription()
    {
        if (isset($this->_response->return->rejectCode)) {
            $rejectCode = (int) $this->_response->return->rejectCode;
        } else {
            $rejectCode = 1;
        }
        
        $messageBlock = Mage::getBlockSingleton('afterpay/rejectDescriptions');
        $rejectDescription = $messageBlock->setRejectDescription($rejectCode)->toHtml();
        
        return $rejectDescription;
    }
}