<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Account_Transaction extends Mage_Core_Block_Template {
	public function __construct() {
		parent::__construct();
		$this->setTemplate('affiliate/account/transaction.phtml');
		$orders = Mage::getModel('affiliate/transaction')->getCollection()
			->addFilter('affiliate_id', Mage::helper('affiliate')->isAffiliate());
		$this->setOrders($orders);

		Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('affiliate')->__('My Transactions'));
	}
	
	protected function _prepareLayout() {
		parent::_prepareLayout();

		$pager = $this->getLayout()->createBlock('page/html_pager', 'affiliate.transaction.history.pager')
			->setCollection($this->getOrders());
		$this->setChild('pager', $pager);
		$this->getOrders()->load();
		return $this;
	}

	public function getPagerHtml() {
		return $this->getChildHtml('pager');
	}
}