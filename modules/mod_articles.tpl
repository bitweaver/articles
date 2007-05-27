{* $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.tpl,v 1.8 2007/05/27 21:34:22 laetzer Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'articles' )}
	{bitmodule title="$moduleTitle" name=$smarty.const.ARTICLES_PKG_NAME}
		<ol>
			{foreach item=modArt from=$modArticles}
				<li><a href="{$modArt.display_url}">{$modArt.title|escape}</a></li>
			{foreachelse}
				<li></li>
			{/foreach}
			{if $params neq ''}
				<li><a href="{$smarty.const.ARTICLES_PKG_URL}list.php?{$params}">{tr}more{/tr}: {$moduleTitle}</a></li>
			{else}
				<li><a href="{$smarty.const.ARTICLES_PKG_URL}list.php">{tr}more{/tr}</a></li>
			{/if}
		</ol>
	{/bitmodule}
{/if}
{/strip}
