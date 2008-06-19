<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/lookup_article_topic_inc.php,v 1.4 2008/06/19 09:29:08 lsces Exp $
 * @package articles
 * @subpackage functions
 */

/**
 * Initialization
 */
	global $gContent;
	require_once( ARTICLES_PKG_PATH.'BitArticle.php');
	
	// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
	if( empty( $gContent ) || !is_object( $gContent ) ) {
		if (!empty($_REQUEST['topic_id']) && is_numeric($_REQUEST['topic_id'])) {
			$gContent = new BitArticleTopic( $_REQUEST['topic_id'] );
		} else {
			$gContent = new BitArticleTopic();
		}

		if( empty( $gContent->mTopicId ) ) {
			//handle legacy forms that use plain 'article' form variable name
		} else {
			$gContent->loadTopic();
		}
		$gBitSmarty->assign_by_ref( 'gContent', $gContent );
	}
?>
