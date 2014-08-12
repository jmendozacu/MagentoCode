<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Adminhtml_Affiliate_CreateController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/affiliate')
			->_addBreadcrumb(Mage::helper('affiliate')->__('Create New Affiliate'), Mage::helper('affiliate')->__('Create New Affiliate'));
		$this->getLayout()->getBlock('head')->setTitle('Create New Affiliate');
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);		
		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		return $this;
	}
	
	public function indexAction() {		
		$this->_initAction()
			->renderLayout();
	}
	
	public function newAction() {
		$customer_id = $this->getRequest()->getParam('customer_id');
		if (!isset($customer_id)) {
			$this->_redirect('*/*/index');
			return;
		}			
		$this->loadLayout();
		$this->_setActiveMenu('affiliate/affiliate');
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		
		$this->getLayout()->getBlock('head')->setTitle("Create New Affiliate");
		$this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliate_create_new'))
				 ->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliate_create_new_tabs'));
		$this->renderLayout();
	}
	
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			if (isset($data['customer_id'])) {
				$customer_id = $data['customer_id'];
				$customer = Mage::getModel('customer/customer')->load($customer_id);
				
				$model = Mage::getModel('affiliate/affiliate');
				if ($model->loadByCustomerId($customer_id)) {
					Mage::getSingleton('adminhtml/session')->addError('Affiliate account is already existed.');
					$this->_redirect('affiliate/adminhtml_affiliate/index');
          return;
				}
				//create new affiliate account
				try {	
					$model = Mage::getModel('affiliate/affiliate');
					$referral_code = Mage::helper('affiliate')->generateReferralCode($customer->getEmail(), $customer->getCreatedAt());
					
					$model->setFirstname($customer->getFirstname())
						->setLastname($customer->getLastname())
						->setUsername($customer->getEmail())
						->setReferralCode($referral_code)
						->setEmail($customer->getEmail())
						->setTelephone($customer->getTelephone())
						->setCurrentBalance(0)
						->setTotalWithdrawn(0)
						->setCustomerId($customer_id)
						->setCreatedAt(now())
						->setStatus($data['status'])
						->save();
					Mage::getSingleton('adminhtml/session')->addSuccess('Affiliate account is created successfully.');
					// Zend_Debug::dump(Mage::getUrl('affiliate/adminhtml_affiliate/index'));
					// die();
					$this->_redirect('affiliate/adminhtml_affiliate/index');
          return;
				}
				catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					$this->_redirect('affiliate/adminhtml_affiliate/index');
          return;
				}
			}
			else {
				Mage::getSingleton('adminhtml/session')->addError('Customer is not specified');
				$this->_redirect('affiliate/adminhtml_affiliate/index');
        return;
			}
		}
	}
	
	public function gridAction() {
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('affiliate/adminhtml_affiliate_create_grid')->toHtml()
		);
	}
}