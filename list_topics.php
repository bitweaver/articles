<?php 
/**
 * @version $Header$
 * @package articles
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once '../kernel/includes/setup_inc.php';

include_once( ARTICLES_PKG_CLASS_PATH.'BitArticle.php' );
include_once( ARTICLES_PKG_INCLUDE_PATH.'lookup_article_topic_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

$topics = BitArticleTopic::getTopicList();

$gBitSmarty->assign( 'topics', $topics );

$gBitSystem->display( 'bitpackage:articles/list_topics.tpl', tra( 'List Topics' ) , array( 'display_mode' => 'list' ));
