<?php 
class TIG_Afterpay_Model_Soap_Cancel extends TIG_Afterpay_Model_Soap_Abstract
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
    
    public function cancelRequest()
    {
        $param        = $this->_addCancel();
        $paramName    = 'ordermanagementobject';
        $functionName = 'cancelOrder';
        
        return $this->soapRequest('service', $functionName, $paramName, $param);
    }
    
    protected function _addCancel()
    {
        $orderManagement = Mage::getModel('afterpay/soap_parameters_orderManagement');
        $invoiceLines    = $this->_addInvoiceLines();
        $transactionKey  = $this->_addTransactionKey();
        
        $orderManagement->creditInvoicenNumber = 'AP110001260';
        $orderManagement->invoicelines         = $invoiceLines;
        $orderManagement->transactionkey       = $transactionKey;
        
        $orderManagement = $this->_cleanEmptyValues($orderManagement);
        
        return $orderManagement;
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
        
        $transactionKey->parentTransactionreference = $this->_vars['parentTransactionReference'];
        $transactionKey->ordernumber                = $this->_vars['orderNumber'];
        
        $transactionKey = $this->_cleanEmptyValues($transactionKey);
        
        return $transactionKey;
    }
}