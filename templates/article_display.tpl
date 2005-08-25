{* $Header: /cvsroot/bitweaver/_bit_articles/templates/article_display.tpl,v 1.5 2005/08/25 11:24:22 squareing Exp $ *}
{strip}
<div class="{$outer_div|default:"post"}">
	<div class="floaticon">
		{if $gContent->viewerCanEdit()}
			<a href="{$smarty.const.ARTICLES_PKG_URL}edit.php?article_id={$article.article_id}">{biticon ipackage=liberty iname=edit iexplain=edit}</a>
		{/if}
		<a style="display:none;" href="{$smarty.const.ARTICLES_PKG_URL}print.php?article_id={$article.article_id}">{biticon ipackage=liberty iname=print iexplain=print}</a>
		{if $bit_p_remove_article eq 'y'}
			<a href="{$smarty.const.ARTICLES_PKG_URL}list.php?remove={$article.article_id}">{biticon ipackage=liberty iname=delete iexplain=remove}</a>
		{/if}
	</div><!-- end .footer -->

	<div class="header">
		<h1>{$article.title}</h1>
		{if $article.show_author || $article.show_pubdate}
			<div class="date">
				{if $article.show_author}
					{tr}By{/tr} {displayname user=$article.author_name}&nbsp;
				{/if}
				{if $article.show_pubdate}
					{tr}on{/tr} {$article.publish_date|bit_short_datetime}
				{/if}
			</div><!-- end .date -->
		{/if}
	</div>

	<div class="body">
		{if $article.use_ratings eq 'y'}
			<span class="rating">
				{repeat count=$article.rating}
					{biticon ipackage=articles iname=rating iexplain="Article Rating"}
				{/repeat}
				&nbsp; ( {$article.rating} / 5 )
			</span>
		{/if}

		<div class="introduction">
			{if $article.show_image eq 'y' && $article.img_url}
				<div class="image">
					{if $article.read_more}<a href="{$smarty.const.ARTICLES_PKG_URL}read.php?article_id={$article.article_id}">{/if}
						<img class="icon" alt="{$article.topic_name}" src="{$article.img_url}"/>
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
				{if $showDescriptionsOnly}<a href="{$smarty.const.ARTICLES_PKG_URL}read.php?article_id={$article.article_id}#bitcomments">{/if}
					{tr}{$article.num_comments} Comment(s){/tr}
				{if $showDescriptionsOnly}</a>{/if}
			{/if}
		</div>
	</div><!-- end .body -->
</div><!-- end .article -->

{if $print_page ne 'y' and $article.allow_comments and !$preview && !$showDescriptionsOnly}
	{include file="bitpackage:liberty/comments.tpl"}
{/if}
{/strip}
