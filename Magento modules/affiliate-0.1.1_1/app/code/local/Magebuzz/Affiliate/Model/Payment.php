<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Payment extends Mage_Core_Model_Abstract {
	public function _construct() {    
		$this->_init('affiliate/payment', 'request_id');
	}
	
	public function getRequestedCollection($affId) {
		$paymentCollection = $this->getCollection();
		$paymentCollection->addFilter('affiliate_id', $affId);
		$paymentCollection->addFieldToFilter('status',array('in'=>array(2, 3)));
		return $paymentCollection->getData();
	}
	
	public function getCurrentBalance($affId) {
		$total = array_sum(Mage::getModel('affiliate/commission')->getCollection()->addFilter('affiliate_id', $affId)->getColumnValues('amount'));
		return $total - $this->getTotalAmountRequested($affId);
	}
	
	public function getRequestCollection($affId) {
		$paymentCollection = $this->getCollection();
		$paymentCollection->addFilter('affiliate_id', $affId);
		$paymentCollection->addFilter('status', 1);
		return $paymentCollection;
	}

	public function getResponseCollection($affId) 
	{
		$paymentCollection = $this->getCollection();
		$paymentCollection->addFilter('affiliate_id', $affId);
		$paymentCollection->addFieldToFilter('status',array('in'=>array(2, 3)));
		return $paymentCollection;
	}
	
	public function getRequestingCollection($affId)
	{
		$paymentCollection = $this->getCollection();
		$paymentCollection->addFilter('affiliate_id', $affId);
		$paymentCollection->addFilter('status', 1);
		return $paymentCollection->getData();
	}
	
	public function getTotalAmountRequested($affId) 
	{
		$paymentCollection = $this->getCollection();
		$paymentCollection->addFilter('affiliate_id', $affId);
		$paymentCollection->addFilter('status', 3);
		return array_sum($paymentCollection->getColumnValues('request_amount'));
	}
	
	public function getTotalAmountRequesting($affId) 
	{
		$paymentCollection = $this->getCollection();
		$paymentCollection->addFilter('affiliate_id', $affId);
		$paymentCollection->addFilter('status', 1);
		return array_sum($paymentCollection->getColumnValues('request_amount'));
	}
	
	public function getAvailableRequestAmount($affId) 
	{
		$total = array_sum(Mage::getModel('affiliate/commission')->getCollection()->addFilter('affiliate_id', $affId)->getColumnValues('amount'));
		return $total - $this->getTotalAmountRequested($affId) - $this->getTotalAmountRequesting($affId);
	}
}