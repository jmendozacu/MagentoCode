<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Campaign extends Mage_Adminhtml_Block_Widget_Grid_Container {
  public function __construct() {
    $this->_controller = 'adminhtml_campaign';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Manage Campaigns');
    $this->_addButtonLabel = Mage::helper('affiliate')->__('Add New Campaign');
    parent::__construct();
  }
}