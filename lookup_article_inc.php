<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/lookup_article_inc.php,v 1.5.2.1 2005/12/22 13:00:06 squareing Exp $
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
		if (@BitBase::verifyId( $_REQUEST['article_id'] ) ) {
			$gContent = new BitArticle( $_REQUEST['article_id'] );
		} elseif( @BitBase::verifyId( $_REQUEST['content_id'] ) ) {
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
