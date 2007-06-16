<?php
// $Header: /cvsroot/bitweaver/_bit_articles/templates/center_list_articles.php,v 1.12 2007/06/16 00:01:44 squareing Exp $
global $gBitSmarty, $gBitSystem, $gQueryUserId, $moduleParams;
extract( $moduleParams );

include_once( ARTICLES_PKG_PATH . 'BitArticle.php' );
include_once(ARTICLES_PKG_PATH."lookup_article_inc.php");
include_once( ARTICLES_PKG_PATH.'article_filter_inc.php' );


$listHash = array();

if( $gBitUser->hasPermission( 'p_articles_admin' ) ) {
    $_REQUEST['status_id']   = !empty( $_REQUEST['status_id'] )   ? $_REQUEST['status_id']   : ARTICLE_STATUS_APPROVED;
    $_REQUEST['max_records'] = !empty( $_REQUEST['max_records'] ) ? $_REQUEST['max_records'] : $gBitSystem->getConfig( 'articles_max_list' );
    $_REQUEST['topic_id']    = !empty( $_REQUEST['topic_id'] )    ? $_REQUEST['topic_id']    : NULL;
    $_REQUEST['type_id']     = !empty( $_REQUEST['type_id'] )     ? $_REQUEST['type_id']     : NULL;

    $gBitSmarty->assign( 'futures', $gContent->getFutureList( $listHash ));
} else {
    $_REQUEST['status_id']   = ARTICLE_STATUS_APPROVED;
    $_REQUEST['max_records'] = $gBitSystem->getConfig( 'articles_max_list' );
}
if ( !empty( $_REQUEST['topic'] ) ) {
    $gBitSmarty->assign( 'topic', $_REQUEST['topic'] );
}

if( !empty( $moduleParams )) {
    $listHash = array_merge( $_REQUEST, $moduleParams['module_params'] );
	$listHash['max_records'] = $module_rows;
	//$listHash['parse_data'] = TRUE;
	//$listHash['load_comments'] = TRUE;
} else {
    $listHash = $_REQUEST;
}

if( empty( $listHash['user_id'] )) {
	if( !empty( $gQueryUserId )) {
		$listHash['user_id'] = $gQueryUserId;
	} elseif( $_REQUEST['user_id'] ) {
		$listHash['user_id'] = $_REQUEST['user_id'];
	}
}
if( @BitBase::verifyId( $_REQUEST['group_id'] ) ) {
	$listHash['group_id'] = $_REQUEST['group_id'];
}

$articles = $gContent->getList( $listHash );
$gBitSmarty->assign( 'articles', $articles );
$gBitSmarty->assign( 'listInfo', $_REQUEST['listInfo'] );

// show only descriptions on listing page
if( count( $articles ) > 1 ) {
	$gBitSmarty->assign( 'showDescriptionsOnly', TRUE );
}

// display submissions if we have the perm to approve them
if( $gBitUser->hasPermission( 'p_articles_approve_submission' ) || ( $gBitSystem->isFeatureActive( 'articles_auto_approve' ) && $gBitUser->isRegistered() )) {
	$listHash = array( 'status_id' => ARTICLE_STATUS_PENDING );
	$submissions = $gContent->getList( $listHash );
	$gBitSmarty->assign( 'submissions', $submissions );
}
?>
