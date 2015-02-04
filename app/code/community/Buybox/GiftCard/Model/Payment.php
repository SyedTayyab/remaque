<?php

class Buybox_GiftCard_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = Buybox_GiftCard_Model_Config::METHOD_EXPRESS;
    protected $_formBlockType = 'gift_card/payment_form';
    protected $_infoBlockType = 'gift_card/payment_info';

    protected $_isGateway                   = false;
    protected $_canAuthorize                = false;
    protected $_canCapture                  = true;
    protected $_canCapturePartial           = false;
    protected $_canRefund                   = false;
    protected $_canRefundInvoicePartial     = false;
    protected $_canVoid                     = false;
    protected $_canUseInternal              = false;
    protected $_canUseCheckout              = true;
    protected $_canUseForMultishipping      = false;
    protected $_isInitializeNeeded          = false;
    protected $_canFetchTransactionInfo     = false;
    protected $_canReviewPayment            = false;
    protected $_canCreateBillingAgreement   = false;
    protected $_canManageRecurringProfiles  = false;
    
    protected $_config = null;

    protected $_api = null;

    protected $_infoInstance = null;

    protected $_apiType = 'gift_card/api_nvp';

    protected $_configType = 'gift_card/config';
    
    protected $_infoType = 'gift_card/info';
    
    
    public function __construct($params = array())
    {
        $this->_setMethod($this->_code);   
    }

    public function setStore($store)
    {
        $this->setData('store', $store);
        if (null === $store) {
            $store = Mage::app()->getStore()->getId();
        }
        $this->_getConfig()->setStoreId(is_object($store) ? $store->getId() : $store);
        return $this;
    }

    public function getConfigPaymentAction()
    {
        return $this->_getConfig()->getPaymentAction();
    }

    public function isAvailable($quote = null)
    {
        if ($this->_getConfig()->isMethodAvailable() && parent::isAvailable($quote)) {
            return true;
        }
        return false;
    }

    public function getConfigData($field, $storeId = null)
    {
        return $this->_getConfig()->$field;
    }
    
 	public function isInitializeNeeded()
    {
    	/* avoid capturing during saveOrder if we are in commit mode */
    	if ($this->_getConfig()->isCommit()) {
    		return true;	
    	}
    	return false;
    }
    
    public function getCheckoutRedirectUrl()
    {
    	if (Mage::getSingleton('gift_card/session')->getActiveStep() === 'review') {
    		Mage::getSingleton('gift_card/session')->unsActiveStep();
    		return '';	
    	}
    	if ($this->_getConfig()->isCommit()) {
    		return '';	
    	}
    	/* continue */
        return Mage::getUrl('gift_card/payment/start');
    }
    
    public function getOrderPlaceRedirectUrl()
    {
    	if (!$this->_getConfig()->isCommit()) {
    		return '';
    	}
    	/* commit */
        return Mage::getUrl('gift_card/payment/start');	
    }
    
    public function assignData($data)
    {
        $result = parent::assignData($data);
        return $result;
    }
    
  	public function capture(Varien_Object $payment, $amount)
    {
        $this->_placeOrder($payment, $amount);
        return $this;
    }
    
    protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount)
    {
        $order = $payment->getOrder();

        // prepare api call
        $token = $payment->getAdditionalInformation(Buybox_GiftCard_Model_Checkout::PAYMENT_INFO_TRANSPORT_TOKEN);
        $api = $this->_getApi()
            ->setToken($token)
            ->setPayerId($payment->getAdditionalInformation(Buybox_GiftCard_Model_Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID))
            ->setAmount($amount)
            ->setPaymentAction($this->_getConfig()->paymentAction)
            ->setInvNum($order->getIncrementId())
            ->setCurrencyCode($order->getBaseCurrencyCode())
        ;

        // add line items
        if ($this->_getConfig()->lineItemsEnabled) {
            list($items, $totals) = Mage::helper('gift_card')->prepareLineItems($order);
            if (Mage::helper('gift_card')->areCartLineItemsValid($items, $totals, $amount)) {
                $api->setLineItems($items)->setLineItemTotals($totals);
            }
        }

        // call api and get details from it
        $api->callDoExpressCheckoutPayment();
        $this->_importToPayment($api, $payment);
        return $this;
    }

    protected function _importToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getTransactionId())->setIsTransactionClosed(0);
        $this->_importPaymentInfo($api, $payment);
    }
    
 	private function _setMethod($code, $storeId = null)
    {
        if (null === $this->_config) {
            $params = array($code);
            if (null !== $storeId) {
                $params[] = $storeId;
            }
            $this->_config = Mage::getModel($this->_configType, $params);
        } else {
            $this->_config->setMethod($code);
            if (null !== $storeId) {
                $this->_config->setStoreId($storeId);
            }
        }
        return $this;
    }

    protected function _getConfig()
    {
        return $this->_config;
    }

    protected function _getApi()
    {
        if (null === $this->_api) {
            $this->_api = Mage::getModel($this->_apiType);
        }
        $this->_api->setConfigObject($this->_config);
        return $this->_api;
    }

    
    protected function _getInfo()
    {
        if (null === $this->_infoInstance) {
            $this->_infoInstance = Mage::getModel($this->_infoType);
        }
        return $this->_infoInstance;
    }

    protected function _importPaymentInfo(Varien_Object $from, Mage_Payment_Model_Info $to)
    {
        // update PayPal-specific payment information in the payment object
        $this->_getInfo()->importToPayment($from, $to);

        // give generic info about transaction state
        if ($this->_getInfo()->isPaymentSuccessful($to)) {
            $to->setIsTransactionApproved(true);
        } elseif ($this->_getInfo()->isPaymentFailed($to)) {
            $to->setIsTransactionDenied(true);
        }

        return $this;
    }
}
