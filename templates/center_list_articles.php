	<?php
// $Header: /cvsroot/bitweaver/_bit_articles/templates/center_list_articles.php,v 1.1 2005/06/30 01:10:46 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization
require_once( '../bit_setup_inc.php' );
include_once( ARTICLES_PKG_PATH . 'BitArticle.php' );

$gBitSystem->verifyPackage( 'articles' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'bit_p_read_article' );

include_once(ARTICLES_PKG_PATH."lookup_article_inc.php");

if ( isset( $_REQUEST["remove"] ) ) {
    
    $gBitSystem->verifyPermission( 'bit_p_remove_article' );
	
}
if (empty($_REQUEST['status_id']) || (!$gBitUser->hasPermission('bit_p_view_submissions') && !$gBitUser->hasPermission('bit_p_admin_articles'))) {
	$_REQUEST['status_id'] = ARTICLE_STATUS_APPROVED;
}

$articles = $gContent->getList($_REQUEST);
$smarty->assign('descriptionLength', $gBitSystem->getPreference('article_description_length', 400));
$smarty->assign('showDescriptionsOnly', TRUE);
$smarty->assign_by_ref('articles', $articles['data']);

?>
