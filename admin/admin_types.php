<?php
// $Header: /cvsroot/bitweaver/_bit_articles/admin/admin_types.php,v 1.11 2010/02/08 21:27:21 wjames5 Exp $

require_once( '../../kernel/setup_inc.php' );
require_once( ARTICLES_PKG_PATH.'BitArticle.php' );
require_once( ARTICLES_PKG_PATH.'BitArticleType.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'p_articles_admin' );

$artTypes = array(
	'use_ratings' => array(
		'name' => tra( 'Rate' ),
		'desc' => tra( 'Allow ratings by the author' ),
	),
	'show_pre_publ' => array(
		'name' => tra( 'Show before publish date' ),
		'desc' => tra( 'non-admins can view before the publish date' ),
	),
	'show_post_expire' => array(
		'name' => tra( 'Show after expire date' ),
		'desc' => tra( 'non-admins can view after the expire date' ),
	),
	'heading_only' => array(
		'name' => tra( 'Heading only' ),
		'desc' => tra( 'No article body, heading only' ),
	),
	'allow_comments' => array(
		'name' => tra( 'Comments' ),
		'desc' => tra( 'Allow comments for this type' ),
	),
	'show_image' => array(
		'name' => tra( 'Show image' ),
		'desc' => tra( 'Show topic or image' ),
	),
	'show_avatar' => array(
		'name' => tra( 'Show avatar' ),
		'desc' => tra( 'Show author\'s avatar' ),
	),
	'show_author' => array(
		'name' => tra( 'Show author' ),
		'desc' => tra( 'Show author\'s name' ),
	),
	'show_pubdate' => array(
		'name' => tra( 'Show publish date' ),
		'desc' => tra( 'Show publication date' ),
	),
	'show_expdate' => array(
		'name' => tra( 'Show expiration date' ),
		'desc' => tra( 'Show expiration date' ),
	),
	'show_reads' => array(
		'name' => tra( 'Show reads' ),
		'desc' => tra( 'Show the number of times an article has been read' ),
	),
	'show_size' => array(
		'name' => tra( 'Show size' ),
		'desc' => tra( 'Show the size of the article' ),
	),
	'creator_edit' => array(
		'name' => tra( 'Creator can edit' ),
		'desc' => tra( 'The person who submits an article of this type can edit it' ),
	),
);
$gBitSmarty->assign( 'artTypes', $artTypes );

$gContent = &new BitArticleType( !empty( $_REQUEST['article_type_id'] ) ? $_REQUEST['article_type_id'] : NULL );

if( isset( $_REQUEST["add_type"] ) ) {
    $gContent->storeType( $_REQUEST );
} elseif( isset( $_REQUEST["remove_type"] ) ) {
    $gContent->removeType( $_REQUEST['remove_type'] );
} elseif( isset( $_REQUEST["update_type"] ) ) {
	foreach( array_keys( $_REQUEST["type_array"] ) as $this_type ) {
		$storeHash['article_type_id'] = $this_type;
		foreach( array_keys( $artTypes ) as $option ) {
			$storeHash[$option] = !empty( $_REQUEST[$option][$this_type] ) ? 'y' : 'n';
		}
		$storeHash['type_name'] = $_REQUEST['type_name'][$this_type];
        $gContent->storeType( $storeHash );
    }
}

$types = BitArticleType::getTypeList();
$gBitSmarty->assign( 'types', $types );

// Display the template
$gBitSystem->display( 'bitpackage:articles/admin_types.tpl',  tra('Edit Article Types') , array( 'display_mode' => 'admin' ));
?>
