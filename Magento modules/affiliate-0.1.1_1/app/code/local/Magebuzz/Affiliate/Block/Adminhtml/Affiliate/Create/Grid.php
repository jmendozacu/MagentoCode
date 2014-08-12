<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Affiliate_Create_Grid extends Mage_Adminhtml_Block_Widget_Grid {
  public function __construct() {
		parent::__construct();
		$this->setId('newAffiliateGrid');
		$this->setUseAjax(true);
    $this->setDefaultSort('entity_id');
		$this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection() {
		$affEmails = Mage::getModel('affiliate/affiliate')->getCollection()->getColumnValues('email');
		// Zend_Debug::dump($affEmails);
		// die();
		$collection = Mage::getResourceModel('customer/customer_collection')
				->addNameToSelect()
				->addAttributeToSelect('email')
				->addAttributeToSelect('created_at')
				->addAttributeToSelect('group_id')
				->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
				->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
				->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
				->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
				->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');
		if($affEmails)
			$collection->addFieldToFilter('email', array('nin'=>$affEmails));
		$this->setCollection($collection);
		return parent::_prepareCollection();
  }

  protected function _prepareColumns() {
		$this->addColumn('entity_id', array(
				'header'    => Mage::helper('customer')->__('ID'),
				'width'     => '50px',
				'index'     => 'entity_id',
				'type'  => 'number',
		));

		$this->addColumn('name', array(
				'header'    => Mage::helper('customer')->__('Name'),
				'index'     => 'name'
		));
		$this->addColumn('email', array(
				'header'    => Mage::helper('customer')->__('Email'),
				'width'     => '150',
				'index'     => 'email'
		));

		$groups = Mage::getResourceModel('customer/group_collection')
				->addFieldToFilter('customer_group_id', array('gt'=> 0))
				->load()
				->toOptionHash();

		$this->addColumn('group', array(
				'header'    =>  Mage::helper('customer')->__('Group'),
				'width'     =>  '100',
				'index'     =>  'group_id',
				'type'      =>  'options',
				'options'   =>  $groups,
		));

		$this->addColumn('Telephone', array(
				'header'    => Mage::helper('customer')->__('Telephone'),
				'width'     => '100',
				'index'     => 'billing_telephone'
		));

		$this->addColumn('billing_postcode', array(
				'header'    => Mage::helper('customer')->__('ZIP'),
				'width'     => '90',
				'index'     => 'billing_postcode',
		));

		$this->addColumn('billing_country_id', array(
				'header'    => Mage::helper('customer')->__('Country'),
				'width'     => '100',
				'type'      => 'country',
				'index'     => 'billing_country_id',
		));

		$this->addColumn('billing_region', array(
				'header'    => Mage::helper('customer')->__('State/Province'),
				'width'     => '100',
				'index'     => 'billing_region',
		));

		$this->addColumn('customer_since', array(
				'header'    => Mage::helper('customer')->__('Customer Since'),
				'type'      => 'datetime',
				'align'     => 'center',
				'index'     => 'created_at',
				'gmtoffset' => true
		));

		if (!Mage::app()->isSingleStoreMode()) {
				$this->addColumn('website_id', array(
						'header'    => Mage::helper('customer')->__('Website'),
						'align'     => 'center',
						'width'     => '80px',
						'type'      => 'options',
						'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
						'index'     => 'website_id',
				));
		}		
		return parent::_prepareColumns();
	}

	

  public function getRowUrl($row) {
    return $this->getUrl('*/adminhtml_affiliate_create/new', array('customer_id' => $row->getId()));
  }

	public function getGridUrl() {
		return $this->getUrl('*/adminhtml_affiliate_create/grid');
	}

}