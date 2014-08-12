<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Banner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('banner_form', array('legend'=>Mage::helper('affiliate')->__('Item information')));
	 
		if ( Mage::getSingleton('adminhtml/session')->getBannerData() ) {
			$data = Mage::getSingleton('adminhtml/session')->getBannerData();			
			Mage::getSingleton('adminhtml/session')->setBannerData(null);
		} elseif ( Mage::registry('banner_data') ) {
			$data = Mage::registry('banner_data')->getData();
		}
		
		if ($data['file'] && $data['file'] != '') {
			$data['file'] = 'affiliate/banner/' . $data['file'];
		}
		

		$fieldset->addField('title', 'text', array(
			'label'     => Mage::helper('affiliate')->__('Title'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'title',
		));
		
		$fieldset->addField('file', 'image', array(
			'label'     => Mage::helper('affiliate')->__('File'),
			'required'  => false,
			'name'      => 'file',
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
}