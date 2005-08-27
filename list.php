<?php
// $Header: /cvsroot/bitweaver/_bit_articles/list.php,v 1.4 2005/08/27 20:26:28 squareing Exp $
// Copyright( c )2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization
require_once( '../bit_setup_inc.php' );
require_once( ARTICLES_PKG_PATH.'BitArticle.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'bit_p_read_article' );

/* mass-remove:
   the checkboxes are sent as the array $_REQUEST["checked[]"], values are the wiki-PageNames,
   e.g. $_REQUEST["checked"][3]="HomePage"
   $_REQUEST["submit_mult"] holds the value of the "with selected do..."-option list
   we look if any page's checkbox is on and if remove_articles is selected.
   then we check permission to delete articles.
   if so, we call histlib's method remove_all_versions for all the checked articles.
*/
if( isset( $_REQUEST["submit_mult"] ) && isset( $_REQUEST["checked"] ) && $_REQUEST["submit_mult"] == "remove_articles" ) {
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
				array_merge( $errors, array_values( $tmpPage->mErrors ));
			}
		}
		if( !empty( $errors ) ) {
			$smarty->assign( 'errors', $errors );
		}
	}
}

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

$topics = BitArticleTopic::listTopics();
$gBitSmarty->assign( 'topics', $topics );

$types = BitArticleType::listTypes();
$gBitSmarty->assign( 'types', $types );

$gBitSmarty->assign( 'control', $_REQUEST["control"] );
$gBitSmarty->assign( 'listpages', $listarticles["data"] );

// Display the template
$gBitSystem->display( 'bitpackage:articles/list_articles.tpl', tra( "Articles" ));
?>
