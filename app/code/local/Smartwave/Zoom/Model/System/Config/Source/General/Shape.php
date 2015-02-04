<?php
// Lens Shape Option

class Smartwave_Zoom_Model_System_Config_Source_General_Shape
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'square',        'label' => Mage::helper('smartwave_zoom')->__('Square')),            
            array('value' => 'round',        'label' => Mage::helper('smartwave_zoom')->__('Round'))
        );
    }
}