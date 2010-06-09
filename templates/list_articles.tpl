{* $Header$ *}
<div class="floaticon">{bithelp}</div>

{strip}
<div class="listing articles">
	<div class="header">
		<h1>{tr}Articles{/tr}</h1>
	</div>

	<div class="body">

		{formfeedback hash=$feedback}

		<div class="navbar">
			<ul>
				<li>{biticon ipackage="icons" iname="emblem-symbolic-link" iexplain="sort by"}</li>
				{if $gBitSystem->isFeatureActive( 'articles_list_title' )}
					<li>{smartlink ititle='Title' isort='title' offset=$offset type=$find_type topic=$find_topic}</li>
				{/if}
				{if $gBitSystem->isFeatureActive( 'articles_list_author' )}
					<li>{smartlink ititle='Author' isort='author_name' offset=$offset type=$find_type topic=$find_topic}</li>
				{/if}
				{if $gBitSystem->isFeatureActive( 'articles_list_date' )}
					<li>{smartlink ititle='Publish Date' isort='publish_date' offset=$offset type=$find_type topic=$find_topic}</li>
				{/if}
				{if $gBitSystem->isFeatureActive( 'articles_list_expire' )}
					<li>{smartlink ititle='Expire Date' isort='expire_date' offset=$offset type=$find_type topic=$find_topic}</li>
				{/if}
				{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='list_sort'}
			</ul>
		</div>

		<div class="clear"></div>

		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='list_options'}

		{include file="bitpackage:articles/article_filter_inc.tpl"}

		{form}
			<table class="data">
				<caption>{tr}Articles Listing{/tr}</caption>
				<tr>
					{counter name=cols start=0 print=false}
					{if $gBitSystem->isFeatureActive( 'articles_list_img' )}
						<th style="width:10px;">{tr}Image{/tr}</th>
					{/if}
					{if $gBitSystem->isFeatureActive( 'articles_list_type' )}
						<th>{smartlink ititle='Type' isort='type_name' offset=$offset type=$find_type topic=$find_topic}</th>
						{counter name=cols assign=cols print=false}
					{/if}
					{if $gBitSystem->isFeatureActive( 'articles_list_topic' )}
						<th>{smartlink ititle='Topic' isort='topic_name' offset=$offset type=$find_type topic=$find_topic}</th>
						{counter name=cols assign=cols print=false}
					{/if}
					{if $gBitSystem->isFeatureActive( 'articles_list_status' )}
						<th>{smartlink ititle='Status' isort='status_id' offset=$offset type=$find_type topic=$find_topic}</th>
						{counter name=cols assign=cols print=false}
					{/if}
					{if $gBitSystem->isFeatureActive( 'articles_list_reads' )}
						<th>{smartlink ititle='Reads' isort='hits' offset=$offset type=$find_type topic=$find_topic}</th>
						{counter name=cols assign=cols print=false}
					{/if}
					<th>{tr}Action{/tr}</th>
				</tr>
				{cycle values="even,odd" print=false}
				{foreach item=article from=$listpages}
					<tr class="{cycle advance=false}">
						{if $gBitSystem->isFeatureActive( 'articles_list_img' )}
							<td rowspan="2">
								{if $article.image_url}
									{if $gBitUser->hasPermission( 'p_articles_read' )}
										<a href="{$article.display_url}">
									{/if}
									<img src="{$article.image_url}" title="{$article.title|escape}" alt="{tr}Article Image{/tr}" />
									{if $gBitUser->hasPermission( 'p_articles_read' )}
										</a>
									{/if}
								{/if}
							</td>
						{/if}

						<td colspan="{$cols}">
							{if $gBitSystem->isFeatureActive( 'articles_list_title' )}
								<h2>
									{if $gBitUser->hasPermission( 'p_articles_read' )}
										<a href="{$article.display_url}">{$article.title|escape}</a>
									{else}
										{$article.title|escape}
									{/if}
								</h2>
							{/if}

							{if $gBitSystem->isFeatureActive( 'articles_list_author' )}
								{tr}Created by{/tr}: {displayname user_id=$article.user_id}
							{/if}

							{if $gBitSystem->isFeatureActive( 'articles_list_date' ) or $gBitSystem->isFeatureActive( 'articles_list_expire' )}<br />{/if}

							{if $gBitSystem->isFeatureActive( 'articles_list_date' ) and $gBitSystem->isFeatureActive( 'articles_list_expire' )}
								{tr}Displayed from <strong>{$article.publish_date|bit_short_datetime}</strong> until <strong>{$article.expire_date|bit_short_datetime}</strong>{/tr}
							{elseif $gBitSystem->isFeatureActive( 'articles_list_date' )}
								{tr}Displayed from <strong>{$article.publish_date|bit_short_datetime}</strong>{/tr}
							{elseif $gBitSystem->isFeatureActive( 'articles_list_expire' )}
								{tr}Displayed until <strong>{$article.expire_date|bit_short_datetime}</strong>{/tr}
							{/if}
						</td>

						<td style="text-align:right; vertical-align:top;">
							{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='list_actions' serviceHash=$article}
						</td>
					</tr>

					<tr class="{cycle}">
						{if $gBitSystem->isFeatureActive( 'articles_list_type' )}
							<td>
								{tr}{$article.type_name}{/tr}
								{if $article.use_ratings eq 'y'}
									<br />
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
							</td>
						{/if}
						{if $gBitSystem->isFeatureActive( 'articles_list_topic' )}
							<td>{$article.topic_name}</td>
						{/if}
						{if $gBitSystem->isFeatureActive( 'articles_list_status' )}
							<td>{$article.status_name}</td>
						{/if}
						{if $gBitSystem->isFeatureActive( 'articles_list_reads' )}
							<td style="text-align:right;">{$article.hits}</td>
						{/if}
						<td style="text-align:right;">
							{if $article.status_id eq $smarty.const.ARTICLE_STATUS_PENDING and $gBitUser->hasPermission( 'p_articles_approve_submission' )}
								{smartlink ititle="Approve Article" ibiticon="icons/dialog-ok" sort_mode=$sort_mode status_id=$smarty.request.status_id article_id=$article.article_id content_id=$article.content_id set_status_id=$smarty.const.ARTICLE_STATUS_APPROVED action=approve}
							{/if}

							{if $gBitUser->hasPermission( 'p_articles_update' ) or ( $article.author eq $user and $article.creator_edit eq 'y' )}
								{smartlink ititle="Edit" ifile="edit.php" ibiticon="icons/accessories-text-editor" article_id=$article.article_id}
							{/if}

							{if $gBitUser->hasPermission( 'p_articles_remove' )}
								{smartlink ititle="Remove" ibiticon="icons/edit-delete" action=remove remove_article_id=$article.article_id status_id=$smarty.request.status_id}
							{/if}
						</td>
					</tr>
				{foreachelse}
					<tr class="norecords">
						<td colspan="5">
							{tr}No records found{/tr}
						</td>
					</tr>
				{/foreach}
			</table>
		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
