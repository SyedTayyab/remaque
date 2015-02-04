<?php 
class Smartwave_Legenda_Helper_Data extends Mage_Core_Helper_Abstract
{
    // get config for theme setting
    public function getConfig($optionName) 
    {
        if (Mage::registry('legenda_css_generate_store')) {
            $store_code = Mage::registry('legenda_css_generate_store');
            $store_id = Mage::getModel('core/store')->load($store_code)->getId();
        } else {
            $store_id = NULL;
        }        
        return Mage::getStoreConfig('legenda_setting/' . $optionName, $store_id);
    }
    // get config group for theme setting section
    public function getConfigGroup($storeId = NULL)
    {
        if (!$storeId) {
            $store_code = Mage::registry('legenda_css_generate_store');
            $storeId = Mage::getModel('core/store')->load($store_code)->getId();
        }

        if ($storeId)
            return Mage::getStoreConfig('legenda_setting', $storeId);
        else
            return Mage::getStoreConfig('legenda_setting');
    }    
    
    // get config for theme design
    public function getConfigDesign($optionName)
    {
        if (Mage::registry('legenda_css_generate_store')) {
            $store_code = Mage::registry('legenda_css_generate_store');
            $store_id = Mage::getModel('core/store')->load($store_code)->getId();
        } else {
            $store_id = NULL;
        }
        return Mage::getStoreConfig('legenda_design/' . $optionName, $store_id);
    }
    
    // get config group for theme design section
    public function getConfigGroupDesign($storeId = NULL)
    {
        if (!$storeId) {
            $store_code = Mage::registry('legenda_css_generate_store');
            $storeId = Mage::getModel('core/store')->load($store_code)->getId();
        }

        if ($storeId)
            return Mage::getStoreConfig('legenda_design', $storeId);
        else
            return Mage::getStoreConfig('legenda_design');
    }
    public function getTopPanelId() {
        if ($this->getConfig('header_settings/top_panel'))
            return $this->getConfig('header_settings/top_panel');
        return false;
    }
    
    public function getCategoryColumns($code) {
//        $id = Mage::getModel('catalog/layer')->getCurrentCategory()->getId();
//        $catModel = Mage::getModel('catalog/category')->load($id);
        $columns = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_lg_cat_design');
        if ($columns) {
            return $columns;
        } else {
            return Mage::getStoreConfig('legenda_setting/category_grid/columns', $code);
        }
    }
    
    public function getCategoryBannerBlock() {
        $cat_banner = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_lg_cat_banner');        
        if($cat_banner) {            
            return $cat_banner;
        }
        return false;
    }
    
    public function getCategoryListPos() {
        $cat_pos = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_lg_cat_position');
        if ($cat_pos) {
            return $cat_pos;
        } else {
            $store = Mage::app()->getStore();
            $code  = $store->getCode();
            $cat_pos = Mage::getStoreConfig('legenda_setting/category/main_cat_position', $code);
            return $cat_pos;
        }
    }
    private function _getBlocks($model, $block_signal) {
        if (!isset($this->_tplProcessor) || !$this->_tplProcessor)
        { 
            $this->_tplProcessor = Mage::helper('cms')->getBlockTemplateProcessor();            
        }
        return $this->_tplProcessor->filter( trim($model->getData($block_signal)) ); 
    }
    public function getHomeUrl() {
        return array(
            "label" => $this->__('Home'),
            "title" => $this->__("Home Page"),
            "link"  => Mage::getUrl()
        );
    }
}