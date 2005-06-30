<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/Attic/received_article_image.php,v 1.1 2005/06/30 01:10:45 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Header: /cvsroot/bitweaver/_bit_articles/Attic/received_article_image.php,v 1.1 2005/06/30 01:10:45 bitweaver Exp $
// application to display an image from the database with
// option to resize the image dynamically creating a thumbnail on the fly.
if ( !isset( $_REQUEST["id"] ) ) {
    die;
} 

global $tikilib;
include_once ( KERNEL_PKG_PATH . 'commlib.php' );
$data = $commlib->get_received_article( $_REQUEST["id"] );
$type = $data["image_type"];
$data = $data["image_data"];
header ( "Content-type: $type" );
echo $data;

?>
