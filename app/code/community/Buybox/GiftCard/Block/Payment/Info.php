<?php

class Buybox_GiftCard_Block_Payment_Info extends Mage_Payment_Block_Info
{

    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $payment = $this->getInfo();
        $payment_info = Mage::getModel('gift_card/info');
        if (!$this->getIsSecureMode()) {
            $info = $payment_info->getPaymentInfo($payment, true);
        } else {
            $info = $payment_info->getPublicPaymentInfo($payment, true);
        }
        return $transport->addData($info);
    }
}
