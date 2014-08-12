<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Account extends Mage_Core_Block_Template {
	public function _prepareLayout() {
		return parent::_prepareLayout();
	}
	
	public function getTotalValue($transactionCollection) {
		$total = 0;
		foreach($transactionCollection as $transaction) {
			$total += $transaction['commission'];
		}
		return $total;
	}
		
	public function getTotalCommitedValue($commissionCollection) {
		$total = 0;
		foreach($commissionCollection as $commission) {
			$total += $commission['amount'];
		}
		return $total;
	}
	
	// return array type
	public function getTransactions($affId) {
		$collection = Mage::getModel('affiliate/transaction')->getCollection()->addFilter('affiliate_id', $affId);
		return $collection->getData();
	}
	
	// return array type
	public function getCommissions($affId) {
		$collection = Mage::getModel('affiliate/commission')->getCollection()->addFilter('affiliate_id', $affId);
		return $collection->getData();
	}

	// returm affiliate code to store in browser cookie
	public function getAffiliateCode() {
		$affiliate = Mage::getModel('affiliate/affiliate')->load($this->getAffiliateId());
		$referral_code = $affiliate->getReferralCode();
		return $referral_code;
	}
	
	public function getRateTitle(Magebuzz_Affiliate_Model_Campaign $campaign) {
		return Mage::helper('affiliate')->getRateTitle($campaign);
	}
	
	public function getStatus($stt) {
		if($stt=='1') return 'Enabled';
		else return 'Disabled';
	}
	
	public function getEarningByCampaign($campaign_id) {
		$affId = $this->getAffiliateId();
		$collection = Mage::getModel('affiliate/transaction')->getCollection();
		$collection->addFilter('campaign_id', $campaign_id);
		$collection->addFilter('affiliate_id', $affId);
		// Zend_Debug::dump($collection->getData());
		// die();
		$value = 0;
		foreach($collection as $item) {
			$value+= $item['commission'];
		}
		return Mage::helper('core')->currency($value);
	}
	
	public function getExcludeUrl($campaign_id) {
		return $this->getUrl('*/*/exclude_campaign', array('affiliate_id' => $this->getAffiliateId(), 'campaign_id' => $campaign_id, '_secure' => true));
	}
	
	public function getIncludedCampaign() {
		$affid = $this->getAffiliateId();
		$relation = Mage::getModel('affiliate/campaignrelation')->getCollection()
			->addFieldToFilter('affiliate_id', $affid);
		return $relation;
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
	
	public function getBanners() {
		$collection = Mage::getModel('affiliate/banner')->getCollection()
			->addFieldToFilter('status', 1);
		return $collection;
	}
	
	public function getMediaUrl() {
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
	}
	
	public function getBannerCode($banner_image) {
		$html = '';
		$affiliate_code = $this->getAffiliateCode();
		$affiliate_url = Mage::getBaseUrl() . '?affid=' . $affiliate_code;
		$image_source = $this->getMediaUrl() . 'affiliate/banner/' . $banner_image;
		$html .= "<a href='" . $affiliate_url . "'><img src='".$image_source."' /></a>";
		return $html;
	}
}