<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Account_Leftmenu extends Mage_Customer_Block_Account_Navigation {	
	public function _construct() {
		parent::_construct();
		$this->addLink('welcome', 'affiliate/index/index', 'Affiliate Welcome Page');
		if (Mage::getSingleton('customer/session')->isLoggedin()) {
			$this->addLink('index', 'affiliate/account/index', 'General Statistics');
			$this->addLink('campaign', 'affiliate/account/campaign', 'Campaigns');
			$this->addLink('material', 'affiliate/account/materials', 'Marketing Materials');
			$this->addLink('commission', 'affiliate/account/commission', 'Commissions');
			$this->addLink('transaction', 'affiliate/account/transaction', 'Transactions');
			$this->addLink('statistic', 'affiliate/account/statistic', 'Statistics');
			$this->addLink('payment', 'affiliate/account/viewPayment', 'Payment');
		}
		else {
			$this->addLink('login', 'customer/account/login', 'Log In');
			$this->addLink('signup', 'affiliate/index/signup', 'Sign Up');
		}
	}

}