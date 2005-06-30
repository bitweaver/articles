{* $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.tpl,v 1.1 2005/06/30 01:10:46 bitweaver Exp $ *}

{if $gBitSystemPrefs.feature_articles eq 'y'}
{bitmodule title="$moduleTitle" name="articles"}
  <table class="mother">
    {section name=ix loop=$modArticles}
    <tr>
      {if $nonums != 'y'}
        <td width="1%">{$smarty.section.ix.index_next}</td>
      {/if}
      <td><a href="{$gBitLoc.ARTICLES_PKG_URL}read.php?article_id={$modArticles[ix].article_id}">{$modArticles[ix].title}</a></td>
    </tr>
    {/section}
  </table>
{/bitmodule}
{/if}
