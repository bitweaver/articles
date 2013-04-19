{strip}
{if $packageMenuTitle}<a href="#"> {tr}{$packageMenuTitle|capitalize}{/tr}</a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=articles">{tr}Articles{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_topics.php">{tr}Article Topics{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_types.php">{tr}Article Types{/tr}</a></li>
</ul>
{/strip}
