<?php
class TIG_Afterpay_Block_Portfolios_Checkout_Form extends Mage_Payment_Block_Form
{
    public $shopName                         = '';
    public $maxOrderAmountNewCustomers       = '&#8364;';
    public $maxOrderAmountReturningCustomers = '&#8364;';
    public $anchorClose                      = '</a>';
    public $privacyStatementUrl              = '<a href="http://www.afterpay.nl/page/privacy-statement" target="_blank">';
    public $consumerContactUrl               = '<a href="http://www.afterpay.nl/page/consument-contact" target="_blank">';
    public $consumerPageUrl                  = '<a href="http://www.afterpay.nl/page/consument" target="_blank">';
    public $paymentConditionsUrl             = '<a href="http://www.afterpay.nl/page/consument-betalingsvoorwaarden" target="_blank">';
	public $country							 = 'nlnl';
    
    protected $_template = 'TIG/Afterpay/portfolios/checkout/form.phtml';
    
    public function setBlockData()
    {
		$shopName = Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId());
		$this->shopName = $shopName ? $shopName : 'deze webshop';
		
		$newCustomerAmount = Mage::getStoreConfig(
			'afterpay/afterpay_' . $this->getMethod()->getCode() . '/portfolio_max_amount_new_customers', 
		    Mage::app()->getStore()->getId()
		);
		
		$returningCustomerAmount = Mage::getStoreConfig(
			'afterpay/afterpay_' . $this->getMethod()->getCode() . '/portfolio_max_amount', 
		    Mage::app()->getStore()->getId()
		);
		
		$this->maxOrderAmountNewCustomers .= round($newCustomerAmount, 2);
		$this->maxOrderAmountReturningCustomers .= round($returningCustomerAmount, 2);
		
		$this->country = Mage::getStoreConfig(
			'afterpay/afterpay_' . $this->getMethod()->getCode() . '/portfolio_country', 
		    Mage::app()->getStore()->getId()
		);
		
		if($this->country == 'benl'){
			// Check if url is Belgium
			$this->privacyStatementUrl 			= '<a href="http://www.afterpay.be/" target="_blank">';
			$this->consumerContactUrl			= '<a href="http://www.afterpay.be/" target="_blank">';
			$this->consumerPageUrl				= '<a href="http://www.afterpay.be/" target="_blank">';
			$this->paymentConditionsUrl			= '<a href="http://www.afterpay.be/" target="_blank">';
		}
    }
    
    public function getMethodLabelAfterHtml()
    {
        
        $labelAfterHtml = '<img src="'. $this->getSkinUrl('images/TIG/Afterpay/afterpay.png') . '" />&nbsp;'.$this->getMethod()->getTitle(); 
		
        if (!$this->getMethod()->getFootnote()) {
            $labelAfterHtml .= '<span class = \'afterpay_paymentmethod_label afterpay_paymentmethod_label_' 
                        . $this->getMethod()->getCode() 
                        . '\'>';$this->getMethod()->getFootnote() . '</span>';
        }
		
        return $labelAfterHtml;
    }
    
    public function hasMethodTitle()
    {
        return true;
    }
    
    public function getMethodTitle()
    {
        return '';
    }
    
    public function isB2B()
    {
        if (Mage::getStoreConfig('afterpay/afterpay_' . $this->getMethod()->getCode() . '/portfolio_type') == 'B2B') {
            return true;
        }
        return false;
    }
    
	public function showBankaccount()
	{
		if (Mage::getStoreConfig('afterpay/afterpay_' . $this->getMethod()->getCode() . '/portfolio_showbankaccount') == '1') {
            return true;
        }
        return false;
	}
	
    public function getCompany()
    {
        $billingAddress = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress();
        
        return $billingAddress->getCompany();
    }
}