<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.php,v 1.1 2005/06/30 01:10:46 bitweaver Exp $
include_once( ARTICLES_PKG_PATH . 'art_lib.php' );
global $artlib, $gQueryUsername, $module_rows, $module_params;

$smarty->assign( 'title', isset( $module_params["title"] ) ? $module_params["title"] : 'Articles' );

if ( isset( $module_params["type"] ) ) {
    $type = $module_params["type"];
} else {
    $type = '';
} 
if ( isset( $module_params["topic_id"] ) ) {
    $topic_id = $module_params["topic_id"];
} else {
    $topic_id = '';
} 
/*
$smarty->assign('type', isset($module_params["type"]) ? $module_params["type"] : '');
$smarty->assign('topic_id', isset($module_params["topic_id"]) ? $module_params["topic_id"] : '');

function list_articles($offset = 0, $maxRecords = -1, $sort_mode = 'publish_date_desc', $find = '', $date = '', $user, $type = '', $topic_id = '') {
*/

$ranking = $artlib->list_articles( 0, $module_rows, 'publish_date_desc', '', '', $user, $type, $topic_id, $gQueryUsername );
$smarty->assign( 'modArticles', $ranking["data"] );

?>
