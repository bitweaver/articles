<?php
global $gBitSystem, $gBitUser, $smarty;
$gBitSystem->registerPackage( 'articles', dirname( __FILE__ ).'/' );

if( $gBitSystem->isPackageActive( 'articles' ) ) {
	$gBitSystem->registerAppMenu( 'articles', 'Articles', ARTICLES_PKG_URL.'index.php', 'bitpackage:articles/menu_articles.tpl', 'articles' );

	$gBitSystem->registerNotifyEvent( array( "article_submitted" => tra( "A user submits an article" ) ) );
}
?>
