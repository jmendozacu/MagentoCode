<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Model_Observer {
	public function updateAffiliate($affId) {
		$aff = Mage::getModel('affiliate/affiliate')->load($affId);
		$currentBalance = Mage::getModel('affiliate/payment')->getCurrentBalance($affId);
		$aff->setCurrentBalance($currentBalance);
		$aff->save();
	}
	
	/*
	* when creating invoice for the order, convert transaction into commission
	*/
	public function invoice() {
		$eventData = $observer->getEvent()->getData();
		$eventName = $eventData['name'];
		if ($eventName == 'sales_order_invoice_pay') {
			$invoice = $observer->getEvent()->getInvoice();
			$order = $invoice->getOrder();
		}
	}
	
	/*
	* convert transaction into commission
	*/
	public function sales_order_item_save_after($observer) {
		$config = Mage::helper('affiliate')->getItemStatusConfig();
		$item = $observer['item'];
		$itemId = $item->getId();
		$updateTransaction = Mage::getModel('affiliate/transaction')->getCollection()
			->addFilter('item_id', $itemId)
			->getFirstItem();
		$updateTransactionData = $updateTransaction->getData();
		//update transaction status
		if (!empty($updateTransactionData)) {
			$updateTransaction->setStatus($item->getStatusId())->save();			
			// if exist a transaction and item's saved status is in config
			if (in_array($item->getStatusId(), $config)) {
				// check if it's new commission or exist commission
				$commissionModel = Mage::getModel('affiliate/commission');
				$commission = $commissionModel->getCollection()
					->addFilter('transaction_id', $updateTransaction->getId())
					->getFirstItem();
				$commissionData = $commission->getData();
				if (empty($commissionData)) {
					$data = array (
						'affiliate_id'	=> $updateTransaction->getAffiliateId(),
						'transaction_id'=> $updateTransaction->getId(),
						'amount'		=> $updateTransaction->getCommission(),
						'date_added'	=> $updateTransaction->getCreated(),
						'status'		=> $item->getStatusId()
					);
					$commissionModel->setData($data)->save();
					$this->updateAffiliate($updateTransaction->getAffiliateId());
				} else {
					$commission->setStatus($item->getStatusId())->save();
				}
			} else {	
				// else if exist a transaction but item's saved status is NOT in config
				// delete commission if exist and do NOT create new commission
				$commissionModel = Mage::getModel('affiliate/commission');
				$commission = $commissionModel->getCollection()
					->addFilter('transaction_id', $updateTransaction->getId())
					->getFirstItem();
				$commission->delete();
				$this->updateAffiliate($updateTransaction->getAffiliateId());
			}
		}		
	}

	/*
	* check if there is affid in the request action
	* if found, store information in cookies
	*/
	public function controller_action_predispatch($observer) {
		$controller = $observer['controller_action'];
		$request = $controller->getRequest();
		$affiliate_code = $request->getParam('affid');		

		if (!$affiliate_code) return;
		$cookie = Mage::getSingleton('core/cookie');
		$cookie->set('affiliate_code', $affiliate_code, 86400*30, null, null, false, false);
	}
	
	public function getCampaignArray($campaign, $storeId) {
		// check if available to all store
		$config = Mage::getModel('affiliate/campaign')->load($campaign)->getStoreId();
		if(in_array('0', $config)) {
			$campaigns = array();
			$table  = 'affiliate_campaign_store';
			$read = Mage::getSingleton('core/resource')->getConnection('core_read');
			$sql = 'SELECT `campaign_id` FROM `'. $table . '` WHERE `store_id` = 0' ;
			$result = $read->fetchAll($sql);
			foreach($result as $item) {
				array_push($campaigns, $item['campaign_id']);
			}
			return $campaigns;
		} else {
		// if not available to all store
			$campaigns = array();
			$table  = 'affiliate_campaign_store';
			$read = Mage::getSingleton('core/resource')->getConnection('core_read');
			$sql = 'SELECT `campaign_id` FROM `'. $table . '` WHERE `store_id` = ' . $storeId;
			$result = $read->fetchAll($sql);
			foreach($result as $item) {
				array_push($campaigns, $item['campaign_id']);
			}
			return $campaigns;
		}
	}
	
	/*
	* run after an order is placed in frontend
	* check if order items are in any campaign
	* create transactions
	*/
	public function checkout_type_onepage_save_order_after($observer) {
		$storeId = Mage::app()->getStore()->getId();
		$order = $observer['order'];
		$items = $order->getItemsCollection();
		$affInfo = Mage::helper('affiliate')->getAffInfo();
		if (!$affInfo) {
			return;
		}
		
		//get a list of included campaigns
		$assignedCampaignIds = Mage::getModel('affiliate/campaignrelation')->getCollection()
			->addFieldToFilter('affiliate_id', $affInfo['affiliate_id'])
			->getColumnValues('campaign_id');
		
		$todayStartOfDayDate  = Mage::app()->getLocale()->date()
			->setTime('00:00:00')
			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
	
		$todayEndOfDayDate  = Mage::app()->getLocale()->date()
			->setTime('23:59:59')
			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		
		$campaigns = Mage::getModel('affiliate/campaign')->getCollection()
			->addFieldToFilter('campaign_id', array('in' => $assignedCampaignIds))
			->addFieldToFilter('date_start', array('or'=> array(
					0 => array('date' => true, 'to' => $todayEndOfDayDate),
					1 => array('is' => new Zend_Db_Expr('null')))
				), 'left')
			->addFieldToFilter('date_end', array('or'=> array(
					0 => array('date' => true, 'from' => $todayStartOfDayDate),
					1 => array('is' => new Zend_Db_Expr('null')))
				), 'left');
		$campaigns->getSelect()->order('priority', 'ASC');
		
		//calculate commission for each item 
		if (count($campaigns)) {
			$commission = 0;
			foreach($items as $item) {
				$value = 0;
				$quoteItemId = $item->getQuoteItemId();
				$quoteItem = Mage::getSingleton('checkout/cart')->getQuote()->getItemsCollection()
					->addFieldToFilter('item_id', $quoteItemId)->getFirstItem();
				foreach ($campaigns as $campaign) {
					$rule = Mage::getModel('affiliate/campaignrule')->load($campaign->getRule());
					if ($rule->getActions()->validate($quoteItem)) {
						$value = Mage::helper('affiliate')->getBenefit($item, $campaign);
						break;
					}
				}
				
				//save transaction for each item
				$transaction = Mage::getModel('affiliate/transaction');
				$data = array(
					'campaign_id'  		=> $campaign->getId(),
					'affiliate_id' 		=> $affInfo['affiliate_id'],
					'affiliate_name' 	=> $affInfo['username'],
					'affiliate_email'	=> $affInfo['email'],
					'order_id'			=> $order->getId(),
					'store_id'			=> $storeId,
					'item_id'			=> $item->getId(),
					'commission'		=> $value,
					'total_amount'		=> $item->getQtyOrdered(),
					'status'			=> $item->getStatusId()
				);
				
				$transaction->setData($data);
				$transaction->setCreated(now());
				$transaction->save();
				$this->updateAffiliate($affInfo['affiliate_id']);
				
			}
		}				
	}
}