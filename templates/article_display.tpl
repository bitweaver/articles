{* $Header: /cvsroot/bitweaver/_bit_articles/templates/article_display.tpl,v 1.27 2006/04/24 09:11:16 squareing Exp $ *}
{strip}
{if !$showDescriptionsOnly}
	{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav' serviceHash=$article}
{/if}

<div class="{$outer_div|default:"post"}">
	<div class="floaticon">
		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon' serviceHash=$article}
		{if $gBitUser->isAdmin() || $gContent->isOwner( $article )}
			<a href="{$smarty.const.ARTICLES_PKG_URL}edit.php?article_id={$article.article_id}">{biticon ipackage=liberty iname=edit iexplain=edit}</a>
		{/if}
		{*<a style="display:none;" href="{$smarty.const.ARTICLES_PKG_URL}print.php?article_id={$article.article_id}">{biticon ipackage=liberty iname=print iexplain=print}</a>*}
		{if $gBitUser->hasPermission( 'p_articles_remove' )}
			{smartlink ititle="Remove" ipackage=articles ifile="list.php" ibiticon="liberty/delete" action=remove remove_article_id=$article.article_id status_id=$smarty.request.status_id}
		{/if}
	</div><!-- end .footer -->

	<div class="header">
		<h1>{$article.title|escape}</h1>
		{if $article.show_author eq 'y' || $article.show_pubdate eq 'y'}
			<div class="date">
				{if $article.show_author eq 'y'}
					{* can't really use the link here since it only works when the user uses his login name *}
					{displayname user=$article.author_name nolink=true}&nbsp;
				{/if}

				{if $article.show_pubdate eq 'y'}
					{if $article.time_difference.orientation eq 'past'}
						&bull; {tr}{$article.time_difference.strings.0} {$article.time_difference.strings.1} ago{/tr}
					{else}
						&bull; {$article.publish_date|bit_short_datetime}
					{/if}
				{/if}
			</div><!-- end .date -->
		{/if}
	</div>

	<div class="body"{if $gBitUser->getPreference( 'user_dbl' ) and $gBitUser->hasPermission( 'p_articles_edit' )} ondblclick="location.href='{$smarty.const.ARTICLES_PKG_URL}edit.php?article_id={$article.article_id}';"{/if}>
		<div class="content">
			{if $article.show_image eq 'y' && $article.image_url}
				<div class="image">
					{if $showDescriptionsOnly and $article.has_more}<a href="{$article.display_url}">{/if}
						<img class="icon" alt="{$article.topic_name|default:$article.title|escape}" title="{$article.topic_name|default:$article.title|escape}" src="{$article.image_url}"/>
					{if $showDescriptionsOnly and $article.has_more}</a>{/if}
				</div>
			{/if}

			{if $article.use_ratings eq 'y'}
				<span class="rating">
					{repeat count=$article.rating}
						{biticon ipackage=articles iname=rating iexplain="Article Rating"}
					{/repeat}
					{math assign=rating_off equation="5-x" x=$article.rating}
					{repeat count=$rating_off}
						{biticon ipackage=articles iname=rating_off iexplain="Article Rating"}
					{/repeat}
				</span>
			{/if}

			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='body' serviceHash=$article}
			{if $showDescriptionsOnly}
				{$article.parsed_description}
			{else}
				{$article.parsed_data}
			{/if}
		</div>

		<div class="footer">
			{if $article.show_reads eq 'y'}
				{assign var=spacer value=TRUE}
				{$article.hits} {tr}reads{/tr}
			{/if}

			{if $showDescriptionsOnly and $article.has_more}
				{if $spacer}&nbsp; &bull; &nbsp;{/if}
				{assign var=spacer value=TRUE}
				<a href="{$article.display_url}">{tr}Read More&hellip;{/tr}</a>
			{/if}

			{if $article.allow_comments eq 'y'}
				{if $spacer}&nbsp; &bull; &nbsp;{/if}
				{if $showDescriptionsOnly}<a href="{$article.display_url}#editcomments">{/if}
					{tr}{$article.num_comments} Comment(s){/tr}
				{if $showDescriptionsOnly}</a>{/if}
			{/if}
		</div>
	</div><!-- end .body -->
</div><!-- end .article -->

{if $print_page ne 'y' and $article.allow_comments eq 'y' and !$preview && !$showDescriptionsOnly and $article.status_id eq $smarty.const.ARTICLE_STATUS_APPROVED}
	{include file="bitpackage:liberty/comments.tpl"}
{/if}

{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='view' serviceHash=$article}
{/strip}
