{* $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.tpl,v 1.2 2005/08/30 22:24:00 squareing Exp $ *}
{strip}
{bitmodule title="$moduleTitle" name="articles"}
	<ol>
		{foreach item=modArt from=$modArticles}
			<li><a href="{$gBitLoc.ARTICLES_PKG_URL}read.php?article_id={$modArt.article_id}">{$modArt.title}</a></li>
		{/foreach}
	</ol>
{/bitmodule}
{/strip}
