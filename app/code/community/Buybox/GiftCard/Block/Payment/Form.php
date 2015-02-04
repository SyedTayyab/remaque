<?php

class Buybox_GiftCard_Block_Payment_Form extends Mage_Payment_Block_Form
{
 
    protected $_methodCode = Buybox_GiftCard_Model_Config::METHOD_EXPRESS;

    public function getMethodCode()
    {
        return $this->_methodCode;
    }
}
