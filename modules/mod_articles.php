<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.php,v 1.4 2005/08/30 23:09:01 squareing Exp $
include_once( ARTICLES_PKG_PATH . 'BitArticle.php' );
global $module_rows, $module_params, $module_title;

$articles = new BitArticle();
$stati = array( 'pending', 'approved' );
if( !empty( $module_params['status'] ) && in_array( $module_params['status'], $stati ) ) {
	$status_id = constant( 'ARTICLE_STATUS_'.strtoupper( $module_params['status'] ) );
} else {
	$status_id = ARTICLE_STATUS_APPROVED;
}

$sortPattern = array(
	"/^last_modified_asc$/",
	"/^last_modified_desc/",
	"/^created_asc$/",
	"/^created_desc$/",
);
if( !empty( $module_params['sort_mode'] ) && preg_match( $sort_pattern, $module_params['sort_mode'] ) ) {
	$sort_mode = $module_params['sort_mode'];
} else {
	$sort_mode = 'last_modified_desc';
}

$getHash['status_id']     = $status_id;
$getHash['sort_mode']     = $sort_mode;
$getHash['max_records']   = !empty( $module_rows ) ? $module_rows : $gBitSystem->mPrefs['max_articles'];
$getHash['topic']         = !empty( $module_params['topic'] ) ? $module_params['topic'] : NULL;
$articles = $articles->getList( $getHash );

$smarty->assign( 'modArticles', $articles['data'] );
?>
