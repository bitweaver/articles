<?php
// $Header: /cvsroot/bitweaver/_bit_articles/templates/center_list_articles.php,v 1.6 2006/04/11 17:52:09 squareing Exp $
require_once( '../bit_setup_inc.php' );
include_once( ARTICLES_PKG_PATH . 'BitArticle.php' );

$gBitSystem->verifyPackage( 'articles' );

$gBitSystem->verifyPermission( 'p_articles_read' );

include_once(ARTICLES_PKG_PATH."lookup_article_inc.php");

if ( isset( $_REQUEST["remove"] ) ) {
    $gBitSystem->verifyPermission( 'p_articles_remove' );
}

if (empty($_REQUEST['status_id']) || (!$gBitUser->hasPermission('p_articles_approve_submission') && !$gBitUser->hasPermission('p_articles_admin'))) {
	$_REQUEST['status_id'] = ARTICLE_STATUS_APPROVED;
}

$gBitSmarty->assign('descriptionLength', $gBitSystem->getConfig( 'article_description_length', 500 ) );
$gBitSmarty->assign('showDescriptionsOnly', TRUE);

//if( empty( $articles ) ) {
//	$articles = $gContent->getList($_REQUEST);
//	$gBitSmarty->assign_by_ref('articles', $articles['data']);
//}
?>
