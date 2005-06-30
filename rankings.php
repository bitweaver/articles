<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/rankings.php,v 1.1 2005/06/30 01:10:46 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization
require_once( '../bit_setup_inc.php' );

include_once( KERNEL_PKG_PATH . 'rank_lib.php' );


$gBitSystem->isPackageActive( 'articles' );

$gBitSystem->isFeatureActive( 'feature_cms_rankings' );

$gBitSystem->verifyPermission( 'bit_p_read_article' );

$allrankings = array( 
    array( 'name' => tra( 'Top articles' ),
        'value' => 'cms_ranking_top_articles' 
        ),
    array( 'name' => tra( 'Top authors' ),
        'value' => 'cms_ranking_top_authors' 
        ) 
    );

$smarty->assign( 'allrankings', $allrankings );

if ( !isset( $_REQUEST["which"] ) ) {
    $which = 'cms_ranking_top_articles';
} else {
    $which = $_REQUEST["which"];
} 

$smarty->assign( 'which', $which );
// Get the page from the request var or default it to HomePage
if ( !isset( $_REQUEST["limit"] ) ) {
    $limit = 10;
} else {
    $limit = $_REQUEST["limit"];
} 

$smarty->assign_by_ref( 'limit', $limit );
// Rankings:
// Top Pages
// Last pages
// Top Authors
$rankings = array();

$rk = $ranklib->$which( $limit );
$rank["data"] = $rk["data"];
$rank["title"] = $rk["title"];
$rank["y"] = $rk["y"];
$rankings[] = $rank;

$smarty->assign_by_ref( 'rankings', $rankings );
$smarty->assign( 'rpage', ARTICLES_PKG_URL . 'rankings.php' );


// Display the template
$gBitSystem->display( 'bitpackage:kernel/ranking.tpl', tra("articles") );

?>
