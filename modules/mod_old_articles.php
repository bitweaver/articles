<?php
// $Header: /cvsroot/bitweaver/_bit_articles/modules/Attic/mod_old_articles.php,v 1.1 2005/06/30 01:10:46 bitweaver Exp $
include_once( ARTICLES_PKG_PATH.'art_lib.php' );
global $artlib, $gQueryUsername, $module_rows;

if (!isset($maxArticles))
	$maxArticles = 0;

$ranking = $artlib->list_articles($maxArticles, $maxArticles + $module_rows, 'publish_date_desc', '', '', $gQueryUsername);
$smarty->assign('modOldArticles', $ranking["data"]);

?>
