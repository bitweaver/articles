{strip}
<ul>
	{if $gBitUser->hasPermission( 'p_articles_read' )}
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}index.php">{booticon iname="icon-home" ipackage="icons" iexplain="Articles Home" ilocation=menu}</a></li>
		{if $gBitUser->hasPermission( 'p_articles_approve_submission ' ) ||
		    $gBitUser->hasPermission( 'p_articles_auto_approve') ||
		    $gBitUser->hasPermission( 'p_articles_submit' ) }
			{if $gBitUser->hasPermission( 'p_articles_approve_submission ' ) || $gBitUser->hasPermission( 'p_articles_auto_approve' )}
				{assign var=iexplain value="Write Article"}
			{else}
				{assign var=iexplain value="Submit Article"}
			{/if}
			<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}edit.php">{booticon iname="icon-file" ipackage="icons" iexplain=$iexplain ilocation=menu}</a></li>
		{/if}
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}list.php">{booticon iname="icon-list" ipackage="icons" iexplain="List Articles" ilocation=menu}</a></li>
	{/if}
	{if $gBitSystem->isFeatureActive('articles_submissions')}
		{if $gBitUser->hasPermission( 'p_articles_submit ' ) ||
			$gBitUser->hasPermission( 'p_articles_approve_submission ' ) ||
			$gBitUser->hasPermission( 'p_articles_remove_submission ' ) ||
			($gBitSystem->isFeatureActive('articles_auto_approve') && $gBitUser->isRegistered())}
			<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}list.php?status_id={$smarty.const.ARTICLE_STATUS_PENDING}">{booticon iname="icon-list" ipackage="icons" iexplain="List Submissions" ilocation=menu}</a></li>
		{/if}
	{/if}
	{if $gBitUser->hasPermission( 'p_articles_admin' )}
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}list.php?get_future=1">{booticon iname="icon-list" ipackage="icons" iexplain="List Upcoming" ilocation=menu}</a></li>
	{/if}
	{if $gBitSystem->isFeatureActive( 'articles_rankings' ) && $gBitUser->hasPermission( 'p_articles_read ' )}
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}rankings.php">{booticon iname="icon-sort"  ipackage="icons"  iexplain="Article Rankings" ilocation=menu}</a></li>
	{/if}
	{*if $gBitSystem->isFeatureActive( 'feature_comm' ) && $gBitUser->hasPermission( 'p_articles_send ' )}
		<li><a class="item" href="{$smarty.const.XMLRPC_PKG_URL}send_objects.php">{biticon ipackage=liberty iname=spacer iexplain="Send Articles" ilocation=menu}</a></li>
	{/if*}
	{if $gBitUser->hasPermission( 'p_articles_read' )}
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}list_topics.php">{booticon iname="icon-list" ipackage="icons" iexplain="List Topics" ilocation=menu}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_articles_admin' )}
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_topics.php">{booticon iname="icon-file"   iexplain="Admin Topics" ilocation=menu}</a></li>
		<li><a class="item" href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_types.php">{booticon iname="icon-file"   iexplain="Admin Types" ilocation=menu}</a></li>
	{/if}
</ul>
{/strip}
