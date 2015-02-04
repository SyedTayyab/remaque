<?php

class Smartwave_Revolutionslider_Model_System_Config_Revolution_Options_Swipetouchtype
{
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label' => Mage::helper('revolutionslider')->__('1')),
            array('value' => '2', 'label' => Mage::helper('revolutionslider')->__('2')),
            array('value' => '3', 'label' => Mage::helper('revolutionslider')->__('3')),
            array('value' => '4', 'label' => Mage::helper('revolutionslider')->__('4')),
            array('value' => '5', 'label' => Mage::helper('revolutionslider')->__('5'))
        );
    }
}