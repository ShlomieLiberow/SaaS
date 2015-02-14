<?php 
class TIG_Afterpay_Model_Request_Refund extends TIG_Afterpay_Model_Request_Abstract
{
    protected $_isPartial = false;
    
    public function setIsPartial($isPartial)
    {
        $this->_isPartial = $isPartial;
        return $this;
    }
    
    public function getIsPartial()
    {
        return $this->_isPartial;
    }
    
    public function refundRequest()
    {
        $param        = $this->_addRefund();
        $paramName    = 'refundobject';
		
		$this->setUseSoapOmServices(true);
        
        if ($this->_isPartial) {
            $functionName = 'refundInvoice';
        } else {
            $functionName = 'refundFullInvoice';
        }
        
        return $this->soapRequest('service', $functionName, $paramName, $param);
    }
    
    protected function _addRefund()
    {
        $refundObject   = Mage::getModel('afterpay/soap_parameters_refund');
        $invoiceLines   = $this->_addInvoiceLines();
        $transactionKey = $this->_addTransactionKey();
        
        $refundObject->creditInvoicenNumber = $this->_vars['invoiceId'];
        $refundObject->invoicelines         = $invoiceLines;
        $refundObject->transactionkey       = $transactionKey;
        $refundObject->invoicenumber        = $this->_vars['invoiceId'];
        
        $refundObject = $this->_cleanEmptyValues($refundObject);
        
        return $refundObject;
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
            
            $invoiceLine = Mage::getModel('afterpay/soap_parameters_orderLine');
            
            $invoiceLine->articleDescription = $line['articleDescription'];
            $invoiceLine->articleId          = $line['articleId'];
            $invoiceLine->quantity           = $line['quantity'];
            $invoiceLine->unitprice          = $line['unitPrice'];
            $invoiceLine->vatcategory        = $line['vatCategory'];
            
            $invoiceLine = $this->_cleanEmptyValues($invoiceLine);
            
            $invoiceLines[] = $invoiceLine;
        }
        
        $invoiceLines = $this->_cleanEmptyValues($invoiceLines);
        
        return $invoiceLines;
    }
    
    protected function _addTransactionKey()
    {
        $transactionKey = Mage::getModel('afterpay/soap_parameters_transactionKey');
        
        $transactionKey->ordernumber                = $this->_vars['orderNumber'];
        
        $transactionKey = $this->_cleanEmptyValues($transactionKey);
        
        return $transactionKey;
    }
}