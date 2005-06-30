{* $Header: /cvsroot/bitweaver/_bit_articles/templates/Attic/submission_display.tpl,v 1.1 2005/06/30 01:10:46 bitweaver Exp $ *}
<div class="display articles">
<div class="header">
<h1>{$title}</h1>
{if ($show_author eq 'y') or ($show_pubdate eq 'y') or ($show_expdate eq 'y') or ($show_reads eq 'y')}
  <div class="date">
    {if $show_author eq 'y'}
      {tr}By{/tr} <a href="{$gBitLoc.USERS_PKG_URL}index.php?fHomepage={$author}">{$author_name}</a>
    {/if}
    {if $show_pubdate eq 'y'}
      {tr}on{/tr} {$publish_date|bit_short_datetime}
    {/if}
    {if $show_expdate eq 'y'}
      {tr}expires{/tr} {$expire_date|bit_short_datetime}
    {/if}
    {if $show_reads eq 'y'}
      {$reads} {tr}reads{/tr}
    {/if}
  </div>
{/if}
</div>

<div class="body">
{if $use_ratings eq 'y'}
  <div class="articlerating">
    {tr}Rating{/tr}:
    {repeat count=$rating}
    <img src="{$gBitLoc.IMG_PKG_URL}icons/blue.gif" alt="" />
    {/repeat}
    {if $rating > $entrating}
      <img src="{$gBitLoc.IMG_PKG_URL}icons/bluehalf.gif" alt="" />
    {/if}
    {$rating}/10
  </div>
{/if}

<div class="introduction">
{if $show_image eq 'y'}
  {if !$parsed_body}
    <a href="{$gBitLoc.ARTICLES_PKG_URL}read.php?article_id={$article_id}">
  {/if}
  {if $hasImage eq 'y'}
    <div class="introductionimage">
      {if $hasImage eq 'y'}
        <img class="icon" alt="{$topic_name}" src="article_image.php?article_id={$article_id}" {if $image_x > 0}width="{$image_x}"{/if}{if $image_y > 0 }height="{$image_y}"{/if}/>
      {else}
        <img class="icon" alt="{$topic_name}" src="topic_image.php?article_id={$topic_id}" />
      {/if}
    </div>
  {else}
    {section name=it loop=$topics}
      {if ($topics[it].topic_id eq $topic_id) and ($topics[it].image_size > 0)}
	  {assign name="iconShown" value="TRUE"}
        <div class="introductionimage">
          <img class="icon" alt="{$topic_name}" src="topic_image.php?id={$topic_id}" />
        </div>
      {/if}
    {/section}
    {if ($show_avatar eq 'y') AND ($iconShown != "TRUE") AND ($avatar_lib_name) }
        <div class="introductionimage">
      <a href="{$gBitLoc.USERS_PKG_URL}index.php?fHomepage={$author}"><img alt="{$author}" class="icon" src="{$avatar_lib_name}" /></a>
        </div>
    {/if}
  {/if}
  {if !$parsed_body}
    </a>
  {/if}
{/if}

{$parsed_heading}
</div> {* end .articleheading *}

{if $parsed_body}
  <div class="content">
    {$parsed_body}
  </div>
{/if}
</div> {* end .body *}

<div class="footer">
  <div class="footericon">
  {if $bit_p_edit_article eq 'y'}
    <a href="{$gBitLoc.ARTICLES_PKG_URL}edit.php?article_id={$article_id}"><img class="icon" src="{$gBitLoc.KERNEL_PKG_URL}icons/edit.gif" alt="{tr}Edit{/tr}" title="{tr}Edit{/tr}" /></a>
  {/if}
    <a href="{$gBitLoc.ARTICLES_PKG_URL}print.php?article_id={$article_id}"><img class="icon" src="{$gBitLoc.KERNEL_PKG_URL}icons/print.gif" alt="{tr}Print{/tr}" title="{tr}Print{/tr}" /></a>
  {if $bit_p_remove_article eq 'y'}
    <a href="{$gBitLoc.ARTICLES_PKG_URL}list.php?remove={$article_id}"><img class="icon" src="{$gBitLoc.KERNEL_PKG_URL}icons/delete.gif" alt="{tr}Remove{/tr}" title="{tr}Remove{/tr}" /></a>
  {/if}
  </div>

{if $parsed_body eq ''}
  {if ($size > 0) or (($gBitSystemPrefs.feature_article_comments eq 'y') and ($bit_p_read_comments eq 'y'))}
    {if ($heading_only ne 'y')}
      <a href="{$gBitLoc.ARTICLES_PKG_URL}read.php?article_id={$article_id}">{tr}Read More{/tr}</a>
    {else}&nbsp;
    {/if}
    {if ($gBitSystemPrefs.feature_article_comments eq 'y')
     and ($bit_p_read_comments eq 'y')
     and ($allow_comments eq 'y')} |
      {if $comments_cant eq 0}{tr}no comments{/tr}
      {elseif $comments_cant eq 1}{tr}1 comment{/tr}
      {else}{$comments_cant} {tr}comments{/tr}
      {/if}
    {/if}
  {/if}
{/if}

</div> {* end .footer *}
</div> {* end .article *}
