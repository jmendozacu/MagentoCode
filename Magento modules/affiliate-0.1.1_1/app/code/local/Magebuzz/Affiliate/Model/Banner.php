<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Banner extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('affiliate/banner');
	}	
}