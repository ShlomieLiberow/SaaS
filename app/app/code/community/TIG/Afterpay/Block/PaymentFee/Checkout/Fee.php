<?php
class TIG_Afterpay_Block_PaymentFee_Checkout_Fee extends Mage_Checkout_Block_Total_Default
{
    protected $_template = 'TIG/Afterpay/paymentFee/checkout/fee.phtml';

    /**
     * Get COD fee exclude tax
     *
     * @return float
     */
    public function getPaymentFee()
    {
        $paymentFee = 0;
        foreach ($this->getTotal()->getAddress()->getQuote()->getAllShippingAddresses() as $address){
            $paymentFee += $address->getPaymentFee();
        }
        return $paymentFee;
    }

    /**
     * Get COD fee including tax
     *
     * @return float
     */
    public function getPaymentFeeInclTax()
    {
        $paymentFee = 0;
        foreach ($this->getTotal()->getAddress()->getQuote()->getAllShippingAddresses() as $address){
            $paymentFee += $address->getPaymentFee() + $address->getPaymentFeeTax();
        }
        return $paymentFee;
    }

}
