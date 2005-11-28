<?php

global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;

$upgrades = array(
	'BONNIE' => array(
		'CLYDE' => array(

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
			'has_topic_image' => array( '`has_topic_image`', 'C(1)' ),
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
// TODO: deal with article type ids
array( 'PHP' => '
	global $gBitSystem;
	require_once( ARTICLES_PKG_PATH."BitArticle.php" );
	// work out what resizer to use
	$resizeFunc = ( $gBitSystem->getPreference( "image_processor" ) == "imagick" ) ? "liberty_imagick_resize_image" : "liberty_gd_resize_image";
	// make sure we have a place to store the images
	$tempDir = TEMP_PKG_PATH.ARTICLES_PKG_NAME;
	if( !is_dir( $tempDir ) ) {
		mkdir_p( $tempDir );
	}

//	currently no sequences are generated in schema_inc
//	$max_articles = $gBitSystem->mDb->getOne( "SELECT MAX(`article_id`) FROM `'.BIT_DB_PREFIX.'tiki_articles`" );
//	$gBitSystem->mDb->CreateSequence( "tiki_articles_article_id_seq", $max_articles + 1 );
//	$max_topics = $gBitSystem->mDb->getOne( "SELECT MAX(`topic_id`) FROM `'.BIT_DB_PREFIX.'tiki_article_topics`" );
//	$gBitSystem->mDb->CreateSequence( "tiki_articles_article_topics_id_seq", $max_topics + 1 );
//	$max_types = $gBitSystem->mDb->getOne( "SELECT MAX(`article_type_id`) FROM `'.BIT_DB_PREFIX.'tiki_article_types`" );
//	$gBitSystem->mDb->CreateSequence( "tiki_articles_article_types_id_seq", $max_types + 1 );

	// tiki_articles
	$query = "
		SELECT
			ta.`article_id`,
			ta.`created` AS `created`,
			ta.`created` AS `last_modified`,
			ta.`body` AS `data`,
			ta.`heading` AS `title`,
			ta.`reads` AS `hits`
		FROM `'.BIT_DB_PREFIX.'tiki_articles` ta";

	if( $rs = $gBitSystem->mDb->query( $query ) ) {
		while( !$rs->EOF ) {
			$artId = $rs->fields["article_id"];
			unset( $rs->fields["article_id"] );
			$conId = $gBitSystem->mDb->GenID( "tiki_content_id_seq" );
			$rs->fields["content_id"] = $conId;
			$rs->fields["content_type_guid"] = BITARTICLE_CONTENT_TYPE_GUID;
			$rs->fields["format_guid"] = PLUGIN_GUID_TIKIWIKI;
			$gBitSystem->mDb->associateInsert( "tiki_content", $rs->fields );
			$gBitSystem->mDb->query( "UPDATE `'.BIT_DB_PREFIX.'tiki_articles` SET `content_id`=? WHERE `article_id`=?", array( $conId, $artId ) );
			$rs->MoveNext();
		}
	}

	// article images
	$query = "
		SELECT
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
					$gBitInstaller->mErrors["upgrade"][ARTICLES_PKG_NAME]["article_image_create"] = "Error while creating article image";
				}
				$storeHash["source_file"] = $tmpImagePath;
				$storeHash["dest_path"] = STORAGE_PKG_NAME."/".ARTICLES_PKG_NAME."/";
				$storeHash["dest_base_name"] = "article_".$rs->fields["article_id"];
				$storeHash["max_width"] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
				$storeHash["max_height"] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
				$storeHash["type"] = $rs->fields["image_type"];

				if( $resizeFunc( $storeHash ) ) {
					$gBitInstaller->mErrors["upgrade"][ARTICLES_PKG_NAME]["article_image_resize"] = "Error while resizing article image";
				}

				@unlink( $tmpImagePath );
				unset( $storeHash );
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
					$gBitInstaller->mErrors["upgrade"][ARTICLES_PKG_NAME]["topic_image_create"] = "Error while creating topic image";
				}

				$storeHash["source_file"] = $tmpImagePath;
				$storeHash["dest_path"] = STORAGE_PKG_NAME."/".ARTICLES_PKG_NAME."/";
				$storeHash["dest_base_name"] = "topic_".$rs->fields["topic_id"];
				$storeHash["max_width"] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
				$storeHash["max_height"] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
				$storeHash["type"] = $rs->fields["image_type"];

				if( $resizeFunc( $storeHash ) ) {
					$gBitInstaller->mErrors["upgrade"][ARTICLES_PKG_NAME]["topic_image_resize"] = "Error while resizing topic image";
				}

				@unlink( $tmpImagePath );
				unset( $storeHash );
			}
			$rs->MoveNext();
		}
	}'
),

