<?php

class Smartwave_Revolutionslider_Model_System_Config_Revolution_Options_Dottedoverlay
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'none', 'label' => Mage::helper('revolutionslider')->__('none')),
            array('value' => 'twoxtwo', 'label' => Mage::helper('revolutionslider')->__('twoxtwo')),
            array('value' => 'threexthree', 'label' => Mage::helper('revolutionslider')->__('threexthree')),
            array('value' => 'twoxtwowhite', 'label' => Mage::helper('revolutionslider')->__('twoxtwowhite')),
            array('value' => 'threexthreewhite', 'label' => Mage::helper('revolutionslider')->__('threexthreewhite'))
        );
    }
}