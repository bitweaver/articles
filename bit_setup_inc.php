<?php
$registerHash = array(
	'package_name' => 'articles',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'articles' ) ) {
	define( 'BITARTICLE_CONTENT_TYPE_GUID', 'bitarticle' );

	define( 'ARTICLE_STATUS_DENIED', 0 );
	define( 'ARTICLE_STATUS_DRAFT', 100 );
	define( 'ARTICLE_STATUS_PENDING', 200 );
	define( 'ARTICLE_STATUS_APPROVED', 300 );
	define( 'ARTICLE_STATUS_RETIRED', 400 );

	$gBitSystem->registerAppMenu( ARTICLES_PKG_NAME, ucfirst( ARTICLES_PKG_DIR ), ARTICLES_PKG_URL.'index.php', 'bitpackage:articles/menu_articles.tpl', ARTICLES_PKG_NAME );

	$gBitSystem->registerNotifyEvent( array( "article_submitted" => tra( "A user submits an article" ) ) );
}
?>
