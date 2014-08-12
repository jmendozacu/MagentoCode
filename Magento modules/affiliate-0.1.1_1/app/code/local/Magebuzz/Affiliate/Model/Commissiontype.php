<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Commissiontype extends Varien_Object {
	const COMMISSION_FLAT_RATE		= 1;
	const COMMISSION_PERCENT_RATE	= 2;

	static public function getOptionArray() {
		return array(
			self::COMMISSION_FLAT_RATE    	=> Mage::helper('affiliate')->__('Fixed flat rate'),
			self::COMMISSION_PERCENT_RATE   => Mage::helper('affiliate')->__('Fixed percent rate')
		);
	}
}