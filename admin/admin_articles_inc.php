<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/admin/admin_articles_inc.php,v 1.13 2006/03/23 16:40:39 squareing Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$formCmsSettings = array(
	'cms_rankings' => array(
		'label' => 'Rankings',
		'note' => 'Activates statistics for article ranking features.',
	),
	'cms_spellcheck' => array(
		'label' => 'Spellchecker',
		'note' => 'Allows users to check their spelling before submitting.',
	),
	'display_article_filter_bar' => array(
		'label' => 'Articles Filter',
		'note' => 'Allows admins to quickly filter articles based on status, topic and type.',
	),
);
$gBitSmarty->assign( 'formCmsSettings',$formCmsSettings );

$articleDateThreshold = array(
	'' => tra( 'never' ),
	'always' => tra( 'always' ),
	'year' => tra( 'up to a year' ),
	'month' => tra( 'up to a month' ),
	'week' => tra( 'up to a week' ),
	'day' => tra( 'up to a day' ),
	'hour' => tra( 'up to an hour' ),
);
$gBitSmarty->assign( 'articleDateThreshold', $articleDateThreshold );

$formArticleListing = array(
	"art_list_title" => array(
		'label' => 'Title',
		'note' => 'List the title of the article.',
	),
	"art_list_type" => array(
		'label' => 'Type',
		'note' => 'Display what type of article it is.',
	),
	"art_list_topic" => array(
		'label' => 'Topic',
		'note' => 'Display the article topic.',
	),
	"art_list_date" => array(
		'label' => 'Creation Date',
		'note' => 'Display when the article was submitted first.',
	),
	"art_list_expire" => array(
		'label' => 'Expiration Date',
		'note' => 'Display when the article will expire.',
	),
	"art_list_author" => array(
		'label' => 'Author',
		'note' => 'Display the name of the author of an article.',
	),
	"art_list_reads" => array(
		'label' => 'Hits',
		'note' => 'Display the number of times a given article has been accessed.',
	),
	"art_list_size" => array(
		'label' => 'Size',
		'note' => 'Display the size of any given article.',
	),
	"art_list_img" => array(
		'label' => 'Image',
		'note' => 'Display the image that is associated with a given article.',
	),
	"art_list_status" => array(
		'label' => 'Status',
		'note' => 'This will indicate whether a given article has been submitted or has been approved.',
	),
);
$gBitSmarty->assign( 'formArticleListing', $formArticleListing );

$processForm = set_tab();

if( $processForm ) {
	$featureToggles = array_merge( $formArticleListing,$formCmsSettings );
	foreach( $featureToggles as $item => $data ) {
		simple_set_toggle( $item, ARTICLES_PKG_NAME );
	}
	simple_set_value( "article_date_threshold", ARTICLES_PKG_NAME );
	simple_set_int( "max_articles", ARTICLES_PKG_NAME );
	simple_set_int( "article_description_length", ARTICLES_PKG_NAME );
	simple_set_int( "article_topic_thumbnail_size", ARTICLES_PKG_NAME );
}
?>
