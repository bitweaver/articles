<?php 
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/rankings.php,v 1.3 2005/10/30 19:48:40 lsces Exp $
 * @package article
 * @subpackage functions
 */

/**
 * Initialization
 */
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

$gBitSmarty->assign( 'allrankings', $allrankings );

if ( !isset( $_REQUEST["which"] ) ) {
    $which = 'cms_ranking_top_articles';
} else {
    $which = $_REQUEST["which"];
} 

$gBitSmarty->assign( 'which', $which );
// Get the page from the request var or default it to HomePage
if ( !isset( $_REQUEST["limit"] ) ) {
    $limit = 10;
} else {
    $limit = $_REQUEST["limit"];
} 

$gBitSmarty->assign_by_ref( 'limit', $limit );
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

$gBitSmarty->assign_by_ref( 'rankings', $rankings );
$gBitSmarty->assign( 'rpage', ARTICLES_PKG_URL . 'rankings.php' );


// Display the template
$gBitSystem->display( 'bitpackage:kernel/ranking.tpl', tra("articles") );

?>
