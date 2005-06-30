<?php
// $Header: /cvsroot/bitweaver/_bit_articles/Attic/received_articles.php,v 1.1 2005/06/30 01:10:45 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization
require_once( '../bit_setup_inc.php' );

include_once( KERNEL_PKG_PATH . 'comm_lib.php' );
include_once( ARTICLES_PKG_PATH . 'art_lib.php' );

if ( $feature_comm != 'y' ) {
    $smarty->assign( 'msg', tra( "This feature is disabled" ) . ": feature_comm" );
    $gBitSystem->display( "error.tpl" );
    die;
}

if ( $gBitUser->hasPermission( '$bit_p_admin_received_articles')) {
    $smarty->assign( 'msg', tra( "You dont have permission to use this feature" ) );
    $gBitSystem->display( 'error.tpl' );
    die;
}

if ( !isset( $_REQUEST["received_article_id"] ) ) {
    $_REQUEST["received_article_id"] = 0;
}

$smarty->assign( 'received_article_id', $_REQUEST["received_article_id"] );

if ( $_REQUEST["received_article_id"] ) {
    $info = $commlib->get_received_article( $_REQUEST["received_article_id"] );

    $info["topic"] = 1;
} else {
    $info = array();

    $info["title"] = '';
    $info["author_name"] = '';
    $info["size"] = 0;
    $info["use_image"] = 'n';
    $info["image_name"] = '';
    $info["image_type"] = '';
    $info["image_size"] = 0;
    $info["image_x"] = 0;
    $info["image_y"] = 0;
    $info["image_data"] = '';
    $info["publish_date"] = date( "U" );
    $info["expire_date"] = mktime ( 0, 0, 0, date( "m" ), date( "d" ), date( "Y" ) + 1 );
    $info["created"] = date( "U" );
    $info["heading"] = '';
    $info["body"] = '';
    $info["hash"] = '';
    $info["author"] = '';
    $info["topic"] = 1;
    $info["type"] = 'Article';
    $info["rating"] = 5;
}

$smarty->assign( 'view', 'n' );

if ( isset( $_REQUEST["view"] ) ) {
    $info = $artlib->get_received_article( $_REQUEST["view"] );

    $smarty->assign( 'view', 'y' );
    $info["topic"] = 1;
}

if ( isset( $_REQUEST["accept"] ) ) {
    
    // CODE TO ACCEPT A PAGE HERE
    $publish_date = mktime( $_REQUEST["Time_Hour"], $_REQUEST["Time_Minute"],
        0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], $_REQUEST["Date_Year"] );
    $expire_date = mktime( $_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"],
        0, $_REQUEST["expire_Month"], $_REQUEST["expire_Day"], $_REQUEST["expire_Year"] );

    $commlib->update_received_article( $_REQUEST["received_article_id"], $_REQUEST["title"], $_REQUEST["author_name"],
        $_REQUEST["use_image"], $_REQUEST["image_x"], $_REQUEST["image_y"],
        $publish_date, $expire_date, $_REQUEST["heading"], $_REQUEST["body"], $_REQUEST["type"], $_REQUEST["rating"] );
    $commlib->accept_article( $_REQUEST["received_article_id"], $_REQUEST["topic"] );
    $smarty->assign( 'preview', 'n' );
    $smarty->assign( 'received_article_id', 0 );
}

$smarty->assign( 'preview', 'n' );
$smarty->assign( 'topic', $info["topic"] );

if ( isset( $_REQUEST["preview"] ) ) {
    $smarty->assign( 'preview', 'y' );

    $info["publish_date"] = mktime( $_REQUEST["Time_Hour"], $_REQUEST["Time_Minute"],
        0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], $_REQUEST["Date_Year"] );
    $info["expire_date"] = mktime( $_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"],
        0, $_REQUEST["expire_Month"], $_REQUEST["expire_Day"], $_REQUEST["expire_Year"] );
    $info["title"] = $_REQUEST["title"];
    $info["author_name"] = $_REQUEST["author_name"];
    $info["received_article_id"] = $_REQUEST["received_article_id"];
    $info["use_image"] = $_REQUEST["use_image"];
    $info["image_name"] = $_REQUEST["image_name"];
    $info["image_size"] = $_REQUEST["image_size"];
    $info["image_x"] = $_REQUEST["image_x"];
    $info["image_y"] = $_REQUEST["image_y"];
    $info["created"] = $_REQUEST["created"];
    $info["heading"] = $_REQUEST["heading"];
    $info["body"] = $_REQUEST["body"];
    $info["topic"] = $_REQUEST["topic"];
    $info["type"] = $_REQUEST["type"];
    $info["rating"] = $_REQUEST["rating"];
}

