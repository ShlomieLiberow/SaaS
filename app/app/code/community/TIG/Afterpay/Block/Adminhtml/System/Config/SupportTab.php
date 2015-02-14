<?php 
class TIG_Afterpay_Block_Adminhtml_System_Config_SupportTab
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'TIG/Afterpay/system/config/supportTab.phtml';

    public $afterPayCheckListUrl = '';
    public $customerServiceEmail = '';
    public $techSupportEmail     = '';
    public $anchorClose          = '</a>';

    protected function _prepareLayout()
    {
        //placed here, instead of in layout.xml to make sure it is only loaded for Buckaroo's section
        $this->getLayout()->getBlock('head')->addCss('css/TIG/Afterpay/supportTab.css');
        return parent::_prepareLayout();
    }
    
    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }
}