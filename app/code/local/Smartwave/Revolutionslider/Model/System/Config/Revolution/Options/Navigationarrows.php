<?php

class Smartwave_Revolutionslider_Model_System_Config_Revolution_Options_Navigationarrows
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'none', 'label' => Mage::helper('revolutionslider')->__('None')),
            array('value' => 'nexttobullets', 'label' => Mage::helper('revolutionslider')->__('Next to bullets')),
            array('value' => 'solo', 'label' => Mage::helper('revolutionslider')->__('Solo'))
        );
    }
}