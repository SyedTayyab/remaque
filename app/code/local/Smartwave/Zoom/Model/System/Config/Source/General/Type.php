<?php
// Zoom Type Array

class Smartwave_Zoom_Model_System_Config_Source_General_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'inner',        'label' => Mage::helper('smartwave_zoom')->__('Inner')),            
            array('value' => 'window',        'label' => Mage::helper('smartwave_zoom')->__('Right Window')),
            array('value' => 'lens',        'label' => Mage::helper('smartwave_zoom')->__('Lens')),
        );
    }
}