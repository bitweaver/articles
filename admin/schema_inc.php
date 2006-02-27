<?php
// removed image data (replaced with storage_id ref)
// removed hash, state
// removed isfloat (?)
// removed size, topic_name (replaced with topic_id)
// replaced type_name with article_type_id

$tables = array(
	'article_status' => "
		status_id	I4 PRIMARY,
		status_name C(64)
	",

	'article_types' => "
		article_type_id I4 PRIMARY,
		type_name C(50),
		use_ratings C(1),
		show_pre_publ C(1),
		show_post_expire C(1) DEFAULT 'y',
		heading_only C(1),
		allow_comments C(1) DEFAULT 'n',
		show_image C(1) DEFAULT 'y',
		show_avatar C(1),
		show_author C(1) DEFAULT 'y',
		show_pubdate C(1) DEFAULT 'y',
		show_expdate C(1),
		show_reads C(1) DEFAULT 'y',
		show_size C(1) DEFAULT 'y',
		creator_edit C(1),
		comment_can_rate_article C(1)
	",

	'article_topics' => "
		topic_id I4 PRIMARY,
		topic_name C(40),
		has_topic_image C(1),
		active_topic C(1),
		created I8
	",

	'articles' => "
		article_id I4 PRIMARY,
		content_id I4 NOTNULL,
		description X,
		author_name C(250),
		topic_id I4,
		image_attachment_id I4,
		publish_date I4,
		expire_date I4,
		article_type_id I4,
		topic_id I4,
		rating F,
		status_id I4
		CONSTRAINT ', CONSTRAINT `articles_content_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)
					, CONSTRAINT `articles_topic_ref` FOREIGN KEY (`topic_id`) REFERENCES `".BIT_DB_PREFIX."article_topics` (`topic_id`)
					, CONSTRAINT `articles_type_ref` FOREIGN KEY (`article_type_id`) REFERENCES `".BIT_DB_PREFIX."article_types` (`article_type_id`)
					, CONSTRAINT `articles_status` FOREIGN KEY (`status_id`) REFERENCES `".BIT_DB_PREFIX."article_status` (`status_id`)'
	",


);

global $gBitInstaller;

$gBitInstaller->makePackageHomeable( ARTICLES_PKG_NAME );

foreach( array_keys( $tables ) AS $tableName ) {
    $gBitInstaller->registerSchemaTable( ARTICLES_PKG_DIR, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( ARTICLES_PKG_DIR, array(
	'description' => "This package manages news articles to create a slashdot-like news site.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
	'version' => '0.1',
	'state' => 'experimental',
	'dependencies' => '',
) );

// these sequences are automatically generated, but Firebird and MSSQL prefers they exist
// Starting the numbering off at 5 for types to allow room for the INSERTs later.
$sequences = array (
	'article_topics_t_id_seq' => array( 'start' => 1 ),
	'article_types_a_t_id_seq' => array( 'start' => 5 ),
	'articles_article_id_seq' => array( 'start' => 1 ),
);
$gBitInstaller->registerSchemaSequences( ARTICLES_PKG_NAME, $sequences );


// $indices = array();
// $gBitInstaller->registerSchemaIndexes( ARTICLES_PKG_DIR, $indices );

$gBitInstaller->registerSchemaDefault( ARTICLES_PKG_DIR, array(
	"INSERT INTO `".BIT_DB_PREFIX."article_types` (`article_type_id`, `type_name`) VALUES (1, 'Article')",
	"INSERT INTO `".BIT_DB_PREFIX."article_types` (`article_type_id`, `type_name`, `use_ratings`) VALUES (2, 'Review','y')",
	"INSERT INTO `".BIT_DB_PREFIX."article_types` (`article_type_id`, `type_name`, `show_post_expire`) VALUES (3, 'Event','n')",
	"INSERT INTO `".BIT_DB_PREFIX."article_types` (`article_type_id`, `type_name`, `show_post_expire`,`heading_only`,`allow_comments`) VALUES (4, 'Classified','n','y','n')",
	"INSERT INTO `".BIT_DB_PREFIX."article_status` (`status_id`, `status_name`) VALUES (  0, 'Denied') ",
	"INSERT INTO `".BIT_DB_PREFIX."article_status` (`status_id`, `status_name`) VALUES (100, 'Draft') ",
	"INSERT INTO `".BIT_DB_PREFIX."article_status` (`status_id`, `status_name`) VALUES (200, 'Pending Approval') ",
	"INSERT INTO `".BIT_DB_PREFIX."article_status` (`status_id`, `status_name`) VALUES (300, 'Approved') ",
	"INSERT INTO `".BIT_DB_PREFIX."article_status` (`status_id`, `status_name`) VALUES (400, 'Retired') "
) );

// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( ARTICLES_PKG_NAME, array(
	array('bit_p_edit_article', 'Can edit articles', 'editors', ARTICLES_PKG_NAME),
	array('bit_p_remove_article', 'Can remove articles', 'editors', ARTICLES_PKG_NAME),
	array('bit_p_read_article', 'Can read articles', 'basic', ARTICLES_PKG_NAME),
	array('bit_p_submit_article', 'Can submit articles', 'basic', ARTICLES_PKG_NAME),
	array('bit_p_edit_submission', 'Can edit submissions', 'editors', ARTICLES_PKG_NAME),
	array('bit_p_remove_submission', 'Can remove submissions', 'editors', ARTICLES_PKG_NAME),
	array('bit_p_approve_submission', 'Can approve submissions', 'editors', ARTICLES_PKG_NAME),
	array('bit_p_send_articles', 'Can send articles to other sites', 'editors', ARTICLES_PKG_NAME),
	array('bit_p_sendme_articles', 'Can send articles to this site', 'registered', ARTICLES_PKG_NAME),
	array('bit_p_autoapprove_submission', 'Submited articles automatically approved', 'editors', ARTICLES_PKG_NAME),
	array('bit_p_admin_articles', 'Can admin the articles package', 'editors', ARTICLES_PKG_NAME),
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( ARTICLES_PKG_NAME, array(
	array( ARTICLES_PKG_NAME, 'art_list_author','y'),
	array( ARTICLES_PKG_NAME, 'art_list_date','y'),
	array( ARTICLES_PKG_NAME, 'art_list_img','y'),
	array( ARTICLES_PKG_NAME, 'art_list_reads','y'),
	array( ARTICLES_PKG_NAME, 'art_list_size','y'),
	array( ARTICLES_PKG_NAME, 'art_list_title','y'),
	array( ARTICLES_PKG_NAME, 'art_list_topic','y'),
	array( ARTICLES_PKG_NAME, 'art_list_type','y'),
	array( ARTICLES_PKG_NAME, 'art_list_expire','y'),
	array( ARTICLES_PKG_NAME, 'max_articles','10'),
	array( ARTICLES_PKG_NAME, 'cms_rankings','y'),
	array( ARTICLES_PKG_NAME, 'article_submissions', 'y'),
	array( ARTICLES_PKG_NAME, 'article_description_length', '500'),
	array( ARTICLES_PKG_NAME, 'article_date_threshold', 'week'),
) );

if( defined( 'RSS_PKG_NAME' ) ) {
	$gBitInstaller->registerPreferences( ARTICLES_PKG_NAME, array(
		array( RSS_PKG_NAME, 'rss_'.ARTICLES_PKG_NAME, 'y'),
	) );
}

?>
