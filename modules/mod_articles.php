<?php 
/**
 * @version $Header$
 * @package articles
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
	"publish_date_desc",
	"publish_date_asc",
	"expire_date_desc",
	"expire_date_asc",
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
$articlelist = $articles->getList( $getHash );

if( ( !empty( $module_params['topic_id'] ) || !empty( $module_params['topic_name'] ) ) && empty($moduleParams['title']) && !empty( $articles ) ) {
	$_template->tpl_vars['moduleTitle'] = new Smarty_variable( $articles[0]['topic_name'] );
} elseif( !empty($moduleParams['title']) ) {
	$_template->tpl_vars['moduleTitle'] = new Smarty_variable( $moduleParams['title'] );
} else {
	$_template->tpl_vars['moduleTitle'] = new Smarty_variable( "Articles" );
}

$_template->tpl_vars['params'] = new Smarty_variable( !empty( $moduleParams['params'] ) );
$_template->tpl_vars['listtype'] = new Smarty_variable(  ( isset($module_params['list_type']) && (strncasecmp($module_params['list_type'], 'u', 1) == 0) ) ? 'ul' : 'ol' );
$_template->tpl_vars['modArticles'] = new Smarty_variable( $articlelist );
?>
