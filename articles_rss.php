<?php
/**
 * @version $Header$
 * @package articles
 * @subpackage functions
 */

/**
 * Initialization
 */
// ensure that we use absolute URLs everywhere
$_REQUEST['uri_mode'] = TRUE;
require_once( "../kernel/setup_inc.php" );

$gBitSystem->verifyPackage( 'articles' );
$gBitSystem->verifyPackage( 'rss' );
$gBitSystem->verifyFeature( 'articles_rss' );

require_once( ARTICLES_PKG_PATH."BitArticle.php" );
require_once( RSS_PKG_PATH."rss_inc.php" );

$rss->title = $gBitSystem->getConfig( 'articles_rss_title', $gBitSystem->getConfig( 'site_title' ).' - '.tra( 'Articles' ) );
$rss->description = $gBitSystem->getConfig( 'articles_rss_description', $gBitSystem->getConfig( 'site_title' ).' - '.tra( 'RSS Feed' ) );

// check permission to view articles
if( !$gBitUser->hasPermission( 'p_articles_read' ) ) {
	require_once( RSS_PKG_PATH."rss_error.php" );
} else {
	// check if we want to use the cache file
	$cacheFile = TEMP_PKG_PATH.RSS_PKG_NAME.'/'.ARTICLES_PKG_NAME.'/'.$cacheFileTail;
	$rss->useCached( $rss_version_name, $cacheFile, $gBitSystem->getConfig( 'rssfeed_cache_time' ));

	$articles = new BitArticle();
	$listHash = array(
		'status_id' => ARTICLE_STATUS_APPROVED,
		'sort_mode' => 'publish_date_desc',
		'max_records' => $gBitSystem->getConfig( 'articles_rss_max_records', 10 ),
	);
	$feeds = $articles->getList( $listHash );

	// set the rss link
	$rss->link = ARTICLES_PKG_URI;

	// get all the data ready for the feed creator
	foreach( $feeds as $feed ) {
		$item = new FeedItem();
		$item->title = $feed['title'];
		$item->link = $articles->getContentUrl( $feed['article_id'] );

		// show the full article in the feed
		$parseHash['content_id'] = $feed['content_id'];
		$parseHash['format_guid'] = $feed['format_guid'];
		$parseHash['data'] = preg_replace( LIBERTY_SPLIT_REGEX, "", $feed['data'] );
		$item->description = $articles->parseData( $parseHash );

		$item->date = ( int )$feed['publish_date'];
		$item->source = BIT_ROOT_URI;
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
