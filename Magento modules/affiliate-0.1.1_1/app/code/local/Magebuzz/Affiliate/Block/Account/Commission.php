<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Account_Commission extends Mage_Core_Block_Template {
	public function __construct() {
		parent::__construct();
		$this->setTemplate('affiliate/account/commission.phtml');
		$commissions = Mage::getModel('affiliate/commission')->getCollection()
			->addFilter('affiliate_id', Mage::helper('affiliate')->isAffiliate());
		$this->setCommissions($commissions);
		Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('affiliate')->__('My Commissions'));
	}
	
	protected function _prepareLayout() {
		parent::_prepareLayout();

		$pager = $this->getLayout()->createBlock('page/html_pager', 'affiliate.commission.history.pager')
			->setCollection($this->getCommissions());
		$this->setChild('pager', $pager);
		$this->getCommissions()->load();
		return $this;
	}

  public function getPagerHtml() {
    return $this->getChildHtml('pager');
	}
}