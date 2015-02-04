<?php

class Smartwave_Revolutionslider_Model_System_Config_Revolution_Options_Halign
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'left', 'label' => Mage::helper('revolutionslider')->__('Left')),
            array('value' => 'center', 'label' => Mage::helper('revolutionslider')->__('Center')),
            array('value' => 'right', 'label' => Mage::helper('revolutionslider')->__('Right'))
        );
    }
}