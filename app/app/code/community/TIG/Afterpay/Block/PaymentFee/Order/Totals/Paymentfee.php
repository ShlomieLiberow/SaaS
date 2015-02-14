<?php
class TIG_Afterpay_Block_PaymentFee_Order_Totals_Paymentfee extends Mage_Core_Block_Abstract
{
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order  = $parent->getOrder();
     
        if ($this->_order->getPaymentFee() < 0.01 || $this->_order->getPaymentFee() < 0.01) {
            return $this;
        }
        
        $paymentmethodCode = $this->_order->getPayment()->getMethod();
        $feeLabel = Mage::helper('afterpay')->getfeeLabel($this->_order->getPayment()->getMethod());
        
        $paymentFee = new Varien_Object();
        $paymentFee->setLabel($feeLabel);
        $paymentFee->setValue($this->_order->getPaymentFee());
        $paymentFee->setBaseValue($this->_order->getBasePaymentFee());
        $paymentFee->setCode('payment_fee');
  
        $parent->addTotalBefore($paymentFee, 'tax');

        return $this;
    }
}