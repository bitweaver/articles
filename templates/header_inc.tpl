{* $Header: /cvsroot/bitweaver/_bit_articles/templates/header_inc.tpl,v 1.4 2006/05/04 19:04:58 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'rss' ) $gBitSystem->isFeatureActive( 'articles_rss' ) and $smarty.const.ACTIVE_PACKAGE eq 'articles' and $gBitUser->hasPermission( 'p_articles_read' )}
	<link rel="alternate" type="application/rss+xml" title="{tr}Articles{/tr} RSS" href="{$smarty.const.ARTICLES_PKG_URL}articles_rss.php?version=rss20" />
	<link rel="alternate" type="application/rss+xml" title="{tr}Articles{/tr} ATOM" href="{$smarty.const.ARTICLES_PKG_URL}articles_rss.php?version=atom" />
{/if}
{/strip}
