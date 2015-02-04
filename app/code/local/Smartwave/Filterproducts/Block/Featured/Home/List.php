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
class Smartwave_Filterproducts_Block_Featured_Home_List extends Smartwave_Filterproducts_Block_Featured_List
{
		protected function _getProductCollection()
    	{
        $storeId    = Mage::app()->getStore()->getId();
        
        $category_id = $this->getCategoryId();
        
        if($category_id) {
            $category = Mage::getModel('catalog/category')->load($category_id);    
            
            $products = Mage::getModel('catalog/product')->getCollection()
            ->addCategoryFilter($category)
            ->addAttributeToFilter(array(array('attribute' => 'featured', 'eq' => '1')))
            ->addAttributeToSelect('*')
            ->addAttributeToSelect(array('name', 'price', 'small_image'))
            ->setStoreId($storeId)
            ->addStoreFilter($storeId);

        }
        else {
            $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter(array(array('attribute' => 'featured', 'eq' => '1')))
            ->addAttributeToSelect('*')
            ->addAttributeToSelect(array('name', 'price', 'small_image'))
            ->setStoreId($storeId)
            ->addStoreFilter($storeId);
        }
        
        $product_count = $this->getProductCount();
            
        if($product_count)
        {
            $products->setPageSize($product_count);
        }


        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);

        $this->_productCollection = $products;

        return $this->_productCollection;
    	}
		
		function get_prod_count()
		{
			//unset any saved limits
	    	Mage::getSingleton('catalog/session')->unsLimitPage();
	    	return (isset($_REQUEST['limit'])) ? intval($_REQUEST['limit']) : 9;
		}// get_prod_count

		function get_cur_page()
		{
			return (isset($_REQUEST['p'])) ? intval($_REQUEST['p']) : 1;
		}// get_cur_page

    	function get_order()
		{
			return (isset($_REQUEST['order'])) ? ($_REQUEST['order']) : 'ordered_qty';
		}// get_order

    	function get_order_dir()
		{
			return (isset($_REQUEST['dir'])) ? ($_REQUEST['dir']) : 'desc';
		}// get_direction

		public function getToolbarHtml()
    	{
        
    	}
		public function get_product_column()
        {
            $columns = 4;
            if ($this->getProductsColumn())
                $columns = $this->getProductsColumn();
            return $columns;
        }
}