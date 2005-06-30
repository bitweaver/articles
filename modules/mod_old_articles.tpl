{* $Header: /cvsroot/bitweaver/_bit_articles/modules/Attic/mod_old_articles.tpl,v 1.1 2005/06/30 01:10:46 bitweaver Exp $ *}

{if $gBitSystemPrefs.feature_articles eq 'y'}
{bitmodule title="$moduleTitle" name="old_articles"}
  <table class="mother">
    {section name=ix loop=$modOldArticles}
      <tr>
        {if $nonums != 'y'}<td width="1%">{$smarty.section.ix.index_next}</td>{/if}
        <td><a href="{$gBitLoc.ARTICLES_PKG_URL}read.php?article_id={$modOldArticles[ix].article_id}">{$modOldArticles[ix].title}</a></td>
      </tr>
    {/section}
  </table>
{/bitmodule}
{/if}
