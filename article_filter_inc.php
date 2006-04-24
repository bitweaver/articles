<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/article_filter_inc.php,v 1.5 2006/04/24 20:47:41 squareing Exp $
 * @package article
 * @subpackage functions
 */

if( $gBitSystem->isFeatureActive( 'articles_display_filter_bar' ) && ( $gBitUser->isAdmin() || $gBitUser->hasPermission( 'p_articles_admin' ) ) ) {
	$filter['topic'][]  = '';
	$filter['type'][]   = '';
	$filter['status'][] = '';

	$_topics = BitArticleTopic::getTopicList();
	foreach( $_topics as $topic ) {
		$filter['topic'][$topic['topic_id']] = $topic['topic_name'];
	}

	$_types = BitArticleType::getTypeList();
	foreach( $_types as $type ) {
		$filter['type'][$type['article_type_id']] = $type['type_name'];
	}

	$_statuses = BitArticle::getStatusList();
	foreach( $_statuses as $status ) {
		$filter['status'][$status['status_id']] = $status['status_name'];
	}

	$gBitSmarty->assign( 'filter', $filter );
}
?>
