<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Account_Payment extends Mage_Core_Block_Template {
  public function __construct() {
		parent::__construct();
		$this->setTemplate('affiliate/account/payment.phtml');
		$requests = Mage::getModel('affiliate/payment')->getRequestCollection(Mage::helper('affiliate')->isAffiliate());
		$this->setRequests($requests);
		$responses = Mage::getModel('affiliate/payment')->getResponseCollection(Mage::helper('affiliate')->isAffiliate());
		$this->setResponses($responses);
		Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('affiliate')->__('My Payments'));
	}
		
	// $transactionCollection is in array type
	public function getTotalValue($transactionCollection) {
		$total = 0;
		foreach($transactionCollection as $transaction) {
			$total += $transaction['commission'];
		}
		return $total;
	}
	
	// $commissionCollection is in array type
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

	protected function _prepareLayout() {
		parent::_prepareLayout();
		$pager = $this->getLayout()->createBlock('page/html_pager', 'affiliate.payment.request.history.pager')
			->setCollection($this->getRequests());
		$this->setChild('pager', $pager);
		$pager2 = $this->getLayout()->createBlock('page/html_pager', 'affiliate.payment.response.history.pager')
			->setCollection($this->getResponses());
		$this->setChild('pager2', $pager2);
		$this->getResponses()->load();
		return $this;
	}

	public function getPagerHtml() {
		return $this->getChildHtml('pager');
	}

	public function getPager2Html() {
			return $this->getChildHtml('pager2');
	}
}