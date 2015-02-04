<?php

class Smartwave_Megamenu_Model_Category_Attribute_Source_Type_Mode
{    
    /**
     * Get list of available block column proportions
     */
    public function toOptionArray()
    {
        return array(            
            array('value' => 'wide',        'label' => 'Wide'),
            array('value' => 'narrow',       'label' => 'Narrow')
        );
    }
}
