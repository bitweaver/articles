{* $Header: /cvsroot/bitweaver/_bit_articles/templates/list_articles.tpl,v 1.9.2.3 2006/01/26 13:45:04 squareing Exp $ *}
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

		{include file="bitpackage:articles/article_filter_inc.tpl"}

		{form}
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
								{if $article.image_url}
									{if $gBitUser->hasPermission( 'bit_p_read_article' )}
										<a href="{$article.display_url}">
									{/if}
									<img src="{$article.image_url}" title="{$article.title}" alt="{tr}Article Image{/tr}" />
									{if $gBitUser->hasPermission( 'bit_p_read_article' )}
										</a>
									{/if}
								{/if}
							</td>
						{/if}

						<td colspan="5">
							{if $art_list_title eq 'y'}
								<h2>
									{if $gBitUser->hasPermission( 'bit_p_read_article' )}
										<a href="{$article.display_url}">{$article.title}</a>
									{else}
										{$article.title}
									{/if}
								</h2>
							{/if}

							{if $art_list_author eq 'y'}
								{tr}Created by{/tr}: {displayname user=$article.author_name}
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
						<td style="text-align:right;">
							{if $article.status_id eq $smarty.const.ARTICLE_STATUS_PENDING and $gBitUser->hasPermission( 'bit_p_approve_submission' )}
								{smartlink ititle="Approve Article" ibiticon="liberty/success" sort_mode=$sort_mode status_id=$smarty.request.status_id article_id=$article.article_id set_status_id=$smarty.const.ARTICLE_STATUS_APPROVED action=approve}
							{/if}

							{if $gBitUser->hasPermission( 'bit_p_edit_article' ) or ( $article.author eq $user and $article.creator_edit eq 'y' )}
								{smartlink ititle="Edit" ifile="edit.php" ibiticon="liberty/edit" article_id=$article.article_id}
							{/if}

							{if $gBitUser->hasPermission( 'bit_p_remove_article' )}
								{smartlink ititle="Remove" ibiticon="liberty/delete" action=remove remove_article_id=$article.article_id status_id=$smarty.request.status_id}
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
		{/form}

		{include file="bitpackage:kernel/pagination.tpl"}
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
