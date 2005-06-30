{* $Header: /cvsroot/bitweaver/_bit_articles/modules/Attic/mod_top_articles.tpl,v 1.1 2005/06/30 01:10:46 bitweaver Exp $ *}

{if $gBitSystemPrefs.feature_articles eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` articles{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top articles{/tr}" assign="tpl_module_title"}
{/if}
{bitmodule title="$moduleTitle" name="top_articles"}
  <table class="mother">
    {section name=ix loop=$modTopArticles}
      <tr>
        {if $nonums != 'y'}<td width="1%">{$smarty.section.ix.index_next}</td>{/if}
        <td><a href="{$gBitLoc.ARTICLES_PKG_URL}read.php?article_id={$modTopArticles[ix].article_id}">{$modTopArticles[ix].title}</a></td>
      </tr>
    {/section}
  </table>
{/bitmodule}
{/if}
