<?php 
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/list_topics.php,v 1.6 2010/02/08 21:27:21 wjames5 Exp $
 * @package articles
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( '../kernel/setup_inc.php' );

include_once( ARTICLES_PKG_PATH.'BitArticle.php' );
include_once( ARTICLES_PKG_PATH.'lookup_article_topic_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

$topics = BitArticleTopic::getTopicList();

$gBitSmarty->assign( 'topics', $topics );

$gBitSystem->display( 'bitpackage:articles/list_topics.tpl', tra( 'List Topics' ) , array( 'display_mode' => 'list' ));
?>
