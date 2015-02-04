<?php

class Smartwave_All_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_texturePath;
    protected $_transPath;
    protected $_transPrev;
    
    public function __construct()
    {
        $this->_texturePath = 'smartwave/texture/default/';
        $this->_transPath = 'smartwave/transparency/default/';
        $this->_transPrev = 'smartwave/transparency/sample.png';
    }
    
    public function getTexturePath()
    {
        return $this->_texturePath;
    }
    
    public function getTransPath()
    {
        return $this->_transPath;
    }
    public function getTransPrev()
    {
        return $this->_transPrev;    
    }
}
