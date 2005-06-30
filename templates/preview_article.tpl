{* $Header: /cvsroot/bitweaver/_bit_articles/templates/preview_article.tpl,v 1.1 2005/06/30 01:10:46 bitweaver Exp $ *}
<h2>{tr}Preview{/tr} {$title}</h2>
<div class="display articles">
<div class="articleheader">
<h1 class="articletitle">{$title}</h1>
<div class="articledate">{tr}By{/tr} {$author_name} {tr}on{/tr} {$publish_date|bit_short_datetime} {$reads} {tr}reads{/tr}</div>
</div>

{if $type eq 'Review'}
<div class="articleheading">
{tr}Rating{/tr}: 
{repeat count=$rating}
<img src="{$gBitLoc.IMG_PKG_URL}icons/blue.gif" class="icon" alt="" />
{/repeat}
{if $rating > $entrating}
<img src="{$gBitLoc.IMG_PKG_URL}icons/bluehalf.gif" class="icon" alt="" />
{/if}
({$rating}/10)
</div>
{/if}


<div class="articleheading">
<table>
<tr><td class="articleheadingimage">
{if $use_image eq 'y'}
  {if $hasImage eq 'y'}
    {if $article_id gt 0}
      <img class="icon" alt="{tr}Article image{/tr}" src="article_image.php?id={$article_id}" />
    {else}
      <img class="icon" alt="{tr}Article image{/tr}" src="{$tempimg}" />
    {/if}
  {else}
    <img class="icon" alt="{tr}Topic image{/tr}" src="topic_image.php?id={$topic_id}" />
  {/if}
{else}
  <img class="icon" alt="{tr}Topic image{/tr}" src="topic_image.php?id={$topic_id}" />
{/if}
</td><td  valign="top">
<div class="articleheadingtext">{$parsed_heading}</div>
</td></tr>
</table>
</div>

<div class="articlebody">
{$parsed_body}
</div>
<div class="articleexpand">
{$size} bytes
</div>
</div>
