<?php 
class TIG_Afterpay_Block_Adminhtml_System_Config_TabColouring
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'TIG/Afterpay/system/config/tabColouring.phtml';
    
    public $configTabs = array(
        'afterpay_general',
        'afterpay_tax',
        'afterpay_support',
    );
    
    public $serviceTabs = array(
        'afterpay_capture',
        'afterpay_refund',
    );
    
    public function getTabClass($tabName = '')
    {
        if (in_array($tabName, $this->configTabs)) {
            return $this->getConfigClass($tabName);
        }
        
        if (in_array($tabName, $this->serviceTabs)) {
            return $this->getServiceClass($tabName);
        }
        
        return $this->getPortfolioClass($tabName);
    }
    
    public function getConfigClass($tabName)
    {
        return 'afterpay_config';
    }
    
    public  function getServiceClass($tabName)
    {
        return 'afterpay_service_enabled';
    }
    
    public function getPortfolioClass($tabName)
    {
        if (Mage::getStoreConfig('afterpay/' . $tabName . '/active')
            && Mage::getStoreConfig('afterpay/' . $tabName . '/mode')
        ) {
            return 'afterpay_portfolio_test';
        } elseif (Mage::getStoreConfig('afterpay/' . $tabName . '/active')) {
            return 'afterpay_portfolio_live';
        } else {
            return 'afterpay_portfolio_disabled';
        }
    }
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }
}