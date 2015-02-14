<?php 
class TIG_Afterpay_Model_Request_Capture extends TIG_Afterpay_Model_Request_Abstract
{
    protected $_invoice;
    
    public function setInvoice($invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }
    
    public function getInvoice()
    {
        return $this->_invoice;
    }
    
    protected function _construct() 
    {       
	    $this->setHelper(Mage::helper('afterpay'));
        
        $this->_debugEmail = '';
    }
    
    public function sendCaptureRequest()
    {
	    $method = $this->_order->getPayment()->getMethod();
        $testMode = (bool) Mage::getStoreConfig('afterpay/afterpay_general/mode', Mage::app()->getStore()->getId());
        if (!$testMode) {
        	$testMode = (bool) Mage::getStoreConfig('afterpay/afterpay_' . $method . '/mode', Mage::app()->getStore()->getId());
        }
		
		$country = (string) Mage::getStoreConfig('afterpay/afterpay_' . $method . '/portfolio_country', Mage::app()->getStore()->getId());
		
        $this->setTestMode($testMode);
        
        $this->_debugEmail .= 'Chosen portfolio: ' . $method . "\n";

        $captureResponseModel = Mage::getModel('afterpay/response_capture');
        
        //if no method has been set (no payment method could identify the chosen method) process the order as if it had failed
        if (empty($this->_method)) {
            $this->_debugEmail .= "No method was set! \n";
            $captureResponseModel->setResponse(false)
                                 ->setResponseXML(false)
                                 ->setDebugEmail($this->getDebugEmail())
                                 ->setInvoice($this->getInvoice())
                                 ->setOrder($this->getOrder());
                                 
            try {
                return $captureResponseModel->processResponse();
            } catch (Exception $e) {
                $captureResponseModel->sendDebugEmail();
                $this->logException($e);
                return false;
            }
        }

        $this->_debugEmail .= "\n";
        //forms an array with all payment-independant variables (such as merchantkey, order id etc.) which are required for the transaction request
        $this->_addShopVariables();
        $this->_addTransactionKey();
        $this->_addCaptureVariables();
        $this->_addPortfolioVariables();
        $this->_addOrderVariables();
        
        $this->_debugEmail .= "Firing request events. \n";
        //event that allows individual payment methods to add additional variables such as bankaccount number
        //currently this is not used, however developers may use this event to easily modify the values sent to AfterPay
        Mage::dispatchEvent('afterpay_capture_request_addcustomvars', array('request' => $this, 'order' => $this->_order));

        $this->_debugEmail .= "Events fired! \n";

        //clean the array for a soap request
        $this->setVars($this->_cleanArrayForSoap($this->getVars()));

        $this->_debugEmail .= "Variable array:" . var_export($this->_vars, true) . "\n\n";
        $this->_debugEmail .= "Building SOAP request... \n";

        //send the transaction request using SOAP
        $soap = Mage::getModel('afterpay/soap_capture');
        $soap->setVars($this->getVars())
        	 ->setTestMode($this->getTestMode())
             ->setMethod($this->getMethod())
			 ->setCountry($country);
        
        list($response, $responseXML, $requestXML) = $soap->captureRequest();

        $this->_debugEmail .= "The SOAP request has been sent. \n";
        
        if (!is_object($requestXML) || !is_object($responseXML)) { 
            $this->_debugEmail .= "Request or response was not an object \n";
        } else {
            $this->_debugEmail .= "Request: " . var_export($requestXML->saveXML(), true) . "\n";
            $this->_debugEmail .= "Response: " . var_export($response, true) . "\n";
            $this->_debugEmail .= "Response XML:" . var_export($responseXML->saveXML(), true) . "\n\n";
        }
        
        $this->_debugEmail .= "Processing response... \n";
        //process the response
        $captureResponseModel->setResponse($response)
                             ->setResponseXML($responseXML)
                             ->setDebugEmail($this->getDebugEmail())
                             ->setInvoice($this->getInvoice())
                             ->setOrder($this->getOrder())
                             ->setRequest($this);
                             
        try {
            return $captureResponseModel->processResponse();
        } catch (Exception $e) {
            $captureResponseModel->sendDebugEmail();
            $this->logException($e);
            return false;
        }
    }
    
    protected function _addCaptureVariables()
    {
        $this->_invoice->save();
        
        $captureDelayDays = Mage::getStoreConfig('aftrpay/afterpay_capture/capture_delay_days', Mage::app()->getStore()->getId());
        if (empty($captureDelayDays)) {
            $captureDelayDays = 0;
        }
        
        $shippingMethod =$this->_order->getShippingDescription();
        
        $array = array(
            'captureDelay'        => $captureDelayDays,
            'invoiceId'           => $this->_invoice->getIncrementId(),
            'shippingMethodTitle' => $shippingMethod,
        );
        
        if (is_array($this->_vars)) {
            $this->_vars = array_merge($this->_vars, $array);
        } else {
            $this->_vars = $array;
        }
        
        $this->_debugEmail .= "Capture variables added! \n";
    }
    
    protected function _addTransactionKey()
    {
        $portfolioId = $this->_getPortfolioId();
        
        $array = array(
            'parentTransactionReference' => $this->_order->getAfterpayOrderReference(),
        );
        
        if (is_array($this->_vars)) {
            $this->_vars = array_merge($this->_vars, $array);
        } else {
            $this->_vars = $array;
        }
        
        $this->_debugEmail .= "Portfolio variables added! \n";
    }
}