$smarty->assign( 'topic', $info["topic"] );
$smarty->assign( 'title', $info["title"] );
$smarty->assign( 'author_name', $info["author_name"] );
$smarty->assign( 'use_image', $info["use_image"] );
$smarty->assign( 'image_name', $info["image_name"] );
$smarty->assign( 'image_size', $info["image_size"] );
$smarty->assign( 'image_x', $info["image_x"] );
$smarty->assign( 'image_y', $info["image_y"] );
$smarty->assign( 'publish_date', $info["publish_date"] );
$smarty->assign( 'expire_date', $info["expire_date"] );
$smarty->assign( 'created', $info["created"] );
$smarty->assign( 'heading', $info["heading"] );
$smarty->assign( 'body', $info["body"] );
$smarty->assign( 'type', $info["type"] );
$smarty->assign( 'rating', $info["rating"] );
// Assign parsed
$smarty->assign( 'parsed_heading', $tikilib->parse_data( $info["heading"] ) );
$smarty->assign( 'parsed_body', $tikilib->parse_data( $info["body"] ) );

if ( isset( $_REQUEST["remove"] ) ) {
    
    $commlib->remove_received_article( $_REQUEST["remove"] );
}

if ( isset( $_REQUEST["save"] ) ) {
    
    $publish_date = mktime( $_REQUEST["Time_Hour"], $_REQUEST["Time_Minute"],
        0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], $_REQUEST["Date_Year"] );
    $expire_date = mktime( $_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"],
        0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], $_REQUEST["Date_Year"] );

    $commlib->update_received_article( $_REQUEST["received_article_id"], $_REQUEST["title"], $_REQUEST["author_name"],
        $_REQUEST["use_image"], $_REQUEST["image_x"], $_REQUEST["image_y"], $publish_date, $expire_date, $_REQUEST["heading"], $_REQUEST["body"] );
    $smarty->assign( 'received_article_id', $_REQUEST["received_article_id"] );
    $smarty->assign( 'title', $_REQUEST["title"] );
    $smarty->assign( 'author_name', $_REQUEST["author_name"] );
    $smarty->assign( 'size', strlen( $_REQUEST["body"] ) );
    $smarty->assign( 'use_image', $_REQUEST["use_image"] );
    $smarty->assign( 'image_x', $_REQUEST["image_x"] );
    $smarty->assign( 'image_y', $_REQUEST["image_y"] );
    $smarty->assign( 'publish_date', $publish_date );
    $smarty->assign( 'expire_date', $expire_date );
    $smarty->assign( 'heading', $_REQUEST["heading"] );
    $smarty->assign( 'body', $_REQUEST["body"] );
}

if (  empty( $_REQUEST["sort_mode"] )  ) {
    $sort_mode = 'received_date_desc';
} else {
    $sort_mode = $_REQUEST["sort_mode"];
}

if ( !isset( $_REQUEST["offset"] ) ) {
    $offset = 0;
} else {
    $offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref( 'offset', $offset );

if ( isset( $_REQUEST["find"] ) ) {
    $find = $_REQUEST["find"];
} else {
    $find = '';
}

$pagination_url = $tikilib->pagination_url($find, $sort_mode);
$smarty->assign_by_ref('pagination_url', $pagination_url);

$channels = $commlib->list_received_articles( $offset, $maxRecords, $sort_mode, $find );

$cant_pages = ceil( $channels["cant"] / $maxRecords );
$smarty->assign_by_ref( 'cant_pages', $cant_pages );
$smarty->assign( 'actual_page', 1 + ( $offset / $maxRecords ) );

if ( $channels["cant"] > ( $offset + $maxRecords ) ) {
    $smarty->assign( 'next_offset', $offset + $maxRecords );
} else {
    $smarty->assign( 'next_offset', -1 );
}
// If offset is > 0 then prev_offset
if ( $offset > 0 ) {
    $smarty->assign( 'prev_offset', $offset - $maxRecords );
} else {
    $smarty->assign( 'prev_offset', -1 );
}

$smarty->assign_by_ref( 'channels', $channels["data"] );

$topics = $artlib->list_topics();
$smarty->assign_by_ref( 'topics', $topics );

$types = $artlib->list_types();
$smarty->assign_by_ref( 'types', $types );


// Display the template
$gBitSystem->display( 'bitpackage:articles/received_articles.tpl' );


?>
