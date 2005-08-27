<?php
// $Header: /cvsroot/bitweaver/_bit_articles/modules/Attic/mod_last_articles.php,v 1.2 2005/08/27 09:35:36 lsces Exp $
include_once( ARTICLES_PKG_PATH.'art_lib.php' );
global $artlib, $gQueryUsername, $module_rows, $module_params;

$ranking = $artlib->list_articles(0,$module_rows,'publish_date_desc', '', $gBitSystem->getUTCTime(), '', '', '', $gQueryUsername);
$smarty->assign('modLastArticles',$ranking["data"]);
?>
