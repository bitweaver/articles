<?php
// $Header: /cvsroot/bitweaver/_bit_articles/Attic/list_submissions.php,v 1.1 2005/06/30 01:10:45 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization
require_once( '../bit_setup_inc.php' );

include_once( ARTICLES_PKG_PATH . 'BitArticle.php' );

$gBitSystem->setBrowserTitle( tra("Article Submissions") );

$gBitSystem->verifyFeature('feature_article_submissions' );

if ( isset( $_REQUEST["deny"] ) ) {
    
    if ($gBitUser->hasPermission('bit_p_remove_submission') || $gBitUser->hasPermission('bit_p_admin_articles')) {
		$article = &new BitArticle($_REQUEST['deny']);
    	$article->setStatus( ARTICLE_STATUS_DENIED );
	}
}

if ( isset( $_REQUEST["approve"] ) ) {
    
    if ($gBitUser->hasPermission('bit_p_approve_submission') || $gBitUser->hasPermission('bit_p_admin_articles')) {
    	$article = &new BitArticle($_REQUEST['approve']);
		$article->setStatus( ARTICLE_STATUS_APPROVED );
	}
}

/*
// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if (  empty( $_REQUEST["sort_mode"] )  ) {
    $sort_mode = 'publish_date_desc';
} else {
    $sort_mode = $_REQUEST["sort_mode"];
}

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use last_modified_desc
if ( !isset( $_REQUEST["offset"] ) ) {
    $offset = 0;
} else {
    $offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref( 'offset', $offset );

$now = date( "U" );
if( ( $bit_p_admin == 'y' ) || ( $bit_p_admin_cms == 'y' ) ) {
    $pdate = '';
} elseif( isset( $_SESSION["thedate"] ) ) {
    if( $_SESSION["thedate"] < $now ) {
        $pdate = $_SESSION["thedate"];
    } else {
        $pdate = $now;
    }
} else {
    $pdate = $now;
}

if ( isset( $_REQUEST["find"] ) ) {
    $find = $_REQUEST["find"];
} else {
    $find = '';
}

$pagination_url = $tikilib->pagination_url($find, $sort_mode);
$smarty->assign_by_ref('pagination_url', $pagination_url);
// Get a list of last changes to the Wiki database
$listpages = $artlib->list_submissions( $offset, $maxRecords, $sort_mode, $find, $pdate );
// If there're more records then assign next_offset
$cant_pages = ceil( $listpages["cant"] / $maxRecords );
$smarty->assign_by_ref( 'cant_pages', $cant_pages );
$smarty->assign( 'actual_page', 1 + ( $offset / $maxRecords ) );

if ( $listpages["cant"] > ( $offset + $maxRecords ) ) {
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
*/
$article = &new BitArticle();
$_REQUEST['status_id'] = ARTICLE_STATUS_PENDING;
$listpages = $article->getList($_REQUEST);

$smarty->assign_by_ref( 'listpages', $listpages["data"] );
// print_r($listpages["data"]);

// Display the template
$gBitSystem->display( 'bitpackage:articles/list_submissions.tpl' );


?>
