<?php

class Buybox_GiftCard_Block_Kadowin_Head extends Mage_Core_Block_Template {
	protected $_shouldRender = true;

	protected function _beforeToHtml() {
		$result = parent::_beforeToHtml();
		$config = Mage::getModel('gift_card/config');

		if (!$config->isFrontEnabled()) {
			$this->_shouldRender = false;
			return $result;
		}

		// Front
		$this->setScriptUrl($config->getScriptUrl());
		$this->setDomain($config->front_domain);
		return $result;
	}

	protected function _toHtml() {
		if (!$this->_shouldRender) {
			return '';
		}

		return parent::_toHtml();
	}

}
