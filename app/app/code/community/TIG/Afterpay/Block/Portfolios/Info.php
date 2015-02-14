<?php
class TIG_Afterpay_Block_Portfolios_Info extends Mage_Payment_Block_Info
{
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('TIG/Afterpay/portfolios/info.phtml');
    }
}