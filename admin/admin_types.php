<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/admin/admin_types.php,v 1.3 2005/08/26 09:33:42 squareing Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization
require_once( '../../bit_setup_inc.php' );
require_once( ARTICLES_PKG_PATH.'BitArticle.php' );
require_once( ARTICLES_PKG_PATH.'BitArticleType.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'bit_p_admin_cms' );

$gContent = &new BitArticleType( !empty( $_REQUEST['article_type_id'] ) ? $_REQUEST['article_type_id'] : NULL );

if( isset( $_REQUEST["add_type"] ) ) {
    $gContent->storeType( $_REQUEST );
} elseif( isset( $_REQUEST["remove_type"] ) ) {
    $gContent->removeType( $_REQUEST['remove_type'] );
} elseif( isset( $_REQUEST["update_type"] ) ) {
	$options = array(
		'use_ratings',
		'show_pre_publ',
		'show_post_expire',
		'heading_only',
		'allow_comments',
		'comment_can_rate_article',
		'show_image',
		'show_avatar',	
		'show_author',	
		'show_pubdate',
		'show_expdate',
		'show_reads',	
		'show_size',		
		'creator_edit',
	);	
	foreach( array_keys( $_REQUEST["type_array"] ) as $this_type ) {
		$storeHash['article_type_id'] = $this_type;
		foreach( $options as $option ) {
			$storeHash[$option] = !empty( $_REQUEST[$option][$this_type] ) ? 'y' : 'n';
		}
        $gContent->storeType( $storeHash );
    } 
} 

$types = BitArticleType::listTypes();
$smarty->assign( 'types', $types );

// Display the template
$gBitSystem->display( 'bitpackage:articles/admin_types.tpl',  tra('Edit Article Types') );
?>
