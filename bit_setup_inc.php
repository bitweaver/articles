<?php
global $gBitSystem, $gBitUser, $gBitSmarty;

$registerHash = array(
	'package_name' => 'articles',
	'package_path' => dirname( __FILE__ ).'/',
);
$gBitSystem->registerPackage( $registerHash );

define( 'BITARTICLE_CONTENT_TYPE_GUID', 'bitarticle' );

if( $gBitSystem->isPackageActive( 'articles' ) ) {
	$gBitSystem->registerAppMenu( ARTICLES_PKG_NAME, ucfirst( ARTICLES_PKG_DIR ), ARTICLES_PKG_URL.'index.php', 'bitpackage:articles/menu_articles.tpl', ARTICLES_PKG_NAME );

	$gBitSystem->registerNotifyEvent( array( "article_submitted" => tra( "A user submits an article" ) ) );
}
?>
