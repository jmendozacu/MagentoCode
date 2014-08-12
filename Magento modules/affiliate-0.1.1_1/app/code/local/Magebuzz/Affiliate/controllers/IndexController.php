<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_IndexController extends Mage_Core_Controller_Front_Action {
  public function indexAction() {		
		if ($this->_getCustomerSession()->isLoggedIn()) {
			if ($this->_isAffiliate()) {
				$this->_redirect('affiliate/account/index');
			}
		}
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$homepage_id = Mage::helper('affiliate')->getAffiliateHomePage();
		$homepage = Mage::getModel('cms/block')->load($homepage_id);
		Mage::register('affiliate_homepage', $homepage);
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('affiliate')->__('Welcome to Affiliate Program!'));
		$this->renderLayout();
  }
	
	public function signupAction() {
		//check if customer is logged in and already an affiliate
		if ($this->_getCustomerSession()->isLoggedIn()) {
			if ($this->_isAffiliate()) {
				$this->_redirect('affiliate/account/index');
			}
		}
		
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('affiliate')->__('Affiliate Registration'));
		$this->renderLayout();
	}
	
	public function loginAction() {
		Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('affiliate/account/index'));
		$this->_redirect('customer/account/login');
	}
	
	/*
	* sign up new affiliate account
	*/
	public function signup_postAction() {
		if ($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost();
			$session = $this->_getCustomerSession();

			//if customer is logged in, create new affiliate account 
			if ($session->isLoggedIn()) {
				$customer_id = $session->getCustomerId();
				$customer = Mage::getModel('customer/customer')->load($customer_id);
				$address = Mage::getModel('customer/address');
				$addressForm = Mage::getModel('customer/form');
				$addressForm->setFormCode('customer_register_address')
					->setEntity($address);
				$addressData    = $addressForm->extractData($this->getRequest(), 'address', false);
				$addressErrors  = $addressForm->validateData($addressData);
				if ($addressErrors === true) {
					$address->setId(null);
					$addressForm->compactData($addressData);
					$customer->addAddress($address);
					$addressErrors = $address->validate();
					if (is_array($addressErrors)) {
						$errors = array_merge($errors, $addressErrors);
					}
				} else {
					$errors = array_merge($errors, $addressErrors);
				}
			}			
			else {
				// customer is not logged in
				// check if this email is already in use
				if (!$this->_validateEmail($data['email'])) {
					$session->addError(Mage::helper('affiliate')->__("This email is already in use. Please log in or use another email address."));
					$session->setCustomerFormData($this->getRequest()->getPost());
					$this->_redirect('*/*/signup');
					return;
				}
				//create new customer account --- copied from customer controller			
				$session->setEscapeMessages(true);
				$errors = array();
				if (!$customer = Mage::registry('current_customer')) {
					$customer = Mage::getModel('customer/customer')->setId(null);
				}
				/* @var $customerForm Mage_Customer_Model_Form */
				$customerForm = Mage::getModel('customer/form');
				$customerForm->setFormCode('customer_account_create')
					->setEntity($customer);
				$customerData = $customerForm->extractData($this->getRequest());
				$customer->getGroupId();
				$address = Mage::getModel('customer/address');
				$addressForm = Mage::getModel('customer/form');
				$addressForm->setFormCode('customer_register_address')
					->setEntity($address);
				$addressData    = $addressForm->extractData($this->getRequest(), 'address', false);
				$addressErrors  = $addressForm->validateData($addressData);
				if ($addressErrors === true) {
					$address->setId(null);
					$addressForm->compactData($addressData);
					$customer->addAddress($address);

					$addressErrors = $address->validate();
					if (is_array($addressErrors)) {
						$errors = array_merge($errors, $addressErrors);
					}
				} else {
					$errors = array_merge($errors, $addressErrors);
				}
				
				try {
					$customerErrors = $customerForm->validateData($customerData);
					if ($customerErrors !== true) {
						$errors = array_merge($customerErrors, $errors);
					} else {
						$customerForm->compactData($customerData);
						$customer->setPassword($this->getRequest()->getPost('password'));
						$customer->setConfirmation($this->getRequest()->getPost('confirmation'));
						$customerErrors = $customer->validate();
						if (is_array($customerErrors)) {
							$errors = array_merge($customerErrors, $errors);
						}
					}
					$validationResult = count($errors) == 0;
					if (true === $validationResult) {
						$customer->save();
						if ($customer->isConfirmationRequired()) {
							$customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl());
							$session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));							
						} else {
							$session->setCustomerAsLoggedIn($customer);	
						}
					} else {
							$session->setCustomerFormData($this->getRequest()->getPost());
							if (is_array($errors)) {
								foreach ($errors as $errorMessage) {
									$session->addError($errorMessage);
								}
							} else {
								$session->addError($this->__('Invalid customer data'));
							}
					}
					
				}
				catch (Mage_Core_Exception $e) {
					$session->setCustomerFormData($this->getRequest()->getPost());
					if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
						$url = Mage::getUrl('customer/account/forgotpassword');
						$message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
						$session->setEscapeMessages(false);
					} else {
						$message = $e->getMessage();
					}
					$session->addError($message);
					$this->_redirectError(Mage::getUrl('*/*/signup', array('_secure' => true)));
					return;
				} catch (Exception $e) {
					$session->setCustomerFormData($this->getRequest()->getPost())
						->addException($e, $this->__('Cannot save the customer.'));
					$this->_redirectError(Mage::getUrl('*/*/signup', array('_secure' => true)));
					return;
				}
				
			}
			
			//create new affiliate account
			$model = Mage::getModel('affiliate/affiliate');
			try {
				$customer_id = $session->getCustomerId();
				$referral_code = Mage::helper('affiliate')->generateReferralCode($data['email'], now());
				$isApproved = Mage::getStoreConfig('affiliate/general/approve_affiliate_user');
				if($isApproved == '1') {
					$statusOption = 2;
				}
				else {
					$statusOption = 1;
				}
				$model->setFirstname($data['firstname'])
					->setLastname($data['lastname'])
					->setUsername($data['email'])
					->setEmail($data['email'])
					->setTelephone($data['telephone'])
					->setReferralCode($referral_code)
					->setCurrentBalance(0)
					->setTotalWithdrawn(0)
					->setCustomerId($customer_id)
					->setCreatedAt(now())
					->setStatus($statusOption)
					->save();
				
				//auto assign affiliate to campaign
				$campaigns = Mage::getModel('affiliate/campaign')->getCollection()
					->addFieldToFilter('auto_assign', 1)
					->addFieldToFilter('status', 1);
				if (count($campaigns)) {
					foreach ($campaigns as $campaign) {
						$relationModel = Mage::getModel('affiliate/campaignrelation')
							->setAffiliateId($model->getId())
							->setCampaignId($campaign->getId())
							->save();
					}
				}
				
				//send notification email	
				Mage::getSingleton('core/translate')->setTranslateInline(false);
				$mailTemplate = Mage::getModel('core/email_template');
				$data = $model->getData();
				$dataObject = new Varien_Object();
				$dataObject->setData($data);
				$mailTemplate->setDesignConfig(array('area' => 'frontend'))
					->setReplyTo($data['email'])
					->sendTransactional(
						Mage::getStoreConfig('affiliate/email_notification/new_user_sign_up_email_template'),
						Mage::getStoreConfig('affiliate/email_notification/sender_email_identity'),
						$data['email'],
						null,
						array(
							'info' =>$dataObject
							)
					);

				//send notification email
				$allowSendEmail = Mage::getStoreConfig('affiliate/email_notification/email_to_admin');
				if(in_array('1', explode(',', $allowSendEmail))){
					Mage::getSingleton('core/translate')->setTranslateInline(false);
					$mailTemplate->sendTransactional(
						Mage::getStoreConfig('affiliate/email_notification/new_user_sign_up_email_template'),
						Mage::getStoreConfig('affiliate/email_notification/sender_email_identity'),
						Mage::getStoreConfig('affiliate/email_notification/admin_email'),
						null,
						array(
							'info' =>$dataObject
						)
					);
					Mage::getSingleton('core/translate')->setTranslateInline(true);
				}
				
				if ($isApproved) {
					$session->addSuccess(Mage::helper('affiliate')->__('Thank you for your registration. We will review and approve your affiliate account as soon as possible.'));
				}
				else {
					$session->addSuccess(Mage::helper('affiliate')->__('Thank you for your registration. You can now start earning commission with our store.'));
				}
			}
			catch (Exception $e) {
				Mage::getSingleton('customer/session')->addError(Mage::helper('affiliate')->__('Cannot create affiliate account. Please try again!'));
				$this->_redirectError(Mage::getUrl('*/*/signup', array('_secure' => true)));
				return;
			}
			
			$this->_redirectSuccess(Mage::getUrl('affiliate/account/index', array('_secure' => true)));
		}
	}
	
	protected function _validateEmail($email) {
		$model = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsite()->getId())->loadByEmail($email);
		if ($model->getId()) {
			return false;
		}
		return true;
	}
	
	protected function _getCustomerSession() {
		return Mage::getSingleton('customer/session');
	}
	
	protected function _isAffiliate() {
		$customer_id = $this->_getCustomerSession()->getCustomerId();
		$affiliate = Mage::getModel('affiliate/affiliate')->loadByCustomerId($customer_id);
		if ($affiliate) {
			return true;
		}
		return false;
	}
	
	public function getCampaignArray($storeId) {
		$campaigns = array();
		$table  = 'affiliate_campaign_store';
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql = 'SELECT `campaign_id` FROM `'. $table . '` WHERE `store_id` = ' . $storeId;
		$result = $read->fetchAll($sql);
		foreach($result as $item) {
			array_push($campaigns, $item['campaign_id']);
		}
	}
}