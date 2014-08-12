<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Affiliate_Block_Adminhtml_Campaign_Serializer extends Mage_Core_Block_Template {
	public function __construct() {
		parent::__construct();
		return $this;
	}
	
	public function initSerializerBlock($gridName,$hiddenInputName) {
		$grid = $this->getLayout()->getBlock($gridName);
        $this->setGridBlock($grid)
                 ->setInputElementName($hiddenInputName);
	}
}