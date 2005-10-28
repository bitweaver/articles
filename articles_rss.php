<?php
require_once( "../bit_setup_inc.php" );
require_once( RSS_PKG_PATH."rss_inc.php" );
require_once( ARTICLES_PKG_PATH."BitArticle.php" );

$gBitSystem->verifyPackage( 'articles' );
$gBitSystem->verifyPackage( 'rss' );

$rss->title = $gBitSystem->getPreference( 'title_rss_articles', $gBitSystem->mPrefs['siteTitle'] );
$rss->description = $gBitSystem->getPreference( 'desc_rss_articles', $gBitSystem->mPrefs['siteTitle'].' - '.tra( 'RSS Feed' ) );

// check permission to view articles
if( !$gBitUser->hasPermission( 'bit_p_read_article' ) ) {
	require_once( RSS_PKG_PATH."rss_error.php" );
} else {
	$articles = new BitArticle();
	$listHash = array(
		'sort_mode' => 'publish_date_desc',
		'max_records' => $gBitSystem->getPreference( 'max_rss_articles', 10 ),
	);
	$feeds = $articles->getList( $listHash );
	$feeds = $feeds['data'];

	// get all the data ready for the feed creator
	foreach( $feeds as $feed ) {
		$item = new FeedItem();
		$item->title = $feed['title'];
		$item->link = 'http://'.$_SERVER['HTTP_HOST'].BIT_ROOT_URL.$articles->getDisplayUrl( $feed['title'] );
		$item->description = $feed['data'];

		$item->date = (int) $feed['publish_date'];
		$item->source = 'http://'.$_SERVER['HTTP_HOST'].BIT_ROOT_URL;
		$item->author = $feed['author_name'];

		$item->descriptionTruncSize = $gBitSystem->getPreference( 'rssfeed_truncate', 500 );
		$item->descriptionHtmlSyndicated = FALSE;

		// pass the item on to the rss feed creator
		$rss->addItem( $item );
	}

	// finally we are ready to serve the data
	$cacheFile = TEMP_PKG_PATH.'rss/articles_'.$version.'.xml';
	$rss->useCached( $cacheFile ); // use cached version if age < 1 hour
	echo $rss->saveFeed( $rss_version_name, $cacheFile );
}
?>
