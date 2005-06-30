<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/admin/admin_types.php,v 1.1 2005/06/30 01:10:46 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization
require_once( '../../bit_setup_inc.php' );
require_once( ARTICLES_PKG_PATH . 'art_lib.php' );
require_once( ARTICLES_PKG_PATH.'BitArticle.php' );
require_once( ARTICLES_PKG_PATH.'BitArticleType.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

$gContent = &new BitArticleType(!empty($_REQUEST['article_type_id']) ? $_REQUEST['article_type_id'] : NULL);

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'bit_p_admin_cms' );

if( isset( $_REQUEST["add_type"] ) ) {
    $gContent->storeType( $_REQUEST );
} elseif( isset( $_REQUEST["remove_type"] ) ) {
    $gContent->removeType( $_REQUEST['remove_type'] );
} elseif( isset( $_REQUEST["update_type"] ) ) {
	foreach( array_keys( $_REQUEST["type_array"] ) as $this_type ) {
		$use_ratings 	= !empty($_REQUEST["use_ratings"][$this_type]) ? $_REQUEST['use_ratings'][$this_type] : NULL;
		$show_pre_publ 	= !empty($_REQUEST["show_pre_publ"][$this_type]) ? $_REQUEST['show_pre_publ'][$this_type] : NULL;
		$show_post_expire = !empty($_REQUEST["show_post_expire"][$this_type]) ? $_REQUEST['show_post_expire'][$this_type] : NULL;
		$heading_only 	= !empty($_REQUEST["heading_only"][$this_type]) ? $_REQUEST['heading_only'][$this_type] : NULL;
		$allow_comments = !empty($_REQUEST["allow_comments"][$this_type]) ? $_REQUEST['allow_comments'] : NULL;
		$comment_can_rate_article = !empty($_REQUEST["comment_can_rate_article"][$this_type]) ? $_REQUEST['comment_can_rate_article'][$this_type] : NULL;
		$show_image 	= !empty($_REQUEST["show_image"][$this_type]) ? $_REQUEST['show_image'][$this_type] : NULL;
		$show_avatar 	= !empty($_REQUEST["show_avatar"][$this_type]) ? $_REQUEST['show_avatar'][$this_type] : NULL;
		$show_author	= !empty($_REQUEST["show_author"][$this_type]) ? $_REQUEST['show_author'][$this_type] : NULL;
		$show_pubdate	= !empty($_REQUEST["show_pubdate"][$this_type]) ? $_REQUEST['show_pubdate'][$this_type] : NULL;
		$show_expdate	= !empty($_REQUEST["show_expdate"][$this_type]) ? $_REQUEST['show_expdate'][$this_type] : NULL;
		$show_reads		= !empty($_REQUEST["show_reads"][$this_type]) ? $_REQUEST['show_reads'][$this_type] : NULL;
		$show_size		= !empty($_REQUEST["show_size"][$this_type]) ? $_REQUEST['show_size'][$this_type] : NULL;
		$creator_edit	= !empty($_REQUEST["creator_edit"][$this_type])? $_REQUEST['creator_edit'][$this_type] : NULL;
		
		$parmHash = array(
			'article_type_id' => $this_type,
            'use_ratings' => ($use_ratings == 'on' ? 'y' : 'n'),
            'show_pre_publ' => ($show_pre_publ == 'on' ? 'y' : 'n'),
            'show_post_expire' => ($show_post_expire == 'on' ? 'y' : 'n'),
            'heading_only' => ($heading_only == 'on' ? 'y' : 'n'),
            'allow_comments' => ($allow_comments == 'on' ? 'y' : 'n'),
            'comment_can_rate_article' => ($comment_can_rate_article == 'on' ? 'y' : 'n'),
            'show_image' => ($show_image == 'on' ? 'y' : 'n'),
            'show_avatar' => ($show_avatar == 'on' ? 'y' : 'n'),	
            'show_author' => ($show_author == 'on' ? 'y' : 'n'),	
            'show_pubdate' => ($show_pubdate == 'on' ? 'y' : 'n'),
            'show_expdate' => ($show_expdate == 'on' ? 'y' : 'n'),
            'show_reads' => ($show_reads == 'on' ? 'y' : 'n'),	
            'show_size' => ($show_size == 'on' ? 'y' : 'n'),		
            'creator_edit' => ($creator_edit == 'on' ? 'y' : 'n') );	
			
        $gContent->storeType( $parmHash );
    } 
} 

$types = BitArticleType::listTypes();
$smarty->assign( 'types', $types );

// Display the template
$gBitSystem->display( 'bitpackage:articles/admin_types.tpl',  tra('Articles') );
?>
