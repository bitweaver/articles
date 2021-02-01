<div class="list-inline navbar">
	<ul>
		<li><a href="{$smarty.const.ARTICLES_PKG_URL}index.php">{tr}View articles{/tr}</a></li>
		<li><a href="{$smarty.const.ARTICLES_PKG_URL}list.php">{tr}List articles{/tr}</a></li>
		<li><a href="{$smarty.const.ARTICLES_PKG_URL}list_submissions.php">{tr}List submissions{/tr}</a></li>
		{if $gBitUser->hasPermission( 'p_articles_submit' )}
			<li><a href="{$smarty.const.ARTICLES_PKG_URL}edit.php">{tr}Submit article{/tr}</a></li>
		{elseif $gBitUser->hasPermission( 'p_articles_auto_approve' ) || $gBitUser->hasPermission( 'p_articles_approve_submission' )}
			<li><a href="{$smarty.const.ARTICLES_PKG_URL}edit.php">{tr}New article{/tr}</a></li>
		{/if}
	</ul>
</div>
