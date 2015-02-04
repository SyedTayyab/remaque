<?php

class Smartwave_Legenda_Model_System_Config_Source_Design_Page_Bgtype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'texture', 'label' => Mage::helper('legenda')->__('texture')),
            array('value' => 'image', 'label' => Mage::helper('legenda')->__('image'))
        );
    }
}