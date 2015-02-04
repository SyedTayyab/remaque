<?php

class Smartwave_Revolutionslider_Model_System_Config_Revolution_Options_Truefalse
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'true', 'label' => Mage::helper('revolutionslider')->__('True')),
            array('value' => 'false', 'label' => Mage::helper('revolutionslider')->__('False'))
        );
    }
}