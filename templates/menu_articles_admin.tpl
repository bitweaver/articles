{strip}
<li class="dropdown-submenu">
    <a href="#" onclick="return(false);" tabindex="-1" class="sub-menu-root">{tr}{$smarty.const.ARTICLES_PKG_DIR|capitalize}{/tr}</a>
	<ul class="dropdown-menu sub-menu">
		<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=articles">{tr}Articles Settings{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_topics.php">{tr}Article Topics{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_types.php">{tr}Article Types{/tr}</a></li>
	</ul>
</li>
{/strip}
