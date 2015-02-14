<?php 
class TIG_Afterpay_Model_Soap_Capture extends TIG_Afterpay_Model_Soap_Abstract
{
    public function captureRequest()
    {
        $param        = $this->_addCapture();
        $paramName    = 'captureobject';
        $functionName = 'captureFull';
		
		$this->setUseSoapOmServices(true);
        
        return $this->soapRequest('service', $functionName, $paramName, $param);
    }
    
    protected function _addCapture()
    {
        $captureObject   = Mage::getModel('afterpay/soap_parameters_capture');
        $invoiceLines    = $this->_addInvoiceLines();
        $transactionKey  = $this->_addTransactionKey();
        
        $captureObject->capturedelaydays     = $this->_vars['captureDelay'];
        $captureObject->invoicelines         = $invoiceLines;
        $captureObject->transactionkey       = $transactionKey;
        $captureObject->shippingCompany      = $this->_vars['shippingMethodTitle'];
        $captureObject->invoicenumber        = $this->_vars['invoiceId'];
        
        $captureObject = $this->_cleanEmptyValues($captureObject);
        
        return $captureObject;
    }
    
    protected function _addInvoiceLines()
    {
        $invoiceLines = array();
        
        if (!array_key_exists('orderLines', $this->_vars)) {
            return false;
        }
        foreach ($this->_vars['orderLines'] as $line) {
            if (empty($line)) {
                continue;
            }
            
            $orderLine = Mage::getModel('afterpay/soap_parameters_orderLine');
            
            $orderLine->articleDescription = $line['articleDescription'];
            $orderLine->articleId          = $line['articleId'];
            $orderLine->quantity           = $line['quantity'];
            $orderLine->unitprice          = $line['unitPrice'];
            $orderLine->vatcategory        = $line['vatCategory'];
            
            $orderLine = $this->_cleanEmptyValues($orderLine);
            
            $invoiceLines[] = $orderLine;
        }
        
        $invoiceLines = $this->_cleanEmptyValues($invoiceLines);
        
        return $invoiceLines;
    }
    
    protected function _addTransactionKey()
    {
        $transactionKey = Mage::getModel('afterpay/soap_parameters_transactionKey');
        
        //$transactionKey->parentTransactionreference = $this->_vars['parentTransactionReference'];
        $transactionKey->ordernumber                = $this->_vars['orderNumber'];
        
        $transactionKey = $this->_cleanEmptyValues($transactionKey);
        
        return $transactionKey;
    }
}