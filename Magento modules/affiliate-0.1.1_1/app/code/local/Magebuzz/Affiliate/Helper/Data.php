<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Helper_Data extends Mage_Core_Helper_Abstract {
	public function transactionStatisticToJsInput() {
		$affId = $this->isAffiliate();
		$commissionCollection = Mage::getModel('affiliate/transaction')->getCollection();
		// $commissionCollection->setOrder('created', 'ASC');
		$firstDate 	= new Zend_Date(Mage::getModel('affiliate/affiliate')->load($affId)->getCreatedAt(), Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate 	= new Zend_Date(null, Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate->addDay(1);
		$string = null;
		while($firstDate <= $lastDate)
		{	
			$commissionCollection = Mage::getModel('affiliate/transaction')->getCollection();
			$string .= '[\''. $firstDate->toString('yyyy-MM-dd').'\',';
			$commissionCollection->addFieldToFilter('affiliate_id', $affId);
			$commissionCollection->addFieldToFilter('created', array(
																'from' 	=> $firstDate->toString('yyyy-MM-dd'),
																'to' 	=> $firstDate->addDay(1)->toString('yyyy-MM-dd'),
																'date' 	=> true, // specifies conversion of comparison values
																));
			$value = 0;
			foreach($commissionCollection->getData() as $item)
			{
				$value += $item['commission'];
			}
			$string .= $value.'],';
		}
		$string = substr($string, 0, -1);
		return $string;
	}
	
	public function commissionStatisticToJsInput() {
		$affId = $this->isAffiliate();
		$commissionCollection = Mage::getModel('affiliate/commission')->getCollection();
		$commissionCollection->setOrder('date_added', 'ASC');
		$firstDate 	= new Zend_Date(Mage::getModel('affiliate/affiliate')->load($affId)->getCreatedAt(), Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate 	= new Zend_Date(null, Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate->addDay(1);
		$string = null;
		while($firstDate <= $lastDate)
		{	
			$commissionCollection = Mage::getModel('affiliate/commission')->getCollection();
			$string .= '[\''. $firstDate->toString('yyyy-MM-dd').'\',';
			$commissionCollection->addFieldToFilter('affiliate_id', $affId);
			$commissionCollection->addFieldToFilter('date_added', array(
																'from' 	=> $firstDate->toString('yyyy-MM-dd'),
																'to' 	=> $firstDate->addDay(1)->toString('yyyy-MM-dd'),
																'date' 	=> true, // specifies conversion of comparison values
																));
			$value = 0;
			foreach($commissionCollection->getData() as $item)
			{
				$value += $item['amount'];
			}
			$string .= $value.'],';
		}
		$string = substr($string, 0, -1);
		return $string;
	}
	
	public function paymentStatisticToJsInput() {
		$affId = $this->isAffiliate();
		$commissionCollection = Mage::getModel('affiliate/payment')->getCollection();
		$commissionCollection->setOrder('request_time', 'ASC');
		$firstDate 	= new Zend_Date(Mage::getModel('affiliate/affiliate')->load($affId)->getCreatedAt(), Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate 	= new Zend_Date(null, Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate->addDay(1);
		$string = null;
		while($firstDate <= $lastDate)
		{	
			$commissionCollection = Mage::getModel('affiliate/payment')->getCollection();
			$commissionCollection->addFilter('status', 3);
			$string .= '[\''. $firstDate->toString('yyyy-MM-dd').'\',';
			$commissionCollection->addFieldToFilter('affiliate_id', $affId);
			$commissionCollection->addFieldToFilter('request_time', array(
																'from' 	=> $firstDate->toString('yyyy-MM-dd'),
																'to' 	=> $firstDate->addDay(1)->toString('yyyy-MM-dd'),
																'date' 	=> true, // specifies conversion of comparison values
																));
			$value = 0;
			foreach($commissionCollection->getData() as $item)
			{
				$value += $item['request_amount'];
			}
			$string .= $value.'],';
		}
		$string = substr($string, 0, -1);
		// Zend_Debug::dump($string);
		// die();
		return $string;
	}
	
	public function convertCommissionToJsInputType() {
		$commissionCollection = Mage::getModel('affiliate/commission')->getCollection();
		$commissionCollection->setOrder('date_added', 'ASC');
		$firstDate 	= new Zend_Date($commissionCollection->getFirstItem()->getDateAdded(), Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate 	= new Zend_Date(null, Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate->addDay(1);
		$string = null;
		while($firstDate <= $lastDate)
		{	
			$commissionCollection = Mage::getModel('affiliate/commission')->getCollection();
			$string .= '[\''. $firstDate->toString('yyyy-MM-dd').'\',';
			$commissionCollection->addFieldToFilter('date_added', array(
																'from' 	=> $firstDate->toString('yyyy-MM-dd'),
																'to' 	=> $firstDate->addDay(1)->toString('yyyy-MM-dd'),
																'date' 	=> true, // specifies conversion of comparison values
																));
			$value = 0;
			foreach($commissionCollection->getData() as $item)
			{
				$value += $item['amount'];
			}
			$string .= $value.'],';
		}
		$string = substr($string, 0, -1);
		// Zend_Debug::dump($string);
		// die();
		return $string;
	}
	
	public function convertPaymentResponseToJsInputType() {
		$commissionCollection = Mage::getModel('affiliate/payment')->getCollection();
		$commissionCollection->setOrder('request_time', 'ASC');
		$firstDate 	= new Zend_Date($commissionCollection->getFirstItem()->getRequestTime(), Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate 	= new Zend_Date(null, Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate->addDay(1);
		$string = null;
		while($firstDate <= $lastDate)
		{	
			$commissionCollection = Mage::getModel('affiliate/payment')->getCollection();
			$commissionCollection->addFilter('status', 3);
			$string .= '[\''. $firstDate->toString('yyyy-MM-dd').'\',';
			$commissionCollection->addFieldToFilter('request_time', array(
																'from' 	=> $firstDate->toString('yyyy-MM-dd'),
																'to' 	=> $firstDate->addDay(1)->toString('yyyy-MM-dd'),
																'date' 	=> true, // specifies conversion of comparison values
																));
			$value = 0;
			foreach($commissionCollection->getData() as $item)
			{
				$value += $item['request_amount'];
			}
			$string .= $value.'],';
		}
		$string = substr($string, 0, -1);
		// Zend_Debug::dump($string);
		// die();
		return $string;
	}
	
	public function getTransactionDateTicks() 
	{
		$commissionCollection = Mage::getModel('affiliate/transaction')->getCollection();
		$commissionCollection->setOrder('created', 'ASC');
		$firstDate 	= new Zend_Date($commissionCollection->getFirstItem($commissionCollection)->getCreated(), Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate 	= new Zend_Date(null, Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate->addDay(1);
		$string = null;
		while($firstDate <= $lastDate)
		{	
			$string .= '\'' . $firstDate->toString('dd-MM-yyyy').'\',';
			$firstDate->addDay(1);
		}
		$string = substr($string, 0, -1);
		return $string;
	}
	
	public function convertTransactionToJsInputType() {
		$commissionCollection = Mage::getModel('affiliate/transaction')->getCollection();
		$commissionCollection->setOrder('created', 'ASC');
		$firstDate 	= new Zend_Date($commissionCollection->getFirstItem()->getCreated(), Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate 	= new Zend_Date(null, Varien_Date::DATETIME_INTERNAL_FORMAT, null);
		$lastDate->addDay(1);
		$string = null;
		while($firstDate <= $lastDate)
		{	
			$commissionCollection = Mage::getModel('affiliate/transaction')->getCollection();
			$string .= '[\''. $firstDate->toString('yyyy-MM-dd').'\',';
			$commissionCollection->addFieldToFilter('created', array(
																'from' 	=> $firstDate->toString('yyyy-MM-dd'),
																'to' 	=> $firstDate->addDay(1)->toString('yyyy-MM-dd'),
																'date' 	=> true, // specifies conversion of comparison values
																));
			$value = 0;
			foreach($commissionCollection->getData() as $item)
			{
				$value += $item['commission'];
			}
			$string .= $value.'],';
		}
		$string = substr($string, 0, -1);
		return $string;
	}

	public function requestStatusToString($status) {
		if($status == '2') return 'Rejected';
		if($status == '3') return 'Completed';
	}
	
	public function getAffiliate() {
		$result = $this->isAffiliate();
		if($result) return Mage::getModel('affiliate/affiliate')->load($result);
		else return false;
	}

	// if aff return id, else return false
	public function isAffiliate() {
		$result = Mage::getModel('affiliate/affiliate')->loadByCustomerId(Mage::helper('customer')->getCustomer()->getId());
		return $result;
	}

	public function getItemStatusConfig() {
		$storeId = Mage::app()->getStore()->getId();
		return explode(',', Mage::getStoreConfig('affiliate/general/switch_status', $storeId));
	}
	
	public function getAffInfo() {
		if (isset($_COOKIE['affiliate_code'])) {
			$cookieCode = $_COOKIE['affiliate_code'];		
			$aff = Mage::getModel('affiliate/affiliate')->getCollection()->addFilter('referral_code', $cookieCode)->getData();
			if (is_array($aff) && isset($aff[0])) return $aff[0];
		}
		return false;
	}
	
	public function getAffiliateHomePage() {
		$homepage = Mage::getStoreConfig('affiliate/general/welcome_page');
		return $homepage;
	}
	
	public function generateReferralCode($email, $time) {
		return md5($email.$time);
	}
	
	public function renameImage($image_name) {
		$string = str_replace("  "," ",$image_name);
		$new_image_name = str_replace(" ","-",$string);
		$new_image_name = strtolower($new_image_name);		
		return $new_image_name;	
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
	
	public function getCookiedAffiliate() {
		$storeId = Mage::app()->getStore()->getId();
		$affiliateInfo = Mage::helper('affiliate/cookie')->getAffiliateInfo();
	}
	
	public function getBenefit($item, $campaign) {
		$commission_type = $campaign->getCommissionType();
		$amount = $campaign->getAmount();
		if ($commission_type == 1) {
			return $amount * $item->getQtyOrdered();
		} else {
			return $amount * $item->getBaseRowTotal() / 100 ;
		}
	}
		
	public function getCampaignArray($campaign, $storeId) {
		// check if available to all store
		$config = Mage::getModel('affiliate/campaign')->load($campaign)->getStoreId();
		if(in_array('0', $config)) {
			$campaigns = array();
			$table  = Mage::getSingleton('core/resource')->getTableName('affiliate_campaign_store');
			$read = Mage::getSingleton('core/resource')->getConnection('core_read');
			$sql = 'SELECT `campaign_id` FROM `'. $table . '` WHERE `store_id` = 0';
			$result = $read->fetchAll($sql);
			foreach($result as $item) {
				array_push($campaigns, $item['campaign_id']);
			}
			return $campaigns;
		} else {
		// if not available to all store
			$campaigns = array();
			$table  = 'affiliate_campaign_store';
			$read = Mage::getSingleton('core/resource')->getConnection('core_read');
			$sql = 'SELECT `campaign_id` FROM `'. $table . '` WHERE `store_id` = ' . $storeId;
			$result = $read->fetchAll($sql);
			foreach($result as $item) {
				array_push($campaigns, $item['campaign_id']);
			}
			return $campaigns;
		}
	}
	
	
	// return false if product is NOT in any campaign
	// return id of the campaign that bring the most value for affiliate user
	// $item is the item in cart after checkout that have the specific value
	// important : NOT AFFECTED BY STORE ***
	public function inCampaign($item, $campaigns, $storeId) {
		if (!count($campaigns)) {
			return false;
		}
		$returnCampaign = false;
		$value = 0;

		// $campaigns is an array that contains campaign ids of $person.
		$campaignModel = Mage::getModel('affiliate/campaign');
		$quoteItemId = $item->getQuoteItemId();
		
		$quoteItem = Mage::getSingleton('checkout/cart')->getQuote()->getItemsCollection()
			->addFieldToFilter('item_id', $quoteItemId)->getFirstItem();
		
		foreach ($campaigns as $campaign) {
			$campaignInstance = $campaignModel->load($campaign);
			$rule = Mage::getModel('affiliate/campaignrule')->load($campaignInstance->getRule());
			
			if($rule->getActions()->validate($quoteItem)&& in_array($campaign, $this->getCampaignArray($campaign, $storeId))) {
				$benefit = $this->getBenefit($item, $campaignInstance);
				if($benefit > $value) {
					$value = $benefit;
					$returnCampaign = $campaign;
				}
			}
		}
		return $returnCampaign;
	}
	
}