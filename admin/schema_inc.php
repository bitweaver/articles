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

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( ARTICLES_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( ARTICLES_PKG_NAME, array(
	'description' => "This package manages news articles to create a slashdot-like news site.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

// these sequences are automatically generated, but Firebird and MSSQL prefers they exist
// Starting the numbering off at 5 for types to allow room for the INSERTs later.
$sequences = array (
	'articles_topics_id_seq' => array( 'start' => 1 ),
	'article_types_id_seq' => array( 'start' => 5 ),
	'articles_article_id_seq' => array( 'start' => 1 ),
);
$gBitInstaller->registerSchemaSequences( ARTICLES_PKG_NAME, $sequences );


// $indices = array();
// $gBitInstaller->registerSchemaIndexes( ARTICLES_PKG_NAME, $indices );

$gBitInstaller->registerSchemaDefault( ARTICLES_PKG_NAME, array(
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
	array('p_articles_update', 'Can update articles', 'editors', ARTICLES_PKG_NAME),
	array('p_articles_remove', 'Can remove articles', 'editors', ARTICLES_PKG_NAME),
	array('p_articles_read', 'Can read articles', 'basic', ARTICLES_PKG_NAME),
	array('p_articles_read_history', 'Can read article history', 'registered', ARTICLES_PKG_NAME),
	array('p_articles_submit', 'Can submit articles', 'basic', ARTICLES_PKG_NAME),
	array('p_articles_update_submission', 'Can update submissions', 'editors', ARTICLES_PKG_NAME),
	array('p_articles_remove_submission', 'Can remove submissions', 'editors', ARTICLES_PKG_NAME),
	array('p_articles_approve_submission', 'Can approve submissions', 'editors', ARTICLES_PKG_NAME),
	array('p_articles_send', 'Can send articles to other sites', 'editors', ARTICLES_PKG_NAME),
	array('p_articles_sendme', 'Can send articles to this site', 'registered', ARTICLES_PKG_NAME),
	array('p_articles_auto_approve', 'Submited articles automatically approved', 'editors', ARTICLES_PKG_NAME),
	array('p_articles_admin', 'Can admin the articles package', 'editors', ARTICLES_PKG_NAME),
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( ARTICLES_PKG_NAME, array(
	array( ARTICLES_PKG_NAME, 'articles_attachments','y'),
	array( ARTICLES_PKG_NAME, 'articles_list_author','y'),
	array( ARTICLES_PKG_NAME, 'articles_list_date','y'),
	array( ARTICLES_PKG_NAME, 'articles_list_img','y'),
	array( ARTICLES_PKG_NAME, 'articles_list_reads','y'),
	array( ARTICLES_PKG_NAME, 'articles_list_size','y'),
	array( ARTICLES_PKG_NAME, 'articles_list_title','y'),
	array( ARTICLES_PKG_NAME, 'articles_list_topic','y'),
	array( ARTICLES_PKG_NAME, 'articles_list_type','y'),
	array( ARTICLES_PKG_NAME, 'articles_list_expire','y'),
	array( ARTICLES_PKG_NAME, 'articles_max_list','10'),
	array( ARTICLES_PKG_NAME, 'articles_rankings','y'),
	array( ARTICLES_PKG_NAME, 'articles_submissions', 'y'),
	array( ARTICLES_PKG_NAME, 'articles_description_length', '500'),
	array( ARTICLES_PKG_NAME, 'articles_date_threshold', 'week'),
) );

if( defined( 'RSS_PKG_NAME' )) {
	$gBitInstaller->registerPreferences( ARTICLES_PKG_NAME, array(
		array( RSS_PKG_NAME, ARTICLES_PKG_NAME.'_rss', 'y'),
	));
}

// ### Register content types
$gBitInstaller->registerContentObjects( ARTICLES_PKG_NAME, array( 
	'BitArticle'=>ARTICLES_PKG_CLASS_PATH.'BitArticle.php',
));

// Requirements
$gBitInstaller->registerRequirements( ARTICLES_PKG_NAME, array(
	'liberty' => array( 'min' => '2.1.4' ),
));
