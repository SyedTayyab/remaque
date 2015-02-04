<?php

class Buybox_GiftCard_Model_Checkout
{
    const PAYMENT_INFO_TRANSPORT_TOKEN    = 'gift_card_checkout_token';
    const PAYMENT_INFO_TRANSPORT_PAYER_ID = 'gift_card_checkout_payer_id';
   
    protected $_quote = null;

    protected $_config = null;

    protected $_api = null;

    protected $_apiType = 'gift_card/api_nvp';

    protected $_methodType = Buybox_GiftCard_Model_Config::METHOD_EXPRESS;

    protected $_redirectUrl = '';

    protected $_customerId = null;

    protected $_order = null;

    public function __construct($params = array())
    {
        if (isset($params['quote']) && $params['quote'] instanceof Mage_Sales_Model_Quote) {
            $this->_quote = $params['quote'];
        } else {
            throw new Exception('Quote instance is required.');
        }
        if (isset($params['config']) && $params['config'] instanceof Buybox_GiftCard_Model_Config) {
            $this->_config = $params['config'];
        } else {
            throw new Exception('Config instance is required.');
        }
    }

    public function setCustomer($customer)
    {
        $this->_quote->assignCustomer($customer);
        $this->_customerId = $customer->getId();
        return $this;
    }

    public function start($returnUrl, $cancelUrl)
    {
    	if (!$this->_config->isCommit()) {
    		$this->_quote->collectTotals();
        	$this->_quote->reserveOrderId()->save();
        	$invNum = $this->_quote->getReservedOrderId();
    	}
    	else {
    		$invNum = $this->getOnepage()->getCheckout()->getLastOrderId();
    	}
        // prepare API
        $this->_getApi();
        $this->_api->setAmount($this->_quote->getBaseGrandTotal())
            ->setCurrencyCode($this->_quote->getBaseCurrencyCode())
            ->setInvNum($invNum)
            ->setReturnUrl($returnUrl)
            ->setCancelUrl($cancelUrl)
            ->setPaymentAction($this->_config->paymentAction)
        ;
    	
        list($items, $totals) = Mage::helper('gift_card')->prepareLineItems($this->_quote);
        if (Mage::helper('gift_card')->areCartLineItemsValid($items, $totals, $this->_quote->getBaseGrandTotal())) {
        	$this->_api->setLineItems($items)->setLineItemTotals($totals);
        }
        
     	if (!$this->_quote->getIsVirtual()) {
            $address = $this->_quote->getShippingAddress();
            if (true === $address->validate()) {
                $this->_api->setAddress($address);
            }
        }
        
        // call API and redirect with token
        $this->_api->callSetExpressCheckout();
        $token = $this->_api->getToken();
        $this->_redirectUrl = $this->_config->getCheckoutStartUrl($token);

        return $token;
    }
    
    public function returnFromBuybox($token, $payerid)
    {
    	if (!$this->_config->isCommit()) {
    		// import payment info
        	$payment = $this->_quote->getPayment();
        	$payment->setMethod($this->_methodType);
        	$payment->setAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_PAYER_ID, $payerid)
	            ->setAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_TOKEN, $token)
    	    ;
        	$payment->save();
    	}
    	else {
    		$order = Mage::getModel('sales/order')->load($this->getOnepage()->getCheckout()->getLastOrderId());
    		
    		$payment = $order->getPayment(); 
    		$payment->setAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_PAYER_ID, $payerid)
	            ->setAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_TOKEN, $token)
    	    ;
        	$payment->save();
    		$payment->setAmountAuthorized($order->getTotalDue());
            $payment->setBaseAmountAuthorized($order->getBaseTotalDue());
            $payment->capture(null);
    		
    		$order->sendNewOrderEmail();
    		$order->save();
    		$this->_redirectUrl = Mage::getUrl('checkout/onepage/success');
    		
    	}
    }
    
    public function getRedirectUrl()
    {
        return $this->_redirectUrl;
    }

    public function getOrder()
    {
        return $this->_order;
    }

    protected function _getApi()
    {
        if (null === $this->_api) {
            $this->_api = Mage::getModel($this->_apiType)->setConfigObject($this->_config);
        }
        return $this->_api;
    }

 	public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
}
