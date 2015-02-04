<?php

abstract class Buybox_GiftCard_Model_Api_Abstract extends Varien_Object
{
    
    protected $_config = null;

  
    protected $_globalMap = array();

 
    protected $_exportToRequestFilters = array();

    
    protected $_importFromRequestFilters = array();

  
    protected $_lineItemExportTotals = array();
    protected $_lineItemExportItemsFormat = array();
    protected $_lineItemExportItemsFilters = array();


    public function getApiUsername()
    {
        return $this->_config->api_username;
    }

    public function getApiPassword()
    {
        return $this->_config->api_password;
    }

    public function getApiSignature()
    {
        return $this->_config->api_signature;
    }
    
    public function getPaymentAction()
    {
        return $this->_getDataOrConfig('payment_action');
    }

  
    public function &import($to, array $publicMap = array())
    {
        return Varien_Object_Mapper::accumulateByMap(array($this, 'getDataUsingMethod'), $to, $publicMap);
    }

   
    public function export($from, array $publicMap = array())
    {
        Varien_Object_Mapper::accumulateByMap($from, array($this, 'setDataUsingMethod'), $publicMap);
        return $this;
    }

   
    public function setConfigObject(Buybox_GiftCard_Model_Config $config)
    {
        $this->_config = $config;
        return $this;
    }


   
    protected function &_exportToRequest(array $privateRequestMap, array $request = array())
    {
        $map = array();
        foreach ($privateRequestMap as $key) {
            if (isset($this->_globalMap[$key])) {
                $map[$this->_globalMap[$key]] = $key;
            }
        }
        $result = Varien_Object_Mapper::accumulateByMap(array($this, 'getDataUsingMethod'), $request, $map);
        foreach ($privateRequestMap as $key) {
            if (isset($this->_exportToRequestFilters[$key]) && isset($result[$key])) {
                $callback   = $this->_exportToRequestFilters[$key];
                $privateKey = $result[$key];
                $publicKey  = $map[$this->_globalMap[$key]];
                $result[$key] = call_user_func(array($this, $callback), $privateKey, $publicKey);
            }
        }
        return $result;
    }

   
    protected function _importFromResponse(array $privateResponseMap, array $response)
    {
        $map = array();
        foreach ($privateResponseMap as $key) {
            if (isset($this->_globalMap[$key])) {
                $map[$key] = $this->_globalMap[$key];
            }
            if (isset($response[$key]) && isset($this->_importFromRequestFilters[$key])) {
                $callback = $this->_importFromRequestFilters[$key];
                $response[$key] = call_user_func(array($this, $callback), $response[$key], $key, $map[$key]);
            }
        }
        Varien_Object_Mapper::accumulateByMap($response, array($this, 'setDataUsingMethod'), $map);
    }

   
    protected function _exportLineItems(array &$request, $i = 0)
    {
        $items = $this->getLineItems();
        if (empty($items)) {
            return;
        }
        // line items
        foreach ($items as $item) {
            foreach ($this->_lineItemExportItemsFormat as $publicKey => $privateFormat) {
                $value = $item->getDataUsingMethod($publicKey);
                if (isset($this->_lineItemExportItemsFilters[$publicKey])) {
                    $callback   = $this->_lineItemExportItemsFilters[$publicKey];
                    $value = call_user_func(array($this, $callback), $value);
                }
                if (is_float($value)) {
                    $value = $this->_filterAmount($value);
                }
                $request[sprintf($privateFormat, $i)] = $value;
            }
            $i++;
        }
        // line item totals
        $lineItemTotals = $this->getLineItemTotals();
        if ($lineItemTotals) {
            $request = Varien_Object_Mapper::accumulateByMap($lineItemTotals, $request, $this->_lineItemExportTotals);
            foreach ($this->_lineItemExportTotals as $privateKey) {
                if (array_key_exists($privateKey, $request)) {
                    $request[$privateKey] = $this->_filterAmount($request[$privateKey]);
                } else {
                    Mage::logException(new Exception(sprintf('Missing index "%s" for line item totals.', $privateKey)));
                    Mage::throwException(Mage::helper('gift_card')->__('Unable to calculate cart line item totals.'));
                }
            }
        }
    }

   
    protected function _filterAmount($value)
    {
        return sprintf('%.2F', $value);
    }

   
    protected function _filterBool($value)
    {
        return ($value) ? 'true' : 'false';
    }

 
    protected function _filterInt($value)
    {
        return (int)$value;
    }

    
    protected function _getDataOrConfig($key, $default = null)
    {
        if ($this->hasData($key)) {
            return $this->getData($key);
        }
        return $this->_config->$key ? $this->_config->$key : $default;
    }

    protected function _lookupRegionCodeFromAddress(Varien_Object $address)
    {
        if ($regionId = $address->getData('region_id')) {
            $region = Mage::getModel('directory/region')->load($regionId);
            if ($region->getId()) {
                return $region->getCode();
            }
        }
        return '';
    }

    protected function _buildQuery($request)
    {
        return http_build_query($request);
    }

    protected function _filterQty($value)
    {
        return intval($value);
    }

}
