<?php
class Magebuzz_Affiliate_Block_Adminhtml_Campaign_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		$this->setId('campaign_product_grid');
		$this->setDefaultSort('entity_id');
		$this->setUseAjax(true);
  }
	
	protected function _addColumnFilterToCollection($column) {
		if ($column->getId() == 'in_campaign') {
			$productIds = $this->_getSelectedProducts();
			if (empty($productIds)) {
				$productIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
			}
			elseif(!empty($productIds)) {
				$this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
			}
		}
		else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}
	
	protected function _prepareCollection() {
		if ($this->getCampaign()->getId()) {
			$this->setDefaultFilter(array('in_campaign'=>1));
		}
		$store = $this->_getStore();
	  $collection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('sku')
			->addAttributeToSelect('name')
			->addAttributeToSelect('attribute_set_id')
			->addAttributeToSelect('type_id');		

		if ($store->getId()) {
			$adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
			$collection->addStoreFilter($store);
			$collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
			$collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
			$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
			$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
			$collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
		}
		else {
			$collection->addAttributeToSelect('price');
			$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
			$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
		}
			
		$this->setCollection($collection);
		
		return parent::_prepareCollection();
  }
	
	protected function _prepareColumns() {
		$this->addColumn('in_campaign', array(
			'header_css_class' => 'a-center',
			'type'      => 'checkbox',
			'name'      => 'in_campaign',
			'align'     => 'center',
			'index'     => 'entity_id',
			'values'    => $this->_getSelectedProducts(),
		));
		
		$this->addColumn('entity_id', array(
			'header'    => Mage::helper('affiliate')->__('ID'),
			'width'     => '50px',
			'index'     => 'entity_id',
			'type'  => 'number',
		));

		$this->addColumn('product_name', array(
			'header'    => Mage::helper('affiliate')->__('Product Name'),
			'index'     => 'name'
		));
		
		$this->addColumn('type',
			array(
					'header'=> Mage::helper('affiliate')->__('Type'),
					'width' => '60px',
					'index' => 'type_id',
					'type'  => 'options',
					'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
		));
		
		$sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
			->load()
			->toOptionHash();

		$this->addColumn('set_name',
				array(
						'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
						'width' => '100px',
						'index' => 'attribute_set_id',
						'type'  => 'options',
						'options' => $sets,
		));

		$this->addColumn('sku',
				array(
						'header'=> Mage::helper('catalog')->__('SKU'),
						'width' => '80px',
						'index' => 'sku',
		));

		$store = $this->_getStore();
		$this->addColumn('price',
				array(
						'header'=> Mage::helper('catalog')->__('Price'),
						'type'  => 'price',
						'currency_code' => $store->getBaseCurrency()->getCode(),
						'index' => 'price',
		));
		
		$this->addColumn('visibility',
				array(
						'header'=> Mage::helper('catalog')->__('Visibility'),
						'width' => '70px',
						'index' => 'visibility',
						'type'  => 'options',
						'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
		));
		
		$this->addColumn('product_status',
				array(
						'header'=> Mage::helper('catalog')->__('Status'),
						'width' => '70px',
						'index' => 'status',
						'type'  => 'options',
						'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
		));
		
		$this->addColumn('position', array(
			'header'            => Mage::helper('affiliate')->__(''),
			'name'              => 'position',
		   // 'validate_class'    => 'validate-number',
			'index'             => 'position',
			'width'             => 0,
			'editable'          => true,//!$this->isReadonly(),
			'filter'			=> false,
		));
		
		return parent::_prepareColumns();
  }

	// public function getGridUrl() {
		// return $this->getUrl('*/*/grid', array('_current' => true));
	// }
  
	public function getGridUrl() {
		return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/productlistGrid', array('_current'=>true,'id'=>$this->getRequest()->getParam('id')));
	}
	
  public function getRowUrl($row) {
		return $this->getUrl('adminhtml/catalog_product/edit', array('id'=>$row->getId()));
	}
	
	protected function _getSelectedProducts() {
		$products = $this->getRequest()->getParam('oproduct');
		if (is_null($products)) {
			$products = array_keys($this->getSelectedProducts());
		}
		return $products;
	}
	
	public function getSelectedProducts() {
		$products = array();
		$productIds = $this->getCampaign()->getSelectedProductIds();
		foreach($productIds as $productId) {
			$products[$productId] = array('position'=>0);
		}
		return $products;
	}
	
	public function getCampaign() {
		return Mage::getModel('affiliate/campaign')->load($this->getRequest()->getParam('id'));
	}
	
	protected function _getStore() {
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}	
}