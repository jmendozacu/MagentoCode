<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Banner extends Mage_Adminhtml_Block_Widget_Grid_Container {
  public function __construct() {
    $this->_controller = 'adminhtml_banner';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Manage Banner');
    $this->_addButtonLabel = Mage::helper('affiliate')->__('Add New Banner');
    parent::__construct();
  }
}