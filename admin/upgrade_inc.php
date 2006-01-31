<?php

global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;
require_once( ARTICLES_PKG_PATH.'BitArticle.php' );

$upgrades = array(

	'BWR1' => array(
		'BWR2' => array(
// de-tikify tables
array( 'DATADICT' => array(
	array( 'RENAMETABLE' => array(
		'tiki_articles' => 'articles',
		'tiki_article_status' => 'article_status',
		'tiki_article_types' => 'article_types',
		'tiki_article_topics' => 'article_topics',
	)),
)),
array( 'PHP' => '
	global $gBitSystem;
	$current = $gBitSystem->mDb->GenID( "tiki_article_topics_topic_id_seq" );
	$gBitSystem->mDb->DropSequence( "tiki_article_topics_topic_id_seq");
	$gBitSystem->mDb->CreateSequence( "article_topics_t_id_seq", $current );
	$current = $gBitSystem->mDb->GenID( "tiki_article_types_article_type_id_seq" );
	$gBitSystem->mDb->DropSequence( "tiki_article_types_article_type_id_seq");
	$gBitSystem->mDb->CreateSequence( "article_types_a_t_id_seq", $current );
' ),
		)
	),

	'TIKIWIKI19' => array (
		'TIKIWIKI18' => array (

array( 'DATADICT' => array(
	array( 'DROPCOLUMN' => array(
		'tiki_submissions' => array( '`bibliographical_references`' ),
	)),
)),

/* Sliced and diced TW 1.9 upgrade scripts that did actual schema alterations

ALTER TABLE `tiki_articles` ADD `topline` VARCHAR( 255 ) AFTER `articleId` ;
ALTER TABLE `tiki_articles` ADD `subtitle` VARCHAR( 255 ) AFTER `title` ;
ALTER TABLE `tiki_articles` ADD `linkto` VARCHAR( 255 ) AFTER `subtitle` ;
ALTER TABLE `tiki_articles` ADD `image_caption` TEXT AFTER `image_name` ;
ALTER TABLE `tiki_submissions` ADD `topline` VARCHAR( 255 ) AFTER `subId` ;
ALTER TABLE `tiki_submissions` ADD `subtitle` VARCHAR( 255 ) AFTER `title` ;
ALTER TABLE `tiki_submissions` ADD `linkto` VARCHAR( 255 ) AFTER `subtitle` ;
ALTER TABLE `tiki_submissions` ADD `image_caption` TEXT AFTER `image_name` ;
ALTER TABLE `tiki_articles` ADD `lang` VARCHAR( 16 ) AFTER `linkto` ;
ALTER TABLE `tiki_submissions` ADD `lang` VARCHAR( 16 ) AFTER `linkto` ;
ALTER TABLE `tiki_article_types` ADD `show_topline` CHAR( 1 ) AFTER `show_size` ;
ALTER TABLE `tiki_article_types` ADD `show_subtitle` CHAR( 1 ) AFTER `show_topline` ;
ALTER TABLE `tiki_article_types` ADD `show_linkto` CHAR( 1 ) AFTER `show_subtitle` ;
ALTER TABLE `tiki_article_types` ADD `show_image_caption` CHAR( 1 ) AFTER `show_linkto` ;
ALTER TABLE `tiki_article_types` ADD `show_lang` CHAR( 1 ) AFTER `show_image_caption` ;

*/
		)
	),


	'BONNIE' => array(
		'BWR1' => array(

// STEP 0
array( 'QUERY' =>
	array( 'MYSQL' => array(
		"ALTER TABLE `".BIT_DB_PREFIX."tiki_articles` DROP PRIMARY KEY",
		"ALTER TABLE `".BIT_DB_PREFIX."tiki_article_types` DROP PRIMARY KEY",
		"ALTER TABLE `".BIT_DB_PREFIX."tiki_topics` DROP PRIMARY KEY",
	)),
),

// STEP 1
array( 'DATADICT' => array(
	// RENAME
	array( 'RENAMETABLE' => array(
		'tiki_topics' => 'tiki_article_topics',
	)),
	array( 'RENAMECOLUMN' => array(
		'tiki_articles' => array(
			'`articleId`' => '`article_id` I4',
			'`authorName`' => '`author_name` C(250)',
			'`publishDate`' => '`publish_date` I4',
			'`expireDate`' => '`expire_date` I4',
			'`topicId`' => '`topic_id` I4',
			'`rating`' => '`rating` F',
		),
		'tiki_article_types' => array(
			'`type`' => '`type_name` C(50)',
		),
		'tiki_article_topics' => array(
			'`topicId`' => '`topic_id` I4',
			'`name`' => '`topic_name` C(40)',
		),
	)),
	// ALTER
	array( 'ALTER' => array(
		'tiki_articles' => array(
			'content_id' => array( '`content_id`', 'I4' ),
			'description' => array( '`description`', 'X' ),
			'status_id' => array( '`status_id`', 'I4' ),
			'image_attachment_id' => array( '`image_attachment_id`', 'I4' ),
			'article_type_id' => array( '`article_type_id`', 'I4' ),
		),
		'tiki_article_types' => array(
			'article_type_id' => array( '`article_type_id`', 'I4' ),
		),
		'tiki_article_topics' => array(
			'has_topic_image' => array( '`has_topic_image`', 'VARCHAR(1)' ),
		),
	)),
	// CREATE
	array( 'CREATE' => array (
		'tiki_article_status' => "
			status_id	I4 PRIMARY,
			status_name C(64)
		",
	)),
)),

// STEP 3
array( 'PHP' => '
	global $gBitSystem;
	require_once( ARTICLES_PKG_PATH."BitArticle.php" );

	// BitArticle has 3 sequences, each needs creating prior to execution
	$max_articles = $gBitSystem->mDb->getOne( "SELECT MAX(`article_id`) FROM `'.BIT_DB_PREFIX.'tiki_articles`" );
	$gBitSystem->mDb->CreateSequence( "tiki_articles_article_id_seq", $max_articles + 1 );
	$max_topics = $gBitSystem->mDb->getOne( "SELECT MAX(`topic_id`) FROM `'.BIT_DB_PREFIX.'tiki_article_topics`" );
	$gBitSystem->mDb->CreateSequence( "tiki_article_topics_topic_id_seq", $max_topics + 1 );
	$max_types = $gBitSystem->mDb->getOne( "SELECT MAX(`article_type_id`) FROM `'.BIT_DB_PREFIX.'tiki_article_types`" );
	$gBitSystem->mDb->CreateSequence( "tiki_article_types_article_type_id_seq", $max_types + 1 );

	// tiki_articles
	$query = "
		SELECT
			ta.`article_id`,
			ta.`created` AS `created`,
			ta.`created` AS `last_modified`,
			ta.`heading`,
			ta.`body`,
			ta.`title`,
			ta.`reads` AS `hits`
		FROM `'.BIT_DB_PREFIX.'tiki_articles` ta";

	if( $rs = $gBitSystem->mDb->query( $query ) ) {
		while( !$rs->EOF ) {
			$artId = $rs->fields["article_id"];
			unset( $rs->fields["article_id"] );
			$conId = $gBitSystem->mDb->GenID( "tiki_content_id_seq" );
			if( !empty( $rs->fields["heading"] ) && !empty( $rs->fields["heading"] ) ) {
				$rs->fields["data"] = $rs->fields["heading"]."...split...\n".$rs->fields["body"];
			} else {
				$rs->fields["data"] = $rs->fields["heading"];
			}
			unset( $rs->fields["body"] );
			unset( $rs->fields["heading"] );
			$rs->fields["content_id"] = $conId;
			$rs->fields["content_type_guid"] = BITARTICLE_CONTENT_TYPE_GUID;
			$rs->fields["format_guid"] = PLUGIN_GUID_TIKIWIKI;
			$gBitSystem->mDb->associateInsert( "tiki_content", $rs->fields );
			$gBitSystem->mDb->query( "UPDATE `'.BIT_DB_PREFIX.'tiki_articles` SET `content_id`=? WHERE `article_id`=?", array( $conId, $artId ) );
			$rs->MoveNext();
		}
	}

	// article images and state
	// work out what resizer to use
	$resizeFunc = ( $gBitSystem->getPreference( "image_processor" ) == "imagick" ) ? "liberty_imagick_resize_image" : "liberty_gd_resize_image";
	// make sure we have a place to store the images
	$tempDir = TEMP_PKG_PATH.ARTICLES_PKG_NAME;
	$storageDir = STORAGE_PKG_PATH.ARTICLES_PKG_NAME;
	if( !is_dir( $tempDir ) || !is_dir( $storageDir ) ) {
		mkdir_p( $tempDir );
		mkdir_p( $storageDir );
	}

	$query = "
		SELECT
			ta.`state`,
			ta.`article_id`,
			ta.`image_name`,
			ta.`image_type`,
			ta.`image_data`
		FROM `'.BIT_DB_PREFIX.'tiki_articles` ta";

	if( $rs = $gBitSystem->mDb->query( $query ) ) {
		while( !$rs->EOF ) {
			// store article image
			if( !empty( $rs->fields["image_name"] ) ) {
				$tmpImagePath = $tempDir.$rs->fields["image_name"];
				if( $handle = fopen( $tmpImagePath, "a" ) ) {
					fwrite( $handle, $rs->fields["image_data"] );
					fclose( $handle );
				} else {
					$gBitInstaller->mErrors["upgrade"][ARTICLES_PKG_NAME]["article_image_create"][] = "Error while creating article image: ".$rs->fields["image_name"];
				}
				$storeHash["source_file"] = $tmpImagePath;
				$storeHash["dest_path"] = STORAGE_PKG_NAME."/".ARTICLES_PKG_NAME."/";
				$storeHash["dest_base_name"] = "article_".$rs->fields["article_id"];
				$storeHash["max_width"] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
				$storeHash["max_height"] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
				$storeHash["type"] = $rs->fields["image_type"];

				if( $resizeFunc( $storeHash ) ) {
					$gBitInstaller->mErrors["upgrade"][ARTICLES_PKG_NAME]["article_image_resize"][] = "Error while resizing article image: ".$rs->fields["image_name"];
				}

				@unlink( $tmpImagePath );
				unset( $storeHash );
			}

			if( $rs->fields["state"] == "p" ) {
				$gBitSystem->mDb->query( "UPDATE `'.BIT_DB_PREFIX.'tiki_articles` SET `status_id`=? WHERE `article_id`=?", array( 400, $rs->fields["article_id"] ) );
			} elseif( $rs->fields["state"] == "s" ) {
				$gBitSystem->mDb->query( "UPDATE `'.BIT_DB_PREFIX.'tiki_articles` SET `status_id`=? WHERE `article_id`=?", array( 300, $rs->fields["article_id"] ) );
			}

			$rs->MoveNext();
		}
	}

	// tiki_article_topics
	$query = "
		SELECT
			ta.`topic_id`,
			ta.`image_name`,
			ta.`image_type`,
			ta.`image_data`
		FROM `'.BIT_DB_PREFIX.'tiki_article_topics` ta";

	if( $rs = $gBitSystem->mDb->query( $query ) ) {
		while( !$rs->EOF ) {
			// store topic image
			if( !empty( $rs->fields["image_name"] ) ) {
				$tmpImagePath = $tempDir.$rs->fields["image_name"];
				if( $handle = fopen( $tmpImagePath, "a" ) ) {
					fwrite( $handle, $rs->fields["image_data"] );
					fclose( $handle );
				} else {
					$gBitInstaller->mErrors["upgrade"][ARTICLES_PKG_NAME]["topic_image_create"][] = "Error while creating topic image: ".$rs->fields["image_name"];
				}

				$storeHash["source_file"] = $tmpImagePath;
				$storeHash["dest_path"] = STORAGE_PKG_NAME."/".ARTICLES_PKG_NAME."/";
				$storeHash["dest_base_name"] = "topic_".$rs->fields["topic_id"];
				$storeHash["max_width"] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
				$storeHash["max_height"] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
				$storeHash["type"] = $rs->fields["image_type"];

				if( $resizeFunc( $storeHash ) ) {
					$gBitInstaller->mErrors["upgrade"][ARTICLES_PKG_NAME]["topic_image_resize"][] = "Error while resizing topic image: ".$rs->fields["image_name"];
				}

				$gBitSystem->mDb->query( "UPDATE `'.BIT_DB_PREFIX.'tiki_article_topics` SET `has_topic_image`=? WHERE `topic_id`=?", array( "y", $rs->fields["topic_id"] ) );
				@unlink( $tmpImagePath );
				unset( $storeHash );
			} else {
				$gBitSystem->mDb->query( "UPDATE `'.BIT_DB_PREFIX.'tiki_article_topics` SET `has_topic_image`=? WHERE `topic_id`=?", array( "n", $rs->fields["topic_id"] ) );
			}
			$rs->MoveNext();
		}
	}

	// tiki_article_types
	// might be cumbersome, but i dont kow how else to do this
	$query = "SELECT * FROM `'.BIT_DB_PREFIX.'tiki_article_types`";

	if( $rs = $gBitSystem->mDb->query( $query ) ) {
		$typeId = 1;
		while( !$rs->EOF ) {
			$gBitSystem->mDb->query( "UPDATE `'.BIT_DB_PREFIX.'tiki_article_types` SET `article_type_id`=? WHERE `type_name`=?", array( $typeId, $rs->fields["type_name"] ) );
			$typeId++;
			$rs->MoveNext();
		}
	}
'),

// STEP 4
array( 'QUERY' =>
	array( 'SQL92' => array(
		"UPDATE `".BIT_DB_PREFIX."tiki_articles` SET `article_type_id`=( SELECT types.`article_type_id` FROM `".BIT_DB_PREFIX."tiki_article_types` types WHERE types.`type_name`=`".BIT_DB_PREFIX."tiki_articles`.`type` )",

		// update comments for articles
		"UPDATE `".BIT_DB_PREFIX."tiki_comments` SET `objectType`='".BITARTICLE_CONTENT_TYPE_GUID."' WHERE `objectType`='articles'",

		// insert default values for status table
		"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status`( `status_id`, `status_name` ) VALUES(   0, 'Denied' )",
		"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status`( `status_id`, `status_name` ) VALUES( 100, 'Draft' )",
		"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status`( `status_id`, `status_name` ) VALUES( 200, 'Pending Approval' )",
		"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status`( `status_id`, `status_name` ) VALUES( 300, 'Approved' )",
		"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status`( `status_id`, `status_name` ) VALUES( 400, 'Retired' )",

		// some default preferences
		"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences`( `name`, `value`, `package` ) VALUES( 'article_description_length', '500', '".ARTICLES_PKG_NAME."' )",
		"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences`( `name`, `value`, `package` ) VALUES( 'max_articles', '10', '".ARTICLES_PKG_NAME."' )",

		// add in permissions not in TW 1.8 - may get failures on some duplicates
		"INSERT INTO `".BIT_DB_PREFIX."users_permissions`( `perm_name`,`perm_desc`, `level`, `package` ) VALUES( 'bit_p_admin_articles', 'Can admin the articles package', 'editors', 'articles' )",
	)),
),

// STEP 5
array( 'PHP' => '' ),

// STEP 6
array( 'DATADICT' => array(
	array( 'DROPCOLUMN' => array(
		'tiki_articles' => array( '`title`', '`state`', '`topicName`', '`size`', '`useImage`', '`image_name`', '`image_size`', '`image_type`', '`image_x`', '`image_y`', '`image_data`', '`created`', '`heading`', '`body`', '`hash`', '`author`', '`reads`', '`votes`', '`points`', '`type`', '`isfloat`' ),
		'tiki_article_topics' => array( '`image_name`', '`image_type`', '`image_size`', '`image_data`' ),
	)),
)),

/*
// STEP 7
array( 'DATADICT' => array(
	array( 'CREATEINDEX' => array(
		'tiki_articles_articles_idx' => array( 'tiki_articles', '`article_id`', array() ),
	)),
)),
*/

		),
	),
);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( ARTICLES_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}
?>
