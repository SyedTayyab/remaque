<?php

class Smartwave_Revolutionslider_Model_System_Config_Revolution_Options_Valign
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'top', 'label' => Mage::helper('revolutionslider')->__('Top')),
            array('value' => 'center', 'label' => Mage::helper('revolutionslider')->__('Center')),
            array('value' => 'bottom', 'label' => Mage::helper('revolutionslider')->__('Bottom'))
        );
    }
}