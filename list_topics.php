<?php 
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/list_topics.php,v 1.4 2008/06/19 09:29:08 lsces Exp $
 * @package articles
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

include_once( ARTICLES_PKG_PATH.'BitArticle.php' );
include_once( ARTICLES_PKG_PATH.'lookup_article_topic_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

$topics = BitArticleTopic::getTopicList();

$gBitSmarty->assign( 'topics', $topics );

$gBitSystem->display( 'bitpackage:articles/list_topics.tpl', tra( 'List Topics' ) );
?>
