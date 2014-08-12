<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Affiliate_Create_New_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('affiliate_form', array('legend'=>Mage::helper('affiliate')->__('Item information')));
		
		$customer = $this->getCustomer();
		$data = array();
		$customer_url = '';
		if ($customer) {
			$data['customer_name'] = $customer->getName();
			$data['customer_id'] = $customer->getId();
			$customer_url = Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit/",array("id" =>  $customer->getId()));
		}
		
		$fieldset->addField('customer_name', 'link', array(
				'label'     => Mage::helper('affiliate')->__('Customer'),
				'name'      => 'customer_name',
				'href'			=> $customer_url,
				'target'		=> '_blank'
		));		
		
		$fieldset->addField('customer_id', 'hidden', array(
				'name'      => 'customer_id'
		));
		
		$fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('affiliate')->__('Status'),
			'name'      => 'status',
			'values'    => array(
				array(
					'value'     => 1,
					'label'     => Mage::helper('affiliate')->__('Active'),
				),
				array(
					'value'     => 2,
					'label'     => Mage::helper('affiliate')->__('Inactive'),
				),
			),
		));
				
		$form->setValues($data);
		return parent::_prepareForm();
	}
	
	public function getCustomer() {
		$customer_id = $this->getRequest()->getParam('customer_id');
		if ($customer_id) {
			$customer = Mage::getModel('customer/customer')->load($customer_id);
			if ($customer->getId()) return $customer;
		}
		return false;
	}
}