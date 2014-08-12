<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Affiliate_Create extends Mage_Adminhtml_Block_Widget_Grid_Container {
  public function __construct() {
    $this->_controller = 'adminhtml_affiliate_create';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Please Select a Customer');
    parent::__construct();
		$this->_removeButton('add');
		$this->_removeButton('reset');
		$this->_removeButton('search');
  }
}