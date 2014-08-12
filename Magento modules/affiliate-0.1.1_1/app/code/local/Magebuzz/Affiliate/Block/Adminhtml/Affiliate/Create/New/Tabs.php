<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Affiliate_Create_New_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
  public function __construct() {
		parent::__construct();
		$this->setId('affiliate_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('affiliate')->__('Affiliate Information'));
  }

  protected function _beforeToHtml() {
		$this->addTab('affiliate_information', array(
			'label'     => Mage::helper('affiliate')->__('Affiliate Information'),
			'title'     => Mage::helper('affiliate')->__('Affiliate Information'),
			'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliate_create_new_tab_form')->toHtml(),
		));
	 
		return parent::_beforeToHtml();
  }
}