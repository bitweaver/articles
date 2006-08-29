{strip}
<ul>
	{if $gBitUser->hasPermission( 'p_articles_read' )}
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}index.php">{biticon ipackage=liberty iname=home iexplain="articles home" iforce=icon} {tr}Articles Home{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}list.php">{biticon ipackage=liberty iname=list iexplain="list articles" iforce=icon} {tr}List articles{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}list_topics.php">{biticon ipackage=liberty iname=list iexplain="list articles" iforce=icon} {tr}List topics{/tr}</a></li>
	{/if}
	{if $gBitSystem->isFeatureActive('articles_submissions')}
		{if $gBitUser->hasPermission( 'p_articles_submit ' ) ||
			$gBitUser->hasPermission( 'p_articles_approve_submission ' ) ||
			$gBitUser->hasPermission( 'p_articles_remove_submission ' ) ||
			($gBitSystem->isFeatureActive('articles_auto_approve')&&$gBitUser->isRegistered())}
			<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}list.php?status_id={$smarty.const.ARTICLE_STATUS_PENDING}">{biticon ipackage=liberty iname=list iexplain="view submissions" iforce=icon} {tr}List submissions{/tr}</a></li>
		{/if}
	{/if}
	{if $gBitUser->hasPermission( 'p_articles_edit') || $gBitUser->hasPermission('p_articles_submit') }
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}edit.php">{biticon ipackage=liberty iname=new iexplain="new article" iforce=icon} {tr}{if $gBitUser->hasPermission('p_articles_edit')}Write{else}Submit{/if} article{/tr}</a></li>
	{/if}
	{if $gBitSystem->isFeatureActive( 'articles_rankings' ) && $gBitUser->hasPermission( 'p_articles_read ' )}
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}rankings.php">{biticon ipackage=liberty iname=list iexplain="article rankings" iforce=icon} {tr}Article rankings{/tr}</a></li>
	{/if}
	{if $gBitSystem->isFeatureActive( 'feature_comm' ) && $gBitUser->hasPermission( 'p_articles_send ' )}
		<li><a class="item" href="{$smarty.const.XMLRPC_PKG_URL}send_objects.php">{biticon ipackage=liberty iname=spacer iexplain="send articles" iforce=icon} {tr}Send articles{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_articles_admin' )}
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_topics.php">{biticon ipackage=liberty iname=spacer iexplain="admin topics" iforce=icon} {tr}Admin topics{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_types.php">{biticon ipackage=liberty iname=spacer iexplain="admin types" iforce=icon} {tr}Admin types{/tr}</a></li>
	{/if}
</ul>
{/strip}
