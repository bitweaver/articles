{* $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.tpl,v 1.3 2005/09/11 08:44:16 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'articles' )}
	{bitmodule title="$moduleTitle" name="articles"}
		<ol>
			{foreach item=modArt from=$modArticles}
				<li><a href="{$gBitLoc.ARTICLES_PKG_URL}read.php?article_id={$modArt.article_id}">{$modArt.title}</a></li>
			{foreachelse}
				<li></li>
			{/foreach}
		</ol>
	{/bitmodule}
{/if}
{/strip}
