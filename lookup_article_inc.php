<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/lookup_article_inc.php,v 1.5 2005/10/30 19:48:40 lsces Exp $
 * @package article
 * @subpackage functions
 */

/**
 * Initialization
 */
	global $gContent;
	require_once( ARTICLES_PKG_PATH.'BitArticle.php');
	require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

	// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
	if( empty( $gContent ) || !is_object( $gContent ) ) {
		if (!empty($_REQUEST['article_id']) && is_numeric( $_REQUEST['article_id'] ) ) {
			$gContent = new BitArticle( $_REQUEST['article_id'] );
		} elseif( !empty($_REQUEST['content_id'] ) && is_numeric( $_REQUEST['content_id'] ) ) {
			$gContent = new BitArticle( NULL, $_REQUEST['content_id'] );
		} else {
			$gContent = new BitArticle();
			$gContent->mInfo['expire_date'] = strtotime( "+1 year" );
		}

		if( empty( $gContent->mArticleId ) && empty( $gContent->mContentId )  ) {
			//handle legacy forms that use plain 'article' form variable name
		} else {
			$gContent->load();
		}
		$gBitSmarty->assign_by_ref( 'gContent', $gContent );
	}
?>
