<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/Attic/edit_submission.php,v 1.1 2005/06/30 01:10:46 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization
require_once( '../bit_setup_inc.php' );

include_once( ARTICLES_PKG_PATH . 'art_lib.php' );

global $bitdomain;
$gBitSystem->setBrowserTitle( tra("articles") );

if ( $feature_submissions != 'y' ) {
    $smarty->assign( 'msg', tra( "This feature is disabled" ) . ": feature_submissions" );

    $smarty->display( "error.tpl" );
    die;
} 
// Now check permissions to access this page
if ( $bit_p_submit_article != 'y' ) {
    $smarty->assign( 'msg', tra( "Permission denied you cannot send submissions" ) );

    $smarty->display( "error.tpl" );
    die;
} 

if ( $bit_p_admin != 'y' ) {
    if ( $bit_p_use_HTML != 'y' ) {
        $_REQUEST["allowhtml"] = 'off';
    } 
} 

if ( isset( $_REQUEST["sub_id"] ) ) {
    $sub_id = $_REQUEST["sub_id"];
} else {
    $sub_id = 0;
} 

$smarty->assign( 'sub_id', $sub_id );
$smarty->assign( 'article_id', $sub_id );
$smarty->assign( 'allowhtml', 'y' );
$publish_date = date( 'U' );
$expire_date = mktime ( 0, 0, 0, date( "m" ), date( "d" ), date( "Y" ) + 1 );
$dc = &$tikilib->get_date_converter( $user );
$smarty->assign( 'title', '' );
$author_name = $tikilib->get_user_preference( $user, 'real_name', $user );
$smarty->assign( 'author_name', $author_name );
$smarty->assign( 'topic_id', '' );
$smarty->assign( 'use_image', 'n' );
$smarty->assign( 'isfloat', 'n' );
$hasImage = 'n';
$smarty->assign( 'hasImage', 'n' );
$smarty->assign( 'image_name', '' );
$smarty->assign( 'image_type', '' );
$smarty->assign( 'image_size', '' );
$smarty->assign( 'image_x', 0 );
$smarty->assign( 'image_y', 0 );
$smarty->assign( 'heading', '' );
$smarty->assign( 'body', '' );
$smarty->assign( 'type', 'Article' );
$smarty->assign( 'rating', 7 );
$smarty->assign( 'edit_data', 'n' );

if ( isset( $_REQUEST["template_id"] ) && $_REQUEST["template_id"] > 0 ) {
    $template_data = $tikilib->get_template( $_REQUEST["template_id"] );

    $_REQUEST["preview"] = 1;
    $_REQUEST["body"] = $template_data["content"];
} 
// If the submissionId is passed then get the submission data
if ( isset( $_REQUEST["sub_id"] ) ) {
    $article_data = $artlib->get_submission( $_REQUEST["sub_id"] );

    $publish_date = $article_data["publish_date"];
    $expire_date = $article_data["expire_date"];
    $smarty->assign( 'title', $article_data["title"] );
    $smarty->assign( 'author_name', $article_data["author_name"] );
    $smarty->assign( 'topic_id', $article_data["topic_id"] );
    $smarty->assign( 'use_image', $article_data["use_image"] );
    $smarty->assign( 'isfloat', $article_data["isfloat"] );
    $smarty->assign( 'image_name', $article_data["image_name"] );
    $smarty->assign( 'image_type', $article_data["image_type"] );
    $smarty->assign( 'image_size', $article_data["image_size"] );
    $smarty->assign( 'image_data', urlencode( $article_data["image_data"] ) );
    $smarty->assign( 'reads', $article_data["reads"] );
    $smarty->assign( 'image_x', $article_data["image_x"] );
    $smarty->assign( 'image_y', $article_data["image_y"] );
    $smarty->assign( 'type', $article_data["type"] );
    $smarty->assign( 'rating', $article_data["rating"] );

    if ( strlen( $article_data["image_data"] ) > 0 ) {
        $smarty->assign( 'hasImage', 'y' );

        $hasImage = 'y';
    } 

    $smarty->assign( 'heading', $article_data["heading"] );
    $smarty->assign( 'body', $article_data["body"] );
    $smarty->assign( 'edit_data', 'y' );

    $data = $article_data["image_data"];
    $imgname = $article_data["image_name"];

    if ( $hasImage == 'y' ) {
        $tmpname = "cache/".$bitdomain."articleimage" . $_REQUEST["sub_id"];
        $tmpfname = TEMP_PKG_PATH . "/" . $tmpname;
        $fp = fopen( $tmpfname, "wb" );
        if ( $fp ) {
            fwrite( $fp, $data );
            fclose ( $fp );
            $smarty->assign( 'tempimg', $tmpname );
        } else {
            $smarty->assign( 'tempimg', 'n' );
        } 
    } 

    $body = $article_data["body"];
    $heading = $article_data["heading"];

    $parsed_body = $tikilib->parse_data( $body );
    $parsed_heading = $tikilib->parse_data( $heading );

    $smarty->assign( 'parsed_body', $parsed_body );
    $smarty->assign( 'parsed_heading', $parsed_heading );
} 

