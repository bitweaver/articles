<?php
/**
 * @version $Header$
 * @package articles
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

/**
 * Initialization
 */
require_once( '../kernel/setup_inc.php' );
require_once( ARTICLES_PKG_PATH . 'BitArticle.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

include_once('lookup_article_inc.php');

if( $gContent->isValid() ){
	$gContent->verifyUpdatePermission();
}else{
	$gContent->verifyCreatePermission();
}

// if we want to remove a custom image, just nuke all custom image settings at once
if( !empty( $_REQUEST['remove_image'] ) ) {
	$_REQUEST['image_attachment_id'] = NULL;
	$gContent->expungeImage( $gContent->mArticleId, !empty( $_REQUEST['preview_image_path'] ) ? $_REQUEST['preview_image_path'] : NULL );
	// set the preview mode to maintain all settings
	$_REQUEST['preview'] = 1;
}

if( isset( $_REQUEST["save"] ) ) {
	// random image code
	if( !( $gContent->hasUserPermission( 'p_articles_approve_submission' ) || $gContent->hasUserPermission( 'p_articles_auto_approve' ) ) && $gBitSystem->isFeatureActive( 'articles_submissions_rnd_img' ) && !$gBitUser->verifyCaptcha( $_REQUEST['captcha'] ) ) {
		$feedback['error'] = tra( "You need to supply the correct code to submit." );
		$_REQUEST['preview'] = TRUE;
		unset( $_REQUEST['save'] );
	}
}
// If we are in preview mode then preview it!
if( !empty( $_REQUEST['preview'] ) ) {
	$article = $gContent->preparePreview( $_REQUEST );
	$gBitSmarty->assign( 'preview', TRUE );
	$gContent->invokeServices( 'content_preview_function', $_REQUEST );
	$gBitSmarty->assign_by_ref( 'article', $article );
} else {
	$gContent->invokeServices( 'content_edit_function' );
	if( empty( $gContent->mInfo['author_name'] ) ) {
		$gContent->mInfo['author_name'] = $gBitUser->getDisplayName();
	}
	$gBitSmarty->assign_by_ref('article', $gContent->mInfo);
}

// If the article was saved, show feedback or show result
if( !empty( $_REQUEST['save'] ) ) {
	if( empty( $_REQUEST['rating'] ) ) $_REQUEST['rating'] = 0;
	if( empty( $_REQUEST['topic_id'] ) ) $_REQUEST['topic_id'] = 0;

	if( $gContent->store( $_REQUEST ) ) {
		if( $gContent->mInfo['status_id'] == ARTICLE_STATUS_PENDING ) {
			header ( "location: " . ARTICLES_PKG_URL. "index.php?feedback=".urlencode( tra( 'Your article has been submitted and is awaiting approval.' ) ) );
		} else {
			header ( "location: " . ARTICLES_PKG_URL . ( ($gBitSystem->isFeatureActive('pretty_urls_extended') || $gBitSystem->isFeatureActive('pretty_urls')) ? $gContent->mArticleId : "read.php?article_id=" . $gContent->mArticleId ) );
		}
	}
}

// Get a topic list
$topics = BitArticleTopic::getTopicList( array( 'active_topic' => TRUE ) );
$gBitSmarty->assign_by_ref( 'topics', $topics );
if ( !empty( $_REQUEST['topic'] ) ) {
	$gBitSmarty->assign( 'topic', $_REQUEST['topic'] );
}
// get list of valid types
$types = BitArticleType::getTypeList();
$gBitSmarty->assign_by_ref( 'types', $types );

if ( !empty( $gContent->mErrors ) || !empty( $feedback ) ) {
	$article = $gContent->preparePreview( $_REQUEST );
	$gBitSmarty->assign_by_ref( 'article', $article );
}

$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
$gBitSmarty->assign( 'feedback', ( !empty( $feedback ) ? $feedback : NULL ) );

// Display the Index Template
$gBitSmarty->assign( 'show_page_bar', 'n' );
$gBitSystem->display( 'bitpackage:articles/edit_article.tpl', tra( "Articles" ) , array( 'display_mode' => 'edit' ));
?>
