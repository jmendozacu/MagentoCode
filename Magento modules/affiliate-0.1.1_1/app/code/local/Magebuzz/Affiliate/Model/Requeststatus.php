<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Requeststatus extends Varien_Object {
	const STATUS_COMPLETE	= 3;
	const STATUS_REJECTED	= 2;

	static public function getOptionArray() {
		return array(
			self::STATUS_COMPLETE    => Mage::helper('affiliate')->__('Accept'),
			self::STATUS_REJECTED   => Mage::helper('affiliate')->__('Reject')
		);
	}
}