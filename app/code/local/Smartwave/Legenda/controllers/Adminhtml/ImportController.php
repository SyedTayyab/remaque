<?php 
    class Smartwave_Legenda_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action{ 
        public function indexAction() {
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/legenda_setting/"));
        }
        public function blocksAction() {
            $isoverwrite = Mage::helper('legenda')->getConfig('install/overwrite_blocks');
            Mage::getSingleton('legenda/import_cms')->importCms('cms/block', 'blocks', $isoverwrite);
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/legenda_setting/"));
        }
        public function pagesAction() {
            $isoverwrite = Mage::helper('legenda')->getConfig('install/overwrite_pages');
            Mage::getSingleton('legenda/import_cms')->importCms('cms/page', 'pages', $isoverwrite);
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/legenda_setting/")); 
        }
    }
?>