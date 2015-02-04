<?php
// Lightbox Icon Position

class Smartwave_Zoom_Model_System_Config_Source_Lightbox_Position
{
    public function toOptionArray()
    {
        return array(            
            array('value' => 'rt_bt',        'label' => Mage::helper('smartwave_zoom')->__('Right-Bottom')),
            array('value' => 'lt_bt',        'label' => Mage::helper('smartwave_zoom')->__('Left-Bottom')),
            array('value' => 'rt_tp',            'label' => Mage::helper('smartwave_zoom')->__('Right-Top')),
            array('value' => 'lt_tp',        'label' => Mage::helper('smartwave_zoom')->__('Left-Top'))
        );
    }
}