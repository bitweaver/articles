<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/articles_rss.php,v 1.12 2006/04/05 06:31:56 squareing Exp $
 * @package article
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( "../bit_setup_inc.php" );
require_once( RSS_PKG_PATH."rss_inc.php" );
require_once( ARTICLES_PKG_PATH."BitArticle.php" );

$gBitSystem->verifyPackage( 'articles' );
$gBitSystem->verifyPackage( 'rss' );

$rss->title = $gBitSystem->getConfig( 'title_rss_articles', $gBitSystem->getConfig( 'site_title' ).' - '.tra( 'Articles' ) );
$rss->description = $gBitSystem->getConfig( 'desc_rss_articles', $gBitSystem->getConfig( 'site_title' ).' - '.tra( 'RSS Feed' ) );

// check permission to view articles
if( !$gBitUser->hasPermission( 'bit_p_read_article' ) ) {
	require_once( RSS_PKG_PATH."rss_error.php" );
} else {
	// check if we want to use the cache file
	$cacheFile = TEMP_PKG_PATH.RSS_PKG_NAME.'/'.ARTICLES_PKG_NAME.'_'.$rss_version_name.'.xml';
	$rss->useCached( $rss_version_name, $cacheFile ); // use cached version if age < 1 hour

	$articles = new BitArticle();
	$listHash = array(
		'status_id' => ARTICLE_STATUS_APPROVED,
		'sort_mode' => 'publish_date_desc',
		'max_records' => $gBitSystem->getConfig( 'max_rss_articles', 10 ),
	);
	$feeds = $articles->getList( $listHash );

	// set the rss link
	$rss->link = 'http://'.$_SERVER['HTTP_HOST'].ARTICLES_PKG_URL;

	// get all the data ready for the feed creator
	foreach( $feeds['data'] as $feed ) {
		$item = new FeedItem();
		$item->title = $feed['title'];
		$item->link = BIT_BASE_URI.$articles->getDisplayUrl( $feed['article_id'] );
		$item->description = $feed['parsed_data'];

		$item->date = ( int )$feed['publish_date'];
		$item->source = 'http://'.$_SERVER['HTTP_HOST'].BIT_ROOT_URL;
		$item->author = $feed['author_name'];

		$item->descriptionTruncSize = $gBitSystem->getConfig( 'rssfeed_truncate', 5000 );
		$item->descriptionHtmlSyndicated = FALSE;

		// pass the item on to the rss feed creator
		$rss->addItem( $item );
	}

	// finally we are ready to serve the data
	echo $rss->saveFeed( $rss_version_name, $cacheFile );
}
?>