// STEP 4
array( 'QUERY' =>
	array( 'SQL92' => array(
	"UPDATE `".BIT_DB_PREFIX."tiki_articles` SET `article_type_id`= (SELECT `article_type_id` FROM `".BIT_DB_PREFIX."tiki_article_types` tat WHERE tat.`type_name`=`".BIT_DB_PREFIX."tiki_articles`.`type_name`)",

	// insert default values for status table
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status` (`status_id`, `status_name`) VALUES (  0, 'Denied') ",
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status` (`status_id`, `status_name`) VALUES (100, 'Draft') ",
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status` (`status_id`, `status_name`) VALUES (200, 'Pending Approval') ",
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status` (`status_id`, `status_name`) VALUES (300, 'Approved') ",
	"INSERT INTO `".BIT_DB_PREFIX."tiki_article_status` (`status_id`, `status_name`) VALUES (400, 'Retired') "

//	// add in permissions not in TW 1.8 - may get failures on some duplicates
//	"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_userfiles', 'Can upload personal files', 'registered', 'users')",
//	"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_user_group_perms', 'Can assign permissions to personal groups', 'editors', 'users')",
//	"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_user_group_members', 'Can assign users to personal groups', 'registered', 'users')",
//	"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_user_group_subgroups', 'Can include other groups in groups', 'editors', 'users')",
//	"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_create_bookmarks', 'Can create user bookmarksche user bookmarks', 'registered', 'users')",
//	"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_configure_modules', 'Can configure modules', 'registered', 'users')",
//	"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_cache_bookmarks', 'Can cache user bookmarks', 'admin', 'users')",
//	"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_usermenu', 'Can create items in personal menu', 'registered', 'users')",
//	"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_tasks', 'Can use tasks', 'registered', 'users')",
//	"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_assume_users', 'Can assume the identity of other users', 'admin', 'users')",
//	"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_admin_users', 'Can edit the information for other users', 'admin', 'users')",
//	"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_view_tabs_and_tools', 'Can view tab and tool links', 'basic', 'users')",
//
//	// update comments on user pages
//	"UPDATE `".BIT_DB_PREFIX."tiki_comments` SET `objectType`='".BITUSER_CONTENT_TYPE_GUID."' WHERE `objectType`='wiki page' AND `object` LIKE 'UserPage%'",
//	"UPDATE `".BIT_DB_PREFIX."tiki_comments` SET `parent_id`=(SELECT `content_id` FROM `".BIT_DB_PREFIX."tiki_content` WHERE `content_type_guid`='".BITUSER_CONTENT_TYPE_GUID."' AND `title`=`".BIT_DB_PREFIX."tiki_comments`.`object` ) WHERE `parent_id`=0 AND `objectType`='".BITUSER_CONTENT_TYPE_GUID."'",
//
//	// update comments on wiki pages
//	"UPDATE `".BIT_DB_PREFIX."tiki_comments` SET `objectType`='".BITPAGE_CONTENT_TYPE_GUID."' WHERE `objectType`='wiki page'",
//
//	// set parent ID = content ID of parent comment
//	// this will only work correctly for TW DB upgrades, and will corrupt the DB if run more then once
//	"create temporary table `".BIT_DB_PREFIX."tiki_comments_temp` as (select * from `".BIT_DB_PREFIX."tiki_comments`) ",
//	"UPDATE `".BIT_DB_PREFIX."tiki_comments` SET `parent_id`=(SELECT i_tcm.`content_id` FROM `".BIT_DB_PREFIX."tiki_content` as i_tcn, `".BIT_DB_PREFIX."tiki_comments_temp` as i_tcm WHERE  i_tcm.`content_id` = i_tcn.`content_id` and `".BIT_DB_PREFIX."tiki_comments`.`parent_id` = i_tcm.`comment_id` ) where  parent_id != 0 and  `objectType`='".BITPAGE_CONTENT_TYPE_GUID."' ",
//	// parent ID = 0 indicates a root comment in TW, but now needs to = content ID of wiki page it is the root comment for
//	"UPDATE `".BIT_DB_PREFIX."tiki_comments` SET `parent_id`=(SELECT `content_id` FROM `".BIT_DB_PREFIX."tiki_content` WHERE `content_type_guid`='".BITPAGE_CONTENT_TYPE_GUID."' AND `title`=`".BIT_DB_PREFIX."tiki_comments`.`object` ) WHERE `parent_id`=0 AND `objectType`='".BITPAGE_CONTENT_TYPE_GUID."'",
//
//	"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences` (`name`, `value`, `package`) VALUES( 'feature_wiki_books', 'y', 'wiki' )",
//	"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences` (`name`, `value`, `package`) VALUES( 'feature_history', 'y', 'wiki' )",
//	"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences` (`name`, `value`, `package`) VALUES( 'feature_listPages', 'y', 'wiki' )",
//
//	"UPDATE `".BIT_DB_PREFIX."tiki_categorized_objects` SET `object_type`='".BITPAGE_CONTENT_TYPE_GUID."', `object_id`=(SELECT tc.`content_id` FROM `".BIT_DB_PREFIX."tiki_content` tc WHERE tc.`title`=`".BIT_DB_PREFIX."tiki_categorized_objects`.`objId` AND `".BIT_DB_PREFIX."tiki_categorized_objects`.`object_type`='wiki page')",
//
//
//	// update user watches
//	"update `".BIT_DB_PREFIX."tiki_user_watches` as `tw` set `object` = (select `tp`.`page_id` from `tiki_pages` as `tp`, `tiki_content` as `tc` where `tp`.`content_id` = `tc`.`content_id` and   `tc`.`title` = `tw`.`title` )",


	),
)),

		),
	),
);

