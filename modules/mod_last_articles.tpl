{* $Header: /cvsroot/bitweaver/_bit_articles/modules/Attic/mod_last_articles.tpl,v 1.1 2005/06/30 01:10:46 bitweaver Exp $ *}

{if $gBitSystemPrefs.feature_articles eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` articles{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last articles{/tr}" assign="tpl_module_title"}
{/if}
{bitmodule title="$moduleTitle" name="last_articles"}
  <table class="mother">
    {section name=ix loop=$modLastArticles}
      <tr>
        {if $nonums != 'y'}<td width="1%">{$smarty.section.ix.index_next}</td>{/if}
        <td>
          <a href="{$gBitLoc.ARTICLES_PKG_URL}read.php?article_id={$modLastArticles[ix].article_id}" title="{$modLastArticles[ix].publish_date|bit_short_datetime}, by {$modLastArticles[ix].author}">{$modLastArticles[ix].title}</a>
        </td>
      </tr>
    {/section}
  </table>
{/bitmodule}
{/if}
