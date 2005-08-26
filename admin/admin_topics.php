<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/admin/admin_topics.php,v 1.2 2005/08/26 09:33:42 squareing Exp $
require_once( '../../bit_setup_inc.php' );

include_once( ARTICLES_PKG_PATH.'BitArticle.php' );
include_once( ARTICLES_PKG_PATH.'lookup_article_topic_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );
$gBitSystem->verifyPermission( 'bit_p_admin_cms' );

if( isset( $_REQUEST["fSubmitAddTopic"] ) ) {
	$gContent->storeTopic( $_REQUEST );	
} elseif( !empty( $_REQUEST['fActivateTopic'] )&& $gContent ) {
	$gContent->activateTopic();
} elseif( !empty( $_REQUEST['fDeactivateTopic'] )&& $gContent ) {
	$gContent->deactivateTopic();
} elseif( !empty( $_REQUEST['fRemoveTopic'] )&& $gContent ) {
	$gContent->removeTopic();
} elseif( !empty( $_REQUEST['fRemoveTopicAll'] )&& $gContent ) {
	$gContent->removeTopic( TRUE );
}

$topics = BitArticleTopic::listTopics();

for( $i = 0; $i < count( $topics ); $i++ ) {
	if( $gBitUser->object_has_one_permission( $topics[$i]["topic_id"], 'topic' ) ) {
		$topics[$i]["individual"] = 'y';

		if( $gBitUser->object_has_permission( $gBitUser->mUserId, $topics[$i]["topic_id"], 'topic', 'bit_p_topic_read' ) ) {
			$topics[$i]["individual_bit_p_topic_read"] = 'y';
		} else {
			$topics[$i]["individual_bit_p_topic_read"] = 'n';
		} 

		if( $bit_p_admin == 'y' || $gBitUser->object_has_permission( $gBitUser->mUserId, $topics[$i]["topic_id"], 'topic', 'bit_p_admin_cms' ) ) {
			$topics[$i]["individual_bit_p_topic_read"] = 'y';
		} 
	} else {
		$topics[$i]["individual"] = 'n';
	} 
}
$gBitSmarty->assign( 'topics', $topics );

$gBitSystem->display( 'bitpackage:articles/admin_topics.tpl', tra( 'Edit Topics' ) );
?>
