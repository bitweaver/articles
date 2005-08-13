<div class="navbar">
	<ul>
		<li><a href="{$smarty.const.ARTICLES_PKG_URL}index.php">{tr}View articles{/tr}</a></li>
		<li><a href="{$smarty.const.ARTICLES_PKG_URL}list.php">{tr}List articles{/tr}</a></li>
		<li><a href="{$smarty.const.ARTICLES_PKG_URL}list_submissions.php">{tr}List submissions{/tr}</a></li>
		{if $bit_p_edit_article eq 'y'}
			<li><a href="{$smarty.const.ARTICLES_PKG_URL}edit.php">{tr}New article{/tr}</a></li>
		{/if}
	</ul>
</div>
