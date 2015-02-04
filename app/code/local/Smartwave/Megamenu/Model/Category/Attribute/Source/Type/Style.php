<?php

class Smartwave_Megamenu_Model_Category_Attribute_Source_Type_Style
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{    
    /**
     * Get list of available block column proportions
     */
    public function getAllOptions()
    {
        if (!$this->_options)
        {
            $this->_options = array(
                array('value' => 'default',     'label' => 'default'),
                array('value' => 'wide',        'label' => 'Wide'),
                array('value' => 'narrow',       'label' => 'Narrow')       
            );
        }
        return $this->_options;
    }
}
