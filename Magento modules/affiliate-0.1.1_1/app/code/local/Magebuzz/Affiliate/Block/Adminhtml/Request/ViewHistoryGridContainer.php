<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Request_ViewHistoryGridContainer extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct()  {
		parent::__construct();
		$this->_headerText = Mage::helper('affiliate')->__('Payment History');
		$this->removeButton('add');
	}
	
	protected function _prepareLayout() {
		$this->setChild('grid', $this->getLayout()->createBlock('affiliate/adminhtml_request_viewHistoryGrid')->setSaveParametersInSession(true));
	}
}