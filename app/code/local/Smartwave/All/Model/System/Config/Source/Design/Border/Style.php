<?php

class Smartwave_All_Model_System_Config_Source_Design_Border_Style
{
    public function toOptionArray()
    {
		return array(
			array('value' => '', 'label' => Mage::helper('all')->__('---select---')),
			array('value' => 'hidden', 'label' => Mage::helper('all')->__('hidden')),
            array('value' => 'dotted', 'label' => Mage::helper('all')->__('dotted')),
            array('value' => 'dashed', 'label' => Mage::helper('all')->__('dashed')),
            array('value' => 'solid', 'label' => Mage::helper('all')->__('solid')),
            array('value' => 'double', 'label' => Mage::helper('all')->__('double')),
            array('value' => 'groove', 'label' => Mage::helper('all')->__('groove')),
            array('value' => 'ridge', 'label' => Mage::helper('all')->__('ridge')),
            array('value' => 'inset', 'label' => Mage::helper('all')->__('inset')),
            array('value' => 'outset', 'label' => Mage::helper('all')->__('outset')),
            array('value' => 'initial', 'label' => Mage::helper('all')->__('initial')),
            array('value' => 'inherit', 'label' => Mage::helper('all')->__('inherit'))
        );
    }
}