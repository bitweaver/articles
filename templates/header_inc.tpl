{* $Header: /cvsroot/bitweaver/_bit_articles/templates/header_inc.tpl,v 1.2 2005/10/29 10:00:25 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'rss' ) and $smarty.const.ACTIVE_PACKAGE eq 'articles' and $gBitUser->hasPermission( 'bit_p_read_article' )}
	<link rel="alternate" type="application/rss+xml" title="{tr}Articles{/tr} RSS" href="{$smarty.const.ARTICLES_PKG_URL}articles_rss.php?version=rss20" />
	<link rel="alternate" type="application/rss+xml" title="{tr}Articles{/tr} ATOM" href="{$smarty.const.ARTICLES_PKG_URL}articles_rss.php?version=atom" />
{/if}
{/strip}
