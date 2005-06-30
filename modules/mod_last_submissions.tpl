{* $Header: /cvsroot/bitweaver/_bit_articles/modules/Attic/mod_last_submissions.tpl,v 1.1 2005/06/30 01:10:46 bitweaver Exp $ *}

{if $gBitSystemPrefs.feature_submissions eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` submissions{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last submissions{/tr}" assign="tpl_module_title"}
{/if}
{bitmodule title="$moduleTitle" name="last_submissions"}
  <table class="mother">
    {section name=ix loop=$modLastSubmissions}
      <tr>
        {if $nonums != 'y'}<td width="1%">{$smarty.section.ix.index_next}</td>{/if}
        {if $bit_p_edit_submission eq 'y'}
          <td><a href="{$gBitLoc.ARTICLES_PKG_URL}edit_submission.php?sub_id={$modLastSubmissions[ix].sub_id}">{$modLastSubmissions[ix].title}</a></td>
        {else}
          <td>{$modLastSubmissions[ix].title}</td>
        {/if}
      </tr>
    {/section}
  </table>
{/bitmodule}
{/if}
