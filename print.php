<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/print.php,v 1.1 2005/06/30 01:10:46 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization
require_once( '../bit_setup_inc.php' );

include_once( ARTICLES_PKG_PATH . 'art_lib.php' );

if ( !isset( $_REQUEST["article_id"] ) ) {
    $smarty->assign( 'msg', tra( "No article indicated" ) );

    $smarty->display( "error.tpl" );
    die;
} 

if ( isset( $_REQUEST["article_id"] ) ) {
    $artlib->add_article_hit( $_REQUEST["article_id"] );

    $smarty->assign( 'article_id', $_REQUEST["article_id"] );
    $article_data = $artlib->get_article( $_REQUEST["article_id"] );

    if ( !$article_data ) {
        $smarty->assign( 'msg', tra( "Article not found" ) );

        $smarty->display( "error.tpl" );
        die;
    } 

    if ( ( $article_data["publish_date"] > date( "U" ) ) && ( $bit_p_admin != 'y' ) ) {
        $smarty->assign( 'msg', tra( "Article is not published yet" ) );

        $smarty->display( "error.tpl" );
        die;
    } 

    $smarty->assign( 'title', $article_data["title"] );
    $smarty->assign( 'author_name', $article_data["author_name"] );
    $smarty->assign( 'topic_id', $article_data["topic_id"] );
    $smarty->assign( 'use_image', $article_data["use_image"] );
    $smarty->assign( 'image_name', $article_data["image_name"] );
    $smarty->assign( 'image_type', $article_data["image_type"] );
    $smarty->assign( 'image_size', $article_data["image_size"] );
    $smarty->assign( 'image_data', urlencode( $article_data["image_data"] ) );
    $smarty->assign( 'reads', $article_data["reads"] );
    $smarty->assign( 'size', $article_data["size"] );

    if ( strlen( $article_data["image_data"] ) > 0 ) {
        $smarty->assign( 'hasImage', 'y' );

        $hasImage = 'y';
    } 

    $smarty->assign( 'heading', $article_data["heading"] );
    $smarty->assign( 'body', $article_data["body"] );
    $smarty->assign( 'publish_date', $article_data["publish_date"] );
    $smarty->assign( 'edit_data', 'y' );

    $body = $article_data["body"];
    $heading = $article_data["heading"];
    $smarty->assign( 'parsed_body', $tikilib->parse_data( $body ) );
    $smarty->assign( 'parsed_heading', $tikilib->parse_data( $heading ) );
} 



$smarty->display( "bitpackage:articles/print_article.tpl" );

?>
