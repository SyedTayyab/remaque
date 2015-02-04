<?php

class Smartwave_Megamenu_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_menuData = null;
    
    //From version 1.1.0 updated
    public function getConfig($optionString)
    {   
        //added from version 1.1.0
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        return Mage::getStoreConfig('megamenu/' . $optionString, $code);
    }
       
    public function getCustomLink()
    {
        $blockClassName = Mage::getConfig()->getBlockClassName('megamenu/navigation');
        $block = new $blockClassName();        
        $customLinks = $block->drawCustomLinks();        
        return $customLinks;
    }
    public function getHomeIcon()
    {
        if ($this->getConfig('general/show_home_link') && $this->getConfig('general/show_home_icon')) {
            $icon = $this->getConfig('general/home_icon');
            if ($icon)
                return Mage::getBaseUrl('media') . 'smartwave/megamenu/html/' . $icon;
            return Mage::getBaseUrl('media') . 'smartwave/megamenu/html/icon_home.png';
        }
        return false;
        
    }
    
    public function getCustomStyle()
    {
        $customStyle = $this->getConfig('custom/custom_style');
        if (!$customStyle) return;
        return $customStyle;
    }
    //From version 1.1.0 updated 
    public function getMenuData()
    {
        //added from version 1.1.0
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        if (!is_null($this->_menuData)) return $this->_menuData;

        $blockClassName = Mage::getConfig()->getBlockClassName('megamenu/navigation');
        $block = new $blockClassName();        
        $categories = $block->getStoreCategories();        
        if (is_object($categories)) $categories = $block->getStoreCategories()->getNodes();

        $this->_menuData = array(
            '_block'                        => $block,
            '_categories'                   => $categories,
            '_isWide'                       => Mage::getStoreConfig('megamenu/general/wide_style', $code),
            '_showHomeLink'                 => Mage::getStoreConfig('megamenu/general/show_home_link', $code),
            '_showHomeIcon'                 => Mage::getStoreConfig('megamenu/general/show_home_icon', $code),
            '_popupWidth'                   => Mage::getStoreConfig('megamenu/popup/width', $code) + 0            
        );        
        return $this->_menuData;
    }
    
    public function getHomeLink($mode = 'dt')
    {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);
        $homeLinkUrl        = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $homeLinkText       = $this->__('Home');
        $homeLink           = '';
        $homeIconClass      = '';
        if ($this->getIsHomePage()) {
            $homeIconClass = 'act';
        }
        if ($_showHomeLink) {
            if ($_showHomeIcon && $mode == 'dt') {
                $homeLinkText = '<img src="'.$this->getHomeIcon().'" alt="'.$this->__('Home').'" title="'.$this->__('Home').'"/>';                
                $homeIconClass .= ' home-icon-img';
            }
            $homeLink = <<<HTML
<li>
    <a href="$homeLinkUrl" class="$homeIconClass">
       <span>$homeLinkText</span>
    </a>
</li>
HTML;
            return $homeLink;
        }
        return '';
    }
    
    public function getBlogLink()
    {
        //added from version 1.1.0
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
		//---updated from version 1.0.2---
     if (Mage::getStoreConfig('blog/menu/top_menu', $code) && Mage::getStoreConfig('blog/blog/enabled', $code)) {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);
        $blogLinkUrl        = Mage::helper('blog')->getRouteUrl();
        //updated from version 1.1.6
        //$blogLinkText       = $this->__('Blog');
        $blogLinkText = '';
        if (Mage::helper('blog')->isTitle()) {
            $blogLinkText = Mage::helper('blog')->getTitle();
        } else {
            $blogLinkText       = $this->__('Blog');
        }
        $blogLink           = <<<HTML
<li>
    <a href="$blogLinkUrl" class="blog-nav">
       <span>$blogLinkText</span>
    </a>
</li>
HTML;
       return $blogLink;
        }else{
            return '';
        }
        
    }
    
    public function getMobileMenuContent()
    {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);
        // --- Home Link ---
        $homeLink = $this->getHomeLink('mb');
        // --- Blog Link ---
        $blogLink = $this->getBlogLink();
        // --- Menu Content ---
        $mobileMenuContent = '';
        $mobileMenuContentArray = array();
        foreach ($_categories as $_category) {
            $mobileMenuContentArray[] = $_block->drawMegaMenuItem($_category,'mb');
        }
        if (count($mobileMenuContentArray)) {
            $mobileMenuContent = implode("\n", $mobileMenuContentArray);
        }
        
        $customMobileLinks = $_block->drawCustomMobileLinks();
        // --- Result ---
        $menu = <<<HTML
