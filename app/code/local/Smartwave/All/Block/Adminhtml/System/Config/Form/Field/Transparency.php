<?php

class Smartwave_All_Block_Adminhtml_System_Config_Form_Field_Transparency extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $element->getElementHtml();
        $jsUrl = $this->getJsUrl('smartwave/jquery/jquery-1.8.3.min.js');
        $transUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . Mage::helper('all')->getTransPath();
        $transPrev = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . Mage::helper('all')->getTransPrev();
        
        $bgcPickerId = str_replace('_trans', '_bg_color', $element->getHtmlId());
        
        $previewId = $element->getHtmlId() . '-trans-preview';
        
        if (Mage::registry('jqueryLoaded') == false)
        {
            $html .= '<script type="text/javascript" src="'. $jsUrl .'"></script>
                <script type="text/javascript">jQuery.noConflict();</script>';
            Mage::register('jqueryLoaded', 1);
        }

        $html .= '<br/><div style="width:280px; height:160px; margin:10px 0; background:url('.$transPrev.') no-repeat;"><div id="'. $previewId .'" style="width:280px; height:80px;border-bottom:2px solid #f00;"></div></div>
            <script type="text/javascript">
                jQuery(function($){
                    var trans        = $("#'. $element->getHtmlId()    .'");
                    var bgcolor        = $("#'. $bgcPickerId            .'");
                    var preview     = $("#'. $previewId                .'");
                    
                    preview.css("background-color", bgcolor.attr("value"));
                    trans.change(function() {
                        var bg_image = "url('. $transUrl .'" + trans.val() + ".png)";
                        if(trans.val() == 0)
                            bg_image = "none";
                        preview.css({
                            "background-color": bgcolor.css("background-color"),
                            "background-image": bg_image
                        });
                    }).change();
                    bgcolor.change(function(){trans.change();});
                });
            </script>';
        
        return $html;
    }
}