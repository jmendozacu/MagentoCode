<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Campaignrelation extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('affiliate/campaignrelation');
	}
	
	public function loadRelation($aff_id, $campaign_id) {
		$adapter = $this->_getReadConnection();
		$query = "Select relation_id from ". $this->_getTableName('affiliate_campaign_relation') ." where affiliate_id='".$aff_id."' and campaign_id=".$campaign_id;
		$relation_id = $adapter->fetchOne($query);
		$relation = $this->load($relation_id);
		return $relation;
	}
	
	protected function _getReadConnection() {
		return Mage::getSingleton('core/resource')->getConnection('core_read');
	}
	
	protected function _getTableName($name) {
		return Mage::getSingleton('core/resource')->getTableName($name);
	}
}