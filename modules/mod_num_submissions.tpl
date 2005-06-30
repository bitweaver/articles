{* $Header: /cvsroot/bitweaver/_bit_articles/modules/Attic/mod_num_submissions.tpl,v 1.1 2005/06/30 01:10:46 bitweaver Exp $ *}

{if $gBitSystemPrefs.feature_submissions eq 'y'}
{bitmodule title="$moduleTitle" name="num_submissions"}
  {tr}There are{/tr} {$modNumSubmissions} {tr}submissions to be examined{/tr}.
{/bitmodule}
{/if}