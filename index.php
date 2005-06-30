<?php
// $Header: /cvsroot/bitweaver/_bit_articles/index.php,v 1.1 2005/06/30 01:10:45 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'bit_p_read_article' );

if (!empty($_REQUEST['article_id'])) {
	header("location: ".ARTICLES_PKG_URL."read.php?article_id=".((int)$_REQUEST['article_id']));
}

/*require_once( ARTICLES_PKG_PATH.'lookup_article_inc.php' );

$articles = $gContent->getList($_REQUEST);
$smarty->assign_by_ref('articles', $articles);

*/


// Display the template
$gBitSystem->display( 'bitpackage:articles/center_list_articles.tpl', tra('Articles') );
?>
