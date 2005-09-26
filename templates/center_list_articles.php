<?php
// $Header: /cvsroot/bitweaver/_bit_articles/templates/center_list_articles.php,v 1.3 2005/09/26 07:15:09 squareing Exp $
require_once( '../bit_setup_inc.php' );
include_once( ARTICLES_PKG_PATH . 'BitArticle.php' );

$gBitSystem->verifyPackage( 'articles' );

$gBitSystem->verifyPermission( 'bit_p_read_article' );

include_once(ARTICLES_PKG_PATH."lookup_article_inc.php");

if ( isset( $_REQUEST["remove"] ) ) {
    $gBitSystem->verifyPermission( 'bit_p_remove_article' );
}

if (empty($_REQUEST['status_id']) || (!$gBitUser->hasPermission('bit_p_view_submissions') && !$gBitUser->hasPermission('bit_p_admin_articles'))) {
	$_REQUEST['status_id'] = ARTICLE_STATUS_APPROVED;
}

$gBitSmarty->assign('descriptionLength', $gBitSystem->getPreference( 'article_description_length', 500 ) );
$gBitSmarty->assign('showDescriptionsOnly', TRUE);

//if( empty( $articles ) ) {
//	$articles = $gContent->getList($_REQUEST);
//	$gBitSmarty->assign_by_ref('articles', $articles['data']);
//}
?>
