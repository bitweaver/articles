<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/display_article_inc.php,v 1.1 2007/07/14 08:17:32 lsces Exp $
 * @package article
 * @subpackage functions
 */

/**
 * Initialization
 */
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