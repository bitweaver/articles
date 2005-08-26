{strip}
<ul>
	{if $gBitUser->hasPermission( 'bit_p_read_article' )}
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}index.php">{biticon ipackage=liberty iname=home iexplain="articles home" iforce=icon} {tr}Articles Home{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}list.php">{biticon ipackage=liberty iname=list iexplain="list articles" iforce=icon} {tr}List articles{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'bit_p_edit_article') || $gBitUser->hasPermission('bit_p_submit_article') }
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}edit.php">{biticon ipackage=liberty iname=new iexplain="new article" iforce=icon} {tr}{if $gBitUser->hasPermission('bit_p_edit_article')}Write{else}Submit{/if} article{/tr}</a></li>
	{/if}
	{if $gBitSystemPrefs.feature_cms_rankings eq 'y' && $gBitUser->hasPermission( 'bit_p_read_article ' )}
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}rankings.php">{biticon ipackage=liberty iname=list iexplain="article rankings" iforce=icon} {tr}Article Rankings{/tr}</a></li>
	{/if}
	{if $gBitSystemPrefs.feature_comm eq 'y' && $gBitUser->hasPermission( 'bit_p_send_articles ' )}
		<li><a class="item" href="{$smarty.const.XMLRPC_PKG_URL}send_objects.php">{biticon ipackage=liberty iname=spacer iexplain="send articles" iforce=icon} {tr}Send articles{/tr}</a></li>
	{/if}
	{if $gBitSystem->isFeatureActive('feature_article_submissions')}
		{if $gBitUser->hasPermission( 'bit_p_submit_article ' ) ||
			$gBitUser->hasPermission( 'bit_p_approve_submission ' ) ||
			$gBitUser->hasPermission( 'bit_p_remove_submission ' )}
			<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}list.php?status_id={$smarty.const.ARTICLE_STATUS_PENDING}">{biticon ipackage=liberty iname=list iexplain="view submissions" iforce=icon} {tr}View submissions{/tr}</a></li>
		{/if}
	{/if}
	{if $gBitUser->hasPermission( 'bit_p_admin_articles' )}
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_topics.php">{biticon ipackage=liberty iname=spacer iexplain="admin topics" iforce=icon} {tr}Article topics{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_types.php">{biticon ipackage=liberty iname=spacer iexplain="admin types" iforce=icon} {tr}Article types{/tr}</a></li>
	{/if}
</ul>
{/strip}
