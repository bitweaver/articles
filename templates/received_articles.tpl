{* $Header: /cvsroot/bitweaver/_bit_articles/templates/Attic/received_articles.tpl,v 1.2 2005/08/13 09:34:09 squareing Exp $ *}
<div class="floaticon">{bithelp}</div>

<div class="admin articles">
<div class="header">
<h1><a href="{$smarty.const.ARTICLES_PKG_URL}received_articles.php">{tr}Received articles{/tr}</a></h1>
</div>

<div class="body">

{if $preview eq 'y'}
<h2>{tr}Preview{/tr}</h2>
<div class="articletitle">
<span class="titlea">{$title}</span><br />
<span class="titleb">{tr}By:{/tr} {$author_name} {tr}on:{/tr} {$publish_date|bit_short_datetime} (0 {tr}reads{/tr})</span>
</div>
<div class="articleheading">
<table>
<tr><td  valign="top">
{if $use_image eq 'y'}
  <img class="icon" alt="{tr}Article image{/tr}" src="received_article_image.php?id={$received_article_id}" />
{else}
  <img class="icon" alt="{tr}Topic image{/tr}" src="topic_image.php?id={$topic}" />
{/if}
</td><td  valign="top">
<span class="articleheading">{$parsed_heading}</span>
</td></tr>
</table>
</div>
<div class="articletrailer">
(xx bytes
)
</div>

<div class="articlebody">
{$parsed_body}
</div>
{/if}


{if $received_article_id > 0}
<h2>{tr}Edit received article{/tr}</h2>
<form action="{$smarty.const.ARTICLES_PKG_URL}received_articles.php" method="post">
<input type="hidden" name="received_article_id" value="{$received_article_id|escape}" />
<input type="hidden" name="created" value="{$created|escape}" />
<input type="hidden" name="image_name" value="{$image_name|escape}" />
<input type="hidden" name="image_size" value="{$image_size|escape}" />
<table class="panel">
<tr><td>{tr}Title{/tr}:</td><td><input type="text" name="title" value="{$title|escape}" /></td></tr>
<tr><td>{tr}Author Name{/tr}:</td><td><input type="text" name="author_name" value="{$author_name|escape}" /></td></tr>

<tr><td>{tr}Type{/tr}</td><td>
<select id="articletype" name="type" onchange="javascript:chgArtType();">
{section name=t loop=$types}
<option value="{$types[t].type|escape}" {if $type eq $types[t].type}selected="selected"{/if}>{$types[t].type}</option>
{/section}
</select>
{if $bit_p_admin_cms eq 'y'}<a href="{$smarty.const.ARTICLES_PKG_URL}article_types.php">{tr}Admin types{/tr}</a>{/if}
</td></tr>
<tr id="isreview" {if $type ne 'Review'}style="display:none;"{else}style="display:block;"{/if}><td>{tr}Rating{/tr}</td><td>
<select name="rating">
<option value="10" {if $rating eq 10}selected="selected"{/if}>10</option>
<option value="9.5" {if $rating eq "9.5"}selected="selected"{/if}>9.5</option>
<option value="9" {if $rating eq 9}selected="selected"{/if}>9</option>
<option value="8.5" {if $rating eq "8.5"}selected="selected"{/if}>8.5</option>
<option value="8" {if $rating eq 8}selected="selected"{/if}>8</option>
<option value="7.5" {if $rating eq "7.5"}selected="selected"{/if}>7.5</option>
<option value="7" {if $rating eq 7}selected="selected"{/if}>7</option>
<option value="6.5" {if $rating eq "6.5"}selected="selected"{/if}>6.5</option>
<option value="6" {if $rating eq 6}selected="selected"{/if}>6</option>
<option value="5.5" {if $rating eq "5.5"}selected="selected"{/if}>5.5</option>
<option value="5" {if $rating eq 5}selected="selected"{/if}>5</option>
<option value="4.5" {if $rating eq "4.5"}selected="selected"{/if}>4.5</option>
<option value="4" {if $rating eq 4}selected="selected"{/if}>4</option>
<option value="3.5" {if $rating eq "3.5"}selected="selected"{/if}>3.5</option>
<option value="3" {if $rating eq 3}selected="selected"{/if}>3</option>
<option value="2.5" {if $rating eq "2.5"}selected="selected"{/if}>2.5</option>
<option value="2" {if $rating eq 2}selected="selected"{/if}>2</option>
<option value="1.5" {if $rating eq "1.5"}selected="selected"{/if}>1.5</option>
<option value="1" {if $rating eq 1}selected="selected"{/if}>1</option>
<option value="0.5" {if $rating eq "0.5"}selected="selected"{/if}>0.5</option>
</select>
</td></tr>