// to test this upgrader you need to uncomment the following
/**/
if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( ARTICLES_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}
/**/



			/*
			// STEP 4
			array( 'QUERY' =>
				array( 'SQL92' => array(
			//	"UPDATE `".BIT_DB_PREFIX."tiki_pages SET `modifier_user_id`=-1 WHERE `modifier_user_id` IS NULL",
				"UPDATE `".BIT_DB_PREFIX."tiki_history` SET `page_id`= (SELECT `page_id` FROM `".BIT_DB_PREFIX."tiki_pages` tp WHERE tp.`pageName`=`".BIT_DB_PREFIX."tiki_history`.`pageName`)",
				"UPDATE `".BIT_DB_PREFIX."tiki_history` SET `user_id`=(SELECT `user_id` FROM `".BIT_DB_PREFIX."users_users` WHERE `".BIT_DB_PREFIX."users_users`.`login`=`".BIT_DB_PREFIX."tiki_history`.`user`)",
				"UPDATE `".BIT_DB_PREFIX."tiki_history` SET `user_id`=".ROOT_USER_ID." WHERE `user_id` IS NULL",Save thiSave this file as s file as 
				"UPDATE `".BIT_DB_PREFIX."tiki_structures` SET `content_id`= (SELECT `content_id` FROM `".BIT_DB_PREFIX."tiki_pages` tp WHERE tp.`page_id`=`".BIT_DB_PREFIX."tiki_structures`.`page_id`)",
				"UPDATE `".BIT_DB_PREFIX."tiki_semaphores` SET `user_id`=(SELECT `user_id` FROM `".BIT_DB_PREFIX."users_users` WHERE `".BIT_DB_PREFIX."users_users`.`login`=`".BIT_DB_PREFIX."tiki_semaphores`.`user`)",
				"UPDATE `".BIT_DB_PREFIX."tiki_semaphores` SET `user_id`=(SELECT `user_id` FROM `".BIT_DB_PREFIX."users_users` WHERE `".BIT_DB_PREFIX."users_users`.`login`=`".BIT_DB_PREFIX."tiki_semaphores`.`user`)",
				"UPDATE `".BIT_DB_PREFIX."tiki_copyrights` SET `page_id`= (SELECT `page_id` FROM `".BIT_DB_PREFIX."tiki_pages` tp WHERE tp.`pageName`=`".BIT_DB_PREFIX."tiki_copyrights`.`page`)",
				"UPDATE `".BIT_DB_PREFIX."tiki_copyrights` SET `user_id`=(SELECT `user_id` FROM `".BIT_DB_PREFIX."users_users` WHERE `".BIT_DB_PREFIX."users_users`.`login`=`".BIT_DB_PREFIX."tiki_copyrights`.`userName`)",
				"UPDATE `".BIT_DB_PREFIX."tiki_copyrights` SET `user_id`=".ROOT_USER_ID." WHERE `user_id` IS NULL",
				"UPDATE `".BIT_DB_PREFIX."tiki_page_footnotes` SET `page_id`= (SELECT `page_id` FROM `".BIT_DB_PREFIX."tiki_pages` tp WHERE tp.`pageName`=`".BIT_DB_PREFIX."tiki_page_footnotes`.`pageName`)",
				"UPDATE `".BIT_DB_PREFIX."tiki_page_footnotes` SET `user_id`=(SELECT `user_id` FROM `".BIT_DB_PREFIX."users_users` WHERE `".BIT_DB_PREFIX."users_users`.`login`=`".BIT_DB_PREFIX."tiki_page_footnotes`.`user`)",
				"UPDATE `".BIT_DB_PREFIX."tiki_page_footnotes` SET `user_id`=".ROOT_USER_ID." WHERE `user_id` IS NULL",
				"UPDATE `".BIT_DB_PREFIX."tiki_actionlog` SET `page_id`= (SELECT `page_id` FROM `".BIT_DB_PREFIX."tiki_pages` tp WHERE tp.`pageName`=`".BIT_DB_PREFIX."tiki_actionlog`.`pageName`)",
				"UPDATE `".BIT_DB_PREFIX."tiki_actionlog` SET `user_id`=(SELECT `user_id` FROM `".BIT_DB_PREFIX."users_users` WHERE `".BIT_DB_PREFIX."users_users`.`login`=`".BIT_DB_PREFIX."tiki_actionlog`.`user`)",
				"UPDATE `".BIT_DB_PREFIX."tiki_actionlog` SET `user_id`=".ROOT_USER_ID." WHERE `user_id` IS NULL",
				"UPDATE `".BIT_DB_PREFIX."tiki_links` SET `from_content_id`= (SELECT `content_id` FROM `".BIT_DB_PREFIX."tiki_pages` tp WHERE tp.`pageName`=`".BIT_DB_PREFIX."tiki_links`.`fromPage`)",
				"UPDATE `".BIT_DB_PREFIX."tiki_links` SET `to_content_id`= (SELECT `content_id` FROM `".BIT_DB_PREFIX."tiki_pages` tp WHERE tp.`pageName`=`".BIT_DB_PREFIX."tiki_links`.`toPage`)",
				"UPDATE `".BIT_DB_PREFIX."users_permissions` SET perm_name='bit_p_edit_books', perm_desc='Can create and edit books' WHERE perm_name='bit_p_edit_structures'",

				"INSERT INTO `".BIT_DB_PREFIX."users_grouppermissions` (`group_id`, `perm_name`) VALUES (2,'bit_p_edit_books')",

			// add in permissions not in TW 1.8 - may get failures on some duplicates
					"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_userfiles', 'Can upload personal files', 'registered', 'users')",
					"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_user_group_perms', 'Can assign permissions to personal groups', 'editors', 'users')",
					"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_user_group_members', 'Can assign users to personal groups', 'registered', 'users')",
					"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_user_group_subgroups', 'Can include other groups in groups', 'editors', 'users')",
					"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_create_bookmarks', 'Can create user bookmarksche user bookmarks', 'registered', 'users')",
					"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_configure_modules', 'Can configure modules', 'registered', 'users')",
					"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_cache_bookmarks', 'Can cache user bookmarks', 'admin', 'users')",
					"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_usermenu', 'Can create items in personal menu', 'registered', 'users')",
					"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_tasks', 'Can use tasks', 'registered', 'users')",
					"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_assume_users', 'Can assume the identity of other users', 'admin', 'users')",
					"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_admin_users', 'Can edit the information for other users', 'admin', 'users')",
					"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`,`perm_desc`, `level`, `package`) VALUES ('bit_p_view_tabs_and_tools', 'Can view tab and tool links', 'basic', 'users')",
			//users don't have any buttons for page functions without this
				"INSERT INTO `".BIT_DB_PREFIX."users_grouppermissions` (`group_id`, `perm_name`) VALUES (-1,'bit_p_view_tabs_and_tools')",
				"INSERT INTO `".BIT_DB_PREFIX."users_grouppermissions` (`group_id`, `perm_name`) VALUES (1,'bit_p_view_tabs_and_tools')",


				"UPDATE `".BIT_DB_PREFIX."tiki_preferences` SET `name`='feature_wiki_generate_pdf' WHERE name='feature_wiki_pdf'",
				"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences` (`name`, `value`, `package`) VALUES( 'feature_page_title', 'y', 'wiki' )",
				"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences` (`name`, `value`, `package`) VALUES( 'package_wiki', 'y', 'wiki' )",

				// Update versions that are out of whack so tiki_pages.versions>tiki_history.version
				"UPDATE `".BIT_DB_PREFIX."tiki_pages` SET `version`=(SELECT th.`version`+1 FROM `".BIT_DB_PREFIX."tiki_history` th WHERE th.`page_id`=`".BIT_DB_PREFIX."tiki_pages`.`page_id` AND `".BIT_DB_PREFIX."tiki_pages`.`version`=th.`version`) WHERE `page_id` IN (SELECT `page_id` FROM `".BIT_DB_PREFIX."tiki_history` th WHERE th.`version`=`".BIT_DB_PREFIX."tiki_pages`.`version` AND th.`page_id`=`".BIT_DB_PREFIX."tiki_pages`.`page_id`)",

				// should go into users, but has to go here do to wiki needing user changes first
				"UPDATE `".BIT_DB_PREFIX."tiki_content` SET content_type_guid='bituser' WHERE title like 'UserPage%'",
				"UPDATE `".BIT_DB_PREFIX."users_users` SET `content_id`=(SELECT `content_id` FROM `".BIT_DB_PREFIX."tiki_content` WHERE `content_type_guid`='bituser' AND `user_id`=`".BIT_DB_PREFIX."users_users`.`user_id`)",

				// update comments on user pages
				"UPDATE `".BIT_DB_PREFIX."tiki_comments` SET `objectType`='".BITUSER_CONTENT_TYPE_GUID."' WHERE `objectType`='wiki page' AND `object` LIKE 'UserPage%'",
				"UPDATE `".BIT_DB_PREFIX."tiki_comments` SET `parent_id`=(SELECT `content_id` FROM `".BIT_DB_PREFIX."tiki_content` WHERE `content_type_guid`='".BITUSER_CONTENT_TYPE_GUID."' AND `title`=`".BIT_DB_PREFIX."tiki_comments`.`object` ) WHERE `parent_id`=0 AND `objectType`='".BITUSER_CONTENT_TYPE_GUID."'",

				// update comments on wiki pages
				"UPDATE `".BIT_DB_PREFIX."tiki_comments` SET `objectType`='".BITPAGE_CONTENT_TYPE_GUID."' WHERE `objectType`='wiki page'",

				// set parent ID = content ID of parent comment
				// this will only work correctly for TW DB upgrades, and will corrupt the DB if run more then once
				"create temporary table `".BIT_DB_PREFIX."tiki_comments_temp` as (select * from `".BIT_DB_PREFIX."tiki_comments`) ",
				"UPDATE `".BIT_DB_PREFIX."tiki_comments` SET `parent_id`=(SELECT i_tcm.`content_id` FROM `".BIT_DB_PREFIX."tiki_content` as i_tcn, `".BIT_DB_PREFIX."tiki_comments_temp` as i_tcm WHERE  i_tcm.`content_id` = i_tcn.`content_id` and `".BIT_DB_PREFIX."tiki_comments`.`parent_id` = i_tcm.`comment_id` ) where  parent_id != 0 and  `objectType`='".BITPAGE_CONTENT_TYPE_GUID."' ",
				// parent ID = 0 indicates a root comment in TW, but now needs to = content ID of wiki page it is the root comment for
				"UPDATE `".BIT_DB_PREFIX."tiki_comments` SET `parent_id`=(SELECT `content_id` FROM `".BIT_DB_PREFIX."tiki_content` WHERE `content_type_guid`='".BITPAGE_CONTENT_TYPE_GUID."' AND `title`=`".BIT_DB_PREFIX."tiki_comments`.`object` ) WHERE `parent_id`=0 AND `objectType`='".BITPAGE_CONTENT_TYPE_GUID."'",

				"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences` (`name`, `value`, `package`) VALUES( 'feature_wiki_books', 'y', 'wiki' )",
				"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences` (`name`, `value`, `package`) VALUES( 'feature_history', 'y', 'wiki' )",
				"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences` (`name`, `value`, `package`) VALUES( 'feature_listPages', 'y', 'wiki' )",

				"UPDATE `".BIT_DB_PREFIX."tiki_categorized_objects` SET `object_type`='".BITPAGE_CONTENT_TYPE_GUID."', `object_id`=(SELECT tc.`content_id` FROM `".BIT_DB_PREFIX."tiki_content` tc WHERE tc.`title`=`".BIT_DB_PREFIX."tiki_categorized_objects`.`objId` AND `".BIT_DB_PREFIX."tiki_categorized_objects`.`object_type`='wiki page')",


				// update user watches
				"update `".BIT_DB_PREFIX."tiki_user_watches` as `tw` set `object` = (select `tp`.`page_id` from `tiki_pages` as `tp`, `tiki_content` as `tc` where `tp`.`content_id` = `tc`.`content_id` and   `tc`.`title` = `tw`.`title` )",


				),
			)),


			// STEP 5
			array( 'PHP' => '
				global $gBitSystem;
				require_once( LIBERTY_PKG_PATH."LibertyStructure.php" );
				require_once( WIKI_PKG_PATH."BitBook.php" );
				$query = "SELECT `structure_id`, `content_id` FROM `".BIT_DB_PREFIX."tiki_structures` WHERE `parent_id` IS NULL OR `parent_id`=0";
				$roots = $gBitSystem->mDb->getAssoc( $query );
				$s = new LibertyStructure();
				foreach( $roots AS $rootId=>$contentId ) {
					$gBitSystem->mDb->query( "UPDATE `".BIT_DB_PREFIX."tiki_structures` SET `root_structure_id`=? WHERE `structure_id`=?", array( $rootId, $rootId ) );
					$gBitSystem->mDb->query( "UPDATE `".BIT_DB_PREFIX."tiki_content` SET `content_type_guid`=? WHERE `content_id`=?", array( BITBOOK_CONTENT_TYPE_GUID, $contentId ) );
					$toc = $s->build_subtree_toc( $rootId );
					$s->setTreeRoot( $rootId, $toc );
				}

			' ),


			// STEP 6
			array( 'DATADICT' => array(
				array( 'DROPCOLUMN' => array(
					'tiki_pages' => array( '`lastModif`', '`data`', '`pageName`', '`ip`', '`hits`', '`user`', '`creator`' ),
					'tiki_semaphores' => array( '`user`' ),
					'tiki_copyrights' => array( '`userName`', '`page`' ),
					'tiki_page_footnotes' => array( '`user`', '`pageName`' ),
					'tiki_actionlog' => array( '`user`', '`pageName`' ),
					'tiki_history' => array( '`user`', '`pageName`' ),
					'tiki_links' => array( '`fromPage`', '`toPage`' ),
					'tiki_structures' => array( '`page_id`' ),
				)),
			)),

			// STEP 7
			array( 'DATADICT' => array(
			array( 'CREATEINDEX' => array(
					'tiki_actlog_page_idx' => array( 'tiki_actionlog', '`page_id`', array() ),
					'tiki_copyrights_page_idx' => array( 'tiki_copyrights', '`page_id`', array() ),
					'tiki_copyrights_user_idx' => array( 'tiki_copyrights', '`user_id`', array() ),
					'tiki_copyrights_up_idx' => array( 'tiki_copyrights', '`user_id`,`page_id`', array( 'UNIQUE' ) ),
					'tiki_footnotes_page_idx' => array( 'tiki_page_footnotes', '`page_id`', array() ),
					'tiki_footnotes_user_idx' => array( 'tiki_page_footnotes', '`user_id`', array() ),
					'tiki_footnotes_up_idx' => array( 'tiki_page_footnotes', '`user_id`,`page_id`', array( 'UNIQUE' ) ),
					'tiki_history_page_idx' => array( 'tiki_history', '`page_id`', array() ),
					'tiki_history_pv_idx' => array( 'tiki_history', '`page_id`,`version`', array( 'UNIQUE' ) ),
					'tiki_links_from_idx' => array( 'tiki_links', '`from_content_id`', array() ),
					'tiki_links_to_idx' => array( 'tiki_links', '`to_content_id`', array() ),
					'tiki_links_ft_idx' => array( 'tiki_links', '`from_content_id`,`to_content_id`', array( 'UNIQUE' ) ),
					'tiki_pages_content_idx' => array( 'tiki_pages', '`content_id`', array( 'UNIQUE' ) ),
					'tiki_sema_user_idx' => array( 'tiki_semaphores', '`user_id`', array() ),
				)),
			)),

			*/

?>
