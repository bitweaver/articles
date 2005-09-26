<?php
// $Header: /cvsroot/bitweaver/_bit_articles/list.php,v 1.8 2005/09/26 07:15:08 squareing Exp $
// Copyright( c )2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization
require_once( '../bit_setup_inc.php' );
require_once( ARTICLES_PKG_PATH.'BitArticle.php' );
include_once( ARTICLES_PKG_PATH.'article_filter_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'bit_p_read_article' );

// nuke articles if requested
if( !empty( $_REQUEST['action'] ) ) {
	if( $_REQUEST['action'] == 'remove' && !empty( $_REQUEST['remove_article_id'] ) ) {
		$tmpArt = new BitArticle( $_REQUEST['remove_article_id'] );
		$tmpArt->load();
		// depending on what the status of the article is, we need to check different permissions
		if( $tmpArt->mInfo['status_id'] == ARTICLE_STATUS_PENDING ) {
			$gBitSystem->verifyPermission( 'bit_p_remove_submission' );
		} else {
			$gBitSystem->verifyPermission( 'bit_p_remove_article' );
		}

		if( isset( $_REQUEST["confirm"] ) ) {
			if( $tmpArt->expunge() ) {
				header( "Location: ".ARTICLES_PKG_URL.'list.php?status_id='.( !empty( $_REQUEST['status_id'] ) ? $_REQUEST['status_id'] : '' ) );
				die;
			} else {
				$feedback['error'] = $tmpArt->mErrors;
			}
		}
		$gBitSystem->setBrowserTitle( 'Confirm removal of '.$tmpArt->mInfo['title'] );
		$formHash['remove'] = TRUE;
		$formHash['action'] = 'remove';
		$formHash['status_id'] = ( !empty( $_REQUEST['status_id'] ) ? $_REQUEST['status_id'] : '' );
		$formHash['remove_article_id'] = $_REQUEST['remove_article_id'];
		$msgHash = array(
			'label' => 'Remove Article',
			'confirm_item' => $tmpArt->mInfo['title'],
			'warning' => 'This will remove the above article. This cannot be undone.',
		);
		$gBitSystem->confirmDialog( $formHash, $msgHash );
	}
}

/* this is a messed up version of a multiple articles removal section
if( isset( $_REQUEST["multi_article"] ) && isset( $_REQUEST["checked"] ) && $_REQUEST["multi_article"] == "remove_articles" ) {
	// Now check permissions to remove the selected articles
	$gBitSystem->verifyPermission( 'bit_p_remove_article' );

	if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
	} elseif( empty( $_REQUEST['confirm'] ) ) {
		$formHash['delete'] = TRUE;
		$formHash['submit_mult'] = 'remove_articles';
		foreach( $_REQUEST["checked"] as $del ) {
			$formHash['input'][] = '<input type="hidden" name="checked[]" value="'.$del.'"/>';
		}
		$gBitSystem->confirmDialog( $formHash, array( 'warning' => 'Are you sure you want to delete '.count($_REQUEST["checked"] ).' articles?', 'error' => 'This cannot be undone!' ));
	} else {
		foreach( $_REQUEST["checked"] as $deleteId ) {
			$tmpPage = new BitArticle( $deleteId );
			if( !$tmpPage->load()|| !$tmpPage->expunge() ) {
				array_merge( $errors, array_values( $tmpPage->mErrors ) );
			}
		}
		if( !empty( $errors ) ) {
			$gBitSmarty->assign( 'errors', $errors );
		}
	}
}
*/

$article = new BitArticle();
// change the status of an article first
if( !empty( $_REQUEST['action'] ) ) {
	if( !empty( $_REQUEST['article_id'] ) && !empty( $_REQUEST['set_status_id'] ) && $gBitUser->hasPermission( 'bit_p_approve_submission' ) ) {
		$article->setStatus( $_REQUEST['set_status_id'], $_REQUEST['article_id'] );
	}
}

if( empty( $_REQUEST['status_id'] ) || ( !$gBitUser->hasPermission( 'bit_p_view_submissions' ) && !$gBitUser->hasPermission( 'bit_p_admin_articles' ) ) ) {
	$_REQUEST['status_id'] = ARTICLE_STATUS_APPROVED;
}
$listarticles = $article->getList( $_REQUEST );

$topics = BitArticleTopic::getTopicList();
$gBitSmarty->assign( 'topics', $topics );

$types = BitArticleType::getTypeList();
$gBitSmarty->assign( 'types', $types );

$gBitSmarty->assign( 'control', $_REQUEST["control"] );
$gBitSmarty->assign( 'listpages', $listarticles["data"] );

// Display the template
$gBitSystem->display( 'bitpackage:articles/list_articles.tpl', tra( "Articles" ));
?>
