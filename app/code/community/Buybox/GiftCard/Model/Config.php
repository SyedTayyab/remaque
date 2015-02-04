<?php

class Buybox_GiftCard_Model_Config {
	const LINKER_VERSION = '1.2';
	const METHOD_EXPRESS = 'gift_card_payment';
	const PAYMENT_ACTION_SALE = 'Sale';
	const PAYMENT_USERACTION_CONTINUE = 'continue';
	const PAYMENT_USERACTION_COMMIT = 'commit';
	const SANDBOX_SUB_DOMAIN = 'sandbox';

	protected $_methodCode = null;
	protected $_storeId = null;

	public function __construct($params = array()) {
		if ($params) {
			$method = array_shift($params);
			$this->setMethod($method);
			if ($params) {
				$storeId = array_shift($params);
				$this->setStoreId($storeId);
			}
		}
	}

	public function setMethod($method) {
		if ($method instanceof Mage_Payment_Model_Method_Abstract) {
			$this->_methodCode = $method->getCode();
		}
		elseif (is_string($method)) {
			$this->_methodCode = $method;
		}
		return $this;
	}

	public function getMethodCode() {
		return $this->_methodCode;
	}

	public function setStoreId($storeId) {
		$this->_storeId = (int) $storeId;
		return $this;
	}

	public function isMethodActive($method) {
		return !!Mage::getStoreConfigFlag("payment/{$method}/active", $this->_storeId);
	}

	public function isMethodAvailable($methodCode = null) {
		if ($methodCode === null) {
			$methodCode = $this->getMethodCode();
		}
		return !!$this->isMethodActive($methodCode);
	}

	public function __get($key) {
		$underscored = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $key));
		$value = Mage::getStoreConfig($this->_getSpecificConfigPath($underscored), $this->_storeId);
		$value = $this->_prepareValue($underscored, $value);
		$this->$key = $value;
		$this->$underscored = $value;
		return $value;
	}

	protected function _prepareValue($key, $value) {
		switch ($key) {
			case 'front_api_key':
			case 'front_client_id':
			case 'api_username':
			case 'api_password':
			case 'api_signature':
				return Mage::helper('core')->decrypt($value);
		}
		return $value;
	}

	public function isCommit() {
		return ($this->user_action == self::PAYMENT_USERACTION_COMMIT);
	}

	public function getCheckoutStartUrl($token) {
		$user_action = $this->user_action;
		return $this->getBuyboxUrl(array(
							'useraction' => $user_action,
							'token' => $token,
							'lang' => Mage::app()->getLocale()->getLocaleCode()
						));
	}

	public function getBuyboxUrl(array $params = array()) {
		return sprintf('https://%s.buybox.net/secure/payment_login.php%s', $this->sandboxFlag ? self::SANDBOX_SUB_DOMAIN : 'www2', $params ? '?' . http_build_query($params) : ''
		);
	}

	public function getScriptUrl() {
		if (!$this->_methodCode) {
			$this->setMethod(self::METHOD_EXPRESS);
		}
		return sprintf('https://%s/linker/%s/buybox-gc-linker.js', $this->frontDomain, self::LINKER_VERSION);
	}

	public function getPaymentActions() {
		return array(
			self::PAYMENT_ACTION_SALE => Mage::helper('gift_card')->__('Sale'),
		);
	}

	public function getUserActions() {
		return array(
			self::PAYMENT_USERACTION_COMMIT => Mage::helper('gift_card')->__('commit'),
			self::PAYMENT_USERACTION_CONTINUE => Mage::helper('gift_card')->__('continue'),
		);
	}

	public function getPaymentAction() {
		switch ($this->paymentAction) {
			case self::PAYMENT_ACTION_SALE:
				return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
		}
	}

	public function isFrontEnabled() {
		return ($this->front_button_enabled
//						&& $this->front_api_key
//						&& $this->front_client_id
						&& $this->front_domain);
	}

	protected function _getSpecificConfigPath($fieldName) {
		$path = $this->_mapPaymentFieldset($fieldName);

		if ($path === null) {
			$path = $this->_mapFrontFieldset($fieldName);
		}
		return $path;
	}

	protected function _mapFrontFieldset($fieldName) {
		switch ($fieldName) {
//			case 'front_api_key':
//			case 'front_client_id':
			case 'front_domain':
//			case 'front_modal_flag':
			case 'front_button_enabled':
			case 'front_button_image':
				return preg_replace('/^front_(.*)$/', 'gift_card/front/$1', $fieldName);
			default:
				return null;
		}
	}

	protected function _mapPaymentFieldset($fieldName) {
		if (!$this->_methodCode) {
			return null;
		}
		switch ($fieldName) {
			case 'active':
			case 'title':
			case 'payment_action':
			case 'user_action':
			case 'sort_order':
			case 'api_username':
			case 'api_password':
			case 'api_signature':
			case 'sandbox_flag':
				return "payment/{$this->_methodCode}/{$fieldName}";
			default:
				return null;
		}
	}

}

