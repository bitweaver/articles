<div class="display articles">
	<div class="header">
		<h1>{tr}News{/tr}</h1>
	</div>
	{foreach from=$articles item=article}
		{include file="bitpackage:articles/article_display.tpl"}
	{foreachelse}
		<div class="norecords">{tr}No records found{/tr}<br />{smartlink ititle="Write article" ipackage=articles ifile="edit.php"}</div>
	{/foreach}
</div>
