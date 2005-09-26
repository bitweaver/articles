<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.php,v 1.6 2005/09/26 07:15:08 squareing Exp $
include_once( ARTICLES_PKG_PATH . 'BitArticle.php' );
global $module_rows, $module_params, $module_title;

$articles = new BitArticle();
$stati = array( 'pending', 'approved' );
if( !empty( $module_params['status'] ) && in_array( $module_params['status'], $stati ) ) {
	$status_id = constant( 'ARTICLE_STATUS_'.strtoupper( $module_params['status'] ) );
} else {
	$status_id = ARTICLE_STATUS_APPROVED;
}

$sortOptions = array(
	"last_modified_asc",
	"last_modified_desc",
	"created_asc",
	"created_desc",
);
if( !empty( $module_params['sort_mode'] ) && in_array( $module_params['sort_mode'], $sortOptions ) ) {
	$sort_mode = $module_params['sort_mode'];
} else {
	$sort_mode = 'last_modified_desc';
}

$getHash['status_id']     = $status_id;
$getHash['sort_mode']     = $sort_mode;
$getHash['max_records']   = !empty( $module_rows ) ? $module_rows : $gBitSystem->mPrefs['max_articles'];
$getHash['topic']         = !empty( $module_params['topic'] ) ? $module_params['topic'] : NULL;
$articles = $articles->getList( $getHash );

$gBitSmarty->assign( 'modArticles', $articles['data'] );
?>
