<?php 
// $Header: /cvsroot/bitweaver/_bit_articles/admin/admin_articles_inc.php,v 1.2 2005/08/27 09:48:36 squareing Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$formCmsSettings = array(
	'feature_cms_rankings' => array(
		'label' => 'Rankings',
		'note' => 'Activates statistics for article ranking features.',
	),
	'cms_spellcheck' => array(
		'label' => 'Spellchecker',
		'note' => 'Allows users to check their spelling before submitting.',
	),
	/*
	'feature_article_comments' => array(
		'label' => 'Comments',
		'note' => 'Allows users to post comments in reponse to a given article.',
	),
	*/
	'feature_cms_templates' => array(
		'label' => 'Use Templates',
		'note' => 'Use customised templates for the creation of articles to standardise and simplify posts.',
	),
);
$smarty->assign( 'formCmsSettings',$formCmsSettings );

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
	/*
	"art_list_visible" => array(
		'label' => 'List Visible',
		'note' => 'Indicate whether an article is visible or not. (is this what this setting does???)',
	),
	*/
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
$smarty->assign('formArticleListing', $formArticleListing);

$processForm = set_tab();

if( $processForm ) {
	
	$featureToggles = array_merge( $formArticleListing,$formCmsSettings );
	foreach( $featureToggles as $item => $data ) {
		simple_set_toggle( $item );
	}
	simple_set_int( "max_articles" );
	simple_set_int( "article_description_length" );
	/* we have global options for comments in liberty now
	simple_set_int( "article_comments_per_page" );
	simple_set_value( "article_comments_default_ordering" );
	*/
}

?>
