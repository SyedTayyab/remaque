<?php

class Buybox_GiftCard_Model_Api_Nvp extends Buybox_GiftCard_Model_Api_Abstract {
	const SET_EXPRESS_CHECKOUT = 'SetExpressCheckout';
	const DO_EXPRESS_CHECKOUT_PAYMENT = 'DoExpressCheckoutPayment';
	const SANDBOX_SUB_DOMAIN = Buybox_GiftCard_Model_Config::SANDBOX_SUB_DOMAIN;

	protected $_globalMap = array(
		// each call
		'VERSION' => 'version',
		'USER' => 'api_username',
		'PWD' => 'api_password',
		'SIGNATURE' => 'api_signature',
		// commands
		'PAYMENTACTION' => 'payment_action',
		'RETURNURL' => 'return_url',
		'CANCELURL' => 'cancel_url',
		'INVNUM' => 'inv_num',
		'TOKEN' => 'token',
		'CORRELATIONID' => 'correlation_id',
		// transaction info
		'TRANSACTIONID' => 'transaction_id',
		'AMT' => 'amount',
		// payment/billing info
		'CURRENCYCODE' => 'currency_code',
		'PAYMENTSTATUS' => 'payment_status',
		'PENDINGREASON' => 'pending_reason',
		'PAYERID' => 'payer_id',
		'EMAIL' => 'email',
		'SHIPPINGAMT' => 'shipping_amount',
		'TAXAMT' => 'tax_amount',
		'SHIPDISCAMT' => 'shipping_discount_amount',
	);
	protected $_exportToRequestFilters = array(
		'AMT' => '_filterAmount',
		'SHIPPINGAMT' => '_filterAmount',
		'TAXAMT' => '_filterAmount',
		'SHIPDISCAMT' => '_filterAmount',
	);
	protected $_importFromRequestFilters = array(
		'PAYMENTSTATUS' => '_filterPaymentStatusFromNvpToInfo',
	);
	protected $_eachCallRequest = array('VERSION', 'USER', 'PWD', 'SIGNATURE');
	protected $_setExpressCheckoutRequest = array(
		'PAYMENTACTION', 'AMT', 'CURRENCYCODE', 'RETURNURL', 'CANCELURL', 'INVNUM',
	);
	protected $_setExpressCheckoutResponse = array('TOKEN');
	protected $_doExpressCheckoutPaymentRequest = array(
		'TOKEN', 'PAYERID', 'PAYMENTACTION', 'AMT', 'CURRENCYCODE',
	);
	protected $_doExpressCheckoutPaymentResponse = array(
		'TRANSACTIONID', 'AMT', 'PAYMENTSTATUS', 'PENDINGREASON',
	);
	protected $_shippingAddressMap = array(
		'SHIPTOCOUNTRYCODE' => 'country_id',
		'SHIPTOSTATE' => 'region',
		'SHIPTOCITY' => 'city',
		'SHIPTOSTREET' => 'street',
		'SHIPTOZIP' => 'postcode',
		'SHIPTOPHONENUM' => 'telephone',
					// 'SHIPTONAME' will be treated manually in address import/export methods
	);
	protected $_paymentInformationResponse = array(
		'PAYERID', 'CORRELATIONID',
		'PAYMENTSTATUS', 'PENDINGREASON', 'EMAIL'
	);
	protected $_lineItemExportTotals = array(
		'subtotal' => 'ITEMAMT',
		'shipping' => 'SHIPPINGAMT',
		'tax' => 'TAXAMT',
		'shipping_discount' => 'SHIPDISCAMT',
	);
	protected $_lineItemExportItemsFormat = array(
		'id' => 'L_NUMBER%d',
		'name' => 'L_NAME%d',
		'qty' => 'L_QTY%d',
		'amount' => 'L_AMT%d',
	);
	protected $_callWarnings = array();
	protected $_callErrors = array();
	protected $_rawResponseNeeded = false;

	public function getApiEndpoint() {
		return sprintf('https://%s.buybox.net/secure/express-checkout/nvp.php', $this->_config->sandboxFlag ? self::SANDBOX_SUB_DOMAIN : 'www2');
	}

	public function getVersion() {
		return '60.0';
	}

	public function callSetExpressCheckout() {
		$request = $this->_exportToRequest($this->_setExpressCheckoutRequest);
		$this->_exportLineItems($request);

		$address = $this->getAddress();
		if ($address) {
			$request = $this->_importAddress($address, $request);
		}

		$response = $this->call(self::SET_EXPRESS_CHECKOUT, $request);
		$this->_importFromResponse($this->_setExpressCheckoutResponse, $response);
	}

	public function callDoExpressCheckoutPayment() {
		$request = $this->_exportToRequest($this->_doExpressCheckoutPaymentRequest);
		$this->_exportLineItems($request);

		$response = $this->call(self::DO_EXPRESS_CHECKOUT_PAYMENT, $request);
		$this->_importFromResponse($this->_paymentInformationResponse, $response);
		$this->_importFromResponse($this->_doExpressCheckoutPaymentResponse, $response);
	}

	protected function _addMethodToRequest($methodName, $request) {
		$request['METHOD'] = $methodName;
		return $request;
	}

