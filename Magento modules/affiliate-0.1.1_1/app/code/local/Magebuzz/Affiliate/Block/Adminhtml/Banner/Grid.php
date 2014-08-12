<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid {
  public function __construct() {
		parent::__construct();
		$this->setId('bannerGrid');
		$this->setDefaultSort('banner_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection() {
		$collection = Mage::getModel('affiliate/banner')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
  }

  protected function _prepareColumns() {
		$this->addColumn('banner_id', array(
			'header'    => Mage::helper('affiliate')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'banner_id',
		));

		$this->addColumn('title', array(
			'header'    => Mage::helper('affiliate')->__('Title'),
			'align'     =>'left',
			'index'     => 'title',
		));
		
		$this->addColumn('file', array(
			'header'    => Mage::helper('affiliate')->__('File'),
			'align'     =>'left',
			'index'     => 'file',
		));

		$this->addColumn('status', array(
			'header'    => Mage::helper('affiliate')->__('Status'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'status',
			'type'      => 'options',
			'options'   => array(
					1 => 'Active',
					0 => 'Inactive',
			),
		));
	
		$this->addColumn('action',
			array(
				'header'    =>  Mage::helper('affiliate')->__('Action'),
				'width'     => '100',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
						array(
								'caption'   => Mage::helper('affiliate')->__('Edit'),
								'url'       => array('base'=> '*/*/edit'),
								'field'     => 'id'
						)
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
		));		
		return parent::_prepareColumns();
  }

	protected function _prepareMassaction() {
		$this->setMassactionIdField('banner_id');
		$this->getMassactionBlock()->setFormFieldName('banner');

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