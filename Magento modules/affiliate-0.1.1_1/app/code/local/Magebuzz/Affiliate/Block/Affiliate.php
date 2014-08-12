<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Affiliate extends Mage_Core_Block_Template {
	public function _prepareLayout() {
		return parent::_prepareLayout();
  }

	public function getSignupUrl() {
		return $this->getUrl('affiliate/index/signup');
	}
	
	public function getLogInUrl() {
		if(!Mage::helper('affiliate')->isAffiliate()) return $this->getUrl('customer/account/login');
		else return $this->getUrl('affiliate/account/index');
	}
	
	public function showAddressFields() {
		return true;
	}
	
	public function getBackUrl() {
		$url = $this->getData('back_url');
		if (is_null($url)) {
			$url = $this->getUrl('affiliate/index/index');
		}
		return $url;
	}
	
	public function getCountryHtmlSelect($type) {
		$countryId = Mage::helper('core')->getDefaultCountry();
		$select = $this->getLayout()->createBlock('core/html_select')
			->setName('country_id')
			->setId('country_id')
			->setTitle(Mage::helper('checkout')->__('Country'))
			->setClass('validate-select')
			->setValue($countryId)
			->setOptions($this->getCountryOptions());
		return $select->getHtml();
	}
	
	public function getCountryOptions() {
		$options    = false;
		$useCache   = Mage::app()->useCache('config');
		if ($useCache) {
			$cacheId    = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
			$cacheTags  = array('config');
			if ($optionsCache = Mage::app()->loadCache($cacheId)) {
				$options = unserialize($optionsCache);
			}
		}

		if ($options == false) {
			$options = $this->getCountryCollection()->toOptionArray();
			if ($useCache) {
				Mage::app()->saveCache(serialize($options), $cacheId, $cacheTags);
			}
		}
		return $options;
	}
	
	public function getCountryCollection() {
		if (!$this->_countryCollection) {
			$this->_countryCollection = Mage::getSingleton('directory/country')->getResourceCollection()
				->loadByStore();
		}
		return $this->_countryCollection;
	}
	
	public function getPostActionUrl() {
		return $this->getUrl('*/*/signup_post', array("_secure" => true));
	}
	
	public function isCustomerLoggedIn() {
		return $this->_getCustomerSession()->isLoggedIn();
	}
	
	public function getCustomerId() {
		return $this->_getCustomerSession()->getCustomerId();
	}
	
	protected function _getCustomerSession() {
		return Mage::getSingleton('customer/session');
	}
}