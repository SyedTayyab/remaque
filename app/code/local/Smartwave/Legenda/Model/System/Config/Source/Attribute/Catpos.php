<?php

class Smartwave_Legenda_Model_System_Config_Source_Attribute_Catpos
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
                array('value' => '0',       'label' => 'Default'),
                array('value' => 'left',       'label' => 'Left'),
                array('value' => 'right',       'label' => 'Right')
            );
        }
        return $this->_options;
    }
}
