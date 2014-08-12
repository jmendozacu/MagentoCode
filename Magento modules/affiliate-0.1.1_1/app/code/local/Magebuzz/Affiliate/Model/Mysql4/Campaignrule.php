<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Mysql4_Campaignrule extends Mage_Core_Model_Mysql4_Abstract {
	public function _construct() {    
		$this->_init('affiliate/campaignrule', 'rule_id');
	}
}