if ( isset( $_REQUEST["sub_id"] ) ) {
    if ( $_REQUEST["sub_id"] > 0 ) {
        if ( $bit_p_edit_submission != 'y' and $article_data["author"] != $user ) {
            $smarty->assign( 'msg', tra( "Permission denied you cannot edit submissions" ) );

            $smarty->display( "error.tpl" );
            die;
        } 
    } 
} 

if ( isset( $_REQUEST["allowhtml"] ) ) {
    if ( $_REQUEST["allowhtml"] == "on" ) {
        $smarty->assign( 'allowhtml', 'y' );
    } 
} 

$smarty->assign( 'preview', 0 );
// If we are in preview mode then preview it!
if ( isset( $_REQUEST["preview"] ) ) {
     
    // convert from the displayed 'site' time to 'server' time
    $publish_date = $dc->getServerDateFromDisplayDate( mktime( $_REQUEST["publish_Hour"], $_REQUEST["publish_Minute"],
            0, $_REQUEST["publish_Month"], $_REQUEST["publish_Day"], $_REQUEST["publish_Year"] ) );
    $expire_date = $dc->getServerDateFromDisplayDate( mktime( $_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"],
            0, $_REQUEST["expire_Month"], $_REQUEST["expire_Day"], $_REQUEST["expire_Year"] ) );

    $smarty->assign( 'reads', '0' );
    $smarty->assign( 'preview', 1 );
    $smarty->assign( 'edit_data', 'y' );
    $smarty->assign( 'title', strip_tags( $_REQUEST["title"], '<a><pre><p><img><hr>' ) );
    $smarty->assign( 'author_name', $_REQUEST["author_name"] );
    $smarty->assign( 'topic_id', $_REQUEST["topic_id"] );

    if ( isset( $_REQUEST["use_image"] ) && $_REQUEST["use_image"] == 'on' ) {
        $use_image = 'y';
    } else {
        $use_image = 'n';
    } 

    if ( isset( $_REQUEST["isfloat"] ) && $_REQUEST["isfloat"] == 'on' ) {
        $isfloat = 'y';
    } else {
        $isfloat = 'n';
    } 

    $smarty->assign( 'image_data', $_REQUEST["image_data"] );

    if ( strlen( $_REQUEST["image_data"] ) > 0 ) {
        $smarty->assign( 'hasImage', 'y' );

        $hasImage = 'y';
    } 

    $smarty->assign( 'image_name', $_REQUEST["image_name"] );
    $smarty->assign( 'image_type', $_REQUEST["image_type"] );
    $smarty->assign( 'image_size', $_REQUEST["image_size"] );
    $smarty->assign( 'image_x', $_REQUEST["image_x"] );
    $smarty->assign( 'image_y', $_REQUEST["image_y"] );
    $smarty->assign( 'use_image', $use_image );
    $smarty->assign( 'isfloat', $isfloat );
    $smarty->assign( 'type', $_REQUEST["type"] );
    $smarty->assign( 'rating', $_REQUEST["rating"] );
    $smarty->assign( 'entrating', floor( $_REQUEST["rating"] ) );
    $imgname = $_REQUEST["image_name"];
    $data = urldecode( $_REQUEST["image_data"] ); 
    // Parse the information of an uploaded file and use it for the preview
    if ( isset( $_FILES['userfile1'] ) && is_uploaded_file( $_FILES['userfile1']['tmp_name'] ) ) {
        $fp = fopen( $_FILES['userfile1']['tmp_name'], "rb" );

        $data = fread( $fp, filesize( $_FILES['userfile1']['tmp_name'] ) );
        fclose ( $fp );
        $imgtype = $_FILES['userfile1']['type'];
        $imgsize = $_FILES['userfile1']['size'];
        $imgname = $_FILES['userfile1']['name'];
        $smarty->assign( 'image_data', urlencode( $data ) );
        $smarty->assign( 'image_name', $imgname );
        $smarty->assign( 'image_type', $imgtype );
        $smarty->assign( 'image_size', $imgsize );
        $hasImage = 'y';
        $smarty->assign( 'hasImage', 'y' );
    } 

    if ( $hasImage == 'y' ) {
        $tmpname = "cache/".$bitdomain."articleimage" . $_REQUEST["sub_id"];
        $tmpfname = TEMP_PKG_PATH . "/" . $tmpname;
        $fp = fopen( $tmpfname, "wb" );
        if ( $fp ) {
            fwrite( $fp, $data );
            fclose ( $fp );
            $smarty->assign( 'tempimg', $tmpname );
        } else {
            $smarty->assign( 'tempimg', 'n' );
        } 
    } 

    $smarty->assign( 'heading', $_REQUEST["heading"] );
    $smarty->assign( 'edit_data', 'y' );

    if ( isset( $_REQUEST["allowhtml"] ) && $_REQUEST["allowhtml"] == "on" ) {
        $body = $_REQUEST["body"];

        $heading = $_REQUEST["heading"];
    } else {
        $body = strip_tags( $_REQUEST["body"], '<a><pre><p><img><hr>' );

        $heading = strip_tags( $_REQUEST["heading"], '<a><pre><p><img><hr>' );
    } 

    $smarty->assign( 'size', strlen( $body ) );

    $parsed_body = $tikilib->parse_data( $body );
    $parsed_heading = $tikilib->parse_data( $heading );

    if ( $cms_spellcheck == 'y' ) {
        if ( isset( $_REQUEST["spellcheck"] ) && $_REQUEST["spellcheck"] == 'on' ) {
            $parsed_body = $tikilib->spellcheckreplace( $body, $parsed_body, $language, 'subbody' );

            $parsed_heading = $tikilib->spellcheckreplace( $heading, $parsed_heading, $language, 'subheading' );
            $smarty->assign( 'spellcheck', 'y' );
        } else {
            $smarty->assign( 'spellcheck', 'n' );
        } 
    } 

    $smarty->assign( 'parsed_body', $parsed_body );
    $smarty->assign( 'parsed_heading', $parsed_heading );

    $smarty->assign( 'body', $body );
    $smarty->assign( 'heading', $heading );
} 
// Pro
if ( isset( $_REQUEST["save"] ) ) {
    
    include_once( IMAGEGALS_PKG_PATH . 'imagegal_lib.php' ); 
    // convert from the displayed 'site' time to 'server' time
    $publish_date = $dc->getServerDateFromDisplayDate( mktime( $_REQUEST["publish_Hour"], $_REQUEST["publish_Minute"],
            0, $_REQUEST["publish_Month"], $_REQUEST["publish_Day"], $_REQUEST["publish_Year"] ) );
    $expire_date = $dc->getServerDateFromDisplayDate( mktime( $_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"],
            0, $_REQUEST["expire_Month"], $_REQUEST["expire_Day"], $_REQUEST["expire_Year"] ) );

    if ( isset( $_REQUEST["allowhtml"] ) && $_REQUEST["allowhtml"] == "on" ) {
        $body = $_REQUEST["body"];

        $heading = $_REQUEST["heading"];
    } else {
        $body = strip_tags( $_REQUEST["body"], '<a><pre><p><img><hr>' );

        $heading = strip_tags( $_REQUEST["heading"], '<a><pre><p><img><hr>' );
    } 

    if ( isset( $_REQUEST["use_image"] ) && $_REQUEST["use_image"] == 'on' ) {
        $use_image = 'y';
    } else {
        $use_image = 'n';
    } 

    if ( isset( $_REQUEST["isfloat"] ) && $_REQUEST["isfloat"] == 'on' ) {
        $isfloat = 'y';
    } else {
        $isfloat = 'n';
    } 

    $imgdata = urldecode( $_REQUEST["image_data"] );

    if ( strlen( $imgdata ) > 0 ) {
        $hasImage = 'y';
    } 

    $imgname = $_REQUEST["image_name"];
    $imgtype = $_REQUEST["image_type"];
    $imgsize = $_REQUEST["image_size"];

    if ( isset( $_FILES['userfile1'] ) && is_uploaded_file( $_FILES['userfile1']['tmp_name'] ) ) {
        $fp = fopen( $_FILES['userfile1']['tmp_name'], "rb" );

        $imgdata = fread( $fp, filesize( $_FILES['userfile1']['tmp_name'] ) );
        fclose ( $fp );
        $imgtype = $_FILES['userfile1']['type'];
        $imgsize = $_FILES['userfile1']['size'];
        $imgname = $_FILES['userfile1']['name'];
    } 
    // Parse $edit and eliminate image references to external URIs (make them internal)
    $body = $imagegallib->capture_images( $body );
    $heading = $imagegallib->capture_images( $heading ); 
    // If page exists
    if ( !isset( $_REQUEST["topic_id"] ) ) {
        $smarty->assign( 'msg', tra( "You have to create a topic first" ) );

        $smarty->display( "error.tpl" );
        die;
    } 
	$_REQUEST['image_x']	= (isset( $_REQUEST["image_x"] ) ? $_REQUEST["image_x"] : '' );
	$_REQUEST['image_y']	= (isset( $_REQUEST["image_y"] ) ? $_REQUEST["image_y"] : '' );

    $subid = $artlib->replace_submission( strip_tags( $_REQUEST["title"], '<a><pre><p><img><hr>' ), $_REQUEST["author_name"], $_REQUEST["topic_id"], $use_image, $imgname, $imgsize, $imgtype, $imgdata, $heading, $body, $publish_date, $expire_date, $user, $sub_id, $_REQUEST["image_x"], $_REQUEST["image_y"], $_REQUEST["type"], $_REQUEST["rating"], $isfloat );
    /*                            
  $links = $tikilib->get_links($body);
  $notcachedlinks = $tikilib->get_links_nocache($body);
  $cachedlinks = array_diff($links, $notcachedlinks);
  $tikilib->cache_links($cachedlinks); 

  $links = $tikilib->get_links($heading);
  $notcachedlinks = $tikilib->get_links_nocache($heading);
  $cachedlinks = array_diff($links, $notcachedlinks);
  $tikilib->cache_links($cachedlinks); 
*/
    if ( $bit_p_autoapprove_submission == 'y' ) {
        $artlib->approve_submission( $subid );

        header ( "location: " . ARTICLES_PKG_URL . "index.php");
        die;
    } 
    $cat_type = 'article';
    $cat_objid = $sub_id;
    $cat_desc = substr( $_REQUEST["heading"], 0, 200 );
    $cat_name = $_REQUEST["title"];
    $cat_href = ARTICLES_PKG_URL . "read.php?article_id=" . $cat_objid;
    include_once( CATEGORIES_PKG_PATH . 'categorize_inc.php' );

    header ( "location: " . ARTICLES_PKG_URL . "list_submissions.php" );
    die;
} 
// Set date to today before it's too late
$_SESSION["thedate"] = date( "U" );
// Armar un select con los topics
$topics = $artlib->list_topics();
$smarty->assign_by_ref( 'topics', $topics );

