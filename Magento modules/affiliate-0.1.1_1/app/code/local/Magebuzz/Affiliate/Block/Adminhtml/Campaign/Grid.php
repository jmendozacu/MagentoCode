<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Campaign_Grid extends Mage_Adminhtml_Block_Widget_Grid {
  public function __construct() {
		parent::__construct();
		$this->setId('campaignGrid');
		$this->setDefaultSort('campaign_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection() {
		$collection = Mage::getModel('affiliate/campaign')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
  }

  protected function _prepareColumns() {
		$this->addColumn('campaign_id', array(
			'header'    => Mage::helper('affiliate')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'campaign_id',
		));

		$this->addColumn('campaign_title', array(
			'header'    => Mage::helper('affiliate')->__('Campaign Title'),
			'align'     =>'left',
			'index'     => 'campaign_title',
		));
		
		$this->addColumn('commission_type', array(
			'header'    => Mage::helper('affiliate')->__('Commission Type'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'commission_type',
			'type'      => 'options',
			'options'   => array(
					1 => 'Fixed Flat Rate',
					2 => 'Fixed Percent Rate',
			),
		));
		
		$this->addColumn('date_start', array(
			'header'    => Mage::helper('affiliate')->__('Date Start'),
			'align'     =>'left',
			'index'     => 'date_start',
			'type'      => 'datetime',
		));
		
		$this->addColumn('date_end', array(
			'header'    => Mage::helper('affiliate')->__('Date End'),
			'align'     =>'left',
			'index'     => 'date_end',
			'type'      => 'datetime',
		));		

		$this->addColumn('status', array(
			'header'    => Mage::helper('affiliate')->__('Status'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'status',
			'type'      => 'options',
			'options'   => array(
					1 => 'Enabled',
					2 => 'Disabled',
			),
		));		
		
		$this->addColumn('description', array(
			'header'	=> Mage::helper('affiliate')->__('Description'),
			'align'		=> 'left',
			'index'		=> 'description'
		));
	
		return parent::_prepareColumns();
  }
	
	protected function _getStore(){
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('campaign_id');
		$this->getMassactionBlock()->setFormFieldName('campaign');

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