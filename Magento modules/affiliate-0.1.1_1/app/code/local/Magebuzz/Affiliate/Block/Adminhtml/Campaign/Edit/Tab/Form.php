<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Campaign_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('campaign_form', array('legend'=>Mage::helper('affiliate')->__('Campaign Information')));
	 
		if ( Mage::getSingleton('adminhtml/session')->getCampaignData() ) {
			$data = Mage::getSingleton('adminhtml/session')->getCampaignData();			
			Mage::getSingleton('adminhtml/session')->getCampaignData(null);
		} elseif ( Mage::registry('campaign_data') ) {
			$data = Mage::registry('campaign_data')->getData();
		}
		
		$model = Mage::registry('campaign_data');
		
		
		$fieldset->addField('campaign_title', 'text', array(
			'label'     => Mage::helper('affiliate')->__('Campaign Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'campaign_title',
		));				
		
		$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
		
		$fieldset->addField('date_start', 'date', array(
			'label'		=> Mage::helper('affiliate')->__('Start Time'),
			'required'	=> false,
			'name'		=> 'date_start',
			'format' => $dateFormatIso, 
			'input_format' => $dateFormatIso,
			'time'		=> true,
			'image'  => $this->getSkinUrl('images/grid-cal.gif'),
			'style' => 'width: 140px;'
		));
		
		$fieldset->addField('date_end', 'date', array(
			'label'		=> Mage::helper('affiliate')->__('End Time'),			
			'required'	=> false,
			'name'		=> 'date_end',
			'format' => $dateFormatIso, 
			'input_format' => $dateFormatIso,
			'time'		=> true,
			'image'  => $this->getSkinUrl('images/grid-cal.gif'),
			'style' => 'width: 140px;'
		));
		
		$fieldset->addField('commission_type', 'select', array(
			'label'     => Mage::helper('affiliate')->__('Commission Type'),
			'name'      => 'commission_type',
			'values'    => array(
				array(
					'value'     => 1,
					'label'     => Mage::helper('affiliate')->__('Fixed flat rate'),
				),
				array(
					'value'     => 2,
					'label'     => Mage::helper('affiliate')->__('Fixed percent rate'),
				),
			),
		));
		
		$fieldset->addField('amount', 'text', array(
			'label'     => Mage::helper('affiliate')->__('Amount'),
			'class'     => 'required-entry validate-greater-than-zero',
			'required'  => true,
			'name'      => 'amount',
		));
		
		$fieldset->addField('auto_assign', 'select', array(
			'label'     => Mage::helper('affiliate')->__('Auto Assign to Affiliates'),
			'name'      => 'auto_assign',
			'values'    => array(
				array(
					'value'     => 1,
					'label'     => Mage::helper('affiliate')->__('Yes'),
				),
				array(
					'value'     => 2,
					'label'     => Mage::helper('affiliate')->__('No'),
				),
			),
		));
				
		$fieldset->addField('description', 'textarea', array(
			'label'     => Mage::helper('affiliate')->__('Description'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'description',
		));
		
		if (!Mage::app()->isSingleStoreMode()) {
			$fieldset->addField('store_id', 'multiselect', array(
				'name'      => 'stores[]',
				'label'     => Mage::helper('affiliate')->__('Store View'),
				'title'     => Mage::helper('affiliate')->__('Store View'),
				'required'  => true,
				'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));
		}
		else {
			$fieldset->addField('store_id', 'hidden', array(
				'name'      => 'stores[]',
				'value'     => Mage::app()->getStore(true)->getId()
			));
			$model->setStoreId(Mage::app()->getStore(true)->getId());
		}
		
		$fieldset->addField('priority', 'text', array(
			'label'     => Mage::helper('affiliate')->__('Priority'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'priority',
		));
		
		$fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('affiliate')->__('Status'),
			'name'      => 'status',
			'values'    => array(
				array(
					'value'     => 1,
					'label'     => Mage::helper('affiliate')->__('Enabled'),
				),
				array(
					'value'     => 2,
					'label'     => Mage::helper('affiliate')->__('Disabled'),
				),
			),
		));
		
		if ($data['date_start']) {
			$data['date_start'] = Mage::app()->getLocale()->date($data['date_start'], Varien_Date::DATETIME_INTERNAL_FORMAT, null, false);
		}
		if ($data['date_end']) { 
			$data['date_end'] = Mage::app()->getLocale()->date($data['date_end'], Varien_Date::DATETIME_INTERNAL_FORMAT, null, false);
		}
		$form->setValues($data);
		return parent::_prepareForm();
  }
}