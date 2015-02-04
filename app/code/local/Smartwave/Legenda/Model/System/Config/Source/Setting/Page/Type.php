<?php

class Smartwave_Legenda_Model_System_Config_Source_Setting_Page_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'wide', 'label' => Mage::helper('legenda')->__('Wide')),
            array('value' => 'boxed', 'label' => Mage::helper('legenda')->__('Boxed'))
        );
    }
}