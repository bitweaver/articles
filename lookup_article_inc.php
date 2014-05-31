<?php
/**
 * @version $Header$
 * @package articles
 * @subpackage functions
 */

/**
 * Initialization
 */
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
	$gBitSmarty->clear_assign( 'gContent' );
	$gBitSmarty->assign( 'gContent', $gContent );
}
