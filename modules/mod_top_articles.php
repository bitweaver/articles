<?php
// $Header: /cvsroot/bitweaver/_bit_articles/modules/Attic/mod_top_articles.php,v 1.1 2005/06/30 01:10:46 bitweaver Exp $
include_once( ARTICLES_PKG_PATH.'art_lib.php' );
global $artlib;

$ranking = $artlib->list_articles(0, $module_rows, 'reads_desc', '', '', $gQueryUsername);

$smarty->assign('modTopArticles', $ranking["data"]);
?>
