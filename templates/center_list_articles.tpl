<div class="display articles">
	{if !$articles}
		<div class="header">
			<h1>{tr}Articles{/tr}</h1>
		</div>
	{/if}

	{formfeedback success=$smarty.request.feedback}

	{include file="bitpackage:articles/article_filter_inc.tpl"}

	{foreach from=$articles item=article}
		{include file="bitpackage:articles/article_display.tpl"}
	{foreachelse}
		<p class="norecords">
			{tr}No records found{/tr}<br />
			{if $gBitUser->hasPermission( 'bit_p_submit_article' )}
				{smartlink ititle="Write article" ipackage=articles ifile="edit.php"}
			{/if}
		</p>
	{/foreach}
</div>
