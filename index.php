<?php
// $Header: /cvsroot/bitweaver/_bit_articles/index.php,v 1.22 2007/06/15 19:09:39 squareing Exp $
// Copyright( c )2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'p_articles_read' );

if( !empty( $_REQUEST['article_id'] ) ) {
	header( "location: ".ARTICLES_PKG_URL."read.php?article_id=".( ( int )$_REQUEST['article_id'] ) );
}

require_once( ARTICLES_PKG_PATH.'lookup_article_inc.php' );
include_once( ARTICLES_PKG_PATH.'article_filter_inc.php' );

if( $gBitUser->hasPermission( 'p_articles_admin' ) ) {
	$_REQUEST['status_id']   = !empty( $_REQUEST['status_id'] )   ? $_REQUEST['status_id']   : ARTICLE_STATUS_APPROVED;
	$_REQUEST['max_records'] = !empty( $_REQUEST['max_records'] ) ? $_REQUEST['max_records'] : $gBitSystem->getConfig( 'articles_max_list' );
	$_REQUEST['topic_id']    = !empty( $_REQUEST['topic_id'] )    ? $_REQUEST['topic_id']    : NULL;
	$_REQUEST['type_id']     = !empty( $_REQUEST['type_id'] )     ? $_REQUEST['type_id']     : NULL;

	$listHash = array();
	$gBitSmarty->assign( 'futures', $gContent->getFutureList( $listHash ));
} else {
	$_REQUEST['status_id']   = ARTICLE_STATUS_APPROVED;
	$_REQUEST['max_records'] = $gBitSystem->getConfig( 'articles_max_list' );
}
if ( !empty( $_REQUEST['topic'] ) ) {
	$gBitSmarty->assign( 'topic', $_REQUEST['topic'] );
}
$articles = $gContent->getList( $_REQUEST );
$gBitSmarty->assign( 'articles', $articles );
$gBitSmarty->assign( 'listInfo', $_REQUEST['listInfo'] );

// display submissions if we have the perm to approve them
if( $gBitUser->hasPermission( 'p_articles_approve_submission' ) || ( $gBitSystem->isFeatureActive( 'articles_auto_approve' ) && $gBitUser->isRegistered() )) {
	$listHash = array( 'status_id' => ARTICLE_STATUS_PENDING );
	$submissions = $gContent->getList( $listHash );
	$gBitSmarty->assign( 'submissions', $submissions );
}

// Display the template
$gDefaultCenter = 'bitpackage:articles/center_list_articles.tpl';
$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );

// Display the template
$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', tra( 'Articles' ) );
?>
