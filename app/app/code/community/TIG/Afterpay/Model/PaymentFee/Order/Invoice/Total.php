<?php
class TIG_Afterpay_Model_PaymentFee_Order_Invoice_Total extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Retrieves Payment Fee values, calculates the amount that needs to be invoiced
     * 
     * @param Mage_Sales_Model_Order_Invoice $invoice
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        
        //retrieve all base fee-related values from order
        $basePaymentFee             = $order->getBasePaymentFee();
        $basePaymentFeeInvoiced     = $order->getBasePaymentFeeInvoiced();
        $basePaymentFeeTax          = $order->getBasePaymentFeeTax();
        $basePaymentFeeTaxInvoiced  = $order->getBasePaymentFeeTaxInvoiced();
        
        //retrieve all fee-related values from order
        $paymentFee                 = $order->getPaymentFee();
        $paymentFeeInvoiced         = $order->getPaymentFeeInvoiced();
        $paymentFeeTax              = $order->getPaymentFeeTax();
        $paymentFeeTaxInvoiced      = $order->getPaymentFeeTaxInvoiced();
        
        //get current invoice totals
        $baseInvoiceTotal            = $invoice->getBaseGrandTotal();
        $invoiceTotal                = $invoice->getGrandTotal();
        
        $baseTaxAmountTotal          = $invoice->getBaseTaxAmount();
        $taxAmountTotal              = $invoice->getTaxAmount();

        //calculate how much needs to be invoiced
        $basePaymentFeeToInvoice    = $basePaymentFee - $basePaymentFeeInvoiced;
        $paymentFeeToInvoice        = $paymentFee - $paymentFeeInvoiced;
        
        $basePaymentFeeTaxToInvoice = $basePaymentFeeTax - $basePaymentFeeTaxInvoiced;
        $paymentFeeTaxToInvoice     = $paymentFeeTax - $paymentFeeTaxInvoiced;
        
        $basePaymentFeeTaxToInvoice -= $basePaymentFeeTaxInvoiced;
        $paymentFeeTaxToInvoice     -= $paymentFeeTaxInvoiced;
        
        $baseInvoiceTotal           += $basePaymentFeeToInvoice;
        $invoiceTotal               += $paymentFeeToInvoice;
        
        $invoice->setBaseGrandTotal($baseInvoiceTotal);
        $invoice->setGrandTotal($invoiceTotal);

        //fix for issue where invoice totals is sometimes missing paymentfee tax
        //underlying cause currently unknown
        if ($invoice->getBaseGrandTotal() < $order->getBaseGrandTotal()
        	&& $invoice->getBaseGrandTotal() + $basePaymentFeeTaxToInvoice == $order->getBaseGrandTotal()
        ) {
        	$invoice->setBaseGrandTotal($baseInvoiceTotal + $basePaymentFeeTaxToInvoice);
        	$invoice->setGrandTotal($invoiceTotal + $paymentFeeTaxToInvoice);
        }
        
        $invoice->setBaseTaxAmount($baseTaxAmountTotal);
        $invoice->setTaxAmount($taxAmountTotal);
        
        $invoice->setBasePaymentFee($basePaymentFeeToInvoice);
        $invoice->setPaymentFee($paymentFeeToInvoice);
        
        $invoice->setBasePaymentFeeTax($basePaymentFeeTaxToInvoice);
        $invoice->setPaymentFeeTax($paymentFeeTaxToInvoice);
        
        return $this;
    }
}