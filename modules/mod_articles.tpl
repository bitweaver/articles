{* $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.tpl,v 1.5 2006/01/10 21:11:09 squareing Exp $ *}
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
