<?php

class Smartwave_Revolutionslider_Model_System_Config_Revolution_Options_Soloarrowtdtype
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('revolutionslider')->__('')),
            array('value' => 'top', 'label' => Mage::helper('revolutionslider')->__('Top')),
            array('value' => 'center', 'label' => Mage::helper('revolutionslider')->__('Center')),
            array('value' => 'bottom', 'label' => Mage::helper('revolutionslider')->__('Bottom'))
        );
    }
}