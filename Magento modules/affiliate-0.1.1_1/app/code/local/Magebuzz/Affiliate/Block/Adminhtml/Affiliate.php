<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Affiliate extends Mage_Adminhtml_Block_Widget_Grid_Container {
  public function __construct() {
    $this->_controller = 'adminhtml_affiliate';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Affiliate Account');
    $this->_addButtonLabel = Mage::helper('affiliate')->__('Add New Affiliate');
    parent::__construct();
  }
	
	public function getCreateUrl() {
		return $this->getUrl('*/adminhtml_affiliate_create/index');
	}
}