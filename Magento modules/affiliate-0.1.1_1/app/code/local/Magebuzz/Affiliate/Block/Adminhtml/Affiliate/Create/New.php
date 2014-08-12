<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Affiliate_Create_New extends Mage_Adminhtml_Block_Widget_Form_Container {
	public function __construct() {
		parent::__construct();

		$this->_objectId = 'id';
		$this->_blockGroup = 'affiliate';
		$this->_controller = 'adminhtml_affiliate_create';
		$this->_mode = 'new';
		
		$this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Save Affiliate'));
		$this->_updateButton('delete', 'label', Mage::helper('affiliate')->__('Delete Affiliate'));

		$this->_addButton('saveandcontinue', array(
				'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
				'onclick'   => 'saveAndContinueEdit()',
				'class'     => 'save',
		), -100);

		$this->_formScripts[] = "
				function toggleEditor() {
						if (tinyMCE.getInstanceById('affiliate_content') == null) {
								tinyMCE.execCommand('mceAddControl', false, 'affiliate_content');
						} else {
								tinyMCE.execCommand('mceRemoveControl', false, 'affiliate_content');
						}
				}

				function saveAndContinueEdit(){
						editForm.submit($('edit_form').action+'back/edit/');
				}
		";
	}

	public function getHeaderText() {
		return Mage::helper('affiliate')->__('Add New Affiliate');
	}
}