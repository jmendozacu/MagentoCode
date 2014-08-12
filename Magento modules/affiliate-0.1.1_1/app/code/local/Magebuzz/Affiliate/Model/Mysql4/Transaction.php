<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Mysql4_Transaction extends Mage_Core_Model_Mysql4_Abstract {
	public function _construct() {    
		$this->_init('affiliate/transaction', 'transaction_id');
	}
}