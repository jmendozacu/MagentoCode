<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Affiliate extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('affiliate/affiliate');
	}
	
	public function loadByCustomerId($customer_id) {
		if (!isset($customer_id) || $customer_id == 0) {
			return false;
		}		
		$adapter = $this->_getReadConnection();
		$query = "Select affiliate_id from ". $this->_getTableName('affiliate_affiliate') ." where customer_id=".$customer_id;

		$result = $adapter->fetchOne($query);
		return $result;
	}
	
	protected function _getReadConnection() {
		return Mage::getSingleton('core/resource')->getConnection('core_read');
	}
	
	protected function _getTableName($name) {
		return Mage::getSingleton('core/resource')->getTableName($name);
	}
}