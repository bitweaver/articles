{* $Header: /cvsroot/bitweaver/_bit_articles/templates/Attic/list_submissions.tpl,v 1.1 2005/06/30 01:10:46 bitweaver Exp $ *}
<div class="listing articles">
<div class="header">
<h1><a href="{$gBitLoc.ARTICLES_PKG_URL}list_submissions.php">{tr}Submissions{/tr}</a></h1>
</div>

<div class="body">
<table class="find">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="{$gBitLoc.ARTICLES_PKG_URL}list_submissions.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="data">
<tr>
{if $art_list_title eq 'y'}
	<th><a href="{$gBitLoc.ARTICLES_PKG_URL}list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></th>
{/if}{if $art_list_topic eq 'y'}
	<th><a href="{$gBitLoc.ARTICLES_PKG_URL}list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'topic_name_desc'}topic_name_asc{else}topic_name_desc{/if}">{tr}Topic{/tr}</a></th>
{/if}{if $art_list_date eq 'y'}
	<th><a href="{$gBitLoc.ARTICLES_PKG_URL}list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'publish_date_desc'}publish_date_asc{else}publish_date_desc{/if}">{tr}PublishDate{/tr}</a></th>
{/if}{if $art_list_img eq 'y'}
<th>{tr}Image{/tr}</th>
{/if}{if $art_list_author eq 'y'}
	<th><a href="{$gBitLoc.ARTICLES_PKG_URL}list_submissions.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'author_desc'}author_asc{else}author_desc{/if}">{tr}User{/tr}</a></th>
{/if}<th>{tr}Action{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{section name=changes loop=$listpages}
<tr class="{cycle}">
{if $art_list_title eq 'y'}
	<td><a title="{$listpages[changes].title}" href="{$gBitLoc.ARTICLES_PKG_URL}edit_submission.php?sub_id={$listpages[changes].sub_id}">{$listpages[changes].title|truncate:20:"...":true}</a>
	{*if $listpages[changes].type eq 'Review'}(r){/if*}
	</td>
{/if}{if $art_list_topic eq 'y'}
	<td>{$listpages[changes].topic_name}</td>
{/if}{if $art_list_date eq 'y'}
	<td>{$listpages[changes].publish_date|bit_short_datetime}</td>
{/if}{if $art_list_img eq 'y'}
	<td>{$listpages[changes].img_url}</td>
{/if}
{if $art_list_author eq 'y'}
<td>{$listpages[changes].author}</td>
{/if}
<td>
	{if $gBitUser->hasPermission('bit_p_edit_submission') || $listpages[changes].user_id eq $gBitUser->mUserId}
		<a href="{$gBitLoc.ARTICLES_PKG_URL}edit.php?article_id={$listpages[changes].article_id}"><img class="icon" src="{$gBitLoc.KERNEL_PKG_URL}icons/edit.gif" alt="{tr}Edit{/tr}" title="{tr}Edit{/tr}" /></a>
	{/if}
	{if $gBitUser->hasPermission('bit_p_admin_articles') || $gBitUser->hasPermission('bit_p_approve_submission')}
		<a href="{$gBitLoc.ARTICLES_PKG_URL}list_submissions.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;deny={$listpages[changes].article_id}"><img class="icon" src="{$gBitLoc.KERNEL_PKG_URL}icons/delete.gif" alt="{tr}Remove{/tr}" title="{tr}Deny{/tr}" /></a>
		<a href="{$gBitLoc.ARTICLES_PKG_URL}list_submissions.php?approve={$listpages[changes].article_id}"><img class="icon" src="{$gBitLoc.IMG_PKG_URL}icons2/post.gif" alt="{tr}Approve{/tr}" title="{tr}Approve{/tr}" /></a>
	{/if}
</td>
</tr>
{sectionelse}
<tr class="norecords"><td colspan="7">{tr}No records found{/tr}</td></tr>
{/section}
</table>

</div> {* end .body *}

{include file="bitpackage:kernel/pagination.tpl"}

</div> {* end .admin *}
