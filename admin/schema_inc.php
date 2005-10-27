<?php
// removed image data (replaced with storage_id ref)
// removed hash, state
// removed isfloat (?)
// removed size, topic_name (replaced with topic_id)
// replaced type_name with article_type_id

$tables = array(
	'tiki_articles' => "
		article_id I4 AUTO PRIMARY,
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
	",

	'tiki_article_status' => "
		status_id	I4 PRIMARY,
		status_name C(64)
	",

	'tiki_article_types' => "
		article_type_id I4 AUTO PRIMARY,
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

	// tiki_topics renamed to tiki_article_topics
	// name renamed to topic_name
	'tiki_article_topics' => "
		topic_id I4 AUTO PRIMARY,
		topic_name C(40),
		has_topic_image C(1),
		active C(1),
		created I8
	"
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

/* commented these out because these sequences are automatically generated
$sequences = array (
	'tiki_article_topics_topic_id_seq' => array( 'start' => 1 ),
	'tiki_article_types_article_type_id_seq' => array( 'start' => 1 ),
	'tiki_articles_article_id_seq' => array( 'start' => 1 ),
);
$gBitInstaller->registerSchemaSequences( ARTICLES_PKG_NAME, $sequences );
*/

// $indices = array();
// $gBitInstaller->registerSchemaIndexes( ARTICLES_PKG_DIR, $indices );

$gBitInstaller->registerSchemaDefault( ARTICLES_PKG_DIR, array(
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_types` (`type_name`) VALUES ('Article')",
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_types` (`type_name`, `use_ratings`) VALUES ('Review','y')",
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_types` (`type_name`, `show_post_expire`) VALUES ('Event','n')",
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_types` (`type_name`, `show_post_expire`,`heading_only`,`allow_comments`) VALUES ('Classified','n','y','n')",
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status` (`status_id`, `status_name`) VALUES (  0, 'Denied') ",
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status` (`status_id`, `status_name`) VALUES (100, 'Draft') ",
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status` (`status_id`, `status_name`) VALUES (200, 'Pending Approval') ",
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status` (`status_id`, `status_name`) VALUES (300, 'Approved') ",
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status` (`status_id`, `status_name`) VALUES (400, 'Retired') "
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
	array('bit_p_admin_received_articles', 'Can admin received articles', 'editors', ARTICLES_PKG_NAME),
	array('bit_p_autoapprove_submission', 'Submited articles automatically approved', 'editors', ARTICLES_PKG_NAME),
	array('bit_p_admin_articles', 'Can admin the articles package', 'editors', ARTICLES_PKG_NAME),
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( ARTICLES_PKG_NAME, array(
	array(ARTICLES_PKG_NAME, 'art_list_author','y'),
	array(ARTICLES_PKG_NAME, 'art_list_date','y'),
	array(ARTICLES_PKG_NAME, 'art_list_img','y'),
	array(ARTICLES_PKG_NAME, 'art_list_reads','y'),
	array(ARTICLES_PKG_NAME, 'art_list_size','y'),
	array(ARTICLES_PKG_NAME, 'art_list_title','y'),
	array(ARTICLES_PKG_NAME, 'art_list_topic','y'),
	array(ARTICLES_PKG_NAME, 'art_list_type','y'),
	array(ARTICLES_PKG_NAME, 'art_list_expire','y'),
	array(ARTICLES_PKG_NAME, 'max_articles','10'),
	array(ARTICLES_PKG_NAME, 'feature_cms_rankings','y'),
	array(ARTICLES_PKG_NAME, 'feature_article_submissions', 'y'),
	array(ARTICLES_PKG_NAME, 'article_description_length', '500')
) );
?>
