<?php
// $Header: /cvsroot/bitweaver/_bit_articles/modules/Attic/mod_num_submissions.php,v 1.1 2005/06/30 01:10:46 bitweaver Exp $
include_once( ARTICLES_PKG_PATH.'art_lib.php' );
global $artlib, $gQueryUsername, $module_rows;

$ranking = $artlib->list_submissions(0, -1, 'created_desc', '', '',$gQueryUsername);

$smarty->assign('modNumSubmissions', $ranking["cant"]);

?>
