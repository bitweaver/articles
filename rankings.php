<?php 
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/rankings.php,v 1.6 2006/02/04 10:10:50 squareing Exp $
 * @package article
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

include_once( LIBERTY_PKG_PATH . 'LibertyContent.php' );
include_once( ARTICLES_PKG_PATH . 'BitArticle.php' );

$gBitSystem->isPackageActive( 'articles' );
$gBitSystem->isFeatureActive( 'feature_cms_rankings' );
$gBitSystem->verifyPermission( 'bit_p_read_article' );

$rankingOptions = array(
	array(
		'output' => tra( 'Most Often Viewed' ),
		'value' => 'hits_desc'
	),
	array(
		'output' => tra( 'Most Recently Modified' ),
		'value' => 'last_modified_desc'
	),
	array(
		'output' => tra( 'Most Active Authors' ),
		'value' => 'top_authors'
	),
);
$gBitSmarty->assign( 'rankingOptions', $rankingOptions );

if( !empty( $_REQUEST['sort_mode'] ) ) {
	switch( $_REQUEST['sort_mode'] ) {
		case 'last_modified_desc':
			$gBitSmarty->assign( 'attribute', 'last_modified' );
			$_REQUEST['attribute'] = tra( 'Date of last modification' );
			break;
		case 'top_authors':
			$gBitSmarty->assign( 'attribute', 'ag_hits' );
			$_REQUEST['attribute'] = tra( 'Hits to items by this Author' );
			break;
		default:
			$gBitSmarty->assign( 'attribute', 'hits' );
			$_REQUEST['attribute'] = tra( 'Hits' );
			break;
	}
} else {
	$gBitSmarty->assign( 'attribute', 'hits' );
	$_REQUEST['attribute'] = tra( 'Hits' );
}

$_REQUEST['title']             = tra( 'Article Rankings' );
$_REQUEST['content_type_guid'] = BITARTICLE_CONTENT_TYPE_GUID;
$_REQUEST['max_records']       = !empty( $_REQUEST['max_records'] ) ? $_REQUEST['max_records'] : 10;

if( empty( $gContent ) ) {
	$gContent = new LibertyContent();
}
$rankList = $gContent->getContentRanking( $_REQUEST );
$gBitSmarty->assign( 'rankList', $rankList );

$gBitSystem->display( 'bitpackage:liberty/rankings.tpl', tra( "Article Rankings" ) );
?>
