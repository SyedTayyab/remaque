<?php

class Buybox_GiftCard_Block_Checkout_OnePage extends Mage_Core_Block_Template 
{
	protected $_shouldRender = true;
   
	
	protected function _beforeToHtml()
    {
        $result = parent::_beforeToHtml();
        
       	$step = Mage::getSingleton('gift_card/session')->getActiveStep();
    
       	if ($step !== 'review') {
       		$this->_shouldRender = false;
       	}
        return $result;
    }

    protected function _toHtml()
    {
        if (!$this->_shouldRender) {
            return '';
        }
        return parent::_toHtml();
    }
    
}
