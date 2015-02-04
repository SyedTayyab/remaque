<?php

class Smartwave_SharingTool_Model_Observer
{
  public function addButtonsHtml($observer)
    {
      $block = $observer->getBlock();
      $transport = $observer->getTransport();
     
      if ($block->getNameInLayout()=='product_review_list.count') {
	  
      	$plugin_enabled = Mage::getStoreConfig('sharing_tool/general/enabled');    	
          	  
		  if($plugin_enabled!=0){
			$html = $transport->getHtml();
			$buttons = Mage::app()->getLayout()->createBlock('sharingtool/share', 'addthis', array('template'=>'sharingtool/share.phtml'));
			$html .= $buttons->toHtml();
			$transport->setHtml($html);
		  }
      }
    }  
}