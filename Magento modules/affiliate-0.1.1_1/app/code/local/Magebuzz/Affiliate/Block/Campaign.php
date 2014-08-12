<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Campaign extends Mage_Core_Block_Template {
	public function __construct() {
		parent::__construct();		
		$campaigns = $this->_getCampaignCollection();
		$this->setCampaigns($campaigns);
	}
	
	public function _prepareLayout() {
		parent::_prepareLayout();
		$pager = $this->getLayout()->createBlock('page/html_pager', 'affiliate.campaign.list.pager')
			->setCollection($this->getCampaigns());
		return $this;
	}
	
	public function getPagerHtml() {
		return $this->getChildHtml('pager');
	}
	
	protected function _getCampaignCollection() {
		$storeId = Mage::app()->getStore()->getId();
		$now = $now = Mage::app()->getLocale()->date(Mage::getModel('core/date')->date(), Varien_Date::DATETIME_INTERNAL_FORMAT, null, false)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		$collection = Mage::getModel('affiliate/campaign')->getCollection()
			->addFieldToFilter('status', array('neq' => '2'));
		$collection->getSelect()->join(array('relation'=>Mage::getSingleton('core/resource')->getTableName('affiliate_campaign_store')), 'main_table.campaign_id = relation.campaign_id', array('relation.*'));
		$collection->addFieldToFilter('relation.store_id', array('in' => array(0, $storeId)));
		return $collection;
	}
	
	public function getRateTitle(Magebuzz_Affiliate_Model_Campaign $campaign) {
		$commission_type = $campaign->getCommissionType();
		$amount = $campaign->getAmount();
		if ($commission_type == 1) {
			return Mage::helper('core')->currency(round($amount, 2));
		}
		else {
			return round($amount, 2) . '%';
		}
	}
	
	public function getIncludeUrl($campaign_id) {
		return $this->getUrl('*/*/include_campaign', array('affiliate_id' => $this->getAffiliateId(), 'campaign_id' => $campaign_id, '_secure' => true));
	}
	
	public function getExcludeUrl($campaign_id) {
		return $this->getUrl('*/*/exclude_campaign', array('affiliate_id' => $this->getAffiliateId(), 'campaign_id' => $campaign_id, '_secure' => true));
	}
	
	public function getCustomerSession() {
		return Mage::getSingleton('customer/session');
	}
	
	public function getCustomerId() {
		return (int)$this->getCustomerSession()->getCustomerId();
	}
	
	public function getAffiliateId() {		
		$affiliate_id = Mage::getModel('affiliate/affiliate')->loadByCustomerId($this->getCustomerId());
		return $affiliate_id;
	}
	
	public function getIncludedStatus($campaign_id) {
		$affiliate_id = $this->getAffiliateId();
		$relation = Mage::getModel('affiliate/campaignrelation')
			->getCollection()
			->addFieldToFilter('affiliate_id', $affiliate_id)
			->addFieldToFilter('campaign_id', $campaign_id)
			->load();
		if (count($relation)) {
			return Mage::helper('affiliate')->__("Included");
		}	
		else {
			return Mage::helper('affiliate')->__('Excluded');
		}
	}
}