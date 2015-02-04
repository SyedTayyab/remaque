<?php

  class Smartwave_Zoom_Helper_Data extends Mage_Core_Helper_Abstract
  {
    /** Get Configuration Value**/ 
    public function getConfig($optionString){
        return Mage::getStoreConfig('zoom/'.$optionString);
    }
    
    /**
     * Check if module is enabled.
     * @return bool
     */
    public function isZoomEnabled()
    {
        return (bool) $this->getConfig('general/enable');
    }
    
    /**
     * Check if lightbox is enabled
     * @return bool
     */
    public function useLightbox()
    {
        return (bool) $this->getConfig('lightbox/lb_enable');
    }
    
    /**
     * Check if zoom is enabled
     * @return bool
     */
    public function useZoom()
    {
        if ($this->getConfig('general/used_zoom') && $this->getConfig('general/enable'))
            return true;
        else
            return false;
    }
    
	/**
     * Get Gallery Item Number
     * @return integer
     */
     public function getGalItemNum()
     {
         if ($this->getConfig('gallery/ga_item_num')) {
             return $this->getConfig('gallery/ga_item_num');
         }
         return 4;
     }

    
    
    /**
    * Get MainImage Options. If image width and height are not specified, return default value (267x267)
    */
    public function getMainImgOptions()
    {
        $imgOpt = array();
        $imgWidth = intval($this->getConfig('image/img_width'));
        $imgHeight = intval($this->getConfig('image/img_height'));
        if ($imgWidth)
            $imgOpt['img_width'] = $imgWidth;
        else
            $imgOpt['img_width'] = 267;
        
        if($imgHeight)
            $imgOpt['img_height'] = $imgHeight;
        else
            $imgOpt['img_height'] = 267;
            
        return $imgOpt;        
    }
    /**
    * Get Big Image Options. If Zoom Image Width and height are not specified, return default value.(600X600)
    */
    public function getBigImgOptions()
    {
        $imgOpt = array();
        $mainImgOpt = $this->getMainImgOptions();
        if ($this->getConfig('general/used_zoom')) {
            $zoomTimes = $this->getConfig('general/zoom_img_times');
            if (!$zoomTimes) {
                $zoomTimes = 2;
            }
            $imgWidth = $mainImgOpt['img_width'] * $zoomTimes;
            $imgHeight = $mainImgOpt['img_height'] * $zoomTimes;
            
            $imgOpt['img_width'] = $imgWidth;
            $imgOpt['img_height'] = $imgHeight;
            
        } else {
            $imgOpt = $this->getMainImgOptions();
        }
        return $imgOpt;
    }
    /**
    * Get Gallery Image Options.If Item Width and Height are not specified, return default value.(65X65)
    */
    public function getGalItemOptions()
    {
        $imgOpt = array();
        $imgWidth = intval($this->getConfig('gallery/ga_img_width'));
        $imgHeight = intval($this->getConfig('gallery/ga_img_height'));
        $imgNum = intval($this->getConfig('gallery/ga_item_num'));
        $imgBorder = intval($this->getConfig('gallery/ga_border_width'));
        $imgBorderCol = $this->getConfig('gallery/ga_border_color');
        $imgMargin = intval($this->getConfig('gallery/ga_item_margin'));
        
        if($imgWidth)
            $imgOpt['img_width'] = $imgWidth;
        else
            $imgOpt['img_width'] = 65;
            
        if($imgHeight)
            $imgOpt['img_height'] = $imgHeight;
        else
            $imgOpt['img_height'] = 65;
            
        if ($imgNum)
            $imgOpt['img_num'] = $imgNum;
        else
            $imgOpt['img_num'] = 4;
        
        if ($imgMargin)
            $imgOpt['img_margin'] = $imgMargin;
        else
            $imgOpt['img_margin'] = 5;
        
        if ($imgBorder)
            $imgOpt['img_border'] = $imgBorder;
        else
            $imgOpt['img_border'] = 0;
        
        if ($imgBorderCol)
            $imgOpt['img_border_color'] = $imgBorderCol;
        else
            $imgOpt['img_border_color'] = 0;
        
        
            
        return $imgOpt;
    }
    /* Get Loading Icon
     * @return string
     */
    public function getLoadingIcon() 
    {
        $iconName = $this->getConfig('general/loading_icon');
        if ($iconName)
            return Mage::getBaseUrl('media').'smartwave/catalog/product/view/media/'.$iconName;
        else
            return Mage::getBaseUrl('media').'smartwave/catalog/product/view/media/loader.gif';
    }
    /**
     * Get string with Image Zoom options
     * @return string
     */
    public function getZoomOptions()  
    {
        if ($this->getConfig('general/used_zoom')) {
            $cfg = array();
            
            $zoomType = $this->getConfig('general/type');
            $cfg[] = "zoomType:'$zoomType'";
            
            $scrollZoom = $this->getConfig('general/scroll_zoom');            
            $cfg[] = "scrollZoom:$scrollZoom";
            $loadingIcon = $this->getLoadingIcon();
            $cfg[] = "loadingIcon:'$loadingIcon'";            
            $easingActive = $this->getConfig('general/easing');
            $easingDuration = $this->getConfig('general/easing_duration');
            $cfg[] = "easing:$easingActive";            
            if ($easingActive && $easingDuration)
                $cfg[] = "easingDuration:$easingDuration";  
            switch($zoomType) {
                case 'inner':
                    $cfg[] = "cursor:'crosshair', zoomWindowFadeIn:500, zoomWindowFadeOut:500";
                    break;
                case 'window':
                    $cfg[] = "cursor: 'pointer'";
                    $zoomAreaBorderWidth = intval($this->getConfig('general/border_width'));
                    if ($zoomAreaBorderWidth) {
                        $cfg[] = "borderSize:$zoomAreaBorderWidth";
                        $borderColor = $this->getConfig('general/border_color');
                        if ($borderColor)
                            $cfg[] = "borderColour:'$borderColor'";
                    } else {
						$cfg[] = "borderSize:0";
					}
                    if (intval($this->getConfig('general/zoom_wind_width'))) {
                        $zoomWindWidth = intval($this->getConfig('general/zoom_wind_width')) + 2*intval($this->getConfig('image/img_border_width'));
                        $zoomWindHeight = intval($this->getConfig('general/zoom_wind_height')) + 2*intval($this->getConfig('image/img_border_width'));
                    } else {
                        $zoomWindWidth = 267 + 2*intval($this->getConfig('image/img_border_width'));
                        $zoomWindHeight = 267 + 2*intval($this->getConfig('image/img_border_width'));
                    }
                    $cfg[] = "zoomWindowWidth:$zoomWindWidth";
                    $cfg[] = "zoomWindowHeight:$zoomWindHeight"; 
                    $lensBorderWidth = intval($this->getConfig("general/lens_border_width"));
                    $lensBorderColor = $this->getConfig("general/lens_border_color");
                    if ($lensBorderWidth) {
                        $cfg[] = "lensBorderSize:$lensBorderWidth";
                        if ($lensBorderColor)
                            $cfg[] = "lensBorderColour:'$lensBorderColor'";                               
                    }
                    $tint = $this->getConfig("general/tint");
                    if($tint) {
                        $cfg[] = "tint:$tint";
                        $tintColor = $this->getConfig("general/tint_color");
                        if ($tintColor)
                            $cfg[] = "tintColour:'$tintColor'";
                        $tintOpacity = $this->getConfig("general/tint_opacity");
                        if ($tintOpacity)
                            $cfg[] = "tintOpacity:$tintOpacity";
                    }
                    $lensOpacity = $this->getConfig('general/lens_opacity');
                    if ($lensOpacity)
                        $cfg[] = "lensOpacity:$lensOpacity";
                    $lensColor = $this->getConfig('general/lens_color');
                    if ($lensColor)
                        $cfg[] = "lensColour:'$lensColor'";
                    break;
                case 'lens':
                    $cfg[] = "cursor: 'pointer'";
                    $zoomAreaBorderWidth = intval($this->getConfig('general/border_width'));
                    if ($zoomAreaBorderWidth) {
                        $cfg[] = "borderSize:$zoomAreaBorderWidth";
                        $borderColor = $this->getConfig('general/border_color');
                        if ($borderColor)
                            $cfg[] = "borderColour:'$borderColor'";
                    }
                    $lensShape = $this->getConfig('general/lens_shape');
                    $lensSize = $this->getConfig("general/lens_size");
                    $cfg[] = "lensShape:'$lensShape'";
                    if ($lensSize)
                        $cfg[] = "lensSize:$lensSize";                    
                    break;
                default:
                    $cfg[] = "cursor:'crosshair', zoomWindowFadeIn:500, zoomWindowFadeOut:500";
                    break;
            }
        } else {
            $cfg[] = "zoomEnabled:false";
        }
        $mainImgBorderSize = $this->getConfig('image/img_border_width');
        $mainImgBorderColor = $this->getConfig('image/img_border_color');
        if ($mainImgBorderSize) {
            $cfg[] = "imgBorderSize:$mainImgBorderSize";
            if ($mainImgBorderColor)
                $cfg[] = "imgBorderColour:'$mainImgBorderColor'";
        }
        $cfg[] = "imageCrossfade: true";
        return implode($cfg, ',');
    }
    /**
     * Get string with Gallery Item Style
     * @echo string
     */ 
    public function getItemStyle()
    {
        $imgBorder = intval($this->getConfig('gallery/ga_border_width'));        
        if ($imgBorder){
            echo '<style type="text/css">.gal-wrapper .slide {border: solid '.$imgBorder.'px '.$this->getConfig('gallery/ga_border_color').';} </style>';
        }
    }
     /**
     * Get string with Lightbox options
     * @return string
     */
     public function getLightBoxOptions()
     {
         if($this->getConfig('lightbox/lb_enable'))
         {
             $options = array();
             $options['position'] = $this->getConfig('lightbox/lb_icon_position');             
             return $options;
         }
         return false;   
     }
  }
