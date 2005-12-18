<?php 
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/rankings.php,v 1.5 2006/01/10 21:11:08 squareing Exp $
 * @package article
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

include_once( KERNEL_PKG_PATH . 'rank_lib.php' );
include_once( ARTICLES_PKG_PATH . 'BitArticle.php' );

$gBitSystem->isPackageActive( 'articles' );
$gBitSystem->isFeatureActive( 'feature_cms_rankings' );
$gBitSystem->verifyPermission( 'bit_p_read_article' );

$allrankings = array( 
	array(
		'name' => tra( 'Top articles' ),
		'value' => 'contentRanking' 
	),
//	array(
//		'name' => tra( 'Top authors' ),
//		'value' => 'cms_ranking_top_authors' 
//	) 
);
$gBitSmarty->assign( 'allrankings', $allrankings );

if ( !isset( $_REQUEST["which"] ) ) {
	$func = 'contentRanking';
} else {
	$func = $_REQUEST["which"];
}
$gBitSmarty->assign( 'which', $func );

$listHash = array(
	'title' => tra( 'Article Rankings' ),
	'content_type_guid' => BITARTICLE_CONTENT_TYPE_GUID,
	'limit' => ( !empty( $_REQUEST['limit'] ) ? $_REQUEST['limit'] : 10 ),
);
$rankings[] = $ranklib->$func( $listHash );
$gBitSmarty->assign( 'rankings', $rankings );

// Display the template
$gBitSystem->display( 'bitpackage:kernel/ranking.tpl', tra( "Articles" ) );
?>
