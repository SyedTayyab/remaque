<?php

class Smartwave_Revolutionslider_Model_System_Config_Revolution_Options_Shadow
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('revolutionslider')->__('No Shadow')),
            array('value' => '1', 'label' => Mage::helper('revolutionslider')->__('1')),
            array('value' => '2', 'label' => Mage::helper('revolutionslider')->__('2')),
            array('value' => '3', 'label' => Mage::helper('revolutionslider')->__('3'))
        );
    }
}