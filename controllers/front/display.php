<?php
class customfaqdisplayModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();
	$this->context->smarty->assign(
	

		  array(
			  'faq_module_content' => unserialize( Configuration::get('FAQMODULE_CONTENT')),
			  //'faq_module_link' => $this->context->link->getModuleLink('myfaq', 'display')
		  )
	  );
    $this->setTemplate('display.tpl');
  }
  public function setMedia(){
parent::setMedia();
 $this->addJqueryUI('ui.accordion');
 $this->context->controller->addJS(_MODULE_DIR_.$this->module->name.'/views/js/acco.js');
 
 }
 
 
}