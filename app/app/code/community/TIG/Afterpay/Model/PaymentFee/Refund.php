<?php
class TIG_Afterpay_Model_PaymentFee_Refund extends Mage_Core_Model_Abstract
{
    private $_creditmemo;
    
    protected $_order;
    protected $_invoice;
    
    public function setCreditmemo(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $this->_creditmemo = $creditmemo;
    }
    
    public function getCreditmemo()
    {
        return $this->_creditmemo;
    }
    
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
    }
    
    public function getOrder()
    {
        return $this->_order;
    }
    
    public function setInvoice(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $this->_invoice = $invoice;
    }
    
    public function getInvoice()
    {
        return $this->_invoice;
    }
    
    protected function _setOrderFromCreditmemo()
    {
        $order = $this->_creditmemo->getOrder();
        $this->setOrder($order);
    }
    
    protected function _setInvoiceFromCreditmemo()
    {
        $invoice = $this->_creditmemo->getInvoice();
        $this->setInvoice($invoice);
    }
    
    public function __construct(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $this->setCreditmemo($creditmemo);
        $this->_setOrderFromCreditmemo();
        $this->_setInvoiceFromCreditmemo();
    }
    
    public function paymentFeeRefund()
    {
        $quoteConvertRate                        = $this->_order->getBaseToQuoteRate();
        
        if (empty($quoteConvertRate)) {
            $quoteConvertRate = 1;
        }
        
        //get amounts that are to be refunded
        $basePaymentFeeToRefund                  = (float) $this->_creditmemo->getPaymentFeeToRefund();
        
        //in order to prevent rounding errors from causing errors
        if (
            $basePaymentFeeToRefund > $this->_order->getBasePaymentFee() 
            && $basePaymentFeeToRefund == round($this->_order->getBasePaymentFee(), 2)
        ) {
            $basePaymentFeeToRefund = $this->_order->getBasePaymentFee();
        }
        
        $paymentFeeToRefund                      = (float) $basePaymentFeeToRefund * $quoteConvertRate;
        
        $basePaymentFeeTaxToRefund               = (float) $this->_calculatePaymentFeeTaxToRefund($basePaymentFeeToRefund, true);
        $paymentFeeTaxToRefund                   = (float) $this->_calculatePaymentFeeTaxToRefund($paymentFeeToRefund);
        
        $paymentFeeToRefund                     -= $paymentFeeTaxToRefund;
        $basePaymentFeeToRefund                 -= $basePaymentFeeTaxToRefund;
        
        if ($this->_invoice) {
            //get the amounts that are available to refund (cant refund more than is available)
            $paymentFeeAvailableForRefund        = $this->_invoice->getPaymentFee() - $this->_order->getPaymentFeeRefunded() + ($this->_invoice->getPaymentFeeTax() - $this->_order->getPaymentFeeTaxRefunded());
            $basePaymentFeeAvailableForRefund    = $this->_invoice->getBasePaymentFee() - $this->_order->getBasePaymentFeeRefunded() + ($this->_invoice->getBasePaymentFeeTax() - $this->_order->getBasePaymentFeeTaxRefunded());
            
            $paymentFeeTaxAvailableForRefund     = $this->_invoice->getPaymentFeeTax() - $this->_order->getPaymentFeeTaxRefunded();
            $basePaymentFeeTaxAvailableForRefund = $this->_invoice->getBasePaymentFeeTax() - $this->_order->getBasePaymentFeeTaxRefunded();
        } else {
            //get the amounts that are available to refund (cant refund more than is available)
            $paymentFeeAvailableForRefund        = $this->_order->getPaymentFee() - $this->_order->getPaymentFeeRefunded() + ($this->_order->getPaymentFeeTax() - $this->_order->getPaymentFeeTaxRefunded());
            $basePaymentFeeAvailableForRefund    = $this->_order->getBasePaymentFee() - $this->_order->getBasePaymentFeeRefunded() + ($this->_order->getBasePaymentFeeTax() - $this->_order->getBasePaymentFeeTaxRefunded());
            
            $paymentFeeTaxAvailableForRefund     = $this->_order->getPaymentFeeTax() - $this->_order->getPaymentFeeTaxRefunded();
            $basePaymentFeeTaxAvailableForRefund = $this->_order->getBasePaymentFeeTax() - $this->_order->getBasePaymentFeeTaxRefunded();
        }
        
        //check if the amount that is to be invoiced exceeds the available amount
//        if (
//            $paymentFeeAvailableForRefund           < $paymentFeeToRefund
//            || $basePaymentFeeAvailableForRefund    < $basePaymentFeeToRefund
//            || $paymentFeeTaxAvailableForRefund     < $paymentFeeTaxToRefund
//            || $basePaymentFeeTaxAvailableForRefund < $basePaymentFeeTaxToRefund
//           )
//        {
//            Mage::getSingleton('adminhtml/session')->addError(
//                Mage::helper('afterpay')->__(
//                	'You cannot refund a larger amount than is available. Maximum Payment Fee available for refund: '
//                ) . $paymentFeeAvailableForRefund
//           );
//            Mage::throwException();
//        }        
        
        $this->_order->setPaymentFeeRefunded($this->_order->getPaymentFeeRefunded() + $paymentFeeToRefund);
        $this->_order->setBasePaymentFeeRefunded($this->_order->getBasePaymentFeeRefunded() + $basePaymentFeeToRefund);
        
        $this->_order->setPaymentFeeTaxRefunded($this->_order->getPaymentFeeTaxRefunded() + $paymentFeeTaxToRefund);
        $this->_order->setBasePaymentFeeTaxRefunded($this->_order->getBasePaymentFeeTaxRefunded() + $basePaymentFeeTaxToRefund);
        
        $this->_creditmemo->setGrandTotal($this->_creditmemo->getGrandTotal() - ($this->_creditmemo->getPaymentFee() + $this->_creditmemo->getPaymentFeeTax()) + ($paymentFeeToRefund + $paymentFeeTaxToRefund));
        $this->_creditmemo->setBaseGrandTotal($this->_creditmemo->getBaseGrandTotal() - ($this->_creditmemo->getBasePaymentFee() + $this->_creditmemo->getBasePaymentFeeTax()) + ($basePaymentFeeToRefund + $basePaymentFeeTaxToRefund));
        
        $this->_creditmemo->setBasePaymentFee($basePaymentFeeToRefund);
        $this->_creditmemo->setPaymentFee($paymentFeeToRefund);
        
        $this->_creditmemo->setBasePaymentFeeTax($basePaymentFeeTaxToRefund);
        $this->_creditmemo->setPaymentFeeTax($paymentFeeTaxToRefund);
        
        $this->_creditmemo->setTaxAmount($this->_creditmemo->getTaxAmount() + $paymentFeeTaxToRefund);
        $this->_creditmemo->setBaseTaxAmount($this->_creditmemo->getBaseTaxAmount() + $basePaymentFeeTaxToRefund);
        
        return $this->_creditmemo;
    }
    
    protected function _calculatePaymentFeeTaxToRefund($feeToRefund, $base = false)
    {
        if ($base === true) {
            $fee = $this->_order->getBasePaymentFeeInvoiced() - $this->_order->getBasePaymentFeeRefunded() + ($this->_order->getBasePaymentFeeTaxInvoiced() - $this->_order->getBasePaymentFeeTaxRefunded());
            $tax = $this->_order->getBasePaymentFeeTaxInvoiced() - $this->_order->getBasePaymentFeeTaxRefunded();
        } else {
            $fee = $this->_order->getPaymentFeeInvoiced() - $this->_order->getPaymentFeeRefunded() + ($this->_order->getPaymentFeeTaxInvoiced() - $this->_order->getPaymentFeeTaxRefunded());
            $tax = $this->_order->getPaymentFeeTaxInvoiced() - $this->_order->getPaymentFeeTaxRefunded();
        }
        
        if ($fee == 0) {
            return 0;
        }
        
        $ratio = $feeToRefund / $fee;
        
        $taxToRefund = $tax * $ratio;
        
        return $taxToRefund;
    }
}