<?php
class TIG_Afterpay_Model_PaymentFee_Quote_Address_Total extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    protected $_code = 'paymentFee';
    
    protected $_tempAddress = '';
    protected $_method = '';
    protected $_rate = '';
    protected $_collection = '';
    
    protected function _construct()
    {
        $this->setCode('paymentFee');
    }
    
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_tempAddress = $address;
        $this->_method = $this->_tempAddress->getQuote()->getPayment()->getMethod();
                
        $this->_tempAddress->setBasePaymentFee(0);
        $this->_tempAddress->setPaymentFee(0);
        $this->_tempAddress->setBasePaymentFeeTax(0);
        $this->_tempAddress->setPaymentFeeTax(0);
        
        $allowed = $this->_isAllowed();
        if ($allowed !== true) {
            return parent::collect($this->_tempAddress);
        }
        
        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }
        
        $baseFee = $this->_getBaseFee();
        $fee = $this->_getFee();
        
        if ($baseFee == 0) {
            return $this;
        }
        
        $baseFeeTax = $this->_calulateTaxForFee($baseFee, true);
        $feeTax = $this->_calulateTaxForFee($baseFee);
                
        $this->_tempAddress->setBasePaymentFee($baseFee - $baseFeeTax);
        $this->_tempAddress->setPaymentFee($fee - $feeTax);
        
        $this->_tempAddress->setBasePaymentFeeTax($baseFeeTax);
        $this->_tempAddress->setPaymentFeeTax($feeTax);
        
        if (Mage::helper('afterpay')->isEnterprise()) {
            $this->_tempAddress->setBaseGrandTotal($this->_tempAddress->getBaseGrandTotal() + $baseFee);
            $this->_tempAddress->setGrandTotal($this->_tempAddress->getGrandTotal() + $fee);
        }
        
        $this->_tempAddress->setTaxAmount($this->_tempAddress->getTaxAmount() + $feeTax);
        $this->_tempAddress->setBaseTaxAmount($this->_tempAddress->getBaseTaxAmount() + $baseFeeTax);
        
        $this->_setAddress($this->_tempAddress); 
        $this->_setBaseAmount($baseFee);
        $this->_setAmount($fee);
        
        $this->_addFeeTaxToAppliedTaxes(
            $address, 
            $feeTax, 
            $baseFeeTax, 
            $this->_getRate()
        );
        
        return $this; 
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {            
        $this->_method = $address->getQuote()->getPayment()->getMethod();
        
        $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') 
                === FALSE ? 'http://' : 'https://';
        
        $currentUrl = $protocol . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        
        if (
            ($address->getShippingAmount() != 0 || $address->getShippingDescription())
            && $address->getBasePaymentFee()    > 0.01    
            && $currentUrl                 != Mage::helper('checkout/cart')->getCartUrl()
        ) {
            $label = Mage::helper('afterpay')->getFeeLabel($this->_method);
                    
            $address->addTotal(
                array(
                    'code'  => 'paymentFee',
                    'title' => $label,
                    'value' => $address->getBasePaymentFee(),
                )
            );
        }
        return $this;
    }
    
    private function _isAllowed()
    {
        $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') 
                === FALSE ? 'http://' : 'https://';
        
        $currentUrl = $protocol . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        
        if ($currentUrl == Mage::helper('checkout/cart')->getCartUrl()) {
            return false;
        }
        
        if (empty($this->_method)) {
            return false;
        }
        
        return true;
    }
    
    private function _getBaseFee()
    {
        $fee = Mage::getStoreConfig('afterpay/afterpay_' . $this->_method . '/portfolio_payment_fee', Mage::app()->getStore()->getId());
        
        $fee = str_replace(',', '.', $fee);
        
        if (strpos($fee, '%') !== false) {
            $feePercentage = str_replace('%', '', $fee);
            
            $quote = Mage::getModel('sales/quote');
            $quote->load($this->_tempAddress->getQuote()->getId());
            
            //calculate the fee. If the fee has already been added, remove it to prevent it from being taken into account in the calculation
            if ($quote->getBasePaymentFee()) {
                $fee = ($quote->getBaseGrandTotal() - $quote->getBasePaymentFee()) * ($feePercentage / 100);
            } elseif(!$quote->getBaseGrandTotal()) {
                $grandTotal = Mage::registry('afterpay_quote_basegrandtotal');
                $fee = $grandTotal * ($feePercentage / 100);
            } else {
                $fee = $quote->getBaseGrandTotal() * ($feePercentage / 100);
            }
        }
        
    	return (float) $fee;
    }
    
    private function _getFee()
    {
        $baseFee = $this->_getBaseFee();
        
        $quoteConvertRate = $this->_tempAddress->getQuote()->getBaseToQuoteRate();
                             
        $fee = $baseFee * $quoteConvertRate;
        return (float) $fee;
    }
    
    protected function _getRate()
    {
        $quote = $this->_tempAddress->getQuote();
        $taxClass = Mage::getStoreConfig('afterpay/afterpay_tax/paymentfee_tax_class', Mage::app()->getStore()->getId());
        
        if ($taxClass == 0) {
            $this->_rate = 1;
            return;
        }
        
        $taxCalculationModel = Mage::getSingleton('tax/calculation');
        
        $request = $taxCalculationModel->getRateRequest($quote->getShippingAddress(), $quote->getBillingAddress(), $quote->getCustomerTaxClassId(), Mage::app()->getStore()->getId());
        $request->setStore(Mage::app()->getStore())->setProductClassId($taxClass);
        
        $rate = $taxCalculationModel->getRate($request);
        
        return $rate;
    }
    
    private function _calulateTaxForFee($fee, $isBase = false)
    {
        $tax = $fee / ($this->_getRate() + 100) * $this->_getRate();
        
        if (!$isBase) {
            $quoteconvertRate = $this->_tempAddress->getQuote()->getBaseToQuoteRate();
            
            $tax *= $quoteconvertRate;
        }
        
        return $tax;
    }
    
    protected function _addFeeTaxToAppliedTaxes(Mage_Sales_Model_Quote_Address $address, $amount, $baseAmount, $taxRate)
    {
        $previouslyAppliedTaxes = $address->getAppliedTaxes();
        $applied = false;
        
        foreach ($previouslyAppliedTaxes as &$row) {
            foreach ($row['rates'] as $rate) {
                if ($rate['percent'] == $taxRate) {
                    $row['amount'] += $amount;
                    $row['base_amount'] += $baseAmount;
                    $applied = true;
                    break 2;
                } else {
                    continue;
                }
            }
        }
        
        if (false === $applied) {
            $previouslyAppliedTaxes['afterpay_fee_tax'] = array(
                'rates' => array(
                    array(
                        'code' => 'afterpay_fee_tax',
                        'title' => 'AfterPay Servicekosten Tax',
                        'percent' => (float) $taxRate,
                        'position' => '0',
                        'priority' => '1',
                        'rule_id' => '2',
                    ),
                ),
                'percent'     => (float) $taxRate,
                'id'          => 'afterpay_fee_tax',
                'process'     => 0,
                'amount'      => $amount,
                'base_amount' => $baseAmount,
            );
        }
        
        $address->setAppliedTaxes($previouslyAppliedTaxes);        
    }
}