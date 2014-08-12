<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Mysql4_Campaign extends Mage_Core_Model_Mysql4_Abstract {
	public function _construct() {    
		$this->_init('affiliate/campaign', 'campaign_id');
	}
	
	protected function _afterSave(Mage_Core_Model_Abstract $object) {
		$oldStores = $this->lookupStoreIds($object->getId());
		$newStores = (array)$object->getStores();
		if (empty($newStores)) {
			$newStores = (array)$object->getStoreId();
		}
		$table  = Mage::getSingleton('core/resource')->getTableName('affiliate_campaign_store');
		$insert = array_diff($newStores, $oldStores);
		$delete = array_diff($oldStores, $newStores);

		if ($delete) {
			$where = array(
				'campaign_id = ?'     => (int) $object->getId(),
				'store_id IN (?)' => $delete
			);

			$this->_getWriteAdapter()->delete($table, $where);
		}

		if ($insert) {
			$data = array();

			foreach ($insert as $storeId) {
				$data[] = array(
					'campaign_id'  => (int) $object->getId(),
					'store_id' => (int) $storeId
				);
			}

			$this->_getWriteAdapter()->insertMultiple($table, $data);
		}

		return parent::_afterSave($object);
	}
	
	protected function _afterLoad(Mage_Core_Model_Abstract $object) {
		if ($object->getId()) {
			$stores = $this->lookupStoreIds($object->getId());
			$object->setData('store_id', $stores);
		}
		return parent::_afterLoad($object);
	}
	
	public function lookupStoreIds($campaignId) {
		$adapter = $this->_getReadAdapter();
		$select  = $adapter->select()
			->from(Mage::getSingleton('core/resource')->getTableName('affiliate_campaign_store'), 'store_id')
			->where('campaign_id = ?',(int)$campaignId);

		return $adapter->fetchCol($select);
	}
}