<?php

class Buybox_GiftCard_PaymentController extends Mage_Core_Controller_Front_Action
{
    protected $_configType = 'gift_card/config';

    protected $_configMethod = Buybox_GiftCard_Model_Config::METHOD_EXPRESS;

    protected $_checkoutType = 'gift_card/checkout';
    
    protected $_checkout = null;

    protected $_config = null;

    protected $_quote = false;

    protected function _construct()
    {
        parent::_construct();
        $this->_config = Mage::getModel($this->_configType, array($this->_configMethod));
    }

    public function startAction()
    {
        try {
            $this->_initCheckout();

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($customer && $customer->getId()) {
                $this->_checkout->setCustomer($customer);
            }

            $token = $this->_checkout->start(Mage::getUrl('*/*/return'), Mage::getUrl('*/*/cancel'));
            if ($token && $url = $this->_checkout->getRedirectUrl()) {
                $this->_initToken($token);
                $this->getResponse()->setRedirect($url);
                return;
            }
        }
        catch (Mage_Core_Exception $e) {
            $this->_getCheckoutSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getCheckoutSession()->addError($this->__('Unable to start Buybox Checkout.'));
            Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
    }


    public function cancelAction()
    {
        try {
            $this->_initToken(false);
            $this->_redirect('checkout/onepage');
            return;
            
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckoutSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getCheckoutSession()->addError($this->__('Unable to cancel Checkout.'));
            Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
    }

    public function returnAction()
    {
        try {
        	/* Hack fix correct Buybox gateway bug */
        	$request = $this->getRequest();
        	
        	$payerid = $request->getParam('PayerID');
        	
            if (!$payerid) {
            	$uri = $request->getRequestUri();
            	if (preg_match('/^.*&token=(.*)&PayerID=(.*)$/', $uri, $matches)) {
            		$request->setParam('token', $matches[1]);
            		$request->setParam('PayerID', $matches[2]);
            	}
            }
            	
        	$this->_initCheckout();
            
            $this->_checkout->returnFromBuybox($this->_initToken(), $this->getRequest()->getParam('PayerID'));
            
            if ($this->_config->isCommit()) {
            	$this->getResponse()->setRedirect($this->_checkout->getRedirectUrl());
            }
            else {
            	$this->_getSession()->setActiveStep('review');
            	$this->_redirect('checkout/onepage');
            }
            return;
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError($this->__('Unable to process Buybox return.'));
            Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
    }

    private function _initCheckout()
    {
        $quote = $this->_getQuote();
        if (!$quote->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
            Mage::throwException(Mage::helper('gift_card')->__('Unable to initialize Buybox Checkout.'));
        }
        $this->_checkout = Mage::getSingleton($this->_checkoutType, array(
            'config' => $this->_config,
            'quote'  => $quote,
        ));
    }

    protected function _initToken($setToken = null)
    {
        if (null !== $setToken) {
            if (false === $setToken) {
                if (!$this->_getSession()->getBuyboxCheckoutToken()) { // security measure for avoid unsetting token twice
                    Mage::throwException($this->__('Buybox Checkout Token does not exist.'));
                }
                $this->_getSession()->unsBuyboxCheckoutToken();
            } else {
                $this->_getSession()->setBuyboxCheckoutToken($setToken);
            }
            return $this;
        }
        if ($setToken = $this->getRequest()->getParam('token')) {
            if ($setToken !== $this->_getSession()->getBuyboxCheckoutToken()) {
                Mage::throwException($this->__('Wrong Buybox Checkout Token specified.'));
            }
        } else {
            $setToken = $this->_getSession()->getBuyboxCheckoutToken();
        }
        return $setToken;
    }

    private function _getSession()
    {
        return Mage::getSingleton('gift_card/session');
    }

    private function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    private function _getQuote()
    {
        if (!$this->_quote) {
        	if ($this->_config->isCommit()) {
        		$this->_quote = Mage::getModel('sales/quote')->load($this->_getCheckoutSession()->getLastQuoteId());
        	}
        	else {
            	$this->_quote = $this->_getCheckoutSession()->getQuote();
        	}
        }
        return $this->_quote;
    }

}
