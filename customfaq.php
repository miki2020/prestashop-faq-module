<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class Customfaq extends Module
{
	public function __construct()
	  {
		$this->name = 'customfaq';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0 alpha';
		$this->author = 'Michał Nowak';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_); 
		$this->bootstrap = true;
	 
		parent::__construct();
	 
		$this->displayName = $this->l('Custom FAQ module');
		$this->description = $this->l('Custom content FAQ module.');
	 
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	 
		if (!Configuration::get('FAQMODULE_CONTENT'))      
		  $this->warning = $this->l('No content provided');
	  }  
  
	public function install()
	{
	  if (Shop::isFeatureActive())
		Shop::setContext(Shop::CONTEXT_ALL);
	 
	  if (!parent::install() ||
		!$this->registerHook('leftColumn') ||
		!$this->registerHook('header') ||
		!Configuration::updateValue('FAQMODULE_CONTENT', serialize(array(array('tresc Pytanie 1', 'tesc Odpowiedź 1'), array('tresc Pytanie 2','tresc Odpowiedź 2')) ) )
	  )
		return false;
	 
	  return true;
	}
	public function uninstall()
	{
	  if (!parent::uninstall() ||
		!Configuration::deleteByName('FAQMODULE_CONTENT')
	  )
		return false;
	 
	  return true;
	}


	public function hookDisplayLeftColumn($params)
	{
	  $this->context->smarty->assign(
		  array(
			  'faq_module_content' => unserialize(Configuration::get('FAQMODULE_CONTENT')),
			  'faq_module_link' => $this->context->link->getModuleLink('customfaq', 'display')
		  )
	  );
	  return $this->display(__FILE__, 'faqmodule.tpl');
	}
	   
	public function hookDisplayRightColumn($params)
	{
	  return $this->hookDisplayLeftColumn($params);
	}
	   
	public function hookDisplayHeader()
	{
		 
		 $this->context->controller->addJqueryUI('ui.accordion');
		 $this->context->controller->addJS($this->_path.'/views/js/acco.js');
 
	  $this->context->controller->addCSS($this->_path.'css/faqmodule.css', 'all');
	}  


	public function getContent()
	{
		$output = null;
	 
		if (Tools::isSubmit('submit'.$this->name))
		{
			
			
			
			
			
			
			
			$my_module_content = array();
			$to_delete = array();
			
			try{
			$content = unserialize(Configuration::get('FAQMODULE_CONTENT'));
			}	catch (Exception $e) {    echo 'Caught exception: ',  $e->getMessage(), "\n";}
			 //echo '<pre>from presta conf:'; print_r( $content);echo '</pre>'; //die();
			if(!$content
			  || empty($content))
			  {
				  //$output .= $this->displayError($this->l('Brak danych w konfiguracji modułu. Dodaj nowe pytamie i odpowiedź,'));
			  }
			  else
			  {
					foreach ($content as $key=>$q_a_set){
					//	echo '<pre>key '; print_r( $key);echo '</pre>';
					$my_module_content[$key][] = (Tools::getValue('q'.$key));
					$my_module_content[$key][] = (Tools::getValue('a'.$key));
					if ((int)Tools::getValue('s'.$key) === 1) $to_delete[] = $key; 			}
			//Configuration::updateValue('FAQMODULE_CONTENT', serialize($my_module_content));
			  }
			// echo '<pre>do delete ids'; print_r( $to_delete);echo '</pre>'; 
			//die();
			
			if (!$my_module_content
			  || empty($my_module_content)
			  //|| !Validate::isCleanHtml($my_module_name)
			  )
			  {//$output .= $this->displayError($this->l('Nieprawidłowe dane wejsciowe z formularza'));
			  
			  }
			else
			{
				
				foreach($to_delete as $id){unset($my_module_content[$id]);}
				$my_module_content = array_values($my_module_content);
				 	
				//print_r( $_POST );
				
				Configuration::updateValue('FAQMODULE_CONTENT', serialize($my_module_content));
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
			
			 if ((int)Tools::getValue('add_set') === 1) {
			$my_module_content[] = array('','');
			 Configuration::updateValue('FAQMODULE_CONTENT', serialize($my_module_content));
			 $output .= $this->displayConfirmation($this->l('Field added'));
			}
		}
		
		$link = new LinkCore;	$url = $link->getAdminLink("Customfaq");
		
		$output .= '<a href="'.$url.'">to comtr</a>';
		
		
		
		
		
		//return $output.$this->displayForm();
		return $output.$this->displayForm()
		.$this->initList($my_module_content)
		;
	}
	
		
	public function displayForm()
	{
		// Get default language
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		 $content_fields = array();
		 
		 $content = unserialize(Configuration::get('FAQMODULE_CONTENT'));
		 
		 //echo '<pre>'; print_r( $content);echo '</pre>'; //die();
		 //getFields list from configuration
		 if (!$content)      
		  $this->warning = $this->l('No content provided');
	  else
	  {
  
  foreach ($content as $key => $q_a_set){
		  
			$content_fields[] = array(
				'type' => 'text',
					'label' => $this->l('Pytanie: '.(string)((int)$key + 1) ),
					'name' => 'q'.$key,
					//'lang' => true,
					//'cols' => 60,
					//'rows' => 10,
					'size' => 40,
					'class' => 'rte',
					/*'autoload_rte' => true,*/
					
					'required' => true);
				$content_fields[] = array(
				'type' => 'textarea',
					'label' => $this->l('Odpowiedz: '.(string)((int)$key + 1)),
					'name' => 'a'.$key,
					//'lang' => true,
					'cols' => 60,
					'rows' => 10,
					//'size' => 40,
					'class' => 'rte',
					'autoload_rte' => true,
					
					'required' => true);
				$content_fields[] = array(
					'type' => 'switch',
        'label' => $this->l('Do usunięcia'),
        'name' => 's'.$key,
        'is_bool' => true,
        'desc' => $this->l('Zaznacz tak aby usunąć'),
        'values' => array(
            array(
                'id' => 's'.$key.'_on',
                'value' => 1,
                'label' => $this->l('Enabled')
            ),
            array(
                'id' => 's'.$key.'_off',
                'value' => 0,
                'label' => $this->l('Disabled')
            )
        )
				);
			
			}
			
			//echo '<pre>'; print_r( $content_fields);echo '</pre>'; die();
	  }
		$content_fields[] = array(
					'type' => 'switch',
					'label' => $this->l('Dodaj nowe pole'),
					'name' => 'add_set',
					'is_bool' => true,
					'desc' => $this->l('Zaznacz by dodać nowe pytanie.'),
					'values' => array(
						array(
                'id' => 'add_set_on',
                'value' => 1,
                'label' => $this->l('Enabled')
            ),
            array(
                'id' => 'add_set_off',
                'value' => 0,
                'label' => $this->l('Disabled')
            )
					)
				); 
		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
			),
			'input' => $content_fields,
			
			
			'submit' => array(
				'title' => $this->l('Save me'),
				'class' => 'btn btn-default pull-right'
			)
		);
		 
		$helper = new HelperForm();
		 
		// Module, token and currentIndex
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		 
		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		 
		// Title and toolbar
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;        // false -> remove toolbar
		$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'submit'.$this->name;
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
				'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);
		 
		// Load current value
		 	$content = unserialize(Configuration::get('FAQMODULE_CONTENT'));
		 if (!$content)      
		  $this->warning = $this->l('No content provided');
	  else
	  {foreach ($content as $key => $q_a_set){
		$helper->fields_value['q'.(string)$key] = $q_a_set[0];
		$helper->fields_value['a'.(string)$key] = $q_a_set[1];
		
		$helper->fields_value['s'.(string)$key] = 0;
		
		}
		
	  }
	  $helper->fields_value['add_set'] = 0;
		return $helper->generateForm($fields_form);
	}	

	private function initList($result)
	{
		$this->fields_list = array(
			'question' => array(
				'title' => $this->l('Pytanie'),
				'width' => 140,
				'type' => 'text',
			),
			'answer' => array(
				'title' => $this->l('Odpowiedź'),
				'width' => 140,
				'type' => 'text',
			),
		);
		$helper = new HelperList();
		 
		$helper->shopLinkType = '';
		 
		$helper->simple_header = false;
		 
		// Actions to be displayed in the "Actions" column
		$helper->actions = array('edit', 'delete', 'view');
		//$helper->addRowAction('details'); 
		$helper->identifier = 'question';
		$helper->show_toolbar = true;
		$helper->title = 'HelperList';
		$helper->table = $this->name.'_categories';
		 
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		
		$this->result = array();
		
		foreach($result as $key => $q_a_set){
		 $this->result[$key] = array(
				'question' => $q_a_set[0],
				'answer' =>$q_a_set[1]
		);}
			
		//print_r($helper);
		return $helper->generateList($this->result,$this->fields_list);
	}
	

	
}