<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Affiliate_Grid extends Mage_Adminhtml_Block_Widget_Grid {
  public function __construct() {
		parent::__construct();
		$this->setId('affiliateGrid');
		$this->setDefaultSort('affiliate_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection() {
		$collection = Mage::getModel('affiliate/affiliate')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
  }

  protected function _prepareColumns() {
		$this->addColumn('affiliate_id', array(
			'header'    => Mage::helper('affiliate')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'affiliate_id',
		));

		$this->addColumn('firstname', array(
			'header'    => Mage::helper('affiliate')->__('First Name'),
			'align'     =>'left',
			'index'     => 'firstname',
		));
		
		$this->addColumn('lastname', array(
			'header'    => Mage::helper('affiliate')->__('Last Name'),
			'align'     =>'left',
			'index'     => 'lastname',
		));
		
		$this->addColumn('email', array(
			'header'    => Mage::helper('affiliate')->__('Email'),
			'align'     =>'left',
			'index'     => 'email',
		));
		
		$store = $this->_getStore();
		$this->addColumn('current_balance', array(
			'header'    => Mage::helper('affiliate')->__('Current Balance'),
			'align'     =>'left',
			'type'  => 'price',
			'currency_code' => $store->getBaseCurrency()->getCode(),
			'index'     => 'current_balance',
		));
		
		$this->addColumn('total_withdrawn', array(
			'header'    => Mage::helper('affiliate')->__('Total Withdrawn'),
			'align'     =>'left',
			'type'  => 'price',
			'currency_code' => $store->getBaseCurrency()->getCode(),
			'index'     => 'total_withdrawn',
		));

		$this->addColumn('status', array(
			'header'    => Mage::helper('affiliate')->__('Status'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'status',
			'type'      => 'options',
			'options'   => array(
					1 => 'Active',
					2 => 'Inactive'
			),
		));

		// $this->addColumn('action',
			// array(
				// 'header'    =>  Mage::helper('affiliate')->__('Action'),
				// 'width'     => '100',
				// 'type'      => 'action',
				// 'getter'    => 'getId',
				// 'actions'   => array(
						// array(
								// 'caption'   => Mage::helper('affiliate')->__('Edit'),
								// 'url'       => array('base'=> '*/*/edit'),
								// 'field'     => 'id'
						// )
				// ),
				// 'filter'    => false,
				// 'sortable'  => false,
				// 'index'     => 'stores',
				// 'is_system' => true,
		// ));		
		return parent::_prepareColumns();
  }
	
	protected function _getStore(){
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('affiliate_id');
		$this->getMassactionBlock()->setFormFieldName('affiliate');

		$this->getMassactionBlock()->addItem('delete', array(
				 'label'    => Mage::helper('affiliate')->__('Delete'),
				 'url'      => $this->getUrl('*/*/massDelete'),
				 'confirm'  => Mage::helper('affiliate')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('affiliate/status')->getOptionArray();
		
		array_unshift($statuses, array('label'=>'', 'value'=>''));
		
		$this->getMassactionBlock()->addItem('status', array(
				 'label'=> Mage::helper('affiliate')->__('Change status'),
				 'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
				 'additional' => array(
								'visibility' => array(
										 'name' => 'status',
										 'type' => 'select',
										 'class' => 'required-entry',
										 'label' => Mage::helper('affiliate')->__('Status'),
										 'values' => $statuses
								 )
				 )
		));
		return $this;
	}

  public function getRowUrl($row) {
    return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}