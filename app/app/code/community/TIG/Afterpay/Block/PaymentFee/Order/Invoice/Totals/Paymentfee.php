<?php
class TIG_Afterpay_Block_PaymentFee_Order_Invoice_Totals_Paymentfee extends Mage_Core_Block_Abstract
{
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_invoice  = $parent->getInvoice();
        
        if(!is_object($this->_invoice))
            return $this;

        if (
            ($this->_invoice->getPaymentFee() < 0.01 || $this->_invoice->getPaymentFee() < 0.01)
            && ($this->_invoice->getOrder()->getBasePaymentFee() - $this->_invoice->getOrder()->getBasePaymentFeeInvoiced()) < 0.01
           ) 
        {
            return $this;
        }
        
        $paymentmethodCode = $this->_invoice->getOrder()->getPayment()->getMethod();
        $feeLabel = Mage::helper('afterpay')->getfeeLabel($paymentmethodCode);
        
        $paymentFee = new Varien_Object();
        $paymentFee->setLabel($feeLabel);
        $paymentFee->setValue($this->_invoice->getPaymentFee());
        $paymentFee->setBaseValue($this->_invoice->getBasePaymentFee());
        $paymentFee->setCode('payment_fee');
        
        $parent->addTotalBefore($paymentFee, 'tax');

        return $this;
    }
}