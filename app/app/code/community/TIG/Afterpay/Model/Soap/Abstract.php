<?php

class TIG_Afterpay_Model_Soap_Abstract extends Mage_Core_Model_Abstract
{
    const WSDL_URL              = 'https://www.acceptgirodienst.nl/soapservices/rm/AfterPaycheck?wsdl';
    const TEST_WSDL_URL         = 'https://test.acceptgirodienst.nl/soapservices/rm/AfterPaycheck?wsdl';
    const SERVICE_WSDL_URL      = 'https://www.acceptgirodienst.nl/soapservices/om/OrderManagement?wsdl';
    const TEST_SERVICE_WSDL_URL = 'https://test.acceptgirodienst.nl/soapservices/om/OrderManagement?wsdl';

    protected $_testMode = false;
    protected $_vars;
    protected $_method;
    protected $_debugEmail;
	protected $_country;
	protected $_useSoapOmServices;
    
    public function getTestMode()
    {
        return $this->_testMode;
    }
    
    public function setTestMode($testMode = false)
    {
        $this->_testMode = $testMode;
        return $this;
    }
    
    public function getVars()
    {
        return $this->_vars;
    }
    
    public function setVars($vars = array())
    {
        $this->_vars = $vars;
        return $this;
    }
	
    public function getCountry()
    {
        return $this->_country;
    }
    
    public function setCountry($country = 'nlnl')
    {
        $this->_country = $country;
        return $this;
    }
	
	public function setUseSoapOmServices($use = true)
	{
		$this->_useSoapOmServices = $use;
	}
    
    public function getMethod()
    {
        return $this->_method;
    }
    
    public function setMethod($method = '')
    {
        $this->_method = $method;
        return $this;
    }
    
    public function soapRequest($clientType, $functionName, $paramName, $param)
    {
        $client = $this->_getCorrectClient($clientType);
        $authorization = $this->_getAuthorization();
        
        try {
            $response = $client->__soapCall(
            	$functionName, 
                array(
                    $functionName => array(
                		'authorization' => $authorization, 
                		$paramName      => $param,
                    ),
                )
            );
        } catch (SoapFault $e) {
            Mage::helper('afterpay')->logException($e);
        	return $this->_error($client);
        } catch (Exception $e) {
            Mage::helper('afterpay')->logException($e);
            return $this->_error($client);
        }
        if (is_null($response)) {
            $response = false;
        }
        
        $responseXML = $client->__getLastResponse();
        $requestXML = $client->__getLastRequest();
        
        $responseDomDOC = new DOMDocument();
        $responseDomDOC->loadXML($responseXML);
		$responseDomDOC->preserveWhiteSpace = FALSE;
		$responseDomDOC->formatOutput = TRUE;
		
		$requestDomDOC = new DOMDocument();
        $requestDomDOC->loadXML($requestXML);
		$requestDomDOC->preserveWhiteSpace = FALSE;
		$requestDomDOC->formatOutput = TRUE;

        return array($response, $responseDomDOC, $requestDomDOC);
    }
    
    /**
     * Method that attempts to retrieve a SoapClient instance in WSDL mode.
     * The method first attempts using a cached version of the WSDL. If that fails, it tries a non-cached version. If that also fails,
     * it will use a local version that is provided with this module
     */
    protected function _getCorrectCLient($wsdlType)
    {
        try {
            $client = $this->_getClient($wsdlType, WSDL_CACHE_DISK);
        } catch (SoapFault $e) {
            try {
                $client = $this->_getClient($wsdlType, WSDL_CACHE_NONE);
            } catch (SoapFault $e) {
                try {
                    $client = $this->_getClient($wsdlType, 'local');
                } catch (SoapFault $e) {
                    Mage::helper('afterpay')->logException($e);
                    $this->_error();
                }
            }
        }
        
        return $client;
    }
    
