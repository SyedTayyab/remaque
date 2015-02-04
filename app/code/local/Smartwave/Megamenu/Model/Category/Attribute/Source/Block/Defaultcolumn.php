<?php

class Smartwave_Megamenu_Model_Category_Attribute_Source_Block_Defaultcolumn
{    
    /**
     * Get list of available block column proportions
     * Added from version 1.1.0
     */
    public function toOptionArray()
    {
        return array(            
            array('value' => '1',        'label' => '1 column'),
            array('value' => '2',        'label' => '2 columns'),
            array('value' => '3',        'label' => '3 columns'),
            array('value' => '4',        'label' => '4 columns'),
            array('value' => '5',        'label' => '5 columns'),
            array('value' => '6',        'label' => '6 columns'),
            array('value' => '7',        'label' => '7 columns'),
            array('value' => '8',        'label' => '8 columns'),
            array('value' => '9',        'label' => '9 columns'),
            array('value' => '10',        'label' => '10 columns'),
            array('value' => '11',        'label' => '11 columns'),
            array('value' => '12',        'label' => '12 columns')
        );
    }
}
