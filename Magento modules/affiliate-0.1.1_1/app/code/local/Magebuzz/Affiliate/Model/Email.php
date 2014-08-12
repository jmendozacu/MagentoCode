<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Email extends Varien_Object {
	static public function toOptionArray() {
		return array(
			array('value' => 0, 'label'	=> Mage::helper('affiliate')->__('Disable All Email Notification')),
			array('value' => 1, 'label'	=> Mage::helper('affiliate')->__('New User Sign Up')),
			array('value' => 2, 'label'	=> Mage::helper('affiliate')->__('User Account Approved')),
			array('value' => 3, 'label'	=> Mage::helper('affiliate')->__('User Account Locked')),
			array('value' => 4, 'label'	=> Mage::helper('affiliate')->__('User Request Money')),
			array('value' => 5, 'label'	=> Mage::helper('affiliate')->__('Admin Accept Payment')),
			array('value' => 6, 'label'	=> Mage::helper('affiliate')->__('Admin Reject Payment')),
			array('value' => 7, 'label'	=> Mage::helper('affiliate')->__('Admin Create Campaign'))
		);
	}
}