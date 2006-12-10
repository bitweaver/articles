{* $Header: /cvsroot/bitweaver/_bit_articles/templates/header_inc.tpl,v 1.7 2006/12/10 15:15:09 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'rss' ) and $gBitSystem->isFeatureActive( 'articles_rss' ) and $smarty.const.ACTIVE_PACKAGE eq 'articles' and $gBitUser->hasPermission( 'p_articles_read' )}
	<link rel="alternate" type="application/rss+xml" title="{$gBitSystem->getConfig('articles_rss_title',"{tr}Articles{/tr} RSS")}" href="{$smarty.const.ARTICLES_PKG_URL}articles_rss.php?version={$gBitSystem->getConfig('rssfeed_default_version',0)}" />
{/if}
{/strip}
