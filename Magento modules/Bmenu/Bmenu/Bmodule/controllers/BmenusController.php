<?php
class Bmenu_Bmodule_BmenusController extends Mage_Adminhtml_Controller_Action
{  
    public function indexAction()
    {
        $this->loadLayout();
         
		$block = $this->getLayout()->createBlock(
			'Mage_Core_Block_Template', // Must be same
			'my_block_name_here', // Can change to any block name
			array('template' => 'Bmenu/menu1.phtml')
		);
		// app/design/adminhtml/default/default/template/inchoo/example_core_block.phtml
		
		$this->_addContent($block);
        
 
         
        $this->_setActiveMenu('bmenu_menu')->renderLayout();      
    }   
}

