<div class="display articles">
	{if !$articles}
		<div class="header">
			<h1>{tr}Articles{/tr}</h1>
		</div>
	{/if}

	{formfeedback success=$smarty.request.feedback}

	{include file="bitpackage:articles/article_filter_inc.tpl"}
	{if $gBitUser->hasPermission( 'bit_p_approve_submission' ) && $submissions}
		<h3>{tr}The following articles are awaiting your attention{/tr}</h3>
		<ul>
			{foreach from=$submissions item=submission}
				<li>{$submission.display_link} <small>[ {tr}Submitted{/tr}: {$submission.last_modified|bit_long_datetime} ]</small></li>
			{/foreach}
		</ul>
	{/if}

	{foreach from=$articles item=article}
		{include file="bitpackage:articles/article_display.tpl"}
	{foreachelse}
		<p class="norecords">
			{tr}No records found{/tr}<br />
			{if $gBitUser->hasPermission( 'bit_p_autoapprove_submission' )}
				{smartlink ititle="Write article" ipackage=articles ifile="edit.php"}
			{elseif $gBitUser->hasPermission( 'bit_p_submit_article' )}
				{smartlink ititle="Submit article" ipackage=articles ifile="edit.php"}
			{/if}
		</p>
	{/foreach}
	{pagination}
</div>
