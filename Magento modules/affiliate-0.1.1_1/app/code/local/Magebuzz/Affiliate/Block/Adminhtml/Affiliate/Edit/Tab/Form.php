<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Affiliate_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('affiliate_form', array('legend'=>Mage::helper('affiliate')->__('Item information')));
	 
		if ( Mage::getSingleton('adminhtml/session')->getAffiliateData() ) {
			$data = Mage::getSingleton('adminhtml/session')->getAffiliateData();			
			Mage::getSingleton('adminhtml/session')->setAffiliateData(null);
		} elseif ( Mage::registry('affiliate_data') ) {
			$data = Mage::registry('affiliate_data')->getData();
		}
		
		$customer_id = $data['customer_id'];
		$customer = Mage::getModel('customer/customer')->load($customer_id);		
		
		$customer_url = Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit/",array("id" =>  $customer->getId()));
		$data['customer_name'] = $customer->getName();
		
		$fieldset->addField('customer_name', 'link', array(
				'label'     => Mage::helper('affiliate')->__('Customer'),
				'class'     => 'required-entry',
				'name'      => 'customer_name',
				'href'			=> $customer_url,
				'target'		=> '_blank'
		));		
		
		// $fieldset->addField('firstname', 'text', array(
				// 'label'     => Mage::helper('affiliate')->__('First Name'),
				// 'class'     => 'required-entry',
				// 'required'  => true,
				// 'name'      => 'firstname',
		// ));
		
		// $fieldset->addField('lastname', 'text', array(
				// 'label'     => Mage::helper('affiliate')->__('Last Name'),
				// 'class'     => 'required-entry',
				// 'required'  => true,
				// 'name'      => 'lastname',
		// ));
		
		// $fieldset->addField('email', 'text', array(
				// 'label'     => Mage::helper('affiliate')->__('Email'),
				// 'class'     => 'required-entry',
				// 'required'  => true,
				// 'name'      => 'email',
		// ));
		
		
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
		
		$data['current_balance'] = Mage::helper('core')->currency($data['current_balance'],true,false);
		
		$fieldset->addField('current_balance', 'label', array(
			'label'     => Mage::helper('affiliate')->__('Current Balance'),
			'name'      => 'current_balance',
		));
	 		
		$form->setValues($data);
		return parent::_prepareForm();
  }
}