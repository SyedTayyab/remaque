<?php

class Smartwave_Legenda_Model_System_Config_Source_Setting_Footer_Column
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('legenda')->__('Do not show')),
            array('value' => 'custom', 'label' => Mage::helper('legenda')->__('Static Block')),
            array('value' => 'sale_products', 'label' => Mage::helper('legenda')->__('Sale Products')),
            array('value' => 'best_seller', 'label' => Mage::helper('legenda')->__('Best Seller Products')),
            array('value' => 'new_products', 'label' => Mage::helper('legenda')->__('New Products')),
            array('value' => 'latest_products', 'label' => Mage::helper('legenda')->__('Latest Products')),
            array('value' => 'flickr', 'label' => Mage::helper('legenda')->__('Photos Flickr')),
            array('value' => 'twitter', 'label' => Mage::helper('legenda')->__('Twitter')),
            array('value' => 'facebook', 'label' => Mage::helper('legenda')->__('Facebook')),
            array('value' => 'blog', 'label' => Mage::helper('legenda')->__('Recent Posts'))
        );
    }
}