<tr><td>{tr}Use Image{/tr}:</td><td>
<select name="use_image">
<option value="y" {if $use_image eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value="n" {if $use_image eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
<tr><td>{tr}Image x size{/tr}:</td><td><input type="text" name="image_x" value="{$image_x|escape}" /></td></tr>
<tr><td>{tr}Image y size{/tr}:</td><td><input type="text" name="image_y" value="{$image_y|escape}" /></td></tr>
<tr><td>{tr}Image name{/tr}:</td><td>{$image_name}</td></tr>
<tr><td>{tr}Image size{/tr}:</td><td>{$image_size}</td></tr>
{if $use_image eq 'y'}
<tr><td>{tr}Image{/tr}:</td><td>
<img alt="article image" width="{$image_x}" height="{$image_y}" src="received_article_image.php?id={$received_article_id}" />
</td></tr>
{/if}
<tr><td>{tr}Created{/tr}:</td><td>{$created|bit_short_datetime}</td></tr>
<tr><td>{tr}Publishing date{/tr}:</td><td>
{html_select_date time=$publish_date end_year="+1"} at {html_select_time time=$publish_date display_seconds=false}
</td></tr>
<tr><td>{tr}Heading{/tr}:</td><td><textarea rows="5" cols="40" name="heading">{$heading|escape}</textarea></td></tr>
<tr><td>{tr}Heading{/tr}:</td><td><textarea rows="25" cols="40" name="body">{$body|escape}</textarea></td></tr>
<tr class="panelsubmitrow"><td colspan="2"><input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;<input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
<tr><td>{tr}Accept Article{/tr}</td><td>
{tr}Topic{/tr}:<select name="topic">
{section name=t loop=$topics}
<option value="{$topics[t].topic_id|escape}" {if $topic eq $topics[t].topic_id}selected="selected"{/if}>{$topics[t].name}</option>
{/section}
</select><input type="submit" name="accept" value="{tr}accept{/tr}" /></td></tr>
</table>
</form>
{/if}

<h2>{tr}Received Articles{/tr}</h2>
<table class="find">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="{$smarty.const.ARTICLES_PKG_URL}received_articles.php">
     <input type="text" name="find" />
     <input type="submit" name="search" value="{tr}find{/tr}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<table class="data">
<tr>
<th><a href="{$smarty.const.ARTICLES_PKG_URL}received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'received_article_id_desc'}received_article_id_asc{else}received_article_id_desc{/if}">{tr}ID{/tr}</a></th>
<th><a href="{$smarty.const.ARTICLES_PKG_URL}received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}title{/tr}</a></th>
<th><a href="{$smarty.const.ARTICLES_PKG_URL}received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'received_date_desc'}received_date_asc{else}received_date_desc{/if}">{tr}Date{/tr}</a></th>
<th><a href="{$smarty.const.ARTICLES_PKG_URL}received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'received_from_site_desc'}received_from_site_asc{else}received_from_site_desc{/if}">{tr}Site{/tr}</a></th>
<th><a href="{$smarty.const.ARTICLES_PKG_URL}received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'received_from_user_desc'}received_from_user_asc{else}received_from_user_desc{/if}">{tr}User{/tr}</a></th>
<th>{tr}action{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$channels}
<tr class="{cycle}">
<td>{$channels[user].received_article_id}</td>
<td>{$channels[user].title}{if $channels[user].type eq 'Review'}(r){/if}</td>
<td>{$channels[user].received_date|bit_short_datetime}</td>
<td>{$channels[user].received_from_site}</td>
<td>{$channels[user].received_from_user}</td>
<td>
   <a href="{$smarty.const.ARTICLES_PKG_URL}received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;received_article_id={$channels[user].received_article_id}"><img class="icon" src="{$smarty.const.KERNEL_PKG_URL}icons/edit.gif" alt="{tr}edit{/tr}" title="{tr}edit{/tr}" /></a>
   <a href="{$smarty.const.ARTICLES_PKG_URL}received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].received_article_id}"><img class="icon" src="{$smarty.const.KERNEL_PKG_URL}icons/delete.gif" alt="{tr}remove{/tr}" title="{tr}remove{/tr}" /></a>
</td>
</tr>
{sectionelse}
	<tr class="norecords"><td colspan="6">{tr}No record found{/tr}</td></tr>
{/section}
</table>

</div> {* end .body *}

{include file="bitpackage:articles/articles_nav.tpl"}

{include file="bitpackage:kernel/pagination.tpl"}

</div> {* end .article *}
