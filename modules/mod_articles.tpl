{* $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.tpl,v 1.7 2006/07/14 16:22:49 spiderr Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'articles' )}
	{bitmodule title="$moduleTitle" name=$smarty.const.ARTICLES_PKG_NAME}
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
