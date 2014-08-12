<?php
class Bmenu_Bmodule_IndexController extends Mage_Adminhtml_Controller_Action
{  
    public function indexAction()
    {
        $this->loadLayout();
         
        $block = $this->getLayout()->createBlock('core/text', 'bmenu-block')->setText('<h1>Boduk Menu Sample</h1>');
        $this->_addContent($block);
         
        $this->_setActiveMenu('bmenu_menu')->renderLayout();      
    }   
}