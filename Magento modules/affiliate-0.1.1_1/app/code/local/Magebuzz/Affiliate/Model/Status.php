<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Status extends Varien_Object {
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 2;

	static public function getOptionArray() {
		return array(
			self::STATUS_ENABLED    => Mage::helper('affiliate')->__('Active'),
			self::STATUS_DISABLED   => Mage::helper('affiliate')->__('Inactive')
		);
	}
}