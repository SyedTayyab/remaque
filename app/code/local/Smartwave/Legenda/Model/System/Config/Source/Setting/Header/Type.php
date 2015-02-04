<?php

class Smartwave_Legenda_Model_System_Config_Source_Setting_Header_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'header-type-1', 'label' => Mage::helper('legenda')->__('Type1')),
            array('value' => 'header-type-2', 'label' => Mage::helper('legenda')->__('Type2')),
            array('value' => 'header-type-3', 'label' => Mage::helper('legenda')->__('Type3')),
            array('value' => 'header-type-4', 'label' => Mage::helper('legenda')->__('Type4'))
        );
    }
}