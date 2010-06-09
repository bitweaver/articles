<?php 
/**
 * @version $Header$
 * @package articles
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( '../kernel/setup_inc.php' );

include_once( ARTICLES_PKG_PATH . 'art_lib.php' );

if ( !isset( $_REQUEST["article_id"] ) ) {
    $gBitSmarty->assign( 'msg', tra( "No article indicated" ) );

    $gBitSmarty->display( "error.tpl" );
    die;
} 

if ( isset( $_REQUEST["article_id"] ) ) {
    $artlib->add_article_hit( $_REQUEST["article_id"] );

    $gBitSmarty->assign( 'article_id', $_REQUEST["article_id"] );
    $article_data = $artlib->get_article( $_REQUEST["article_id"] );

    if ( !$article_data ) {
        $gBitSmarty->assign( 'msg', tra( "Article not found" ) );

        $gBitSmarty->display( "error.tpl" );
        die;
    } 

	if ( ( $article_data["publish_date"] > date( "U" ) ) && ( !$gBitUser->isAdmin() ) ) {
        $gBitSmarty->assign( 'msg', tra( "Article is not published yet" ) );

        $gBitSmarty->display( "error.tpl" );
        die;
    } 

    $gBitSmarty->assign( 'title', $article_data["title"] );
    $gBitSmarty->assign( 'author_name', $article_data["author_name"] );
    $gBitSmarty->assign( 'topic_id', $article_data["topic_id"] );
    $gBitSmarty->assign( 'use_image', $article_data["use_image"] );
    $gBitSmarty->assign( 'image_name', $article_data["image_name"] );
    $gBitSmarty->assign( 'image_type', $article_data["image_type"] );
    $gBitSmarty->assign( 'image_size', $article_data["image_size"] );
    $gBitSmarty->assign( 'image_data', urlencode( $article_data["image_data"] ) );
    $gBitSmarty->assign( 'reads', $article_data["hits"] );
    $gBitSmarty->assign( 'size', $article_data["size"] );

    if ( strlen( $article_data["image_data"] ) > 0 ) {
        $gBitSmarty->assign( 'hasImage', 'y' );

        $hasImage = 'y';
    } 

    $gBitSmarty->assign( 'heading', $article_data["heading"] );
    $gBitSmarty->assign( 'body', $article_data["body"] );
    $gBitSmarty->assign( 'publish_date', $article_data["publish_date"] );
    $gBitSmarty->assign( 'edit_data', 'y' );

    $body = $article_data["body"];
    $heading = $article_data["heading"];
    $gBitSmarty->assign( 'parsed_body', $tikilib->parse_data( $body ) );
    $gBitSmarty->assign( 'parsed_heading', $tikilib->parse_data( $heading ) );
} 



$gBitSmarty->display( "bitpackage:articles/print_article.tpl" );

?>
