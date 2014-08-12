<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Campaign_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
  public function __construct() {
		parent::__construct();
		$this->setId('campaign_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('affiliate')->__('Campaign Information'));
  }

  protected function _beforeToHtml() {
		$this->addTab('form_section', array(
			'label'     => Mage::helper('affiliate')->__('Campaign Information'),
			'title'     => Mage::helper('affiliate')->__('Campaign Information'),
			'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_campaign_edit_tab_form')->toHtml(),
		));
		
		$this->addTab('product_rule', array(
			'label'     => Mage::helper('affiliate')->__('Product Rules'),
			'title'     => Mage::helper('affiliate')->__('Product Rules'),
			// 'class'			=> 'ajax',
			//'url'				=> $this->getUrl('affiliate/adminhtml_campaign/categories', array('_current'=>true,'id'=>$this->getRequest()->getParam('id'))),
			'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_campaign_edit_tab_categories')->toHtml(),
		));	
	 
		return parent::_beforeToHtml();
  }
}