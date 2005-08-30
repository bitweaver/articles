<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.php,v 1.3 2005/08/30 22:25:50 squareing Exp $
include_once( ARTICLES_PKG_PATH . 'BitArticle.php' );
global $module_rows, $module_params, $module_title;

$articles = new BitArticle();
$stati = array( 'pending', 'approved' );
if( !empty( $module_params['status'] ) && in_array( $module_params['status'], $stati ) ) {
	$status_id = constant( 'ARTICLE_STATUS_'.strtoupper( $module_params['status'] ) );
} else {
	$status_id = ARTICLE_STATUS_APPROVED;
}

$getHash['status_id']     = $status_id;
$getHash['max_records']   = !empty( $module_rows ) ? $module_rows : $gBitSystem->mPrefs['max_articles'];
$getHash['sort_mode']     = !empty( $module_params['sort_mode'] ) ? $module_params['sort_mode'] : 'last_modified_desc';
$articles = $articles->getList( $getHash );

$smarty->assign( 'modArticles', $articles['data'] );
?>
