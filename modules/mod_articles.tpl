{* $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.tpl,v 1.3.2.1 2005/12/21 18:32:08 mej Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'articles' )}
	{bitmodule title="$moduleTitle" name="articles"}
		<ol>
			{foreach item=modArt from=$modArticles}
				<li><a href="{$modArt.display_url}">{$modArt.title}</a></li>
			{foreachelse}
				<li></li>
			{/foreach}
		</ol>
	{/bitmodule}
{/if}
{/strip}
