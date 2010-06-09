{* $Header$ *}
{strip}
{if $gBitSystem->isPackageActive( 'articles' )}
	{bitmodule title="$moduleTitle" name=$smarty.const.ARTICLES_PKG_NAME}
		<{$listtype} id="modarticles-{$moduleParams.layout_area}{$moduleParams.pos}">
			{foreach item=modArt from=$modArticles}
				<li><a href="{$modArt.display_url}">{$modArt.title|escape}</a></li>
			{foreachelse}
				<li><em>{tr}No records found{/tr}</em></li>
			{/foreach}
			{if !empty( $modArticles )}
				{if $params neq ''}
					<li><a href="{$smarty.const.ARTICLES_PKG_URL}list.php?{$params}">{tr}more{/tr}: {$moduleTitle}</a></li>
				{else}
					<li><a href="{$smarty.const.ARTICLES_PKG_URL}list.php">{tr}more{/tr}</a></li>
				{/if}
			{/if}
		</{$listtype}>
	{/bitmodule}
{/if}
{/strip}
