<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/Attic/article_image.php,v 1.1 2005/06/30 01:10:45 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Header: /cvsroot/bitweaver/_bit_articles/Attic/article_image.php,v 1.1 2005/06/30 01:10:45 bitweaver Exp $
// application to display an image from the database with
// option to resize the image dynamically creating a thumbnail on the fly.

include_once( '../bit_setup_inc.php' );
include_once( ARTICLES_PKG_PATH.'art_lib.php' );

global $gBitUser, $bitdomain;

$topiccachefile = '1234';
if( $_REQUEST["article_id"] ) {
	$topiccachefile = TEMP_PKG_PATH."cache/".$bitdomain."articleimage." . $_REQUEST["article_id"];
} elseif( $gBitUser->mUserId ) {
	$topiccachefile = TEMP_PKG_PATH."cache/".$bitdomain."temp_article.".$gBitUser->mUserId;
}

if ( is_file( $topiccachefile ) and ( !isset( $_REQUEST["reload"] ) ) ) {
    $size = getimagesize( $topiccachefile );
    if (isset($size['mime'])) header ("Content-type: ".$size['mime']);
    readfile( $topiccachefile );
    die();
} elseif( $_REQUEST["article_id"] ) {
    global $tikilib;
    $data = $artlib->get_article_image( $_REQUEST["article_id"] );
    $type = $data["image_type"];
    $data = $data["image_data"];
    if ( $data["image_data"] ) {
        $fp = fopen( $topiccachefile, "wb" );
        fputs( $fp, $data );
        fclose( $fp );
    } 
}

header ( "Content-type: $type" );
if ( is_file( $topiccachefile ) ) {
    readfile( $topiccachefile );
} else {
    echo $data;
} 

?>
