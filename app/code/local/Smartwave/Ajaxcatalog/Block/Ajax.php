<?php 

class Smartwave_Ajaxcatalog_Block_Ajax extends Mage_Core_Block_Template{
	public function __construct(){
		
		$this->config = Mage::getStoreConfig('ajax_catalog');
		$this->url = Mage::getStoreConfig('web/unsecure/base_url');
		
		$this->ajaxSlider = $this->config['price_slider_settings']['slider'];
		$this->ajaxLayered = $this->config['price_slider_settings']['layered'];
		$this->ajaxToolbar = $this->config['price_slider_settings']['toolbar'];
		$this->overlayColor = $this->config['ajax_conf']['overlay_color'];
		$this->overlayOpacity = $this->config['ajax_conf']['overlay_opacity'];
	}
	
	public function getCallbackJs(){
		return Mage::getStoreConfig('ajax_catalog/ajax_conf/afterAjax');
	}
}