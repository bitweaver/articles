<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/edit.php,v 1.32 2006/12/23 09:29:04 squareing Exp $
 * @package article
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );
require_once( ARTICLES_PKG_PATH . 'BitArticle.php' );

// get plugin help
include_once( LIBERTY_PKG_PATH.'edit_help_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

include_once('lookup_article_inc.php');

$isOwner = FALSE;
if( $gBitUser->hasPermission('p_articles_admin' ) || $gBitUser->hasPermission( 'p_articles_edit' ) ) {
	$isOwner = TRUE;
} elseif( !empty($gContent->mInfo['user_id'] ) && $gContent->mInfo['user_id'] == $gBitUser->mUserId ) {
	$isOwner = TRUE;
} elseif( !$gContent->mArticleId && $gBitUser->hasPermission( 'p_articles_submit' ) ) {
	$isOwner = TRUE;
}

// Now check permissions to access this page
if( !$isOwner ) {
	if ( empty( $gContent->mArticleId ) ) {
		$gBitSystem->fatalPermission('p_articles_submit');
	} else {
		$gBitSystem->fatalPermission('p_articles_edit');
	}
}

// if we want to remove a custom image, just nuke all custom image settings at once
if( !empty( $_REQUEST['remove_image'] ) ) {
	$_REQUEST['image_attachment_id'] = NULL;
	$gContent->expungeImage( $gContent->mArticleId, !empty( $_REQUEST['preview_image_path'] ) ? $_REQUEST['preview_image_path'] : NULL );
	// set the preview mode to maintain all settings
	$_REQUEST['preview'] = 1;
}

// random image code
if( !( $gBitUser->hasPermission( 'p_articles_approve_submission' ) || $gBitUser->hasPermission( 'p_articles_auto_approve' ) ) && !empty( $_REQUEST["save"] ) && $gBitSystem->isFeatureActive( 'articles_submissions_rnd_img' ) && ( !isset( $_SESSION['random_number'] ) || $_SESSION['random_number'] != $_REQUEST['rnd_img'] ) ) {
	$feedback['error'] = tra( "You need to supply the correct code to submit." );
	$_REQUEST['preview'] = TRUE;
	unset( $_REQUEST['save'] );
}

// If we are in preview mode then preview it!
if( !empty( $_REQUEST['preview'] ) ) {
	$article = $gContent->preparePreview( $_REQUEST );
	$gBitSmarty->assign( 'preview', TRUE );
	$gContent->invokeServices( 'content_preview_function' );
	$gBitSmarty->assign_by_ref( 'article', $article );
} else {
	$gContent->invokeServices( 'content_edit_function' );
	if( empty( $gContent->mInfo['author_name'] ) ) {
		$gContent->mInfo['author_name'] = $gBitUser->getDisplayName();
	}
	$gBitSmarty->assign_by_ref('article', $gContent->mInfo);
}

if( !empty( $_REQUEST["save"] ) ) {
	if( empty( $_REQUEST["rating"] ) ) $_REQUEST['rating'] = 0;
	if( empty( $_REQUEST['topic_id'] ) ) $_REQUEST['topic_id'] = 0;

	if( $gContent->store( $_REQUEST ) ) {
		if( $gContent->mInfo['status_id'] == ARTICLE_STATUS_PENDING ) {
			header ( "location: " . ARTICLES_PKG_URL. "index.php?feedback=".urlencode( tra( 'Your article has been submitted and is awaiting approval.' ) ) );
		} else {
			header ( "location: " . ARTICLES_PKG_URL. "index.php" );
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

// WYSIWYG and Quicktag variable
$gBitSmarty->assign( 'textarea_id', LIBERTY_TEXT_AREA );

if ($gBitSystem->isPackageActive( 'quicktags' )) {
	include_once( QUICKTAGS_PKG_PATH . 'quicktags_inc.php' );
}

if ( !empty( $gContent->mErrors ) || !empty( $feedback ) ) {
	$article = $gContent->preparePreview( $_REQUEST );
	$gBitSmarty->assign_by_ref( 'article', $article );
}

$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
$gBitSmarty->assign( 'feedback', ( !empty( $feedback ) ? $feedback : NULL ) );

// Display the Index Template
$gBitSmarty->assign( 'show_page_bar', 'n' );
// load the ajax library for this page
$gBitSmarty->assign( 'loadAjax', 'prototype' );
$gBitSystem->display( 'bitpackage:articles/edit_article.tpl', tra( "Articles" ) );
?>
