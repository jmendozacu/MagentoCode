<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Session extends Mage_Core_Model_Session_Abstract {
	public function __construct() {
		$namespace = 'affiliate';
		$namespace .= '_' . (Mage::app()->getStore()->getWebsite()->getCode());
 
		$this->init($namespace);
		Mage::dispatchEvent('affiliate_session_init', array('affiliate_session' => $this));
	}
}