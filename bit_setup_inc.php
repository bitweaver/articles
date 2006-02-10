<?php
global $gBitSystem, $gBitUser, $gBitSmarty;
$gBitSystem->registerPackage( 'articles', dirname( __FILE__ ).'/' );

define( 'BITARTICLE_CONTENT_TYPE_GUID', 'bitarticle' );

if( $gBitSystem->isPackageActive( 'articles' ) ) {
	$gBitSystem->registerAppMenu( ARTICLES_PKG_NAME, ucfirst( ARTICLES_PKG_DIR ), ARTICLES_PKG_URL.'index.php', 'bitpackage:articles/menu_articles.tpl', ARTICLES_PKG_NAME );

	$gBitSystem->registerNotifyEvent( array( "article_submitted" => tra( "A user submits an article" ) ) );
}
?>
