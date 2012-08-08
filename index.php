<?php
// $Header$
// Copyright( c )2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

// Initialization
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );
require_once( ARTICLES_PKG_PATH.'BitArticle.php' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'p_articles_read' );

if( !empty( $_REQUEST['article_id'] ) ) {
	$param = array ( 'article_id' => ( int )$_REQUEST['article_id'] );
	bit_redirect( BitArticle::getDisplayUrlFromHash( $param ) );
}

// Display the template
$gDefaultCenter = 'bitpackage:articles/center_list_articles.tpl';
$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );

// Display the template
$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', tra( 'Articles' ) , array( 'display_mode' => 'display' ));
?>