$types = $artlib->list_types();
$smarty->assign_by_ref( 'types', $types );

if ( $feature_cms_templates == 'y' && $bit_p_use_content_templates == 'y' ) {
    $templates = $tikilib->list_templates( 'cms', 0, -1, 'name_asc', '' );
} 

$smarty->assign_by_ref( 'templates', $templates["data"] );

$cat_type = 'article';
$cat_objid = $sub_id;
include_once( CATEGORIES_PKG_PATH . 'categorize_list_inc.php' );

$smarty->assign( 'publish_date', $publish_date );
$smarty->assign( 'publish_dateSite', $dc->getDisplayDateFromServerDate( $publish_date ) );
$smarty->assign( 'expire_date', $expire_date );
$smarty->assign( 'expire_dateSite', $dc->getDisplayDateFromServerDate( $expire_date ) );
$smarty->assign( 'siteTimeZone', $dc->getTzName() );

include_once( KERNEL_PKG_PATH . 'textarea_size_inc.php' );

if ($gBitSystem->getPreference('package_quicktags','n') == 'y') {
  include_once( QUICKTAGS_PKG_PATH.'quicktags_inc.php' );
}

// Display the Index Template
$gBitSystem->display( 'bitpackage:articles/edit_submission.tpl' );
$smarty->assign( 'show_page_bar', 'n' );


?>
