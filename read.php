<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/read.php,v 1.17 2007/01/23 16:07:15 squareing Exp $
 * @package article
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

require_once( ARTICLES_PKG_PATH.'BitArticle.php' );

$gBitSystem->verifyPackage( 'articles' );

if( !$gBitUser->hasPermission( 'p_articles_read' ) ) {
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
if( $gContent->getField( 'status_id' ) != ARTICLE_STATUS_APPROVED ) {
	if( !( $gBitUser->hasPermission( 'p_articles_edit_submission' ) || $gBitUser->hasPermission( 'p_articles_approve_submission' ))) {
		$gBitSystem->fatalError( "Permission denied you cannot view this article" );
	}
}

$gContent->addHit();
$gBitSmarty->assign_by_ref( 'article', $gContent->mInfo );

// get all the services that want to display something on this page
$displayHash = array( 'perm_name' => 'p_articles_read' );
$gContent->invokeServices( 'content_display_function', $displayHash );

$topics = BitArticleTopic::getTopicList();
$gBitSmarty->assign_by_ref( 'topics', $topics );

// Comments engine!
if( @$gContent->mInfo['allow_comments'] == 'y' ) {
	$comments_vars = Array( 'article' );
	$comments_prefix_var='article:';
	$comments_object_var='article';
	$commentsParentId = $gContent->mContentId;
	$comments_return_url = $_SERVER['PHP_SELF']."?article_id=".$_REQUEST['article_id'];
	include_once( LIBERTY_PKG_PATH.'comments_inc.php' );
}

// Display the Index Template
$gBitSystem->display( 'bitpackage:articles/read_article.tpl', @$gContent->mInfo['title'] );
?>
