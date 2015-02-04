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
class Smartwave_Filterproducts_Block_Bestsellers_Home_List extends Smartwave_Filterproducts_Block_Bestsellers_List
{

	protected function _getProductCollection()
    {
        $storeId = Mage::app()->getStore()->getId();
        $category_id = $this->getCategoryId();
		$visibility = array(
			Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH, 
			Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
		);
        if($category_id) {
        
            $category = Mage::getModel('catalog/category')->load($category_id);
        
            $products = Mage::getResourceModel('reports/product_collection')
                ->addCategoryFilter($category)
                ->addAttributeToSelect('*')
                ->addOrderedQty()
                ->setStoreId($storeId)
                ->addStoreFilter($storeId)
                ->setOrder('ordered_qty', 'desc')
                ->setPageSize($this->get_prod_count())
                ->setOrder($this->get_order(), $this->get_order_dir())
                ->setCurPage($this->get_cur_page());
        }
        else {
            
            $products = Mage::getResourceModel('reports/product_collection')
                ->addAttributeToSelect('*')
                ->addOrderedQty()
                ->setStoreId($storeId)
                ->addStoreFilter($storeId)
                ->setOrder('ordered_qty', 'desc')
                ->setPageSize($this->get_prod_count())
                ->setOrder($this->get_order(), $this->get_order_dir())
                ->setCurPage($this->get_cur_page());            
        }
		$product_count = $this->getProductCount();
            
        if($product_count)
        {
            $products->setPageSize($product_count);
        }

		$productFlatData = Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
		if($productFlatData == "1")
		{
			$products->getSelect()->joinLeft(
	                array('flat' => 'catalog_product_flat_'.$storeId),
	                "(e.entity_id = flat.entity_id ) ",	                
					array(
	                   'flat.name AS name','flat.small_image AS small_image','flat.price AS price','flat.special_price as special_price','flat.special_from_date AS special_from_date','flat.special_to_date AS special_to_date'
					)
	            );
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