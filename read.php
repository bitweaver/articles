<?php
// $Header: /cvsroot/bitweaver/_bit_articles/read.php,v 1.3 2005/08/26 09:17:14 squareing Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization
require_once( '../bit_setup_inc.php' );

require_once( ARTICLES_PKG_PATH.'BitArticle.php' );

$gBitSystem->isPackageActive( 'articles' );
if ( !$gBitUser->hasPermission( 'bit_p_read_article' )) {
	$smarty->assign( 'msg', tra( "Permission denied you cannot view this section" ) );
	$gBitSystem->display( "error.tpl" );
	die;
} elseif ( !isset( $_REQUEST["article_id"] ) ) {
	$smarty->assign( 'msg', tra( "No article indicated" ) );
	$gBitSystem->display( "error.tpl" );
	die;
}

include_once( ARTICLES_PKG_PATH.'lookup_article_inc.php' );

$gContent->addHit();
$smarty->assign_by_ref('article', $gContent->mInfo);

// get all the services that want to display something on this page
$displayHash = array( 'perm_name' => 'bit_p_view' );
$gContent->invokeServices( 'content_display_function', $displayHash );

$topics = BitArticleTopic::listTopics();
$smarty->assign_by_ref( 'topics', $topics );

$section = 'cms';

// Comments engine!
if( $gContent->mInfo['allow_comments'] == 'y' ) {
	$comments_vars = Array( 'article' );
	$comments_prefix_var='article:';
	$comments_object_var='article';
	$commentsParentId = $gContent->mContentId;
	$comments_return_url = ARTICLES_PKG_URL.'read.php?article_id='.$gContent->mArticleId;
	include_once( LIBERTY_PKG_PATH.'comments_inc.php' );
}

if ( $feature_theme_control == 'y' ) {
	$cat_type = 'article';
	$cat_objid = $_REQUEST["article_id"];
	include( THEMES_PKG_PATH . 'tc_inc.php' );
}

if ( isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] == 'mobile' ) {
	include_once( HAWHAW_PKG_PATH . 'hawtiki_lib.php' );
	HAWBIT_read_article( $article_data, $pages );
}

// Display the Index Template
$gBitSystem->display( 'bitpackage:articles/read_article.tpl' );
?>