	public function call($methodName, array $request) {
		$request = $this->_addMethodToRequest($methodName, $request);
		$request = $this->_exportToRequest($this->_eachCallRequest, $request);

		/*
		  $debugData = array('url' => $this->getApiEndpoint(), $methodName => $request);
		  Mage::getModel('core/log_adapter', 'payment_' . $this->_config->getMethodCode() . '.log')
		  ->log($debugData);
		  throw new Exception('end');
		 */

		try {
			$http = new Varien_Http_Adapter_Curl();
			$config = array('timeout' => 30);
			$http->setConfig($config);
			$http->write(Zend_Http_Client::POST, $this->getApiEndpoint(), '1.1', array(), $this->_buildQuery($request));
			$response = $http->read();
		} catch (Exception $e) {
			throw $e;
		}

		$http->close();

		$response = preg_split('/^\r?$/m', $response, 2);
		$response = trim($response[1]);
		$response = $this->_deformatNVP($response);

		// handle transport error
		if ($http->getErrno()) {
			Mage::logException(new Exception(
											sprintf('Buybox CURL connection error #%s: %s', $http->getErrno(), $http->getError())
			));
			Mage::throwException(Mage::helper('gift_card')->__('Unable to communicate with the Buybox gateway.'));
		}

		$this->_callErrors = array();
		if ($this->_isCallSuccessful($response)) {
			if ($this->_rawResponseNeeded) {
				$this->setRawSuccessResponseData($response);
			}
			return $response;
		}
		$this->_handleCallErrors($response);
		return $response;
	}

	public function setRawResponseNeeded($flag) {
		$this->_rawResponseNeeded = $flag;
		return $this;
	}

	protected function _handleCallErrors($response) {
		$errors = array();
		for ($i = 0; isset($response["L_ERRORCODE{$i}"]); $i++) {
			$longMessage = isset($response["L_LONGMESSAGE{$i}"]) ? preg_replace('/\.$/', '', $response["L_LONGMESSAGE{$i}"]) : '';
			$shortMessage = preg_replace('/\.$/', '', $response["L_SHORTMESSAGE{$i}"]);
			$errors[] = $longMessage ? sprintf('%s (#%s: %s).', $longMessage, $response["L_ERRORCODE{$i}"], $shortMessage) : sprintf('#%s: %s.', $response["L_ERRORCODE{$i}"], $shortMessage);
			$this->_callErrors[] = $response["L_ERRORCODE{$i}"];
		}
		if ($errors) {
			$errors = implode(' ', $errors);
			$e = Mage::exception('Mage_Core', sprintf('Buybox gateway errors: %s Correlation ID: %s. Version: %s.', $errors, isset($response['CORRELATIONID']) ? $response['CORRELATIONID'] : '', isset($response['VERSION']) ? $response['VERSION'] : ''
											));
			Mage::logException($e);
			$e->setMessage(Mage::helper('gift_card')->__('Buybox gateway has rejected request. %s', $errors));
			throw $e;
		}
	}

	protected function _isCallSuccessful($response) {
		$ack = strtoupper($response['ACK']);
		$this->_callWarnings = array();
		if ($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
			// collect warnings
			if ($ack == 'SUCCESSWITHWARNING') {
				for ($i = 0; isset($response["L_ERRORCODE{$i}"]); $i++) {
					$this->_callWarnings[] = $response["L_ERRORCODE{$i}"];
				}
			}
			return true;
		}
		return false;
	}

	protected function _deformatNVP($nvpstr) {
		$intial = 0;
		$nvpArray = array();

		$nvpstr = strpos($nvpstr, "\r\n\r\n") !== false ? substr($nvpstr, strpos($nvpstr, "\r\n\r\n") + 4) : $nvpstr;

		while (strlen($nvpstr)) {
			//postion of Key
			$keypos = strpos($nvpstr, '=');
			//position of value
			$valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);

			/* getting the Key and Value values and storing in a Associative Array */
			$keyval = substr($nvpstr, $intial, $keypos);
			$valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] = urldecode($valval);
			$nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
		}
		return $nvpArray;
	}

	protected function _importAddress(Varien_Object $address, array $to) {
		$to = Varien_Object_Mapper::accumulateByMap($address, $to, array_flip($this->_shippingAddressMap));
		if ($regionCode = $this->_lookupRegionCodeFromAddress($address)) {
			$to['SHIPTOSTATE'] = $regionCode;
		}
		$to['SHIPTONAME'] = $address->getName();
		return $to;
	}

	protected function _filterToBool($value) {
		if ('false' === $value || '0' === $value) {
			return false;
		}
		elseif ('true' === $value || '1' === $value) {
			return true;
		}
		return $value;
	}

	protected function _filterPaymentStatusFromNvpToInfo($value) {
		switch ($value) {
			case 'Completed': return Buybox_GiftCard_Model_Info::PAYMENTSTATUS_COMPLETED;
			case 'Failed': return Buybox_GiftCard_Model_Info::PAYMENTSTATUS_FAILED;
			case 'Refunded': return Buybox_GiftCard_Model_Info::PAYMENTSTATUS_REFUNDED;
			case 'Partially-Refunded': return Buybox_GiftCard_Model_Info::PAYMENTSTATUS_REFUNDEDPART;
		}
	}

}
