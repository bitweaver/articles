<?php
/**
 * @version $Header$
 * @package articles
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once '../kernel/setup_inc.php';
require_once( ARTICLES_PKG_CLASS_PATH.'BitArticle.php' );

include_once( ARTICLES_PKG_INCLUDE_PATH.'lookup_article_topic_inc.php' );

if ( !$gBitSystem->verifyPackage( 'articles' ) ) {
   $gBitSmarty->assign( 'msg', tra( "This package is disabled" ) . ": Articles" );
   $gBitSystem->display( "error.tpl" , NULL, array( 'display_mode' => 'edit' ));
   die;
}

if( !$gContent->isValid() ) {
	$gBitSmarty->assign( 'msg', tra("Article topic not found") );
	$gBitSystem->display('error.tpl', NULL, array( 'display_mode' => 'edit' ));
	die;
}

$gBitSmarty->assignByRef( 'topic_info', $gContent->mInfo);

if( isset( $_REQUEST["fSubmitSaveTopic"] ) ) {
    $gContent->storeTopic( $_REQUEST );
	$gContent->loadTopic();
    bit_redirect( ARTICLES_PKG_URL . "admin/admin_topics.php" );
} elseif( isset( $_REQUEST['fRemoveTopicImage'] ) ) {
	$gContent->removeTopicImage();
}

$gBitSystem->display( 'bitpackage:articles/edit_topic.tpl' , NULL, array( 'display_mode' => 'edit' ));
