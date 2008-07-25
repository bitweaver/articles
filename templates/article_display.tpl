{* $Header: /cvsroot/bitweaver/_bit_articles/templates/article_display.tpl,v 1.50 2008/07/25 14:54:49 wolff_borg Exp $ *}
{strip}
{if !$showDescriptionsOnly}
	{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav' serviceHash=$article}
{/if}

<div class="{$outer_div|default:"post"}">
	<div class="floaticon">
		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon' serviceHash=$article}
		{if $gContent->hasUserPermission( 'p_articles_read_history' ) && !$version && $article.version>1}
			{smartlink ititle="View History" ipackage=articles ifile="article_history.php" ibiticon="icons/appointment-new" article_id=$article.article_id}
		{/if}
		{if $gContent->hasUserPermission('p_articles_edit') || ($gBitUser->getField('user_id') != -1 && $gBitUser->getField('user_id') == $article.user_id ) }
			<a href="{$smarty.const.ARTICLES_PKG_URL}edit.php?article_id={$article.article_id}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain=edit}</a>
		{/if}
		{*<a style="display:none;" href="{$smarty.const.ARTICLES_PKG_URL}print.php?article_id={$article.article_id}">{biticon ipackage="icons" iname="document-print" iexplain=print}</a>*}
		{if $gContent->hasUserPermission( 'p_articles_remove' )}
			{smartlink ititle="Remove" ipackage=articles ifile="list.php" ibiticon="icons/edit-delete" action=remove remove_article_id=$article.article_id status_id=$smarty.request.status_id}
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
					&bull; {$article.publish_date|reltime}
				{/if}
			</div><!-- end .date -->
		{/if}
	</div>

	<div class="body"{if $gBitUser->getPreference( 'users_double_click' ) and $gContent->hasUserPermission( 'p_articles_edit' )} ondblclick="location.href='{$smarty.const.ARTICLES_PKG_URL}edit.php?article_id={$article.article_id}';"{/if}>
		<div class="content">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='body' serviceHash=$article}
			{* deal with the article image if there is one *}
			{if $article.show_image eq 'y'}
				<div class="image">
					{assign var=size value=$gBitSystem->getConfig('articles_image_size','small')}
					{if $showDescriptionsOnly and $article.has_more}
						<a href="{$article.display_url}">
							<img class="icon" alt="{$article.topic_name|default:$article.title|escape}" title="{$article.topic_name|default:$article.title|escape}" src="{$article.thumbnail_url.$size}"/>
						</a>
					{else}
						{include file=$gLibertySystem->getMimeTemplate('inline',$article.primary_attachment.attachment_plugin_guid) attachment=$article.primary_attachment thumbsize=$size}
					{/if}
				</div>
			{/if}

			{if $article.show_image eq 'y'}{assign var=hideprimary value=y}{/if}
			{if $gBitSystem->isFeatureActive( 'articles_attachments' ) && $gBitSystem->isFeatureActive( 'liberty_auto_display_attachment_thumbs' )}
				{include file="bitpackage:liberty/storage_thumbs.tpl"}
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

			{if $showDescriptionsOnly}
				{$article.parsed_description}
			{else}
				{$article.parsed}
			{/if}
		</div>

		<div class="footer">
			{if $article.show_reads eq 'y'}
				{assign var=spacer value=TRUE}
				{$article.hits|default:0} {tr}reads{/tr}
			{/if}

			{if $showDescriptionsOnly and $article.has_more}
				{if $spacer}&nbsp; &bull; &nbsp;{/if}
				{assign var=spacer value=TRUE}
				<a class="more" href="{$article.display_url}">{tr}Read More&hellip;{/tr}</a>
			{/if}

			{if $article.allow_comments eq 'y'}
				{if $spacer}&nbsp; &bull; &nbsp;{/if}
				{if $showDescriptionsOnly}<a href="{$article.display_url}#editcomments">{/if}
					{$article.num_comments} 
					&nbsp;
					{if $article.num_comments eq 1}
						{tr}Comment{/tr}
					{else}
						{tr}Comments{/tr}
					{/if}
				{if $showDescriptionsOnly}</a>{/if}
			{/if}
		</div>

		{if $article.status_id == $smarty.const.ARTICLE_STATUS_PENDING && !$preview}
			<div class="row">
				{formlabel label="Approve or deny Submission"}
				{forminput}
					{if $gContent->hasUserPermission( 'p_articles_approve_submission' )}
						<a href="{$smarty.const.ARTICLES_PKG_URL}list.php?status_id={$article.status_id}&amp;article_id={$article.article_id}&amp;content_id={$article.content_id}&amp;set_status_id=300&amp;action=approve">{biticon ipackage=icons iname="large/dialog-ok" iexplain="Approve Article"}</a> &nbsp;
					{/if}

					{if $gContent->hasUserPermission( 'p_articles_remove' ) || $gContent->hasUserPermission( 'p_articles_remove_submission' )}
						<a href="{$smarty.const.ARTICLES_PKG_URL}list.php?status_id={$article.status_id}&amp;remove_article_id={$article.article_id}&amp;action=remove">{biticon ipackage=icons iname="large/dialog-cancel" iexplain="Remove Article"}</a>
					{/if}
				{/forminput}
			</div>
		{/if}

	</div><!-- end .body -->
</div><!-- end .article -->

{if $print_page ne 'y' and $article.allow_comments eq 'y' and !$preview && !$showDescriptionsOnly and $article.status_id eq $smarty.const.ARTICLE_STATUS_APPROVED}
	{include file="bitpackage:liberty/comments.tpl"}
{/if}

{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='view' serviceHash=$article}
{/strip}