$homeLink
$mobileMenuContent
$blogLink
$customMobileLinks
<div class="clearBoth"></div>
HTML;
        return $menu;
    }
    
    public function getMenuContent()
    {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);
        // --- Home Link ---        
        $homeLink = $this->getHomeLink();
        // --- Blog Link ---
        $blogLink = $this->getBlogLink();
        // --- Menu Content ---
        $menuContent = '';
        //From version 1.1.0 updated
        //version 1.1.0 start
        $store = Mage::app()->getStore();
        $code  = $store->getCode();        
        if ($this->getConfig('general/inc_cat_top')) {
            $menuContentArray = array();
            foreach ($_categories as $_category) {
                $menuContentArray[] = $_block->drawMegaMenuItem($_category,'dt');
            }
            if (count($menuContentArray)) {
                $menuContent = implode("\n", $menuContentArray);
            }   
        } else {
            if ($this->getConfig('general/inc_cat_item_top')) {
                $menuContent = $this->getCategoryMenuItem();   
            }
        }
        //version 1.1.0 end               
        // --- Custom Links
        $customLinks = $_block->drawCustomLinks();              
        // --- Custom Blocks
        $customBlocks = $_block->drawCustomBlock();              
        // --- Result ---
        $menu = <<<HTML
$homeLink
$menuContent
$blogLink
$customLinks
$customBlocks
HTML;
        return $menu;
    }
    public function getLogoAlt() 
    {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);
        return $_block->getLogoAlt();
    }
    public function getLogoSrc()
    {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);
        return $_block->getLogoSrc();
    }
    public function getIsHomePage()
    {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);
        return $_block->getIsHomePage();
    }
    
    //added from version 1.1.0
    public function getSideCategoryList()
    {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);
        $menuContent = '';
        $menuContentArray = array();
        foreach ($_categories as $_category) {
            $menuContentArray[] = $_block->drawMegaMenuItem($_category,'dt');
        }
        if (count($menuContentArray)) {
            $menuContent = implode("\n", $menuContentArray);
        }
        return $menuContent;
    }
    
    //added from version 1.1.0
    public function getCategoryMenuItem()
    {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);        
        $menuContent = '';
        $menuContentArray = array();
        $i = 1;      
		//updated from version 1.1.5
		if (!count($_categories)) return '';
        foreach ($_categories as $_category) {
            $menuContentArray[] = $_block->drawCategoryMenuItem($_category, $i);
            $i++;            
        }        
        if (count($menuContentArray)) {
            $menuContent .= implode("\n", $menuContentArray);
        }
          
        $parentClass = '';
        if ($this->getConfig('general/wide_style') == 'wide') {
            $parentClass = 'menu-full-width category-top-menu-item';
        } else {
            $parentClass = 'menu-item menu-item-has-children menu-parent-item';
        }
        $menuHtml = '<li class="'.$parentClass.'">';
        $menuHtml .= '<a href="javascript:void(0);">';
        $menuHtml .= $this->__('Categories');
        $menuHtml .= '</a>';
        if ($this->getConfig('general/wide_style') == 'wide') {
            $menuHtml .= '<div class="nav-sublist-dropdown" style="display: none;"><div class="container"><div class="mega-columns row"><div class="block1 col-sm-12"><div class="row"><ul>';    
            $menuHtml .= $menuContent;
            $menuHtml .= '</ul></div></div></div></div></div>';
        } else {
            $menuHtml .= '<div class="nav-sublist-dropdown" style="display: none;"><div class="container"><ul>';    
            $menuHtml .= $menuContent;
            $menuHtml .= '</ul></div></div>';                           
        }
        $menuHtml .= '</li>';
        
        return $menuHtml;
    }
    
    //added from version 1.1.0
    public function getCategoryMobileItem() {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);
        // --- Menu Content ---
        $mobileMenuContent = '';
        $mobileMenuContentArray = array();
        foreach ($_categories as $_category) {
            $mobileMenuContentArray[] = $_block->drawMegaMenuItem($_category,'mb');
        }
        if (count($mobileMenuContentArray)) {
            $mobileMenuContent = implode("\n", $mobileMenuContentArray);
        }   
        // --- Result ---
        $menu = '<ul>';
        $menu .= $mobileMenuContent;
        $menu .= '</ul>';
        return $menu;
    }    
    
    //added from version 1.2.0
    public function getSideCategoryAccordion() {
        $menuData = Mage::helper('megamenu')->getMenuData();
        extract($menuData);
        $menuContent = '';
        $menuContentArray = array();
        foreach ($_categories as $_category) {
            $menuContentArray[] = $_block->drawMegaMenuItem($_category,'mb');
        }
        if (count($menuContentArray)) {
            $menuContent = implode("\n", $menuContentArray);
        }
        return $menuContent;
    }
}