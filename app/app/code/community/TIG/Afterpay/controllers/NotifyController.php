<?php
class TIG_Afterpay_NotifyController extends Mage_Core_Controller_Front_Action
{
	public function pushAction()
	{
	    Mage::log('called', null, 'TIG_AP_PUSH.log', true);
	    Mage::log($this->getRequest()->getPost(), null, 'TIG_AP_PUSH.log', true);
		
		$jsonDataStream = file_get_contents("php://input");
        $pushMessage = $this->getRequest()->getPost();
        $order = Mage::getModel('sales/order')->loadByIncrementId($pushMessage['orderReference']);
		
		// merchantId + portefeuilleId + password + orderReference + statusCode
		$pushpassword = Mage::getStoreConfig('afterpay/afterpay_general/push_password', Mage::app()->getStore()->getId());
		Mage::log($pushpassword, null, 'TIG_AP_PUSH.log', true);
		$hash = $pushMessage['merchantId'] . $pushMessage['portefeuilleId'] . $pushpassword . $pushMessage['orderReference'] . $pushMessage['statusCode'];
		$checksum = md5($hash);
		if ($checksum != $pushMessage['signature']) {
			Mage::log('Error in push, checksum not correct.' , null, 'TIG_AP_PUSH.log', true);
			return;
		}
		
		switch ($pushMessage['statusCode']) {
            case 'A':
                $this->acceptOrder($order);
                break;
            case 'W':
                $this->cancelOrder($order);
                break;
            case 'V':
                $this->cancelOrder($order);
                break;
            case 'P':
                $this->updateStatusOrder($order,$pushMessage['subStatusCode']);
                break;
            default :
                Mage::log('Error in status of the push message, result is the message is not handled', null, 'TIG_AP_PUSH.log', true);
                break;
        }
    }

    protected function cancelOrder($order){
		$response = Mage::getModel('afterpay/response_abstract');
		$response->setCurrentOrder($order);
		$response->setRejectMessage('Reject');
		$response->setRejectDescription('Rejected by AfterPay');
		$response->_rejectFinal();
    }

    protected function acceptOrder($order){
        
		$response = Mage::getModel('afterpay/response_abstract');
		$response->setCurrentOrder($order);
		$response->_updateAndInvoice();
    }

    protected function updateStatusOrder($order,$message){
        Mage::log('Order Payment is still Pending but there is a update on the status for Order :'.$order->getid().' with sub status :'.$message, null, 'TIG_AP_PUSH.log', true);
        $order->addStatusHistoryComment($message);
        $order->save();
    }

    public function indexAction()
    {
        Mage::log('called index', null, 'TIG_AP_PUSH.log', true);
        Mage::log($this->getRequest()->getPost(), null, 'TIG_AP_PUSH.log', true);
    }
}