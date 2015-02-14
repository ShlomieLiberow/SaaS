<?php
class TIG_Afterpay_Block_PaymentFee_Order_Creditmemo_Totals_Paymentfee extends Mage_Core_Block_Abstract
{
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_creditmemo  = $parent->getCreditmemo();
        
        $paymentmethodCode = $this->_creditmemo->getOrder()->getPayment()->getMethod();
        $feeLabel = Mage::helper('afterpay')->getfeeLabel($paymentmethodCode);
        
        if ($this->_creditmemo->getinvoice()) {
            $paymentFee = new Varien_Object();
            $paymentFee->setLabel($feeLabel . ' available for refund');
            $paymentFee->setValue($this->_creditmemo->getInvoice()->getPaymentFee() - $this->_creditmemo->getOrder()->getPaymentFeeRefunded() + ($this->_creditmemo->getInvoice()->getPaymentFeeTax() - $this->_creditmemo->getOrder()->getPaymentFeeTaxefunded()));
            $paymentFee->setBaseValue($this->_creditmemo->getInvoice()->getBasePaymentFee() - $this->_creditmemo->getOrder()->getBasePaymentFeeRefunded() + ($this->_creditmemo->getInvoice()->getBasePaymentFeeTax() - $this->_creditmemo->getOrder()->getBasePaymentFeeTaxRefunded()));
            $paymentFee->setCode('payment_fee');
        } else {
            $paymentFee = new Varien_Object();
            $paymentFee->setLabel($feeLabel . ' available for refund');
            $paymentFee->setValue($this->_creditmemo->getOrder()->getPaymentFee() - $this->_creditmemo->getOrder()->getPaymentFeeRefunded() + ($this->_creditmemo->getOrder()->getPaymentFeeTax() - $this->_creditmemo->getOrder()->getPaymentFeeTaxRefunded()));
            $paymentFee->setBaseValue($this->_creditmemo->getOrder()->getBasePaymentFee() - $this->_creditmemo->getOrder()->getBasePaymentFeeRefunded() + ($this->_creditmemo->getOrder()->getBasePaymentFeeTax() - $this->_creditmemo->getOrder()->getBasePaymentFeeTaxRefunded()));
            $paymentFee->setCode('payment_fee');
        }
        
        $paymentFeeRefunded = new Varien_Object();
        $paymentFeeRefunded->setLabel($feeLabel . ' refunded');
        $paymentFeeRefunded->setValue($this->_creditmemo->getOrder()->getPaymentFeeRefunded() + $this->_creditmemo->getOrder()->getPaymentFeeTaxRefunded());
        $paymentFeeRefunded->setBaseValue($this->_creditmemo->getOrder()->getBasePaymentFeeRefunded() + $this->_creditmemo->getOrder()->getBasePaymentFeeTaxRefunded());
        $paymentFeeRefunded->setCode('payment_fee_refunded');
        
        $parent->addTotalBefore($paymentFee, 'tax');
        $parent->addTotalBefore($paymentFeeRefunded, 'payment_fee');

        return $this;
    }
}