<?php
class TIG_Afterpay_Block_PaymentFee_Order_Creditmemo_Totals_Frontend_Paymentfee extends Mage_Core_Block_Abstract
{
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_creditmemo  = $parent->getCreditmemo();
        
        $paymentmethodCode = $this->_creditmemo->getOrder()->getPayment()->getMethod();
        $feeLabel = Mage::helper('afterpay')->getfeeLabel($paymentmethodCode);
        
        $paymentFeeRefunded = new Varien_Object();
        $paymentFeeRefunded->setLabel($feeLabel);
        $paymentFeeRefunded->setValue($this->_creditmemo->getOrder()->getPaymentFeeRefunded() + $this->_creditmemo->getOrder()->getPaymentFeeTaxRefunded());
        $paymentFeeRefunded->setBaseValue($this->_creditmemo->getOrder()->getBasePaymentFeeRefunded() + $this->_creditmemo->getOrder()->getBasePaymentFeeTaxRefunded());
        $paymentFeeRefunded->setCode('payment_fee_refunded');
        
        $parent->addTotalBefore($paymentFeeRefunded, 'tax');

        return $this;
    }
}