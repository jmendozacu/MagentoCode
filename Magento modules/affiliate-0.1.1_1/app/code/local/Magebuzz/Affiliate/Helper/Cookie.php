<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Helper_Cookie extends Mage_Core_Helper_Abstract {
	public function getAffiliateInfo() {
		$info = array();
		$cookie = Mage::getSingleton('core/cookie');
		$map_index = $cookie->get('magebuzz_cookie_index');
		for($i = $map_index; $i>0; $i--) {
			$affiliate_code = $cookie->get('map_affiliate_code_'.$i);
			if($affiliate_code) {
				$info[] = $affiliate_code;
			}
		}
		return $info;
	}
}