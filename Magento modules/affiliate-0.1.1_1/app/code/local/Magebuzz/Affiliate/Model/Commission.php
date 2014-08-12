<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Commission extends Mage_Core_Model_Abstract  {
	public function _construct() {    
		$this->_init('affiliate/commission', 'commission_id');
	}
}