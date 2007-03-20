<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/admin/admin_topics.php,v 1.7 2007/03/20 17:36:08 spiderr Exp $
require_once( '../../bit_setup_inc.php' );

include_once( ARTICLES_PKG_PATH.'BitArticle.php' );
include_once( ARTICLES_PKG_PATH.'lookup_article_topic_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );
$gBitSystem->verifyPermission( 'p_articles_admin' );

if( isset( $_REQUEST["fSubmitAddTopic"] ) ) {
	$gContent->storeTopic( $_REQUEST );
	if ( !empty( $gContent->mErrors ) ) {
		$gBitSmarty->assign_by_ref('errors', $gContent->mErrors );
	}
} elseif( !empty( $_REQUEST['fActivateTopic'] )&& $gContent ) {
	$gContent->activateTopic();
} elseif( !empty( $_REQUEST['fDeactivateTopic'] )&& $gContent ) {
	$gContent->deactivateTopic();
} elseif( !empty( $_REQUEST['fRemoveTopic'] )&& $gContent ) {
	$gContent->removeTopic();
} elseif( !empty( $_REQUEST['fRemoveTopicAll'] )&& $gContent ) {
	$gContent->removeTopic( TRUE );
}

$topics = BitArticleTopic::getTopicList();
$gBitSmarty->assign( 'topics', $topics );

$gBitSystem->display( 'bitpackage:articles/admin_topics.tpl', tra( 'Edit Topics' ) );
?>
