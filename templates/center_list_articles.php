<?php
// $Header$
global $gBitSmarty, $gBitSystem, $gQueryUserId, $moduleParams;
if( !empty( $moduleParams ) ) {
	extract( $moduleParams );
}

include_once( ARTICLES_PKG_PATH.'BitArticle.php' );
include_once( ARTICLES_PKG_PATH."lookup_article_inc.php" );
include_once( ARTICLES_PKG_PATH.'article_filter_inc.php' );

$listHash = array();

if( $gBitUser->hasPermission( 'p_articles_admin' ) ) {
	$_REQUEST['status_id']   = !empty( $_REQUEST['status_id'] )   ? $_REQUEST['status_id']   : ARTICLE_STATUS_APPROVED;
	$_REQUEST['max_records'] = !empty( $_REQUEST['max_records'] ) ? $_REQUEST['max_records'] : $gBitSystem->getConfig( 'articles_max_list' );
	$_REQUEST['topic_id']    = !empty( $_REQUEST['topic_id'] )    ? $_REQUEST['topic_id']    : NULL;
	$_REQUEST['type_id']     = !empty( $_REQUEST['type_id'] )     ? $_REQUEST['type_id']     : NULL;

	$_template->tpl_vars['futures'] = new Smarty_variable( $gContent->getFutureList( $listHash ) );
} else {
	$_REQUEST['status_id']   = ARTICLE_STATUS_APPROVED;
	$_REQUEST['max_records'] = $gBitSystem->getConfig( 'articles_max_list' );
}
if ( !empty( $_REQUEST['topic'] ) ) {
	$_template->tpl_vars['topic'] = new Smarty_variable( $_REQUEST['topic'] );
}

if( !empty( $moduleParams )) {
	$listHash = array_merge( $_REQUEST, $moduleParams['module_params'] );
	$listHash['max_records'] = $module_rows;
	//$listHash['parse_data'] = TRUE;
	//$listHash['load_comments'] = TRUE;
} else {
	$listHash = $_REQUEST;
}

BitUser::userCollection( $_REQUEST, $listHash );

$articles = $gContent->getList( $listHash );
$_template->tpl_vars['gContent'] = new Smarty_variable( $gContent );
$_template->tpl_vars['articles'] = new Smarty_variable( $articles );
$_template->tpl_vars['listInfo'] = new Smarty_variable( $listHash['listInfo'] );

// show only descriptions on listing page
$_template->tpl_vars['showDescriptionsOnly'] = new Smarty_variable( TRUE );

// display submissions if we have the perm to approve them
if( $gBitUser->hasPermission( 'p_articles_approve_submission' ) || ( $gBitSystem->isFeatureActive( 'articles_auto_approve' ) && $gBitUser->isRegistered() )) {
	$listHash = array( 'status_id' => ARTICLE_STATUS_PENDING );
	$submissions = $gContent->getList( $listHash );
	$_template->tpl_vars['submissions'] = new Smarty_variable( $submissions );
}
?>
