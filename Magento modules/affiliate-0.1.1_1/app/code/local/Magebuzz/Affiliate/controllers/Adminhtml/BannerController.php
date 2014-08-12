<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/banner')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Banners'), Mage::helper('adminhtml')->__('Manage Banners'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('affiliate/banner')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('banner_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('affiliate/banner');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_banner_edit'))
				->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_banner_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}		
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('affiliate/banner');
			if ($id = $this->getRequest()->getParam('id')) {
				$model->load($id);
			}
			
			$data['file'] = '';
			//handle banner upload
			if	(isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
				try {
					$image_name = $_FILES['file']['name'];
					$new_image_name = Mage::helper('affiliate')->renameImage($image_name);
					
					$uploader = new Varien_File_Uploader('file');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media') . DS . 'affiliate' . DS . 'banner';
					if (!is_dir($path)) {
						mkdir($path, 0777, true);
					}
					
					if (!file_exists($path . DS . $new_image_name)) {
						$uploader->save($path, $new_image_name);
					}
				}
				catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}			
				$data['file'] = $new_image_name;
			}
			elseif ($model->getFile()) {
				$data['file'] = $model->getFile();
			}
			
			$post = $this->getRequest()->getPost();	
			if(isset($post['file']['delete']) && $post['file']['delete'] == 1) {
				$data['file'] = '';
			}
					
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('affiliate')->__('Banner was successfully saved'));
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
				$model = Mage::getModel('affiliate/banner');
				 
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
		$bannerIds = $this->getRequest()->getParam('banner');
		if(!is_array($bannerIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		} else {
			try {
				foreach ($bannerIds as $bannerId) {
					$banner = Mage::getModel('affiliate/banner')->load($bannerId);
					$banner->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
						'Total of %d record(s) were successfully deleted', count($bannerIds)
					)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function massStatusAction() {
		$bannerIds = $this->getRequest()->getParam('banner');
		if(!is_array($bannerIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
		} else {
			try {
				foreach ($bannerIds as $bannerId) {
					$banner = Mage::getSingleton('affiliate/banner')
						->load($bannerId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($bannerIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}


}