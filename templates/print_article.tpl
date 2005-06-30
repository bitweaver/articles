{* $Header: /cvsroot/bitweaver/_bit_articles/templates/print_article.tpl,v 1.1 2005/06/30 01:10:46 bitweaver Exp $ *}
{* Index we display a wiki page here *}

{include file="bitpackage:kernel/header.tpl"}
<div id="main">
	<div class="articletitle">
		<span class="titlea">{$title}</span><br />
		<span class="titleb">{tr}By:{/tr}{$author_name} {tr}on:{/tr}{$publish_date|bit_short_datetime} ({$reads} {tr}reads{/tr})</span>
	</div> {* end articletitle *}
	<br />

	<div class="articleheading">
		<table cellpadding="0" cellspacing="0">
			<tr><td valign="top">
				{if $use_image eq 'y'}
					{if $hasImage eq 'y'}
						<img alt="{tr}Article image{/tr}" border="0" src="article_image.php?id={$article_id}" />
					{else}
						<img alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topic_id}" />
					{/if}
				{else}
					<img alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topic_id}" />
				{/if}
			</td><td valign="top">
				<span class="articleheading">{$parsed_heading}</span>
			</td></tr>
		</table>
	</div> {* end articleheading *}
	<div class="articletrailer">
		({$size} bytes
			{if $bit_p_edit_article}
				[<a href="{$gBitLoc.ARTICLES_PKG_URL}edit.php?article_id={$article_id}">{tr}Edit{/tr}</a>] 
			{/if}{if $bit_p_remove_article}
				[<a href="{$gBitLoc.ARTICLES_PKG_URL}list.php?remove={$article_id}">{tr}Remove{/tr}</a>]
			{/if}
		)
	</div> {* end articletrailer *}
	<div class="articlebody">
		{$parsed_body}
	</div> {* end articlebody *}
</div> {* end main *}

{include file="bitpackage:kernel/footer.tpl"}
