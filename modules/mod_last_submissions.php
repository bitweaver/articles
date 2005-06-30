<?php
// $Header: /cvsroot/bitweaver/_bit_articles/modules/Attic/mod_last_submissions.php,v 1.1 2005/06/30 01:10:46 bitweaver Exp $
include_once( ARTICLES_PKG_PATH.'art_lib.php' );
global $artlib, $gQueryUsername, $module_rows, $module_params;

$ranking = $artlib->list_submissions(0, $module_rows, 'created_desc', '', '',$gQueryUsername);

$smarty->assign('modLastSubmissions', $ranking["data"]);
?>
