<?php

class Smartwave_Legenda_Model_System_Config_Source_Design_Page_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'default', 'label' => Mage::helper('legenda')->__('Light(Default)')),
            array('value' => 'dark', 'label' => Mage::helper('legenda')->__('Dark'))
        );
    }
}