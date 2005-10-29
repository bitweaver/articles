{* $Header: /cvsroot/bitweaver/_bit_articles/templates/header_inc.tpl,v 1.1 2005/10/29 07:55:59 squareing Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'rss' ) and $smarty.const.ACTIVE_PACKAGE eq 'articles' and $gBitUser->hasPermission( 'bit_p_read_article' )}
	<link rel="alternate" type="application/rss+xml" title="{$siteTitle} - articles" href="{$smarty.const.ARTICLES_PKG_URL}articles_rss.php" />
{/if}
{/strip}
