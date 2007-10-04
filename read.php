<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/read.php,v 1.23 2007/10/04 16:29:22 bitweaver Exp $
 * @package article
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

require_once( ARTICLES_PKG_PATH.'BitArticle.php' );

$gBitSystem->verifyPackage( 'articles' );

if( !@BitBase::verifyId( $_REQUEST["article_id"] )) {
	$gBitSystem->fatalError( tra( "No article indicated" ));
}

include_once( ARTICLES_PKG_PATH.'lookup_article_inc.php' );

// Check if we actually have some content
if( !$gContent->isValid() ) {
	$gBitSystem->fatalError( tra( 'Article cannot be found' ));
}

$gContent->verifyViewPermission();

// additionally we need to check if this article is a submission and see if user has perms to view it.
if( $gContent->getField( 'status_id' ) != ARTICLE_STATUS_APPROVED ) {
	if( !( $gContent->hasUserPermission( 'p_articles_edit_submission' ) || $gContent->hasUserPermission( 'p_articles_approve_submission' ))) {
		$gBitSystem->fatalError( tra( "Permission denied you cannot view this article" ));
	}
}

// we also need to check and see if the article is future dated - we will display it if the user can edit it otherwise we pretend it does not exist.
$timestamp = $gBitSystem->getUTCTime();
if ( ($gContent->mInfo['publish_date'] > $timestamp) && !$gContent->hasUserPermission( 'p_articles_edit' ) ){
	$gBitSystem->fatalError( tra( 'Article cannot be found' ));
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
