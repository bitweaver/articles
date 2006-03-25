{* $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.tpl,v 1.6 2006/03/25 20:47:10 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'articles' )}
	{bitmodule title="$moduleTitle" name="articles"}
		<ol>
			{foreach item=modArt from=$modArticles}
				<li><a href="{$modArt.display_url}">{$modArt.title|escape}</a></li>
			{foreachelse}
				<li></li>
			{/foreach}
		</ol>
	{/bitmodule}
{/if}
{/strip}
