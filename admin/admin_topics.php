<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/admin/admin_topics.php,v 1.1 2005/06/30 01:10:46 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../../bit_setup_inc.php' );

include_once( ARTICLES_PKG_PATH . 'art_lib.php' );
include_once( ARTICLES_PKG_PATH.'BitArticle.php');

$gBitSystem->setBrowserTitle( tra("Articles: Edit Topics") );

include_once( ARTICLES_PKG_PATH.'lookup_article_topic_inc.php' );

// PERMISSIONS: NEEDS p_admin
if ( !$gBitUser->hasPermission('bit_p_admin_cms') ) {
    $smarty->assign( 'msg', tra( "You dont have permission to use this feature" ) );

    $gBitSystem->display( "error.tpl" );
    die;
}

if ( isset( $_REQUEST["fSubmitAddTopic"] ) ) {
		
	$gContent->storeTopic($_REQUEST);	
} elseif ( !empty( $_REQUEST['fActivateTopic']) && $gContent) {
	$gContent->activateTopic();
} elseif ( !empty( $_REQUEST['fDeactivateTopic']) && $gContent) {
	$gContent->deactivateTopic();
} elseif ( !empty( $_REQUEST['fRemoveTopic']) && $gContent) {
	$gContent->removeTopic();
} elseif ( !empty($_REQUEST['fRemoveTopicAll']) && $gContent) {
	$gContent->removeTopic(TRUE);
}

$topics = BitArticleTopic::listTopics();

for ( $i = 0; $i < count( $topics ); $i++ ) {
    if ( $gBitUser->object_has_one_permission( $topics[$i]["topic_id"], 'topic' ) ) {
        $topics[$i]["individual"] = 'y';

        if ( $gBitUser->object_has_permission( $gBitUser->mUserId, $topics[$i]["topic_id"], 'topic', 'bit_p_topic_read' ) ) {
            $topics[$i]["individual_bit_p_topic_read"] = 'y';
        } else {
            $topics[$i]["individual_bit_p_topic_read"] = 'n';
        } 

        if ( $bit_p_admin == 'y' || $gBitUser->object_has_permission( $gBitUser->mUserId, $topics[$i]["topic_id"], 'topic', 'bit_p_admin_cms' ) ) {
            $topics[$i]["individual_bit_p_topic_read"] = 'y';
        } 
    } else {
        $topics[$i]["individual"] = 'n';
    } 
}

$smarty->assign( 'topics', $topics );


$gBitSystem->display( 'bitpackage:articles/admin_topics.tpl' );


?>
