<?php
// $Header: /cvsroot/bitweaver/_bit_articles/edit.php,v 1.9 2005/08/27 20:26:28 squareing Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );
require_once( ARTICLES_PKG_PATH . 'BitArticle.php' );

// get plugin help
include_once( LIBERTY_PKG_PATH.'edit_help_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

include_once('lookup_article_inc.php');

if( $gBitUser->hasPermission('bit_p_admin_articles' ) || $gBitUser->hasPermission( 'bit_p_edit_article' ) ) {
	$viewerCanEdit = TRUE;
} elseif( !empty($gContent->mInfo['user_id'] ) && $gContent->mInfo['user_id'] == $gBitUser->mUserId ) {
	$viewerCanEdit = TRUE;
} elseif( !$gContent->mArticleId && $gBitUser->hasPermission( 'bit_p_submit_article' ) ) {
	$viewerCanEdit = TRUE;
}

// Now check permissions to access this page
if ( !$viewerCanEdit ) {
	$gBitSmarty->assign( 'msg', tra( "Permission denied you cannot edit this article" ) );
	$gBitSystem->display( "error.tpl" );
	die;
}

// If we are in preview mode then preview it!
if (isset($_REQUEST["preview"])) {
	$article = $gContent->preparePreview($_REQUEST);
	$gBitSmarty->assign('preview', 'y');
	$gContent->invokeServices( 'content_preview_function' );
	$gBitSmarty->assign_by_ref('article', $article);
} else {
	$gContent->invokeServices( 'content_edit_function' );
	$gBitSmarty->assign_by_ref('article', $gContent->mInfo);
}

if ( isset( $_REQUEST["template_id"] ) && $_REQUEST["template_id"] > 0 ) {
	$template_data = $tikilib->get_template( $_REQUEST["template_id"] );

	$_REQUEST["preview"] = 1;
	$_REQUEST["body"] = $template_data["content"].$_REQUEST["data"];
}

$gBitSmarty->assign( 'author_name', $gBitUser->getDisplayName() );

if ( isset( $_REQUEST["save"] ) ) {
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
$topics = BitArticleTopic::listTopics();
$gBitSmarty->assign_by_ref( 'topics', $topics );
// get list of valid types
$types = BitArticleType::listTypes();
$gBitSmarty->assign_by_ref( 'types', $types );

/*if ( $feature_cms_templates == 'y' && $bit_p_use_content_templates == 'y' ) {
	$templates = $tikilib->list_templates( 'cms', 0, -1, 'name_asc', '' );
}*/
$gBitSmarty->assign_by_ref( 'templates', $templates["data"] );

// WYSIWYG and Quicktag variable
$gBitSmarty->assign( 'textarea_id', 'editarticle' );

if ($gBitSystem->isPackageActive( 'quicktags' )) {
	include_once( QUICKTAGS_PKG_PATH . 'quicktags_inc.php' );
}

// Display the Index Template
$gBitSmarty->assign( 'show_page_bar', 'n' );
$gBitSystem->display( 'bitpackage:articles/edit_article.tpl', tra( "Articles" ) );
?>
