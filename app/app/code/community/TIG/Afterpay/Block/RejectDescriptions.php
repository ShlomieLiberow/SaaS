<?php 
class TIG_Afterpay_Block_RejectDescriptions extends Mage_Core_Block_Abstract
{
    protected $_rejectTemplate;
    
    public $template1 = "Overig";
    
    public $template29 = "Te hoog eerste orderbedrag";
    
    public $template30 = "Maximale aantal openstaande betalingen bereikt";
    
    public $template36 = "Ongeldig emailadres";
    
    public $template40 = "Leeftijd onder 18 jaar";
    
    public $template42 = "Adres onjuist";
    
    public $template71 = "Onjuist KVK nummer en/of tenaamstelling";
    
    public function setRejectDescription($id = 1) {
        $templateId = 'template' . $id;
        
        if (isset($this->$templateId)) {
            $this->_rejectTemplate = $this->$templateId;
        } else {
            $this->_rejectTemplate = $this->template1;
        }
        
        return $this;
    }
    
    protected function _toHtml()
    {
        return 'Rejected by AfterPay: ' . $this->_rejectTemplate;
    }
}