<?php

class Smartwave_Revolutionslider_Model_System_Config_Revolution_Options_Navigationstyle
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'round', 'label' => Mage::helper('revolutionslider')->__('Round')),
            array('value' => 'square', 'label' => Mage::helper('revolutionslider')->__('Square')),
            array('value' => 'round-old', 'label' => Mage::helper('revolutionslider')->__('Round Old')),
            array('value' => 'square-old', 'label' => Mage::helper('revolutionslider')->__('Square Old')),
            array('value' => 'navbar-old', 'label' => Mage::helper('revolutionslider')->__('Navbar Old'))
        );
    }
}