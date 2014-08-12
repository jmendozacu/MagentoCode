<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Adminhtml_CampaignController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/campaign')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Campaign Manager'), Mage::helper('adminhtml')->__('Campaign Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('affiliate/campaign')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			$ruleModel = Mage::getModel('affiliate/campaignrule')->load($model->getRule());
			$ruleModel->getActions()->setJsFormObject('rule_actions_fieldset');
			Mage::register('campaign_data', $model);
			Mage::register('campaign_rule_data', $ruleModel);
			$this->loadLayout();
			$this->_setActiveMenu('affiliate/campaign');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
			$this->getLayout()->getBlock('head')
				->setCanLoadExtJs(true)
				->setCanLoadRulesJs(true);
			$this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_campaign_edit'))
				->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_campaign_edit_tabs'));
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	
	public function refineCategoryIds($ids) {		
		if (is_string($ids)) {
			$ids = explode(',', $ids);			
		} elseif (!is_array($ids)) {
			Mage::throwException(Mage::helper('catalog')->__('Invalid category IDs.'));
		}
		foreach ($ids as $i => $v) {
			if (empty($v)) {
				unset($ids[$i]);
			}
		}
		$ids = array_unique($ids);		
		return $ids;
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			try {
				$ruleModel = Mage::getModel('affiliate/campaignrule');
				$model = Mage::getModel('affiliate/campaign');
				$id = $this->getRequest()->getParam('id');
				
				if($id) {
					$model->load($id);
					$ruleId = $model->getRule();
					$ruleModel->load($ruleId);
				}
				
				if (isset($data['rule']['actions'])) {
	        $data['actions'] = $data['rule']['actions'];
	      }
				unset($data['rule']);
				$ruleModel->loadPost($data);
				$ruleModel->save();
				
				$data['rule'] = $ruleModel->getId();
				
				if(!$data['date_start']) $data['date_start'] = null;
				if(!$data['date_end']) $data['date_end'] = null;				
				
				//save campaign data
				$model->setData($data)->setId($id)->save();
				
				//auto assign campaign
				if ($model->getAutoAssign() == 1) {
					$affiliates = Mage::getModel('affiliate/affiliate')->getCollection();
					if (count($affiliates)) {
						foreach ($affiliates as $affiliate) {
							$existed = Mage::getModel('affiliate/campaignrelation')->getCollection()
								->addFieldToFilter('affiliate_id', $affiliate->getId())
								->addFieldToFilter('campaign_id', $model->getId());
							if (!count($existed)) {
								$relationModel = Mage::getModel('affiliate/campaignrelation')
									->setAffiliateId($affiliate->getId())
									->setCampaignId($model->getId())
									->save();
							}
						}
					}
				}
				
				//send campaign email notification to all affiliates
				$affiliateEmails = Mage::getModel('affiliate/affiliate')->getCollection()->getColumnValues('email');
				$allowSendEmail = Mage::getStoreConfig('affiliate/email_notification/email_to_admin');
				if (count($affiliateEmails)) {
					if (!$this->getRequest()->getParam('id')) {
						if(in_array('7', explode(',', $allowSendEmail)) && $model->getStatus()=='1') {
							$dataObject = new Varien_Object();
							$dataObject->setData($model->getData());
							Mage::getSingleton('core/translate')->setTranslateInline(false);
							$mailTemplate = Mage::getModel('core/email_template');
							$mailTemplate->setDesignConfig(array('area' => 'frontend'));
							foreach ($affiliateEmails as $affiliateEmail) {
								$mailTemplate->sendTransactional(
										Mage::getStoreConfig('affiliate/email_notification/admin_create_campaign_email_template'),
										Mage::getStoreConfig('affiliate/email_notification/sender_email_identity'),
										$affiliateEmail,
										null,
										array('info' =>$dataObject)
									);
							}
							Mage::getSingleton('core/translate')->setTranslateInline(true);
						}
					}
				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('affiliate')->__('Campaign was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('There was a problem when saving campaign. Please try again.'));
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Unable to find campaign to save'));
			$this->_redirect('*/*/');
			return;
		}
	}
	
	public function productlistAction() {
		$this->loadLayout();
		$this->getLayout()->getBlock('campaign.edit.tab.products')
			->setProducts($this->getRequest()->getPost('oproduct', null));
		$this->renderLayout();
	}
	
	public function productlistGridAction() {
		$this->loadLayout();
		$this->getLayout()->getBlock('campaign.edit.tab.products')
      ->setProducts($this->getRequest()->getPost('oproduct', null));
    $this->renderLayout();
	}
	
	public function categoriesAction() {
		$this->loadLayout();
    $this->renderLayout();
	}
	
	public function categoriesJsonAction() {
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('affiliate/adminhtml_campaign_edit_tab_categories')
				->getCategoryChildrenJson($this->getRequest()->getParam('category'))
		);
	}
	
	public function categorylistAction() {
		$this->loadLayout();
    $this->renderLayout();
	}
	
	public function categorylistGridAction() {
		$this->loadLayout();
		$this->getLayout()->getBlock('campaign.edit.tab.categories')
      ->setProducts($this->getRequest()->getPost('ocategory', null));
    $this->renderLayout();
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('affiliate/campaign');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
		$campaignIds = $this->getRequest()->getParam('campaign');
		if(!is_array($campaignIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		} else {
			try {
				foreach ($campaignIds as $campaignId) {
					$campaign = Mage::getModel('affiliate/campaign')->load($campaignId);
					$campaign->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
						'Total of %d record(s) were successfully deleted', count($campaignIds)
					)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function massStatusAction() {
		$campaignIds = $this->getRequest()->getParam('campaign');
		if(!is_array($campaignIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
		} else {
			try {
				foreach ($campaignIds as $campaignId) {
					$campaign = Mage::getSingleton('affiliate/campaign')
						->load($campaignId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($campaignIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}


}