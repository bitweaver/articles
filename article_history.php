<?php
/**
 * $Header$
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * $Id$
 * @package wiki
 * @subpackage functions
 */

/**
 * required setup
 */
require_once '../kernel/includes/setup_inc.php';
require_once( ARTICLES_PKG_CLASS_PATH.'BitArticle.php' );

$gBitSystem->verifyPackage( 'articles' );
$gBitSystem->verifyPermission( 'p_articles_read' );
$gBitSystem->verifyPermission( 'p_articles_read_history' );

if( !isset( $_REQUEST["article_id"] ) ) {
	$gBitSystem->fatalError( tra( "No article indicated" ));
}

include_once( ARTICLES_PKG_INCLUDE_PATH.'lookup_article_inc.php' );

//vd($gContent->mPageId);vd($gContent->mInfo);
if( !$gContent->isValid() || empty( $gContent->mInfo ) ) {
	$gBitSystem->fatalError( tra( "Unknown article" ));
}

// additionally we need to check if this article is a submission and see if user has perms to view it.
if( $gContent->getField( 'status_id' ) != ARTICLE_STATUS_APPROVED && !( $gContent->hasUserPermission( 'p_articles_update_submission' ) || $gBitUser->isAdmin() ) ) {
	$gBitSmarty->assign( 'msg', tra( "Permission denied you cannot view this article" ) );
	$gBitSystem->display( "error.tpl" , NULL, array( 'display_mode' => 'display' ));
	die;
}

$smartyContentRef = 'article';
include_once( LIBERTY_PKG_INCLUDE_PATH.'content_history_inc.php' );

$gBitSmarty->assign( 'page', $page = !empty( $_REQUEST['list_page'] ) ? $_REQUEST['list_page'] : 1 );
$offset = ( $page - 1 ) * $gBitSystem->getConfig( 'max_records' );
$history = $gContent->getHistory( NULL, NULL, $offset, $gBitSystem->getConfig( 'max_records' ) );
$gBitSmarty->assignByRef( 'data', $history['data'] );
$gBitSmarty->assignByRef( 'listInfo', $history['listInfo'] );

//vd($gContent->getHistoryCount());

// calculate page number
$numPages = ceil( $gContent->getHistoryCount() / $gBitSystem->getConfig('max_records', 20) );
$gBitSmarty->assign( 'numPages', $numPages );

// Display the template
$gBitSmarty->assignByRef( 'gContent', $gContent );
$gBitSystem->display( 'bitpackage:articles/article_history.tpl', NULL, array( 'display_mode' => 'display' ));
