<div id="accordion">


{if isset($faq_module_content) && $faq_module_content}
           {*$faq_module_content|@print_r*}
      {foreach from=$faq_module_content item=foo}
    <h3>{$foo[0]}</h3>
	<div>{$foo[1]}</div>
{/foreach}
       {/if}
	   
	   
</div>