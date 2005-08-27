{* $Header: /cvsroot/bitweaver/_bit_articles/templates/list_articles.tpl,v 1.5 2005/08/27 09:48:36 squareing Exp $ *}
<div class="floaticon">{bithelp}</div>

{strip}
<div class="listing articles">
	<div class="header">
		<h1>{tr}Articles{/tr}</h1>
	</div>

	<div class="body">
		<div class="navbar">
			<ul>
				<li>{biticon ipackage=liberty iname=sort iexplain="sort by"}</li>
				{if $art_list_title eq 'y'}
					<li>{smartlink ititle='Title' isort='title' offset=$offset type=$find_type topic=$find_topic}</li>
				{/if}
				{if $art_list_author eq 'y'}
					<li>{smartlink ititle='Author' isort='author_name' offset=$offset type=$find_type topic=$find_topic}</li>
				{/if}
				{if $art_list_date eq 'y'}
					<li>{smartlink ititle='Publish Date' isort='publish_date' offset=$offset type=$find_type topic=$find_topic}</li>
				{/if}
				{if $art_list_expire eq 'y'}
					<li>{smartlink ititle='Expire Date' isort='expire_date' offset=$offset type=$find_type topic=$find_topic}</li>
				{/if}
			</ul>
		</div>

		<div class="clear"></div>

		<table class="data">
			<caption>{tr}Articles Listing{/tr}</caption>
			<tr>
				{if $art_list_img eq 'y'}
					<th style="width:10px;">{tr}Image{/tr}</th>
				{/if}
				{if $art_list_type eq 'y'}
					<th>{smartlink ititle='Type' isort='type_name' offset=$offset type=$find_type topic=$find_topic}</th>
				{/if}
				{if $art_list_topic eq 'y'}
					<th>{smartlink ititle='Topic' isort='topic_name' offset=$offset type=$find_type topic=$find_topic}</th>
				{/if}
				{if $art_list_status eq 'y'}
					<th>{smartlink ititle='Status' isort='status_id' offset=$offset type=$find_type topic=$find_topic}</th>
				{/if}
				{if $art_list_reads eq 'y'}
					<th>{smartlink ititle='Reads' isort='hits' offset=$offset type=$find_type topic=$find_topic}</th>
				{/if}
				<th>{tr}Action{/tr}</th>
			</tr>
			{cycle values="even,odd" print=false}
			{foreach item=article from=$listpages}
				<tr class="{cycle advance=false}">
					{if $art_list_img eq 'y'}
						<td rowspan="2">
							{if $article.img_url}<img src="{$article.img_url}"/>{/if}
						</td>
					{/if}

					<td colspan="5">
						{if $art_list_title eq 'y'}
							<h2>
								{if $gBitUser->hasPermission( 'bit_p_read_article' )}
									<a href="{$smarty.const.ARTICLES_PKG_URL}read.php?article_id={$article.article_id}">
								{/if}
								{$article.title}
								{if $gBitUser->hasPermission( 'bit_p_read_article' )}
									</a>
								{/if}
							</h2>
						{/if}

						{if $art_list_author eq 'y'}
							{tr}Created by {displayname user=$article.author_name}{/tr}
						{/if}

						{if $art_list_date eq 'y' or $art_list_expire eq 'y'}<br />{/if}

						{if $art_list_date eq 'y' and $art_list_expire eq 'y'}
							{tr}Displayed from <strong>{$article.publish_date|bit_short_datetime}</strong> until <strong>{$article.expire_date|bit_short_datetime}</strong>{/tr}
						{elseif $art_list_date eq 'y'}
							{tr}Displayed from <strong>{$article.publish_date|bit_short_datetime}</strong>{/tr}
						{elseif $art_list_expire eq 'y'}
							{tr}Displayed until <strong>{$article.expire_date|bit_short_datetime}</strong>{/tr}
						{/if}
					</td>
				</tr>

				<tr class="{cycle}">
					{if $art_list_type eq 'y'}
						<td>{tr}{$article.type_name}{/tr}</td>
					{/if}
					{if $art_list_topic eq 'y'}
						<td>{$article.topic_name}</td>
					{/if}
					{if $art_list_status eq 'y'}
						<td>{$article.status_name}</td>
					{/if}
					{if $art_list_reads eq 'y'}
						<td style="text-align:right;">{$article.hits}</td>
					{/if}
					<td>
						{if $bit_p_edit_article eq 'y' or ($article.author eq $user and $article.creator_edit eq 'y')}
							<a title="{tr}Edit{/tr}" href="{$smarty.const.ARTICLES_PKG_URL}edit.php?article_id={$article.article_id}"><img class="icon" src="{$smarty.const.KERNEL_PKG_URL}icons/edit.gif" alt="{tr}Edit{/tr}" /></a>
						{/if}
						{if $bit_p_remove_article eq 'y'}
							<a title="{tr}Remove{/tr}" href="{$smarty.const.ARTICLES_PKG_URL}list.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$article.article_id}"><img class="icon" src="{$smarty.const.KERNEL_PKG_URL}icons/delete.gif" alt="{tr}Remove{/tr}" /></a>
						{/if}
					</td>
				</tr>
			{foreachelse}
				<tr class="norecords">
					<td colspan="11">
						{tr}No records found{/tr}
					</td>
				</tr>
			{/foreach}
		</table>

		{include file="bitpackage:kernel/pagination.tpl"}
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
