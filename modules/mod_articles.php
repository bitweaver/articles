<?php 
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/modules/mod_articles.php,v 1.13 2007/05/23 05:26:29 laetzer Exp $
 * @package article
 * @subpackage modules
 */

/**
 * Initialization
 */
include_once( ARTICLES_PKG_PATH.'BitArticle.php' );

extract( $moduleParams );

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
$getHash['max_records']   = !empty( $module_rows ) ? $module_rows : $gBitSystem->getConfig( 'articles_max_list' );
$getHash['topic_name']    = !empty( $module_params['topic_name'] ) ? $module_params['topic_name'] : NULL;
$getHash['topic_id']      = !empty( $module_params['topic_id'] ) ? $module_params['topic_id'] : NULL;
$articles = $articles->getList( $getHash );

if( ( !empty( $module_params['topic_id'] ) || !empty( $module_params['topic_name'] ) ) && empty( $module_title ) && !empty( $articles ) ) {
	$gBitSmarty->assign( 'moduleTitle', $articles['data'][0]['topic_name'] );
} elseif( !empty( $module_title ) ) {
	$gBitSmarty->assign( 'moduleTitle', $module_title );
} else {
	$gBitSmarty->assign( 'moduleTitle', "Articles" );
}

// if user provided a name for this module, use it (overrides guessing above)
if ( !empty($moduleParams['title']) ){
	$gBitSmarty->assign( 'moduleTitle', $moduleParams['title'] );
}

$gBitSmarty->assign( 'modArticles', $articles['data'] );
?>