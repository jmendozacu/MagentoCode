<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_AccountController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		$aff = Mage::helper('affiliate')->getAffiliate();
		if($aff) {
			$this->loadLayout();
			$active = $aff->getStatus();
			if($active!='1') {			
				Mage::getSingleton('customer/session')->addError('You account is not active. It might be locked or not approved by store owner.');				
			}
			$this->getLayout()->getBlock('head')->setTitle(Mage::helper('affiliate')->__("Affiliate Program"));
			$this->_initLayoutMessages('customer/session');
			$this->renderLayout();
		} else $this->_redirect('affiliate/index/index');
	}
	
	public function campaignAction() {
		$aff = Mage::helper('affiliate')->getAffiliate();
		if($aff) {
			if($aff->getStatus()=='1') {
				$this->loadLayout();
				$this->_initLayoutMessages('customer/session');		
				$this->getLayout()->getBlock('head')->setTitle(Mage::helper('affiliate')->__("Affiliate Campaigns"));
				$this->renderLayout();
			} else {
				// Mage::getSingleton('customer/session')->addError('You account is not active. It might be locked or not approved by store owner.');
				$this->_redirectError(Mage::getUrl('affiliate/account/index', array('_secure' => true)));
			}
		} else {
			$this->_redirect('affiliate/index/signup');
		}
	}
	
	public function include_campaignAction() {
		if ($post = $this->getRequest()->getParams()) {
			$affiliate_id = (int)$post['affiliate_id'];
			$campaign_id = (int)$post['campaign_id'];
			$relation = Mage::getModel('affiliate/campaignrelation');
			$relation->loadRelation($affiliate_id, $campaign_id);
			if (!$relation->getId()) {
				try {
					$relation->setAffiliateId($affiliate_id)
						->setCampaignId($campaign_id)
						->save();
					Mage::getSingleton('customer/session')->addSuccess(Mage::helper('affiliate')->__("Affiliate campaign is included in your account successfully."));
				}
				catch (Exception $e) {
					Mage::getSingleton('customer/session')->addError($e->getMessage());
				}
			}
			
		}
		$this->_redirect('affiliate/account/campaign');
		return;
	}
	
	public function exclude_campaignAction() {
		if ($post = $this->getRequest()->getParams()) {
			$affiliate_id = (int)$post['affiliate_id'];
			$campaign_id = (int)$post['campaign_id'];
			$relation = Mage::getModel('affiliate/campaignrelation');
			$relation->loadRelation($affiliate_id, $campaign_id);
			if ($relation->getId()) {
				try {
					$relation->delete();
					Mage::getSingleton('customer/session')->addSuccess(Mage::helper('affiliate')->__("Affiliate campaign is excluded from your account successfully."));
				}
				catch (Exception $e) {
					Mage::getSingleton('customer/session')->addError($e->getMessage());
				}
			}
			
		}
		$this->_redirect('affiliate/account/campaign');
		return;
	}
	
	public function materialsAction() {
		$aff = Mage::helper('affiliate')->getAffiliate();
		if($aff) {
			if($aff->getStatus()=='1') {
				$this->loadLayout();
				$this->_initLayoutMessages('customer/session');		
				$this->getLayout()->getBlock('head')->setTitle(Mage::helper('affiliate')->__("Affiliate Marketing Materials"));
				$this->renderLayout();
			} else {
				// Mage::getSingleton('customer/session')->addError('You account is not active. It might be locked or not approved by store owner.');
				$this->_redirectError(Mage::getUrl('affiliate/account/index', array('_secure' => true)));
			}
		} else {
			$this->_redirect('affiliate/index/signup');
		}
	}
	
	public function commissionAction() {
		$aff = Mage::helper('affiliate')->getAffiliate();
		if($aff) {
			if($aff->getStatus()=='1') {
				$this->loadLayout();
				$this->_initLayoutMessages('customer/session');		
				$this->getLayout()->getBlock('head')->setTitle(Mage::helper('affiliate')->__("Affiliate Commissions"));
				$this->renderLayout();
			} else {
				// Mage::getSingleton('customer/session')->addError('You account is not active. It might be locked or not approved by store owner.');
				$this->_redirectError(Mage::getUrl('affiliate/account/index', array('_secure' => true)));
			}
		} else {
			$this->_redirect('affiliate/index/signup');
		}
	}
	
	public function transactionAction() {
		$aff = Mage::helper('affiliate')->getAffiliate();
		if($aff) {
			if($aff->getStatus()=='1') {
				$this->loadLayout();
				$this->_initLayoutMessages('customer/session');		
				$this->getLayout()->getBlock('head')->setTitle(Mage::helper('affiliate')->__("Affiliate Transactions"));
				$this->renderLayout();
			} else {
				// Mage::getSingleton('customer/session')->addError('You account is not active. It might be locked or not approved by store owner.');
				$this->_redirectError(Mage::getUrl('affiliate/account/index', array('_secure' => true)));
			}
		} else {
			$this->_redirect('affiliate/index/signup');
		}
	}
	
	public function viewPaymentAction() {
		$aff = Mage::helper('affiliate')->getAffiliate();
		if($aff) {
			if($aff->getStatus()=='1') {
				$this->loadLayout();
				$this->_initLayoutMessages('customer/session');		
				$this->getLayout()->getBlock('head')->setTitle(Mage::helper('affiliate')->__("Affiliate Payment"));
				$this->renderLayout();
			} else {
				// Mage::getSingleton('customer/session')->addError('You account is not active. It might be locked or not approved by store owner.');
				$this->_redirectError(Mage::getUrl('affiliate/account/index', array('_secure' => true)));
			}
		} else {
			$this->_redirect('affiliate/index/signup');
		}
	}
	
	public function statisticAction() {
		$aff = Mage::helper('affiliate')->getAffiliate();
		if($aff) {
			if($aff->getStatus()=='1') {
				$this->loadLayout();
				$this->_initLayoutMessages('customer/session');		
				$this->getLayout()->getBlock('head')->setTitle(Mage::helper('affiliate')->__("Affiliate Statistic"));
				$this->renderLayout();
			} else {
				// Mage::getSingleton('customer/session')->addError('You account is not active. It might be locked or not approved by store owner.');
				$this->_redirectError(Mage::getUrl('affiliate/account/index', array('_secure' => true)));
			}
		} else {
			$this->_redirect('affiliate/index/signup');
		}
	}
	
	public function requestAction() {
		$data = $this->getRequest()->getPost();
		$allowCreateRequest = $this->validate($data['amount']);
		if ($allowCreateRequest) {
			// create request
			$model = Mage::getModel('affiliate/payment');
			$data = array(
				'affiliate_id' 		=> Mage::helper('affiliate')->isAffiliate(),
				'request_amount'	=> $data['amount'],
				'request_time'		=> now(),
				'payment_method'	=> 0,
				'status'			=> 1,
				'request_note'		=> $data['message']
			);
			$model->setData($data)->save();
			$allowSendEmail = Mage::getStoreConfig('affiliate/email_notification/email_to_admin');
			if (in_array('4', explode(',', $allowSendEmail))) {
				$aff = Mage::getModel('affiliate/affiliate')->load($data['affiliate_id']);
				Mage::getSingleton('core/translate')->setTranslateInline(false);
				$mailTemplate = Mage::getModel('core/email_template');
				$affObject = new Varien_Object();
				$affObject->setData($aff->getData());
				$requestObject = new Varien_Object();
				$requestObject->setData($data);
				try {
					$mailTemplate->setDesignConfig(array('area' => 'frontend'))
						->setReplyTo($aff['email'])
						->sendTransactional(
							Mage::getStoreConfig('affiliate/email_notification/user_request_money_email_template'),
							Mage::getStoreConfig('affiliate/email_notification/sender_email_identity'),
							Mage::getStoreConfig('affiliate/email_notification/admin_email'),
							null,
							array(
								'affInfo' =>$affObject,
								'requestInfo' => $requestObject
								)
						);
					Mage::getSingleton('core/translate')->setTranslateInline(true);
					Mage::getSingleton('customer/session')->addSuccess('Your request has been sent successfully.');
					$this->_redirectSuccess(Mage::getUrl('affiliate/account/viewPayment', array('_secure' => true)));
					return;
				} catch (Exception $e) {
					Mage::getSingleton('customer/session')->addError('Something went wrong. Try again later.');
					$this->_redirectError(Mage::getUrl('affiliate/account/viewPayment', array('_secure' => true)));	
					return;
				}				
			}			
		} else {
			Mage::getSingleton('customer/session')->addError('Something went wrong. Try again later.');
			$this->_redirectError(Mage::getUrl('affiliate/account/viewPayment', array('_secure' => true)));
			return;
		}
	}
	
	public function validate($value) {
		$min = Mage::getStoreConfig('affiliate/general/minimum_amount');
		$max = Mage::getStoreConfig('affiliate/general/maximum_amount');
		$affId = Mage::helper('affiliate')->isAffiliate();
		$availableRequestAmount = Mage::getModel('affiliate/payment')->getAvailableRequestAmount($affId);
		if($value >= $min  && $value <= $availableRequestAmount && ($max <=0 || ($max >0 && $value <= $max))) return true;
		else return false;
	}
}