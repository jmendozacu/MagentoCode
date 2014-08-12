<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Campaign extends Mage_Core_Model_Abstract {	
	protected $_eventPrefix = 'campaign';
	
	public function _construct() {
		parent::_construct();
		$this->_init('affiliate/campaign');
	}
	
	public function getSelectedProductIds() {
		$productIds = array();
		$products = $this->getInProducts();
		$product_data = json_decode($products);
		foreach ($product_data as $product_id) {
			if ($product_id > 0)
				$productIds[] = $product_id;
		}

		return $productIds;
	}
	
	public function getCategoryIds() {
		$categoryIds = array();
		$categories = $this->getInCategories();
		$category_data = json_decode($categories);
		foreach ($category_data as $categoryId) {
			$categoryIds[] = $categoryId;
		}
		return $categoryIds;
	}
}