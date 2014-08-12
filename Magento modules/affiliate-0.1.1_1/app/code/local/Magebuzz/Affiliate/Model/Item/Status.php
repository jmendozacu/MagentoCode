<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Item_Status extends Varien_Object {
	static public function toOptionArray() {
		$statusArray = Mage::getModel('sales/order_item')->getStatuses();
		$returnArray = array();
		foreach($statusArray as $key => $value) {
			array_push($returnArray, array(
										'value' => $key,
										'label'	=> $value
			));
		}
		return $returnArray;
	}
}