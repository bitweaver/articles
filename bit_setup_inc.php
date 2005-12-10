<?php
global $gBitSystem, $gBitUser, $gBitSmarty;
$gBitSystem->registerPackage( 'articles', dirname( __FILE__ ).'/' );

if( $gBitSystem->isPackageActive( 'articles' ) ) {
	$gBitSystem->registerAppMenu( ARTICLES_PKG_DIR, 'Articles', ARTICLES_PKG_URL.'index.php', 'bitpackage:articles/menu_articles.tpl', ARTICLES_PKG_NAME );

	$gBitSystem->registerNotifyEvent( array( "article_submitted" => tra( "A user submits an article" ) ) );
}
?>
