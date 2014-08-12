<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Campaign_Edit_Tab_Categories extends Mage_Adminhtml_Block_Widget_Form  {
  protected function _prepareForm() {
		if (Mage::getSingleton('adminhtml/session')->getFormData()) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData();
			$model = Mage::getModel('affiliate/campaign')
				->load($data['campaign_id'])
				->setData($data);
			$ruleModel = Mage::getModel('affiliate/campaignrule')
				->load($data['rule'])
				->setData($data);
			Mage::getSingleton('adminhtml/session')->setFormData(null);
		} elseif (Mage::registry('campaign_data')) {
			$model = Mage::registry('campaign_data');
			$ruleModel = Mage::registry('campaign_rule_data');
			$data = $model->getData();
		}
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('rule_');
		$renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
			->setTemplate('promo/fieldset.phtml')
			->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newActionHtml/form/rule_actions_fieldset'));
		$fieldset = $form->addFieldset('actions_fieldset', array(
			'legend'=>Mage::helper('salesrule')->__('Apply the rule only to cart items matching the following conditions (leave blank for all items)')
		))->setRenderer($renderer);

		$fieldset->addField('actions', 'text', array(
			'name' => 'actions',
			'label' => Mage::helper('salesrule')->__('Apply To'),
			'title' => Mage::helper('salesrule')->__('Apply To'),
			'required' => true,
		))
		->setRule($ruleModel)->setRenderer(Mage::getBlockSingleton('rule/actions'));
		$form->setValues($data);
		$this->setForm($form);
		return $this;
  }
}