<div class="display articles">
	{if !$articles}
		<div class="header">
			<h1>{tr}Articles{/tr}</h1>
		</div>
	{/if}

	{formfeedback success=$smarty.request.feedback}

	{include file="bitpackage:articles/article_filter_inc.tpl"}
	{if $gBitUser->hasPermission( 'p_articles_approve_submission' ) && $submissions}
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
			{if $gBitUser->hasPermission( 'p_articles_auto_approve' )}
				{assign var="ititle" value="Write article"}
			{else}
				{assign var="ititle" value="Submit article"}
			{/if}
			{if $topic}
				{smartlink ititle=$ititle ipackage=articles ifile="edit.php" topic="$topic"}
			{else}
				{smartlink ititle=$ititle ipackage=articles ifile="edit.php"}
			{/if}
		</p>
	{/foreach}
	{pagination}
</div>
