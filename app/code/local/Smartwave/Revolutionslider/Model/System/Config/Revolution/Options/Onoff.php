<?php

class Smartwave_Revolutionslider_Model_System_Config_Revolution_Options_Onoff
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'off', 'label' => Mage::helper('revolutionslider')->__('Off')),
            array('value' => 'on', 'label' => Mage::helper('revolutionslider')->__('On'))
        );
    }
}