<!-- Block FAQmodule -->
<div id="faqmodule_block_home" class="block">
  <h4 class= "title_block">Custom FAQ!</h4>
  <div class="block_content">
     
	 
	 
	 
	 <div id="accordion_column">


{if isset($faq_module_content) && $faq_module_content}
           {*$faq_module_content|@print_r*}
      {foreach from=$faq_module_content item=foo}
    <h3>{$foo[0]}</h3>
	<div>{$foo[1]}</div>
{/foreach}
       {/if}
	   
	   
</div>
	 
	 
	 <P>  
    <ul>
	
	
	
	
	
      <li><a href="{$faq_module_link}" title="Click this link">Przejdź do listy pytań!</a></li>
    </ul>
	</p>
  </div>
</div>
<!-- /Block FAQmodule -->