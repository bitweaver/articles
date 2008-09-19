<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/list.php,v 1.23 2008/09/19 01:34:36 laetzer Exp $
 * @package articles
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );
require_once( ARTICLES_PKG_PATH.'BitArticle.php' );
include_once( ARTICLES_PKG_PATH.'article_filter_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'p_articles_read' );

// get services set up
$gContent = new BitArticle();
$gContent->invokeServices( 'content_list_function', $_REQUEST );

// nuke articles if requested
if( !empty( $_REQUEST['action'] ) ) {
	if( $_REQUEST['action'] == 'remove' && !empty( $_REQUEST['remove_article_id'] ) ) {
		$tmpArt = new BitArticle( $_REQUEST['remove_article_id'] );
		$tmpArt->load();
		// depending on what the status of the article is, we need to check different permissions
		if( $tmpArt->mInfo['status_id'] == ARTICLE_STATUS_PENDING ) {
			$gBitSystem->verifyPermission( 'p_articles_remove_submission' );
		} else {
			$gBitSystem->verifyPermission( 'p_articles_remove' );
		}

		if( isset( $_REQUEST["confirm"] ) ) {
			if( $tmpArt->expunge() ) {
				header( "Location: ".ARTICLES_PKG_URL.'list.php?status_id='.( !empty( $_REQUEST['status_id'] ) ? $_REQUEST['status_id'] : '' ) );
				die;
			} else {
				$feedback['error'] = $tmpArt->mErrors;
			}
		}
		$gBitSystem->setBrowserTitle( tra('Confirm removal of'). ' ' .$tmpArt->mInfo['title'] );
		$formHash['remove'] = TRUE;
		$formHash['action'] = 'remove';
		$formHash['status_id'] = ( !empty( $_REQUEST['status_id'] ) ? $_REQUEST['status_id'] : '' );
		$formHash['remove_article_id'] = $_REQUEST['remove_article_id'];
		$msgHash = array(
			'label' => tra('Remove Article'),
			'confirm_item' => $tmpArt->mInfo['title'],
			'warning' => tra('Remove the above article.'),
			'error' => tra('This cannot be undone!'),
		);
		$gBitSystem->confirmDialog( $formHash, $msgHash );
	}
}

/* this is a messed up version of a multiple articles removal section
if( isset( $_REQUEST["multi_article"] ) && isset( $_REQUEST["checked"] ) && $_REQUEST["multi_article"] == "remove_articles" ) {
	// Now check permissions to remove the selected articles
	$gBitSystem->verifyPermission( 'p_articles_remove' );

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
	if( !empty( $_REQUEST['article_id'] ) && !empty( $_REQUEST['set_status_id'] ) && $gBitUser->hasPermission( 'p_articles_approve_submission' ) ) {
		$article->setStatus( $_REQUEST['set_status_id'], $_REQUEST['article_id'], $_REQUEST['content_id'] );
	}
}

if( empty( $_REQUEST['status_id'] ) || (!(($gBitSystem->isFeatureActive('articles_auto_approve') && $gBitUser->isRegistered()) || $gBitUser->hasPermission( 'p_articles_edit_submission' ) || $gBitUser->hasPermission( 'p_articles_admin' ) ) ) ) {
	$_REQUEST['status_id'] = ARTICLE_STATUS_APPROVED;
}
$listArticles = $article->getList( $_REQUEST );

$topics = BitArticleTopic::getTopicList();
$gBitSmarty->assign( 'topics', $topics );

$types = BitArticleType::getTypeList();
$gBitSmarty->assign( 'types', $types );

$gBitSmarty->assign( 'listInfo', $_REQUEST['listInfo'] );
$gBitSmarty->assign( 'listpages', $listArticles );

// Display the template
$gBitSystem->display( 'bitpackage:articles/list_articles.tpl', tra( "Articles" ), array( 'display_mode' => 'list' ));
?>
