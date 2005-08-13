{popup_init src="`$smarty.const.THEMES_PKG_URL`overlib.js"}
<div class="floaticon">{bithelp}</div>
{if $preview}
	{include file="bitpackage:articles/submission_display.tpl"}
{/if}

<div class="admin articles">
<div class="header">
<h1><a href="{$smarty.const.ARTICLES_PKG_URL}edit_submission.php">{tr}Edit{/tr}: {$title}</a></h1>
</div>

<div class="body">
<form enctype="multipart/form-data" method="post" action="{$smarty.const.ARTICLES_PKG_URL}edit_submission.php" id="tikieditsubmission">
<input type="hidden" name="sub_id" value="{$sub_id|escape}" />
<input type="hidden" name="image_data" value="{$image_data|escape}" />
<input type="hidden" name="use_image" value="{$use_image|escape}" />
<input type="hidden" name="image_type" value="{$image_type|escape}" />
<input type="hidden" name="image_name" value="{$image_name|escape}" />
<input type="hidden" name="image_size" value="{$image_size|escape}" />
<table class="panel">
<tr><td>{tr}Title{/tr}</td><td><input type="text" name="title" value="{$title|escape}" /></td></tr>
<tr><td>{tr}Author Name{/tr}</td><td><input type="text" name="author_name" value="{$author_name|escape}" /></td></tr>
<tr><td>{tr}Topic{/tr}</td><td>
<select name="topic_id">
{section name=t loop=$topics}
<option value="{$topics[t].topic_id|escape}" {if $topic_id eq $topics[t].topic_id}selected="selected"{/if}>{$topics[t].name}</option>
{/section}
<option value="" {if $topic_id eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
</select>
{if $bit_p_admin_cms eq 'y'}<a href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_topics.php">{tr}Admin topics{/tr}</a>{/if}
</td></tr>

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
<option value="10" {if $rating eq 10}selected="selected"{/if}>{tr}10{/tr}</option>
<option value="9.5" {if $rating eq "9.5"}selected="selected"{/if}>{tr}9.5{/tr}</option>
<option value="9" {if $rating eq 9}selected="selected"{/if}>{tr}9{/tr}</option>
<option value="8.5" {if $rating eq "8.5"}selected="selected"{/if}>{tr}8.5{/tr}</option>
<option value="8" {if $rating eq 8}selected="selected"{/if}>{tr}8{/tr}</option>
<option value="7.5" {if $rating eq "7.5"}selected="selected"{/if}>{tr}7.5{/tr}</option>
<option value="7" {if $rating eq 7}selected="selected"{/if}>{tr}7{/tr}</option>
<option value="6.5" {if $rating eq "6.5"}selected="selected"{/if}>{tr}6.5{/tr}</option>
<option value="6" {if $rating eq 6}selected="selected"{/if}>{tr}6{/tr}</option>
<option value="5.5" {if $rating eq "5.5"}selected="selected"{/if}>{tr}5.5{/tr}</option>
<option value="5" {if $rating eq 5}selected="selected"{/if}>{tr}5{/tr}</option>
<option value="4.5" {if $rating eq "4.5"}selected="selected"{/if}>{tr}4.5{/tr}</option>
<option value="4" {if $rating eq 4}selected="selected"{/if}>{tr}4{/tr}</option>
<option value="3.5" {if $rating eq "3.5"}selected="selected"{/if}>{tr}3.5{/tr}</option>
<option value="3" {if $rating eq 3}selected="selected"{/if}>{tr}3{/tr}</option>
<option value="2.5" {if $rating eq "2.5"}selected="selected"{/if}>{tr}2.5{/tr}</option>
<option value="2" {if $rating eq 2}selected="selected"{/if}>{tr}2{/tr}</option>
<option value="1.5" {if $rating eq "1.5"}selected="selected"{/if}>{tr}1.5{/tr}</option>
<option value="1" {if $rating eq 1}selected="selected"{/if}>{tr}1{/tr}</option>
<option value="0.5" {if $rating eq "0.5"}selected="selected"{/if}>{tr}0.5{/tr}</option>
</select>
</td></tr>

<tr><td>{tr}Image{/tr}</td><td><input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
<input name="userfile1" type="file" /></td></tr>
{if $hasImage eq 'y'}
  <tr><td>Image: </td><td>{$image_name} [{$image_type}] ({$image_size} bytes)</td></tr>
  {if $tempimg ne 'n'}
    <tr><td>Image:</td><td>
    <img class="icon" alt="{tr}Article image{/tr}" src="{$smarty.const.TEMP_PKG_URL}{$tempimg}" {if $image_x > 0}width="{$image_x}"{/if}{if $image_y > 0 }height="{$image_y}"{/if} />
    </td></tr>
  {/if}
{/if}
<tr><td>{tr}Use image{/tr}</td><td>
<input type="checkbox" name="use_image" {if $use_image eq 'y'}checked="checked"{/if}/>
</td></tr>
<tr><td>{tr}image size x{/tr}</td><td><input type="text" name="image_x" value="{$image_x|escape}" /></td></tr>
<tr><td>{tr}image size y{/tr}</td><td><input type="text" name="image_y" value="{$image_y|escape}" /></td></tr>

{if $gBitSystemPrefs.feature_cms_templates eq 'y' and $bit_p_use_content_templates eq 'y'}
<tr><td>{tr}Apply template{/tr}</td><td>
<select name="template_id" onchange="javascript:document.getElementById('tikieditsubmission').submit();">
<option value="0">{tr}none{/tr}</option>
{section name=ix loop=$templates}
<option value="{$templates[ix].template_id|escape}">{tr}{$templates[ix].name}{/tr}</option>
{/section}
</select>
</td></tr>
{/if}

{include file="bitpackage:categories/categorize.tpl"}

<tr><td>{tr}Heading{/tr}</td><td><textarea id="subheading" name="heading" rows="5" cols="80">{$heading|escape}</textarea></td></tr>
<tr><td>{tr}Quicklinks{/tr}</td><td>
{assign var=area_name value="subbody"}
{include file="bitpackage:quicktags/edit_help_tool.tpl"}
</td>
</tr>
<tr><td>{tr}Body{/tr}</td><td>
<b>{tr}Use ...page... to separate pages in a multi-page article{/tr}</b><br />
<textarea id="subbody" name="body" rows="25" cols="80">{$body|escape}</textarea></td></tr>
{if $cms_spellcheck eq 'y'}
<tr><td>{tr}Spellcheck{/tr}: </td><td><input type="checkbox" name="spellcheck" {if $spellcheck eq 'y'}checked="checked"{/if}/></td></tr>
{/if}
<tr><td>{tr}Publish Date{/tr}</td><td>
{html_select_date prefix="publish_" time=$publish_dateSite end_year="+1"} {tr}at{/tr} <span dir="ltr">{html_select_time prefix="publish_" time=$publish_dateSite display_seconds=false}
&nbsp;{$siteTimeZone}
</span>
</td></tr>
<tr><td>{tr}Expiration Date{/tr}</td><td>
{html_select_date prefix="expire_" time=$expire_dateSite end_year="+1"} {tr}at{/tr} <span dir="ltr">{html_select_time prefix="expire_" time=$expire_dateSite display_seconds=false}
&nbsp;{$siteTimeZone}
</span>
</td></tr>
{if $bit_p_use_HTML eq 'y'}
<tr><td>{tr}Allow HTML{/tr}</td><td><input type="checkbox" name="allowhtml" {if $allowhtml eq 'y'}checked="checked"{/if}/></td></tr>
{/if}
<tr class="panelsubmitrow">
<td colspan="2"><input type="submit" name="preview" value="{tr}preview{/tr}" />
<input type="submit" name="save" value="{tr}save{/tr}" />
</td></tr>
</table>
</form>

</div> {* end .body *}
{include file="bitpackage:articles/articles_nav.tpl"}
</div> {* end .admin *}
{include file="bitpackage:kernel/edit_help.tpl"}
