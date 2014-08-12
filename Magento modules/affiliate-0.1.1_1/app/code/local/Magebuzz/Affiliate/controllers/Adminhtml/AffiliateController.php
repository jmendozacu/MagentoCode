<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Adminhtml_AffiliateController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/affiliate')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function viewRequestAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('affiliate/affiliate')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('affiliate_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('affiliate/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliate_edit'))
				->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliate_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->loadLayout();
		$this->_setActiveMenu('affiliate/affiliate');
		$this->getLayout()->getBlock('head')->setTitle("Create New Affiliate");
		$this->renderLayout();
	}
	
	public function submitAction() {
		$this->_redirect('affiliate/adminhtml_affiliate/viewRequest');
	}
	
	public function rejectAction() {
		$data = $this->getRequest()->getPost();
		$model = Mage::getModel('affiliate/payment');
		$model->load($data['id']);
		$model->setStatus(2);
		$model->setRequestTime(now());
		$model->setResponseNote($data['response_message']);
		$model->save();
		// Zend_Debug::dump($model->getData());
		// die();
		
		$allowSendEmail = Mage::getStoreConfig('affiliate/email_notification/email_to_admin');
		if(in_array('6', explode(',', $allowSendEmail))){
			$aff = Mage::getModel('affiliate/affiliate')->load($model->getAffiliateId());
			Mage::getSingleton('core/translate')->setTranslateInline(false);
			$mailTemplate = Mage::getModel('core/email_template');
			$data = $model->getData();
			$dataObject = new Varien_Object();
			$dataObject->setData($data);
			
			// Zend_Debug::dump($dataObject);
			// die();
			
			$mailTemplate->setDesignConfig(array('area' => 'frontend'))
				->setReplyTo($aff->getEmail())
				->sendTransactional(
					Mage::getStoreConfig('affiliate/email_notification/admin_reject_payment_email_template'),
					Mage::getStoreConfig('affiliate/email_notification/sender_email_identity'),
					$aff->getEmail(),
					null,
					array(
						'info' =>$dataObject
						)
				);
			Mage::getSingleton('core/translate')->setTranslateInline(true);
		}
		
		// Do NOT update balance and withdrawn of affiliate user
		$this->_redirect('affiliate/adminhtml_affiliate/viewRequest');
	}
	
	private function updateAffiliate($requestId) {
		// Get affiliate user ID
		$model = Mage::getModel('affiliate/payment');
		$affId = $model->load($requestId)->getAffiliateId();
		// Get total withdrawn of this user
		$totalWithdrawn = Mage::getModel('affiliate/payment')->getTotalAmountRequested($affId);
		// Get balance of this user
		$balance = Mage::getModel('affiliate/payment')->getCurrentBalance($affId);
		// update
		$model = Mage::getModel('affiliate/affiliate');
		$model->load($affId);
		$model->setCurrentBalance($balance);
		$model->setTotalWithdrawn($totalWithdrawn);
		$model->save();
	}
	
	public function completeAction() {
		$data = $this->getRequest()->getPost();
		$model = Mage::getModel('affiliate/payment');
		$model->load($data['id']);
		$model->setStatus(3);
		$model->setRequestTime(now());
		$model->setResponseNote($data['response_message']);
		$model->save();
		
		$allowSendEmail = Mage::getStoreConfig('affiliate/email_notification/email_to_admin');
		
		if(in_array('5', explode(',', $allowSendEmail))){
			$aff = Mage::getModel('affiliate/affiliate')->load($model->getAffiliateId());
			Mage::getSingleton('core/translate')->setTranslateInline(false);
			$mailTemplate = Mage::getModel('core/email_template');
			$data1 = $model->getData();
			$dataObject = new Varien_Object();
			$dataObject->setData($data1);
			
			// Zend_Debug::dump($dataObject);
			// die();
			
			$mailTemplate->setDesignConfig(array('area' => 'frontend'))
				->setReplyTo($aff->getEmail())
				->sendTransactional(
					Mage::getStoreConfig('affiliate/email_notification/admin_accept_payment_email_template'),
					Mage::getStoreConfig('affiliate/email_notification/sender_email_identity'),
					$aff->getEmail(),
					null,
					array(
						'info' =>$dataObject
						)
				);
			Mage::getSingleton('core/translate')->setTranslateInline(true);
		}
		
		// Update balance and withdrawn of affiliate user
		$this->updateAffiliate($data['id']);
		$this->_redirect('affiliate/adminhtml_affiliate/viewRequest');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('affiliate/affiliate');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				$model->save();
				$aff = Mage::getModel('affiliate/affiliate')->load($model->getAffiliateId());
				$allowSendEmail = Mage::getStoreConfig('affiliate/email_notification/email_to_admin');
				Mage::getSingleton('core/translate')->setTranslateInline(false);
				$mailTemplate = Mage::getModel('core/email_template');
				$dataObject = new Varien_Object();
				$dataObject->setData($aff->getData());
				if($model->getStatus()!='1') {
					if(in_array('3', explode(',', $allowSendEmail))){
						// Send Email Notification : Your affiliate account has been locked!
						$mailTemplate->setDesignConfig(array('area' => 'frontend'))
							->setReplyTo($aff->getEmail())
							->sendTransactional(
								Mage::getStoreConfig('affiliate/email_notification/user_account_locked_email_template'),
								Mage::getStoreConfig('affiliate/email_notification/sender_email_identity'),
								$aff->getEmail(),
								null,
								array(
									'info' =>$dataObject
									)
							);
					}
				} else {
					if(in_array('2', explode(',', $allowSendEmail))){
						// Send Email Notification : Account Actived!
						$mailTemplate->setDesignConfig(array('area' => 'frontend'))
							->setReplyTo($aff->getEmail())
							->sendTransactional(
								Mage::getStoreConfig('affiliate/email_notification/user_account_approved_email_template'),
								Mage::getStoreConfig('affiliate/email_notification/sender_email_identity'),
								$aff->getEmail(),
								null,
								array(
									'info' =>$dataObject
									)
							);
					}
				}
				Mage::getSingleton('core/translate')->setTranslateInline(true);
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('affiliate')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('affiliate/affiliate');
				 
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
        $affiliateIds = $this->getRequest()->getParam('affiliate');
        if(!is_array($affiliateIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($affiliateIds as $affiliateId) {
                    $affiliate = Mage::getModel('affiliate/affiliate')->load($affiliateId);
                    $affiliate->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($affiliateIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	public function viewSpecificRequestAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('affiliate/payment')->load($id);
		Mage::register('affiliate_payment_request', $model);
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function viewPaymentHistoryAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function massChangeStatusAction()
	{
		$affiliateIds = $this->getRequest()->getParam('affiliate');
        if(!is_array($affiliateIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($affiliateIds as $affiliateId) {
					$status = $this->getRequest()->getParam('status');
                    $affiliate = Mage::getSingleton('affiliate/payment')
                        ->load($affiliateId)
                        ->setStatus($this->getRequest()->getParam('status'))
						->setRequestTime(now());
					$defaultResponseMessage = null;
					if ($status=='2') $defaultResponseMessage = 'Your request has been rejected!';
					else {
						$defaultResponseMessage = 'Your request has been accepted!';
						$this->updateAffiliate($affiliateId);
					}
                    $affiliate->setResponseNote($defaultResponseMessage);
                    $affiliate->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($affiliateIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/viewRequest');
	}
	
    public function massStatusAction()
    {
        $affiliateIds = $this->getRequest()->getParam('affiliate');
        if(!is_array($affiliateIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($affiliateIds as $affiliateId) {
                    $affiliate = Mage::getSingleton('affiliate/affiliate')
                        ->load($affiliateId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($affiliateIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'affiliate.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliate_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'affiliate.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliate_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}