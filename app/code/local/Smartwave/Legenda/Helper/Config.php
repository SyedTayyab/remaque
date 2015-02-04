<?php

class Smartwave_Legenda_Helper_Config extends Mage_Core_Helper_Abstract
{
    // Path and directory of the automatically generated CSS
    protected $_generatedCssFolder;
    protected $_generatedCssPath;
    protected $_generatedCssDir;
    
    public function __construct()
    {
        //Create paths
        $this->_generatedCssFolder = 'css/_config/';
        $this->_generatedCssPath = 'frontend/smartwave/legenda/' . $this->_generatedCssFolder;
        $this->_generatedCssDir = Mage::getBaseDir('skin') . '/' . $this->_generatedCssPath;
    }
    
    // Get automatically generated CSS directory
    public function getGeneratedCssDir()
    {
        return $this->_generatedCssDir;
    }

    // Get design configuration css file path
    public function getDesignCssFile()
    {
        return $this->_generatedCssFolder . 'design_' . Mage::app()->getStore()->getCode() . '.css';
    }
    // Get setting configuration css file path
    public function getSettingCssFile()
    {
        return $this->_generatedCssFolder . 'setting_' . Mage::app()->getStore()->getCode() . '.css';
    }
    
    
    // Get responsive css file path
    public  function getCssResponsiveFile() 
    {
        if (Mage::getStoreConfig('zonda_setting/general/active_responsive'))
            return 'css/styles-zonda-responsive.css';
        return 'css/empty.css';
    }  
    
    // Get skin responsive css file path
    public  function getCssSkinResponsiveFile() 
    {
        if (Mage::getStoreConfig('zonda_setting/general/active_responsive'))
            return 'css/skin-responsive.css';
        return 'css/empty.css';
    } 
}
