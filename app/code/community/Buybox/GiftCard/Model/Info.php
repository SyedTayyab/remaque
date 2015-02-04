<?php

class Buybox_GiftCard_Model_Info
{
    const PAYER_ID       = 'payer_id';
    const PAYER_EMAIL    = 'email';
    const CORRELATION_ID = 'correlation_id';

    const PAYMENT_STATUS = 'payment_status';
    const PENDING_REASON = 'pending_reason';
    
    const PAYMENT_STATUS_GLOBAL = 'gift_card_payment_status';
    const PENDING_REASON_GLOBAL = 'gift_card_pending_reason';

    protected $_paymentMap = array(
        self::PAYER_ID       => 'gift_card_payer_id',
        self::PAYER_EMAIL    => 'gift_card_payer_email',
        self::CORRELATION_ID => 'gift_card_correlation_id',
    );

    protected $_systemMap = array(
        self::PAYMENT_STATUS => self::PAYMENT_STATUS_GLOBAL,
        self::PENDING_REASON => self::PENDING_REASON_GLOBAL,
    );

    const PAYMENTSTATUS_COMPLETED    = 'completed';
    const PAYMENTSTATUS_FAILED       = 'failed';
    const PAYMENTSTATUS_REFUNDED     = 'refunded';
    const PAYMENTSTATUS_REFUNDEDPART = 'partially_refunded';

    protected $_paymentPublicMap = array(
        'gift_card_payer_email',
    );

    protected $_paymentMapFull = array();

 
    public function getPaymentInfo(Mage_Payment_Model_Info $payment, $labelValuesOnly = false)
    {
        $result = $this->_getFullInfo(array_values($this->_paymentMap), $payment, $labelValuesOnly);

        // add last_trans_id
        $label = Mage::helper('gift_card')->__('Last Transaction ID');
        $value = $payment->getLastTransId();
        if ($labelValuesOnly) {
            $result[$label] = $value;
        } else {
            $result['last_trans_id'] = array('label' => $label, 'value' => $value);
        }

        return $result;
    }

   
    public function getPublicPaymentInfo(Mage_Payment_Model_Info $payment, $labelValuesOnly = false)
    {
        return $this->_getFullInfo($this->_paymentPublicMap, $payment, $labelValuesOnly);
    }

  
    public function importToPayment($from, Mage_Payment_Model_Info $payment)
    {
        $fullMap = array_merge($this->_paymentMap, $this->_systemMap);
        if (is_object($from)) {
            $from = array($from, 'getDataUsingMethod');
        }
        Varien_Object_Mapper::accumulateByMap($from, array($payment, 'setAdditionalInformation'), $fullMap);
    }

    public function &exportFromPayment(Mage_Payment_Model_Info $payment, $to, array $map = null)
    {
        $fullMap = array_merge($this->_paymentMap, $this->_systemMap);
        Varien_Object_Mapper::accumulateByMap(array($payment, 'getAdditionalInformation'), $to,
            $map ? $map : array_flip($fullMap)
        );
        return $to;
    }

    public static function isPaymentCompleted(Mage_Payment_Model_Info $payment)
    {
        $paymentStatus = $payment->getAdditionalInformation(self::PAYMENT_STATUS_GLOBAL);
        return self::PAYMENTSTATUS_COMPLETED === $paymentStatus;
    }

    public static function isPaymentSuccessful(Mage_Payment_Model_Info $payment)
    {
        $paymentStatus = $payment->getAdditionalInformation(self::PAYMENT_STATUS_GLOBAL);
        if (in_array($paymentStatus, array(
            self::PAYMENTSTATUS_COMPLETED, self::PAYMENTSTATUS_REFUNDED, self::PAYMENTSTATUS_REFUNDEDPART,
        ))) {
            return true;
        }
        return false;
     }

    public static function isPaymentFailed(Mage_Payment_Model_Info $payment)
    {
        $paymentStatus = $payment->getAdditionalInformation(self::PAYMENT_STATUS_GLOBAL);
        return in_array($paymentStatus, array(
            self::PAYMENTSTATUS_FAILED,
        ));
    }

    public static function explainPendingReason($code)
    {
        switch ($code) {
            case 'none':
            default:
                return Mage::helper('gift_card')->__('None');
        }
    }

    public static function explainReasonCode($code)
    {
        switch ($code) {
            case 'none':
            default:
                return Mage::helper('gift_card')->__('None');
        }
    }

    protected function _getFullInfo(array $keys, Mage_Payment_Model_Info $payment, $labelValuesOnly)
    {
        $result = array();
        foreach ($keys as $key) {
            if (!isset($this->_paymentMapFull[$key])) {
                $this->_paymentMapFull[$key] = array();
            }
            if (!isset($this->_paymentMapFull[$key]['label'])) {
                if (!$payment->hasAdditionalInformation($key)) {
                    $this->_paymentMapFull[$key]['label'] = false;
                    $this->_paymentMapFull[$key]['value'] = false;
                } else {
                    $value = $payment->getAdditionalInformation($key);
                    $this->_paymentMapFull[$key]['label'] = $this->_getLabel($key);
                    $this->_paymentMapFull[$key]['value'] = $this->_getValue($value, $key);
                }
            }
            if (!empty($this->_paymentMapFull[$key]['value'])) {
                if ($labelValuesOnly) {
                    $result[$this->_paymentMapFull[$key]['label']] = $this->_paymentMapFull[$key]['value'];
                } else {
                    $result[$key] = $this->_paymentMapFull[$key];
                }
            }
        }
        return $result;
    }

    protected function _getLabel($key)
    {
        switch ($key) {
            case 'gift_card_payer_id':
                return Mage::helper('gift_card')->__('Payer ID');
            case 'gift_card_payer_email':
                return Mage::helper('gift_card')->__('Payer Email');
            case 'gift_card_correlation_id':
                return Mage::helper('gift_card')->__('Last Correlation ID');
        }
        return '';
    }

    protected function _getValue($value, $key)
    {
        $label = '';
        return sprintf('#%s%s', $value, $value == $label ? '' : ': ' . $label);
    }


}
