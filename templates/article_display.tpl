{* $Header: /cvsroot/bitweaver/_bit_articles/templates/article_display.tpl,v 1.13 2005/10/26 10:58:16 squareing Exp $ *}
{strip}
{assign var=serviceNavTpls value=$gLibertySystem->getServiceValues('content_nav_tpl')}
{assign var=serviceViewTpls value=$gLibertySystem->getServiceValues('content_view_tpl')}

{if $serviceNavTpls.categorization and !$showDescriptionsOnly}
	{include file=$serviceNavTpls.categorization"}
{/if}

<div class="{$outer_div|default:"post"}">
	<div class="floaticon">
		{if $gContent->viewerCanEdit()}
			<a href="{$smarty.const.ARTICLES_PKG_URL}edit.php?article_id={$article.article_id}">{biticon ipackage=liberty iname=edit iexplain=edit}</a>
		{/if}
		<a style="display:none;" href="{$smarty.const.ARTICLES_PKG_URL}print.php?article_id={$article.article_id}">{biticon ipackage=liberty iname=print iexplain=print}</a>
		{if $gBitUser->hasPermission( 'bit_p_remove_article' )}
			{smartlink ititle="Remove" ipackage=articles ifile="list.php" ibiticon="liberty/delete" action=remove remove_article_id=$article.article_id status_id=$smarty.request.status_id}
		{/if}
	</div><!-- end .footer -->

	<div class="header">
		<h1>{$article.title}</h1>
		{if $article.show_author || $article.show_pubdate}
			<div class="date">
				{if $article.show_author}
					{* can't really use the link here since it only works when the user uses his login name *}
					{displayname user=$article.author_name nolink=true}&nbsp;
				{/if}

				{if $article.show_pubdate}
					{if $article.time_difference.orientation eq 'past'}
						&bull; {tr}{$article.time_difference.strings.0} {$article.time_difference.strings.1} ago{/tr}
					{else}
						&bull; {$article.publish_date|bit_short_datetime}
					{/if}
				{/if}
			</div><!-- end .date -->
		{/if}
	</div>

	<div class="body"{if $user_dbl eq 'y' and $gBitUser->hasPermission( 'bit_p_edit_article' )} ondblclick="location.href='{$smarty.const.ARTICLES_PKG_URL}edit.php?article_id={$article.article_id}';"{/if}>
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

		<div class="introduction">
			{if $article.show_image eq 'y' && $article.image_url}
				<div class="image">
					{if $article.read_more}<a href="{$smarty.const.ARTICLES_PKG_URL}read.php?article_id={$article.article_id}">{/if}
						<img class="icon" alt="{$article.topic_name}" src="{$article.image_url}"/>
					{if $article.read_more}</a>{/if}
				</div>
			{/if}
		</div><!-- end .introduction -->

		{if $showDescriptionsOnly}
			<div class="content">
				{$article.parsed_description}
			</div>
		{else}
			<div class="content">
				{$article.parsed_data}
			</div>
		{/if}

		<div class="footer">
			{if $article.show_reads}
				{assign var=spacer value=TRUE}
				{$article.hits} {tr}reads{/tr}
			{/if}

			{if $showDescriptionsOnly and $article.read_more}
				{if $spacer}&nbsp; &bull; &nbsp;{/if}
				{assign var=spacer value=TRUE}
				<a href="{$smarty.const.ARTICLES_PKG_URL}read.php?article_id={$article.article_id}">{tr}Read More...{/tr}</a>
			{/if}

			{if $article.allow_comments}
				{if $spacer}&nbsp; &bull; &nbsp;{/if}
				{if $showDescriptionsOnly}<a href="{$smarty.const.ARTICLES_PKG_URL}read.php?article_id={$article.article_id}#editcomments">{/if}
					{tr}{$article.num_comments} Comment(s){/tr}
				{if $showDescriptionsOnly}</a>{/if}
			{/if}
		</div>
	</div><!-- end .body -->
</div><!-- end .article -->

{if $print_page ne 'y' and $article.allow_comments eq 'y' and !$preview && !$showDescriptionsOnly and $article.status_id eq $smarty.const.ARTICLE_STATUS_APPROVED}
	{include file="bitpackage:liberty/comments.tpl"}
{/if}

{if $serviceViewTpls.categorization and !$showDescriptionsOnly}
	{include file=$serviceViewTpls.categorization"}
{/if}
{/strip}
