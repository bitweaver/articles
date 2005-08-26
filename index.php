<?php
// $Header: /cvsroot/bitweaver/_bit_articles/index.php,v 1.4 2005/08/26 10:15:15 squareing Exp $
// Copyright( c )2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'bit_p_read_article' );

if( !empty( $_REQUEST['article_id'] ) ) {
	header( "location: ".ARTICLES_PKG_URL."read.php?article_id=".( ( int )$_REQUEST['article_id'] ) );
}

require_once( ARTICLES_PKG_PATH.'lookup_article_inc.php' );

// only display approved articles
$_REQUEST['status_id'] = ARTICLE_STATUS_APPROVED;
$articles = $gContent->getList( $_REQUEST );

foreach( $articles['data'] as $key => $article ) {
	if( strlen( $article['parsed_description'] ) != strlen( $article['parsed_data'] ) ) {
		$articles['data'][$key]['read_more'] = TRUE;
	}
}
$smarty->assign_by_ref( 'articles', $articles['data'] );

// Display the template
$gBitSystem->display( 'bitpackage:articles/center_list_articles.tpl', tra( 'Articles' ) );
?>
