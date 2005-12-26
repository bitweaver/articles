<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/read.php,v 1.9 2006/01/10 21:11:08 squareing Exp $
 * @package article
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

require_once( ARTICLES_PKG_PATH.'BitArticle.php' );

$gBitSystem->verifyPackage( 'articles' );

if( !$gBitUser->hasPermission( 'bit_p_read_article' ) ) {
	$gBitSmarty->assign( 'msg', tra( "Permission denied you cannot view this section" ) );
	$gBitSystem->display( "error.tpl" );
	die;
} elseif( !isset( $_REQUEST["article_id"] ) ) {
	$gBitSmarty->assign( 'msg', tra( "No article indicated" ) );
	$gBitSystem->display( "error.tpl" );
	die;
}

include_once( ARTICLES_PKG_PATH.'lookup_article_inc.php' );

// additionally we need to check if this article is a submission and see if user has perms to view it.
if( $gContent->mInfo['status_id'] != ARTICLE_STATUS_APPROVED && !( $gBitUser->hasPermission( 'bit_p_edit_submission' ) || $gBitUser->hasPermission( 'bit_p_edit_submission' ) || $gBitUser->hasPermission( 'bit_p_edit_submission' ) || $gBitUser->isAdmin() ) ) {
	$gBitSmarty->assign( 'msg', tra( "Permission denied you cannot view this article" ) );
	$gBitSystem->display( "error.tpl" );
	die;
}

$gContent->addHit();
$gBitSmarty->assign_by_ref( 'article', $gContent->mInfo );

// get all the services that want to display something on this page
$displayHash = array( 'perm_name' => 'bit_p_view' );
$gContent->invokeServices( 'content_display_function', $displayHash );

$topics = BitArticleTopic::getTopicList();
$gBitSmarty->assign_by_ref( 'topics', $topics );

// Comments engine!
if( $gContent->mInfo['allow_comments'] == 'y' ) {
	$comments_vars = Array( 'article' );
	$comments_prefix_var='article:';
	$comments_object_var='article';
	$commentsParentId = $gContent->mContentId;
	$comments_return_url = $gContent->mInfo['display_url'];
	include_once( LIBERTY_PKG_PATH.'comments_inc.php' );
}

if( $feature_theme_control == 'y' ) {
	$cat_type = 'article';
	$cat_objid = $_REQUEST["article_id"];
	include( THEMES_PKG_PATH . 'tc_inc.php' );
}

/*
if( isset( $_REQUEST['mode'] )&& $_REQUEST['mode'] == 'mobile' ) {
	include_once( HAWHAW_PKG_PATH . 'hawtiki_lib.php' );
	HAWBIT_read_article( $article_data, $pages );
}
*/

// Display the Index Template
$gBitSystem->display( 'bitpackage:articles/read_article.tpl', $gContent->mInfo['title'] );
?>
