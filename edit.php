<?php
// $Header: /cvsroot/bitweaver/_bit_articles/edit.php,v 1.1 2005/06/30 01:10:45 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );
require_once( ARTICLES_PKG_PATH . 'BitArticle.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'articles' );

// Now check permissions to access this page
//$gBitSystem->verifyPermission( 'bit_p_edit_article' );


include_once('lookup_article_inc.php');

if ($gBitUser->hasPermission('bit_p_admin_articles') || $gBitUser->hasPermission('bit_p_edit_article') ) {
	$viewerCanEdit = TRUE;
} elseif (!empty($gContent->mInfo['user_id']) && $gContent->mInfo['user_id'] == $gBitUser->mUserId) {
	$viewerCanEdit = TRUE;
} elseif (!$gContent->mArticleId && $gBitUser->hasPermission('bit_p_submit_article')) {
	$viewerCanEdit = TRUE;
}


// Now check permissions to access this page
if ( !$viewerCanEdit ) { //!$gBitSystem->verifyPermission( 'bit_p_edit_article' ) && ( $article_data["author_name"] != $gBitUser->mUserId || $article_data["creator_edit"] != 'y' ) ) {
    $smarty->assign( 'msg', tra( "Permission denied you cannot edit this article" ) );
    $gBitSystem->display( "error.tpl" );
    die;
}

/*if ($gBitSystem->isPackageActive( 'categories' )) {
    include_once( CATEGORIES_PKG_PATH . 'categorize_inc.php' );
    include_once( CATEGORIES_PKG_PATH . 'categorize_list_inc.php' );
}*/

if ($gBitSystem->isPackageActive( 'quicktags' )) {
    include_once( QUICKTAGS_PKG_PATH . 'quicktags_inc.php' );
}


// If we are in preview mode then preview it!
if (isset($_REQUEST["preview"])) {
	$article = $gContent->preparePreview($_POST);
    $smarty->assign('preview', 'y');
	$smarty->assign_by_ref('article', $article);
} else {
	$smarty->assign_by_ref('article', $gContent->mInfo);
}

if ( !$gBitUser->hasPermission( 'bit_p_use_HTML' )) {
	$_REQUEST["allowhtml"] = 'off';
}

if ( isset( $_REQUEST["template_id"] ) && $_REQUEST["template_id"] > 0 ) {
    $template_data = $tikilib->get_template( $_REQUEST["template_id"] );

    $_REQUEST["preview"] = 1;
    $_REQUEST["body"] = $template_data["content"].$_REQUEST["data"];
}

$publish_date = date( "U" );
$cur_time = getdate();
$expire_date = mktime ( $cur_time["hours"], $cur_time["minutes"], 0, $cur_time["mon"], $cur_time["mday"] + 365, $cur_time["year"] );

$smarty->assign('author_name', $gBitUser->getDisplayName());

if ( isset( $_REQUEST["allowhtml"] ) ) {
    if ( $_REQUEST["allowhtml"] == "on" ) {
        $smarty->assign( 'allowhtml', 'y' );
    }
}

if ( isset( $_REQUEST["save"] ) ) {
    

    if ( isset( $_REQUEST["use_image"] ) && $_REQUEST["use_image"] == 'on' ) {
        $use_image = 'y';
    } else {
        $use_image = 'n';
    }

    if ( isset( $_REQUEST["isfloat"] ) && $_REQUEST["isfloat"] == 'on' ) {
        $isfloat = 'y';
    } else {
        $isfloat = 'n';
    }

    if ( !isset( $_REQUEST["rating"] ) )
        $_REQUEST['rating'] = 0;
    if ( !isset( $_REQUEST['topic_id'] ) || $_REQUEST['topic_id'] == '' ) $_REQUEST['topic_id'] = 0;
    
	
	if ($gContent->store($_REQUEST)) {
		header ( "location: " . ARTICLES_PKG_URL. "index.php" );
	}
   
	/*$cat_type = 'article';
    $cat_objid = $artid;
    $cat_desc = substr( $_REQUEST["heading"], 0, 200 );
    $cat_name = $_REQUEST["title"];
    $cat_href = ARTICLES_PKG_URL . "read_article.php?article_id=" . $cat_objid;*/

    
}

// Get a topic list
$topics = BitArticleTopic::listTopics();
$smarty->assign_by_ref( 'topics', $topics );
// get list of valid types
$types = BitArticleType::listTypes();
$smarty->assign_by_ref( 'types', $types );

/*if ( $feature_cms_templates == 'y' && $bit_p_use_content_templates == 'y' ) {
    $templates = $tikilib->list_templates( 'cms', 0, -1, 'name_asc', '' );
}*/

// WYSIWYG and Quicktag variable
$smarty->assign( 'textarea_id', 'editarticle' );

$smarty->assign_by_ref( 'templates', $templates["data"] );


// Display the Index Template
$smarty->assign( 'show_page_bar', 'n' );
$gBitSystem->display( 'bitpackage:articles/edit_article.tpl', tra("articles") );

?>
