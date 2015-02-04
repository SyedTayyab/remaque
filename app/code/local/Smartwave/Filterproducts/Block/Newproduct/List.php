<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of List
 *
 * @author om
 */
class Smartwave_Filterproducts_Block_Newproduct_List extends Mage_Catalog_Block_Product_List
{
	
	protected function _getProductCollection()
    {
       parent::__construct();

       $todayStartOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());


        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', array('or'=> array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter('news_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayStartOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                    array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                    )
              )
            ->addAttributeToSort('news_from_date', 'desc')
            ->setPageSize($this->get_prod_count())
            ->setCurPage($this->get_cur_page());

        $this->_productCollection = $collection;

        return $this->_productCollection;
    }

    function get_prod_count()
    {
        //unset any saved limits
        Mage::getSingleton('catalog/session')->unsLimitPage();
        return (isset($_REQUEST['limit'])) ? intval($_REQUEST['limit']) : 9;
    }// get_prod_count

    function getItems()
    {
        return $this->_productCollection;
    }
    
    function get_cur_page()
    {
        return (isset($_REQUEST['p'])) ? intval($_REQUEST['p']) : 1;
    }// get_cur_page

    function get_order()
    {
        return (isset($_REQUEST['order'])) ? ($_REQUEST['order']) : 'position';
    }// get_order

    function get_order_dir()
    {
        return (isset($_REQUEST['dir'])) ? ($_REQUEST['dir']) : 'desc';
    }// get_direction
}

?>
