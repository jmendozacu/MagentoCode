<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Banner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	public function __construct() {
		parent::__construct();
						 
		$this->_objectId = 'id';
		$this->_blockGroup = 'affiliate';
		$this->_controller = 'adminhtml_banner';
		
		$this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Save Banner'));
		$this->_updateButton('delete', 'label', Mage::helper('affiliate')->__('Delete Banner'));

		$this->_addButton('saveandcontinue', array(
				'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
				'onclick'   => 'saveAndContinueEdit()',
				'class'     => 'save',
		), -100);

		$this->_formScripts[] = "
				function toggleEditor() {
						if (tinyMCE.getInstanceById('banner_content') == null) {
								tinyMCE.execCommand('mceAddControl', false, 'banner_content');
						} else {
								tinyMCE.execCommand('mceRemoveControl', false, 'banner_content');
						}
				}

				function saveAndContinueEdit(){
						editForm.submit($('edit_form').action+'back/edit/');
				}
		";
	}

	public function getHeaderText() {
		if( Mage::registry('banner_data') && Mage::registry('banner_data')->getId() ) {
			return Mage::helper('affiliate')->__("Edit '%s'", $this->htmlEscape(Mage::registry('banner_data')->getTitle()));
		} else {
			return Mage::helper('affiliate')->__('New Banner');
		}
	}
}