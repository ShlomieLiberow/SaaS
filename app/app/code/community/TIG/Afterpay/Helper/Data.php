<?php
class TIG_Afterpay_Helper_Data extends Mage_Core_Helper_Abstract
{    
    public function getFeeLabel($paymentMethodCode = false)
    {
        if ($paymentMethodCode) {
            $feeLabel = Mage::getStoreConfig('afterpay/afterpay_' . $paymentMethodCode . '/portfolio_payment_fee_label', Mage::app()->getStore()->getId());
            if (empty($feeLabel)) {
                $feeLabel = 'AfterPay servicekosten';
            }
        } else {
            $feeLabel = 'AfterPay servicekosten';
        }
        
        $feeLabel = $this->__($feeLabel);
        
        return $feeLabel;
    }
    
    public function resetPaymentFeeInvoicedValues($order, $invoice)
    {
    	$basePaymentFee    = $invoice->getBasePaymentFee();
    	$paymentFee        = $invoice->getPaymentFee();
    	$basePaymentFeeTax = $invoice->getBasePaymentFeeTax();
    	$paymentFeeTax     = $invoice->getPaymentFeeTax();
    	 
    	$basePaymentFeeInvoiced    = $order->getBasePaymentFeeInvoiced();
    	$paymentFeeInvoiced        = $order->getPaymentFeeInvoiced();
    	$basePaymentFeeTaxInvoiced = $order->getBasePaymentFeeTaxInvoiced();
    	$paymentFeeTaxInvoiced     = $order->getPaymentFeeTaxInvoiced();
    	 
    	if ($basePaymentFeeInvoiced && $basePaymentFee && $basePaymentFeeInvoiced >= $basePaymentFee) {
	    	$order->setBasePaymentFeeInvoiced($basePaymentFeeInvoiced - $basePaymentFee)
	    	      ->setPaymentFeeInvoiced($paymentFeeInvoiced - $paymentFee)
	    	      ->setBasePaymentFeeTaxInvoiced($basePaymentFeeTaxInvoiced - $basePaymentFeeTax)
	    	      ->setBasePaymentFeeInvoiced($paymentFeeTaxInvoiced - $paymentFeeTax);
	    	$order->save();
    	}
    }
    
    public function sendDebugEmail($email) 
    {
        $recipients = explode(',', Mage::getStoreConfig('afterpay/afterpay_general/debug_mail', Mage::app()->getStore()->getStoreId()));
	    
	    foreach($recipients as $recipient) {
    	    mail(
    	        trim($recipient), 
    	        'Afterpay Debug E-mail', 
    	        $email
    	    );
	    }
    }
    
	public function isAdmin()
    {
        if(Mage::app()->getStore()->isAdmin()) {
            return true;
        }

        if(Mage::getDesign()->getArea() == 'adminhtml') {
            return true;
        }

        return false;
    }
    
    public function getAfterPayPaymentMethods()
    {
        $array = array(
            'portfolio_a',
            'portfolio_b',
            'portfolio_c',
            'portfolio_d',
            'portfolio_e',
            'portfolio_f',
            // 'portfolio_g',
            // 'portfolio_h',
            // 'portfolio_i',
            // 'portfolio_j',
            // 'portfolio_k',
            // 'portfolio_l',
        );
        
        return $array;
    }
    
    public function isEnterprise()
    {
        return (bool) Mage::getConfig()->getModuleConfig("Enterprise_Enterprise")->version;
    }
    
	public function log($message, $force = false)
	{
	    Mage::log($message, Zend_Log::DEBUG, 'TIG_AfterPay.log', $force);
	}

	public function logException($e)
	{
	    if ($e instanceof Exception) {
	        Mage::log($e->getMessage(), Zend_Log::ERR, 'TIG_AfterPay_Exception.log', true);
	        Mage::log($e->getTraceAsString(), Zend_Log::ERR, 'TIG_AfterPay_Exception.log', true);
	    } else {
	        Mage::log($e, Zend_Log::ERR, 'TIG_AfterPay_Exception.log', true);
	    }
	}
}