<?php

class Smartwave_Revolutionslider_Model_System_Config_Revolution_Options_Navigationtype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'bullet', 'label' => Mage::helper('revolutionslider')->__('Bullet')),
            array('value' => 'thumb', 'label' => Mage::helper('revolutionslider')->__('Thumb')),
            array('value' => 'none', 'label' => Mage::helper('revolutionslider')->__('None'))
        );
    }
}