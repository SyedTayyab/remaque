<?php

class Smartwave_All_Model_System_Config_Source_Design_Font_Weight_Basic
{
    public function toOptionArray()
    {
		return array(
            array('value' => '0',    'label' => Mage::helper('all')->__('Default')),
            array('value' => '200',    'label' => Mage::helper('all')->__('Extra-Light 200')),
			array('value' => '300',	'label' => Mage::helper('all')->__('Light 300')),
			array('value' => '400',	'label' => Mage::helper('all')->__('Normal 400')),
            array('value' => '500',    'label' => Mage::helper('all')->__('Medium 500')),
            array('value' => '600',    'label' => Mage::helper('all')->__('Semi-Bold 600')),
            array('value' => '700',    'label' => Mage::helper('all')->__('Bold 700')),
            array('value' => '900',	'label' => Mage::helper('all')->__('Ultra-Bold 900'))
        );
    }
}