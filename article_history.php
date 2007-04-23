<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_wiki/page_history.php,v 1.14 2006/05/12 20:33:25 sylvieg Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: page_history.php,v 1.14 2006/05/12 20:33:25 sylvieg Exp $
 * @package wiki
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );
require_once( ARTICLES_PKG_PATH.'BitArticle.php' );

$gBitSystem->verifyPackage( 'articles' );
$gBitSystem->verifyPermission( 'p_articles_read', tra( "Permission denied you cannot browse this article history" ) );
$gBitSystem->verifyPermission( 'p_articles_read_history', tra( "Permission denied you cannot browse this article history" ) );

if( !isset( $_REQUEST["article_id"] ) ) {
	$gBitSmarty->assign( 'msg', tra( "No article indicated" ) );
	$gBitSystem->display( "error.tpl" );
	die;
}

include_once( ARTICLES_PKG_PATH.'lookup_article_inc.php' );

//vd($gContent->mPageId);vd($gContent->mInfo);
if( !$gContent->isValid() || empty( $gContent->mInfo ) ) {
	$gBitSystem->fatalError( tra( "Unknown article" ));
}

// additionally we need to check if this article is a submission and see if user has perms to view it.
if( $gContent->getField( 'status_id' ) != ARTICLE_STATUS_APPROVED && !( $gBitUser->hasPermission( 'p_articles_edit_submission' ) || $gBitUser->hasPermission( 'p_articles_edit_submission' ) || $gBitUser->hasPermission( 'p_articles_edit_submission' ) || $gBitUser->isAdmin() ) ) {
	$gBitSmarty->assign( 'msg', tra( "Permission denied you cannot view this article" ) );
	$gBitSystem->display( "error.tpl" );
	die;
}

$gBitSmarty->assign('source', 0);
// If we have to include a preview please show it
$gBitSmarty->assign('preview', false);
$gBitSmarty->assign('compare', 'n');
$gBitSmarty->assign('diff2', 'n');
if (isset($_REQUEST["delete"]) && isset($_REQUEST["hist"])) {
	foreach (array_keys($_REQUEST["hist"])as $version) {
		$gContent->expungeVersion( $version );
	}
} elseif (isset($_REQUEST['source'])) {
	$gBitSmarty->assign('source', $_REQUEST['source']);
	if ($_REQUEST['source'] == 'current') {
		$gBitSmarty->assign('sourcev', nl2br(htmlentities($gContent->mInfo['data'])));
	} else {
		$version = $gContent->getHistory($_REQUEST["source"]);
		$gBitSmarty->assign('sourcev', nl2br(htmlentities($version[0]["data"])));
	}
} elseif (isset($_REQUEST["preview"])) {
	if( $version = $gContent->load($_REQUEST["preview"])) {
		$gContent->parseData();
		//$gBitSmarty->assign_by_ref('parsed', $gContent->parseData( $version[0] ) );
		$gBitSmarty->assign_by_ref('version', $_REQUEST["preview"]);
		$gBitSmarty->assign_by_ref('article', $gContent->mInfo );

	}
} elseif( isset( $_REQUEST["diff2"] ) ) {
	$from_version = $_REQUEST["diff2"];
	$from_page = $gContent->getHistory( $from_version );
	$from_lines = explode("\n",$from_page[0]["data"]);
	$to_version = $gContent->mInfo["version"];
	$to_lines = explode("\n",$gContent->mInfo["data"]);

	include_once( WIKI_PKG_PATH.'diff.php');
	$diffx = new WikiDiff($from_lines,$to_lines);
	$fmt = new WikiUnifiedDiffFormatter;
	$html = $fmt->format($diffx, $from_lines);
	$gBitSmarty->assign('diffdata', $html);
	$gBitSmarty->assign('diff2', 'y');
	$gBitSmarty->assign('version_from', $from_version);
	$gBitSmarty->assign('version_to', $to_version);

} elseif( isset( $_REQUEST["compare"] ) ) {
	$from_version = $_REQUEST["compare"];
	$from_page = $gContent->getHistory($from_version);
	$gBitSmarty->assign('compare', 'y');
	$gBitSmarty->assign_by_ref('diff_from', $gContent->parseData( $from_page[0] ) );
	$gBitSmarty->assign_by_ref('diff_to', $gContent->parseData() );
	$gBitSmarty->assign_by_ref('version_from', $from_version);
} elseif (isset($_REQUEST["rollback"])) {
	if( $version = $gContent->getHistory( $_REQUEST["preview"] ) ) {
		$gBitSmarty->assign_by_ref('parsed', $gContent->parseData( $version[0] ) );
		$gBitSmarty->assign_by_ref('version', $_REQUEST["preview"]);
	}
}

// pagination stuff
$gBitSmarty->assign( 'page', $page = !empty( $_REQUEST['page'] ) ? $_REQUEST['page'] : 1 );
$offset = ( $page - 1 ) * $gBitSystem->getConfig( 'max_records' );
$history = $gContent->getHistory( NULL, NULL, $offset, $gBitSystem->getConfig( 'max_records' ) );
$gBitSmarty->assign_by_ref( 'history', $history );

//vd($gContent->getHistoryCount());

// calculate page number
$numPages = ceil( $gContent->getHistoryCount() / $gBitSystem->getConfig('max_records', 20) );
$gBitSmarty->assign( 'numPages', $numPages );


// Display the template
$gBitSmarty->assign_by_ref( 'gContent', $gContent );
$gBitSystem->display( 'bitpackage:articles/article_history.tpl');
?>
