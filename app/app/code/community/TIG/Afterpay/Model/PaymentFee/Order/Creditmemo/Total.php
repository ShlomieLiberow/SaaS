<?php
class TIG_Afterpay_Model_PaymentFee_Order_Creditmemo_Total extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Retrieves Payment Fee values, calculates the amount that can be refunded
     * 
     * @param Mage_Sales_Model_Order_Creditmemo $invoice
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();

        //retreive all base fee-related values from order
        $basePaymentFee             = $order->getBasePaymentFeeInvoiced();
        $basePaymentFeeRefunded     = $order->getBasePaymentFeeRefunded();
        $basePaymentFeeTax          = $order->getBasePaymentFeeTaxInvoiced();
        $basePaymentFeeTaxRefunded  = $order->getBasePaymentFeeTaxRefunded();
        
        //retreive all fee-related values from order
        $paymentFee                 = $order->getPaymentFeeInvoiced();
        $paymentFeeRefunded         = $order->getPaymentFeeRefunded();
        $paymentFeeTax              = $order->getPaymentFeeTaxInvoiced();
        $paymentFeeTaxRefunded      = $order->getPaymentFeeTaxRefunded();
        
        //get current creditmemo totals
        $baseRefundTotal             = $creditmemo->getBaseGrandTotal();
        $creditmemoTotal             = $creditmemo->getGrandTotal();
        
        $baseTaxAmountTotal          = $creditmemo->getBaseTaxAmount();
        $taxAmountTotal              = $creditmemo->getTaxAmount();

        //calculate how much needs to be creditmemod
        $basePaymentFeeToRefund     = $basePaymentFee - $basePaymentFeeRefunded;
        $paymentFeeToRefund         = $paymentFee - $paymentFeeRefunded;
        
        $basePaymentFeeTaxToRefund  = $basePaymentFeeTax - $basePaymentFeeTaxRefunded;
        $paymentFeeTaxToRefund      = $paymentFeeTax - $paymentFeeTaxRefunded;
        
        $baseRefundTotal            += $basePaymentFeeToRefund;
        $creditmemoTotal            += $paymentFeeToRefund;
        
        $baseTaxAmountTotal         += $basePaymentFeeTaxToRefund;
        $taxAmountTotal             += $paymentFeeTaxToRefund;
        
        //set the new creditmemod values
        $creditmemo->setBaseGrandTotal($baseRefundTotal + $basePaymentFeeTaxToRefund);
        $creditmemo->setGrandTotal($creditmemoTotal + $paymentFeeTaxToRefund);
        
        $creditmemo->setBaseTaxAmount($baseTaxAmountTotal);
        $creditmemo->setTaxAmount($taxAmountTotal);

        $creditmemo->setBasePaymentFee($basePaymentFeeToRefund);
        $creditmemo->setPaymentFee($paymentFeeToRefund);
        
        $creditmemo->setBasePaymentFeeTax($basePaymentFeeTax);
        $creditmemo->setPaymentFeeTax($paymentFeeTax);
        
        return $this;
    }
}