    protected function _getClient($wsdlType, $cacheMode = WSDL_CACHE_NONE)
    {
        if ($cacheMode == 'local') {
            $wsdl = $this->_getLocalWsdl($wsdlType);
        } elseif ($this->_testMode) {
            $wsdl = $this->_getTestWsdlUrl($wsdlType);
        } else {
            $wsdl = $this->_getWsdlUrl($wsdlType);
        }
		
		$endpoints['nlnl']['test']['rm'] = 'https://test.acceptgirodienst.nl/soapservices/rm/AfterPaycheck?wsdl';
		$endpoints['nlnl']['test']['om'] = 'https://test.acceptgirodienst.nl/soapservices/om/OrderManagement?wsdl';
		$endpoints['nlnl']['live']['rm'] = 'https://www.acceptgirodienst.nl/soapservices/rm/AfterPaycheck?wsdl';
		$endpoints['nlnl']['live']['om'] = 'https://www.acceptgirodienst.nl/soapservices/om/OrderManagement?wsdl';
		$endpoints['benl']['test']['rm'] = 'https://test.afterpay.be/soapservices/rm/AfterPaycheck';
		$endpoints['benl']['test']['om'] = 'https://test.afterpay.be/soapservices/om/OrderManagement';
		$endpoints['benl']['live']['rm'] = 'https://api.afterpay.be/soapservices/rm/AfterPaycheck';
		$endpoints['benl']['live']['om'] = 'https://api.afterpay.be/soapservices/om/OrderManagement';
		$endpoints['dede']['test']['rm'] = '';
		$endpoints['dede']['test']['om'] = '';
		$endpoints['dede']['live']['rm'] = '';
		$endpoints['dede']['live']['om'] = '';			

		if ($this->_country == 'nlnl') {
			$client = new SoapClient(
				$wsdl,
				array(
					'trace' => 1,
					'cache_wsdl' => $cacheMode,
			));
		} else {
			if ($this->_testMode) {
				if ($this->_useSoapOmServices) {
					$location = $endpoints[$this->_country]['test']['om'];
				} else {
					$location = $endpoints[$this->_country]['test']['rm'];
				}
			} else {
				if ($this->_useSoapOmServices) {
					$location = $endpoints[$this->_country]['live']['om'];
				} else {
					$location = $endpoints[$this->_country]['live']['rm'];
				}
			}
			
			$client = new SoapClient(
				$wsdl,
				array(
					'location' => $location,
					'trace' => 1,
					'cache_wsdl' => $cacheMode,
			));
		}
        
        return $client;
    }
    
    protected function _getWsdlUrl($wsdlType)
    {
        switch ($wsdlType) {
            case 'authorize':    return self::WSDL_URL;
                                 break;
            case 'service':      return self::SERVICE_WSDL_URL;
                                 break;
            default:             Mage::throwException('desired WSDL type not found. Requested WSDl type: ' . $wsdlType);
        }
    }
    
    protected function _getTestWsdlUrl($wsdlType)
    {
        switch ($wsdlType) {
            case 'authorize':    return self::TEST_WSDL_URL;
                                 break;
            case 'service':      return self::TEST_SERVICE_WSDL_URL;
                                 break;
            default:             Mage::throwException('desired WSDL type not found. Requested WSDl type: ' . $wsdlType);
        }
    }
    
    protected function _getLocalWsdl($wsdlType)
    {
        switch ($wsdlType) {
            case 'authorize':    return Mage::getBaseDir()
                                        . DS 
                                        . 'app' 
                                        . DS 
                                        . 'code' 
                                        . DS 
                                        . 'community' 
                                        . DS 
                                        . 'TIG' 
                                        . DS 
                                        . 'Afterpay' 
                                        . DS 
                                        . 'Model' 
                                        . DS 
                                        . 'Soap' 
                                        . DS 
                                        . 'Wsdl' 
                                        . DS 
                                        . 'AfterPaycheck_1.wsdl';
                                 break;
            case 'service':      return Mage::getBaseDir()
                                        . DS 
                                        . 'app' 
                                        . DS 
                                        . 'code' 
                                        . DS 
                                        . 'community' 
                                        . DS 
                                        . 'TIG' 
                                        . DS 
                                        . 'Afterpay' 
                                        . DS 
                                        . 'Model' 
                                        . DS 
                                        . 'Soap' 
                                        . DS 
                                        . 'Wsdl' 
                                        . DS 
                                        . 'OrderManagement_1.wsdl';
                                 break;
            default:             Mage::throwException('desired WSDL type not found. Requested WSDl type: ' . $wsdlType);
        }
    }
    
    protected function _getAuthorization()
    {
        $authorization = Mage::getModel('afterpay/soap_parameters_authorization');
        
        $authorization->merchantId    = $this->_vars['merchantId'];
        $authorization->portfolioId   = $this->_vars['portfolioId'];
        $authorization->password      = $this->_vars['password'];
        
        return $authorization;
    }
    
    protected function _cleanEmptyValues($object)
    {
        foreach($object as $key => $value) {
            if (is_null($value) || $value === '') {
                unset($object->$key);
            }
        }
        
        return $object;
    }
    
    protected function _error($client = false)
    {
        $response = false;
        
        $responseDomDOC = new DOMDocument();
		$requestDomDOC = new DOMDocument();
        if ($client) {
            $responseXML = $client->__getLastResponse();
            $requestXML = $client->__getLastRequest();
        
            if (!empty($responseXML)) {
                $responseDomDOC->loadXML($responseXML);
        		$responseDomDOC->preserveWhiteSpace = FALSE;
        		$responseDomDOC->formatOutput = TRUE;
            }
    		
            if (!empty($requestXML)) {
                $requestDomDOC->loadXML($requestXML);
        		$requestDomDOC->preserveWhiteSpace = FALSE;
        		$requestDomDOC->formatOutput = TRUE;
            }
        }

        return array($response, $responseDomDOC, $requestDomDOC);
    }
}