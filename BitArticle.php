<?php
/**
 * @version $Header$
 * @package articles
 *
 * Copyright( c )2004 bitweaver.org
 * Copyright( c )2003 tikwiki.org
 * Copyright( c )2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * $Id$
 *
 * Article class is used when accessing BitArticles. It is based on TikiSample
 * and builds on core bitweaver functionality, such as the Liberty CMS engine.
 *
 * created 2004/8/15
 * @author wolffy <wolff_borg@yahoo.com.au>
 * @version $Revision$
 */

/**
 * Required setup
 */
require_once( ARTICLES_PKG_PATH.'BitArticleTopic.php' );
require_once( ARTICLES_PKG_PATH.'BitArticleType.php' );
require_once( LIBERTY_PKG_PATH.'LibertyMime.php' );
require_once( LIBERTY_PKG_PATH.'LibertyComment.php' );

define( 'BITARTICLE_CONTENT_TYPE_GUID', 'bitarticle' );

/**
 * @package articles
 */
class BitArticle extends LibertyMime {
	/**
	* Primary key for articles
	* @access public
	*/
	var $mArticleId;
	var $mTypeId;
	var $mTopicId;

	/**
	* Initiate the articles class
	* @param $pArticleId article id of the article we want to view
	* @param $pContentId content id of the article we want to view
	* @access private
	**/
	function BitArticle( $pArticleId=NULL, $pContentId=NULL ) {
		LibertyMime::LibertyMime();
		$this->registerContentType(
			BITARTICLE_CONTENT_TYPE_GUID, array(
				'content_type_guid' => BITARTICLE_CONTENT_TYPE_GUID,
				'content_name' => 'Article',
				'handler_class' => 'BitArticle',
				'handler_package' => 'articles',
				'handler_file' => 'BitArticle.php',
				'maintainer_url' => 'http://www.bitweaver.org'
		));
		$this->mContentId = $pContentId;
		$this->mArticleId = $pArticleId;
		$this->mTypeId  = NULL;
		$this->mTopicId = NULL;
		$this->mContentTypeGuid = BITARTICLE_CONTENT_TYPE_GUID;
		$this->mDate = new BitDate();
		$offset = $this->mDate->get_display_offset();

		// Permission setup
		$this->mViewContentPerm  = 'p_articles_read';
		$this->mCreateContentPerm  = 'p_articles_submit';
		$this->mUpdateContentPerm  = 'p_articles_update';
		$this->mAdminContentPerm = 'p_articles_admin';
	}

	/**
	* Load the data from the database
	* @access public
	**/
	function load() {
		if( @$this->verifyId( $this->mArticleId ) || @$this->verifyId( $this->mContentId ) ) {
			// LibertyContent::load()assumes you have joined already, and will not execute any sql!
			// This is a significant performance optimization
			$lookupColumn = @$this->verifyId( $this->mArticleId ) ? 'article_id' : 'content_id';
			$bindVars[] = $lookupId = @BitBase::verifyId( $this->mArticleId ) ? $this->mArticleId : $this->mContentId;
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "SELECT a.*, lc.*, atype.*, atopic.*, lch.hits,
				uue.`login` AS `modifier_user`, uue.`real_name` AS `modifier_real_name`,
				uuc.`login` AS `creator_user`, uuc.`real_name` AS `creator_real_name` ,
				la.`attachment_id` AS `primary_attachment_id`, lf.storage_path AS `image_attachment_path` $selectSql
				FROM `".BIT_DB_PREFIX."articles` a
					LEFT OUTER JOIN `".BIT_DB_PREFIX."article_types` atype ON( atype.`article_type_id` = a.`article_type_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."article_topics` atopic ON( atopic.`topic_id` = a.`topic_id` )
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = a.`content_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON( lch.`content_id` = lc.`content_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."users_users` uue ON( uue.`user_id` = lc.`modifier_user_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."users_users` uuc ON( uuc.`user_id` = lc.`user_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` la ON( la.`content_id` = lc.`content_id` AND la.`is_primary` = 'y' )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lf ON( lf.`file_id` = la.`foreign_id` )
					$joinSql
				WHERE a.`$lookupColumn`=? $whereSql";
			$result = $this->mDb->query( $query, $bindVars );

			global $gBitSystem;
			if( $result && $result->numRows() ) {
				$this->mInfo = $result->fetchRow();

				// if a custom image for the article exists, use that, then use an attachment, then use the topic image
				$isTopicImage = false;

				$this->mContentId = $this->mInfo['content_id'];
				$this->mArticleId = $this->mInfo['article_id'];
				$this->mTopicId   = $this->mInfo['topic_id'];
				$this->mTypeId	  = $this->mInfo['article_type_id'];

				$this->mInfo['thumbnail_url'] = BitArticle::getImageThumbnails( $this->mInfo );
				$this->mInfo['creator']       = ( !empty( $this->mInfo['creator_real_name'] ) ? $this->mInfo['creator_real_name'] : $this->mInfo['creator_user'] );
				$this->mInfo['editor']        = ( !empty( $this->mInfo['modifier_real_name'] ) ? $this->mInfo['modifier_real_name'] : $this->mInfo['modifier_user'] );
				$this->mInfo['display_url']   = $this->getDisplayUrl();
				// we need the raw data to display in the textarea
				$this->mInfo['raw']           = $this->mInfo['data'];
				// here we have the displayed data without the ...split... stuff
				$this->mInfo['data']          = preg_replace( LIBERTY_SPLIT_REGEX, "", $this->mInfo['data'] );

				$comment = new LibertyComment();
				$this->mInfo['num_comments'] = $comment->getNumComments( $this->mInfo['content_id'] );

				LibertyMime::load();

				if( !empty( $this->mInfo['primary_attachment_id'] ) && !empty( $this->mStorage[$this->mInfo['primary_attachment_id']] )) {
					$this->mInfo['primary_attachment'] = &$this->mStorage[$this->mInfo['primary_attachment_id']];
				}

				$this->mInfo['parsed'] = $this->parseData();
			} else {
				$this->mArticleId = NULL;
			}
		}

		return( count( $this->mInfo ) );
	}

	/**
	* Store article data after submission
	* @param array pParamHash of values that will be used to store the page
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	* @access public
	**/
	function store( &$pParamHash ) {
		global $gBitSystem;
		$this->mDb->StartTrans();
		if( $this->verify( $pParamHash )&& LibertyMime::store( $pParamHash ) ) {
			$table = BIT_DB_PREFIX."articles";

			if( $this->isValid() ) {
				$result = $this->mDb->associateUpdate( $table, $pParamHash['article_store'], array( "article_id" => $this->mArticleId ));
			} else {
				$pParamHash['article_store']['content_id'] = $pParamHash['content_id'];
				if( isset( $pParamHash['article_id'] )&& is_numeric( $pParamHash['article_id'] ) ) {
					// if pParamHash['article_id'] is set, someone is requesting a particular article_id. Use with caution!
					$pParamHash['article_store']['article_id'] = $pParamHash['article_id'];
				} else {
					$pParamHash['article_store']['article_id'] = $this->mDb->GenID( 'articles_article_id_seq' );
				}
				$this->mArticleId = $pParamHash['article_store']['article_id'];
				$result = $this->mDb->associateInsert( $table, $pParamHash['article_store'] );
			}

			$this->mDb->CompleteTrans();
			$this->load();
		}
		return ( count( $this->mErrors ) == 0 );
	}

	/**
	* Make sure the data is safe to store
	* @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	* @param array pParams reference to hash of values that will be used to store the page, they will be modified where necessary
	* @return bool TRUE on success, FALSE if verify failed. If FALSE, $this->mErrors will have reason why
	* @access private
	**/
	function verify( &$pParamHash ) {
		global $gBitUser, $gBitSystem;

		// make sure we're all loaded up of we have a mArticleId
		if( $this->mArticleId && empty( $this->mInfo ) ) {
			$this->load();
		}

		if( @$this->verifyId( $this->mInfo['content_id'] ) ) {
			$pParamHash['content_id'] = $this->mInfo['content_id'];
		}

		// It is possible a derived class set this to something different
		if( empty( $pParamHash['content_type_guid'] )&& !empty( $this->mContentTypeGuid ) ) {
			$pParamHash['content_type_guid'] = $this->mContentTypeGuid;
		}

		if( @$this->verifyId( $pParamHash['content_id'] ) ) {
			$pParamHash['article_store']['content_id'] = $pParamHash['content_id'];
		}

		if( !empty( $pParamHash['author_name'] ) ) {
			$pParamHash['article_store']['author_name'] = $pParamHash['author_name'];
		}

		if( @$this->verifyId( $pParamHash['topic_id'] ) ) {
			$pParamHash['article_store']['topic_id'] =( int )$pParamHash['topic_id'];
		}

		if( @$this->verifyId( $pParamHash['article_type_id'] ) ) {
			$pParamHash['article_store']['article_type_id'] =( int )$pParamHash['article_type_id'];
		}

		if( !empty( $pParamHash['format_guid'] ) ) {
			$pParamHash['content_store']['format_guid'] = $pParamHash['format_guid'];
		}

		// we do the substr on load. otherwise we need to store the same data twice.
		if( !empty( $pParamHash['edit'] ) ) {
			$pParamHash['content_store']['data'] = $pParamHash['edit'];
		}

		if( !empty( $pParamHash['rating'] ) ) {
			$pParamHash['article_store']['rating'] =( int )( $pParamHash['rating'] );
		}

		// check for name issues, first truncate length if too long
		if( !empty( $pParamHash['title'] ) ) {
			if( !$this->isValid() ) {
				if( empty( $pParamHash['title'] ) ) {
					$this->mErrors['title'] = 'You must specify a title.';
				} else {
					$pParamHash['content_store']['title'] = substr( $pParamHash['title'], 0, BIT_CONTENT_MAX_TITLE_LEN );
				}
			} else {
				$pParamHash['content_store']['title'] =( isset( $pParamHash['title'] ))? substr( $pParamHash['title'], 0, BIT_CONTENT_MAX_TITLE_LEN ): '';
			}
		} else if( empty( $pParamHash['title'] ) ) {
			// no name specified
			$this->mErrors['title'] = 'You must specify a title';
		}

		if( !empty( $pParamHash['publish_Month'] ) ) {
			$dateString = $this->mDate->gmmktime(
				$pParamHash['publish_Hour'],
				$pParamHash['publish_Minute'],
				isset($pParamHash['publish_Second']) ? $pParamHash['publish_Second'] : 0,
				$pParamHash['publish_Month'],
				$pParamHash['publish_Day'],
				$pParamHash['publish_Year']
			);

			$timestamp = $this->mDate->getUTCFromDisplayDate( $dateString );
			if( $timestamp !== -1 ) {
				$pParamHash['publish_date'] = $timestamp;
			}
		}
		if( !empty( $pParamHash['publish_date'] ) ) {
			$pParamHash['article_store']['publish_date'] = $pParamHash['publish_date'];
		}

		if( !empty( $pParamHash['expire_Month'] ) ) {
			$dateString = $this->mDate->gmmktime(
				$pParamHash['expire_Hour'],
				$pParamHash['expire_Minute'],
				isset($pParamHash['expire_Second']) ? $pParamHash['expire_Second'] : 0,
				$pParamHash['expire_Month'],
				$pParamHash['expire_Day'],
				$pParamHash['expire_Year']
			);

			$timestamp = $this->mDate->getUTCFromDisplayDate( $dateString );
			if( $timestamp !== -1 ) {
				$pParamHash['expire_date'] = $timestamp;
			}
		}
		if( !empty( $pParamHash['expire_date'] ) ) {
			$pParamHash['article_store']['expire_date'] = $pParamHash['expire_date'];
		}

		if( @$this->verifyId( $pParamHash['status_id'] ) ) {
			if( $pParamHash['status_id'] > ARTICLE_STATUS_PENDING ) {
				if( $gBitUser->hasPermission( 'p_articles_approve_submission' )) {
					$pParamHash['article_store']['status_id'] =( int )( $pParamHash['status_id'] );
				} else {
					$pParamHash['article_store']['status_id'] = ARTICLE_STATUS_PENDING;
				}
			} else {
				$pParamHash['article_store']['status_id'] =( int )( $pParamHash['status_id'] );
			}
		} elseif( @$this->verifyId( $this->mInfo['status_id'] ) ) {
			$pParamHash['article_store']['status_id'] = $this->mInfo['status_id'];
		} else {
			if( $gBitUser->hasPermission( 'p_articles_approve_submission' ) || $gBitUser->hasPermission( 'p_articles_auto_approve' ) ) {
				$pParamHash['article_store']['status_id'] = ARTICLE_STATUS_APPROVED;
			} else {
				$pParamHash['article_store']['status_id'] = ARTICLE_STATUS_PENDING;		// Default status
			}
		}

		// content preferences
		$prefs = array();
		if( $gBitUser->hasPermission( 'p_liberty_enter_html' ) ) {
			$prefs[] = 'content_enter_html';
		}

		foreach( $prefs as $pref ) {
			if( !empty( $pParamHash['preferences'][$pref] ) ) {
				$pParamHash['preferences_store'][$pref] = $pParamHash['preferences'][$pref];
			} else {
				$pParamHash['preferences_store'][$pref] = NULL;
			}
		}

		if ( array_search( $pParamHash['article_store']['status_id'], array( ARTICLE_STATUS_DENIED, ARTICLE_STATUS_DRAFT, ARTICLE_STATUS_PENDING ) ) ) {
				$this->mInfo["no_index"] = true ;
		}
		
		// if we have an error we get them all by checking parent classes for additional errors
		if( count( $this->mErrors ) > 0 ){
			parent::verify( $pParamHash );
		}

		return( count( $this->mErrors )== 0 );
	}

	/**
	* Deal with images and text, modify them apprpriately that they can be returned to the form.
	* @param $previewData data submitted by form - generally $_REQUEST
	* @return array of data compatible with article form
	* @access public
	**/
	function preparePreview( $pParamHash ) {
		global $gBitSystem, $gBitUser;

		$data = $pParamHash;
		$this->verify( $data );
		$data = array_merge( $pParamHash, $data['content_store'], $data['article_store'] );
		$data['raw'] = $data['edit'];

		if( empty( $data['user_id'] ) ) {
			$data['user_id'] = $gBitUser->mUserId;
		}

		if( empty( $data['hits'] ) ) {
			$data['hits'] = 0;
		}

		if( empty( $data['publish_date'] ) ) {
			$data['publish_date'] = $gBitSystem->getUTCTime();
		}

		if( empty( $data['article_type_id'] ) ) {
			$data['article_type_id'] = 1;
		}

		if( empty( $data['topic_id'] ) ) {
			$data['topic_id'] = 1;
		}

		if( empty( $data['parsed'] ) ) {
			$data['no_cache']    = TRUE;
			$data['parsed'] = $this->parseData( $data );
			// replace the split syntax with a horizontal rule
			$data['parsed'] = preg_replace( LIBERTY_SPLIT_REGEX, "<hr />", $data['parsed'] );
		}

		$articleType = new BitArticleType( $data['article_type_id'] );
		$articleTopic = new BitArticleTopic( $data['topic_id'] );
		$data = array_merge( $data, $articleType->mInfo, $articleTopic->mInfo );

		return $data;
	}

	/**
	* Get the URL for any given article image
	* @param $pParamHash pass in full set of data returned from article query
	* @return url to image
	* @access public
	**/
	function getImageThumbnails( $pParamHash ) {
		global $gBitSystem, $gThumbSizes;
		$ret = NULL;

		$thumbHash['mime_image'] = FALSE;
		if( !empty( $pParamHash['image_attachment_path'] )) {
			$thumbHash['storage_path'] = $pParamHash['image_attachment_path'];
			$ret = liberty_fetch_thumbnails( $thumbHash );
		} elseif( !empty( $pParamHash['has_topic_image'] ) && $pParamHash['has_topic_image'] == 'y' ) {
			$thumbHash['storage_path'] = preg_replace( "#^/+#", "", BitArticleTopic::getTopicImageStorageUrl( $pParamHash['topic_id'] ));
			$ret = liberty_fetch_thumbnails( $thumbHash );
		}

		return $ret;
	}

	/**
	* Removes currently loaded article
	* @return bool TRUE on success, FALSE on failure
	* @access public
	**/
	function expunge() {
		$ret = FALSE;
		if( $this->isValid() ) {
			$this->mDb->StartTrans();
			$query = "DELETE FROM `".BIT_DB_PREFIX."articles` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			if( LibertyMime::expunge() ) {
				$ret = TRUE;
				$this->mDb->CompleteTrans();
			} else {
				$this->mDb->RollbackTrans();
			}
		}
		return $ret;
	}

	/**
	* Check if there is an article loaded
	* @return bool TRUE on success, FALSE on failure
	* @access public
	**/
	function isValid() {
		return( $this->verifyId( $this->mArticleId ) && $this->verifyId( $this->mContentId ) );
	}

	/**
	* This function generates a list of records from the liberty_content database for use in a list page
	* @param $pParamHash contains an array of conditions to sort by
	* @return array of articles
	* @access public
	**/
	function getList( &$pParamHash ) {
		global $gBitSystem, $gBitUser, $gLibertySystem;

		if( empty( $pParamHash['sort_mode'] ) ) {
			// no idea what this is supposed to do
			//$pParamHash['sort_mode'] = $gBitSystem->isFeatureActive('articles_auto_approve') ? 'order_key_desc' : 'publish_date_desc';
			$pParamHash['sort_mode'] = 'publish_date_desc';
		}

		LibertyContent::prepGetList( $pParamHash );

		$joinSql = '';
		$selectSql = '';
		$bindVars = array();
		array_push( $bindVars, $this->mContentTypeGuid );
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars, NULL, $pParamHash );

		$find = $pParamHash['find'];
		if( is_array( $find ) ) {
			// you can use an array of articles
			$whereSql .= " AND lc.`title` IN( ".implode( ',',array_fill( 0, count( $find ),'?' ) )." )";
			$bindVars = array_merge( $bindVars, $find );
		} elseif( is_string( $find ) ) {
			// or a string
			$whereSql .= " AND UPPER( lc.`title` ) LIKE ? ";
			$bindVars[] = '%'.strtoupper( $find ).'%';
		} elseif( @$this->verifyId( $pParamHash['user_id'] ) ) {
			// or gate on a user
			$whereSql .= " AND lc.`user_id` = ? ";
			$bindVars[] = (int)$pParamHash['user_id'];
		}

		if( @$this->verifyId( $pParamHash['status_id'] ) ) {
			$whereSql .= " AND a.`status_id` = ? ";
			$bindVars[] = $pParamHash['status_id'];
		}

		if( @$this->verifyId( $pParamHash['type_id'] ) ) {
			$whereSql .= " AND a.`article_type_id` = ? ";
			$bindVars[] = ( int )$pParamHash['type_id'];
		}

		// TODO: we need to check if the article wants to be viewed before / after respective dates
		// someone better at SQL please get this working without an additional db call - xing
		$now = $gBitSystem->getUTCTime();
		if( !empty( $pParamHash['show_future'] ) && !empty( $pParamHash['show_expired'] ) && $gBitUser->hasPermission( 'p_articles_admin' )) {
			// this will show all articles at once - future, current and expired
		} elseif( !empty( $pParamHash['show_future'] ) && $gBitUser->hasPermission( 'p_articles_admin' )) {
			// hide expired articles
			$whereSql .= " AND ( a.`expire_date` > ? OR atype.`show_post_expire` = ? ) ";
			$bindVars[] = ( int )$now;
			$bindVars[] = 'y';
		} elseif( !empty( $pParamHash['show_expired'] ) && $gBitUser->hasPermission( 'p_articles_admin' )) {
			// hide future articles
			$whereSql .= " AND ( a.`publish_date` < ? OR atype.`show_pre_publ` = ? ) ";
			$bindVars[] = ( int )$now;
			$bindVars[] = 'y';
		} elseif( !empty( $pParamHash['get_future'] )) {
			// show only future
			// if we're trying to view these articles, we better have the perms to do so
			if( !$gBitUser->hasPermission( 'p_articles_admin' )) {
				return array();
			}
			$whereSql .= " AND a.`publish_date` > ?";
			$bindVars[] = ( int )$now;
		} elseif( !empty( $pParamHash['get_expired'] )) {
			// show only expired articles
			// if we're trying to view these articles, we better have the perms to do so
			if( !$gBitUser->hasPermission( 'p_articles_admin' )) {
				return array();
			}
			$whereSql .= " AND a.`expire_date` < ? ";
			$bindVars[] = ( int )$now;
		} else {
			// hide future and expired articles - this is the default behaviour
			// we need all these AND and ORs to ensure that other conditions such as status_id are respected as well
			$whereSql .= " AND (( a.`publish_date` > a.`expire_date` ) OR (( a.`publish_date` < ? OR atype.`show_pre_publ` = ? ) AND ( a.`expire_date` > ? OR atype.`show_post_expire` = ? ))) ";
			$bindVars[] = ( int )$now;
			$bindVars[] = 'y';
			$bindVars[] = ( int )$now;
			$bindVars[] = 'y';
		}

		if( @$this->verifyId( $pParamHash['topic_id'] ) ) {
			$whereSql .= " AND a.`topic_id` = ? ";
			$bindVars[] = ( int )$pParamHash['topic_id'];
		} elseif( !empty( $pParamHash['topic'] ) ) {
			$whereSql .= " AND UPPER( atopic.`topic_name` ) = ? ";
			$bindVars[] = strtoupper( $pParamHash['topic'] );
		} else {
			$whereSql .= " AND ( atopic.`active_topic` != 'n' OR atopic.`active_topic` IS NULL ) ";
			//$whereSql .= " AND atopic.`active_topic` != 'n' ";
		}

		// Oracle is very particular about naming multiple columns, so need to explicity name them ORA-00918: column ambiguously defined
		$query =
			"SELECT
				a.`article_id`, a.`description`, a.`author_name`, a.`publish_date`, a.`expire_date`, a.`rating`,
				atopic.`topic_id`, atopic.`topic_name`, atopic.`has_topic_image`, atopic.`active_topic`,
				astatus.`status_id`, astatus.`status_name`,
				lch.`hits`,
				atype.*, lc.*, la.`attachment_id` AS `primary_attachment_id`, lf.storage_path AS `image_attachment_path` $selectSql
			FROM `".BIT_DB_PREFIX."articles` a
				INNER JOIN      `".BIT_DB_PREFIX."liberty_content`       lc ON( lc.`content_id`         = a.`content_id` )
				INNER JOIN      `".BIT_DB_PREFIX."article_status`   astatus ON( astatus.`status_id`     = a.`status_id` )
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON( lc.`content_id`         = lch.`content_id` )
				LEFT OUTER JOIN `".BIT_DB_PREFIX."article_topics`    atopic ON( atopic.`topic_id`       = a.`topic_id` )
				LEFT OUTER JOIN `".BIT_DB_PREFIX."article_types`      atype ON( atype.`article_type_id` = a.`article_type_id` )
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments`   la ON( la.`content_id`         = lc.`content_id` AND la.`is_primary` = 'y' )
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files`         lf ON( lf.`file_id`            = la.`foreign_id` )
				$joinSql
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY ".$this->mDb->convertSortmode( $pParamHash['sort_mode'] );

		$query_cant = "SELECT COUNT( * )FROM `".BIT_DB_PREFIX."articles` a
			INNER JOIN      `".BIT_DB_PREFIX."liberty_content`    lc ON lc.`content_id`   = a.`content_id`
			LEFT OUTER JOIN `".BIT_DB_PREFIX."article_topics` atopic ON atopic.`topic_id` = a.`topic_id` $joinSql
			LEFT OUTER JOIN `".BIT_DB_PREFIX."article_types`   atype ON atype.`article_type_id` = a.`article_type_id`
			WHERE lc.`content_type_guid` = ? $whereSql";

		$result = $this->mDb->query( $query, $bindVars, $pParamHash['max_records'], $pParamHash['offset'] );
		$ret = array();
		$comment = new LibertyComment();
		while( $res = $result->fetchRow() ) {
			// get this stuff parsed
			$res = array_merge( $this->parseSplit( $res, $gBitSystem->getConfig( 'articles_description_length', 500 )), $res );

			$res['thumbnail_url'] = BitArticle::getImageThumbnails( $res );
			$res['num_comments']  = $comment->getNumComments( $res['content_id'] );
			$res['display_url']   = $this->getDisplayUrl( $res['article_id'] );
			$res['display_link']  = $this->getDisplayLink( $res['title'], $res );

			// fetch the primary attachment that we can display the file on the front page if needed
			$res['primary_attachment'] = $this->getAttachment( $res['primary_attachment_id'] );

			$ret[] = $res;
		}

		$pParamHash["cant"] = $this->mDb->getOne( $query_cant, $bindVars );

		LibertyContent::postGetList( $pParamHash );

		return $ret;
	}

    /**
    * Returns include file that will setup vars for display
    * @return the fully specified path to file to be included
    */
	function getRenderFile() {
		return ARTICLES_PKG_PATH."display_article_inc.php";
	}

	/**
	 * Get a list of articles that are to be published in the future
	 * 
	 * @param array $pParamHash contains listing options - same as getList()
	 * @access public
	 * @return array of articles
	 */
	function getFutureList( &$pParamHash ) {
		$pParamHash['get_future'] = TRUE;
		return( $this->getList( $pParamHash ));
	}

	/**
	 * Get list of articles that have expired and are not displayed on the site anymore
	 * 
	 * @param array $pParamHash contains listing options - same as getList()
	 * @access public
	 * @return array of articles
	 */
	function getExpiredList( &$pParamHash ) {
		$pParamHash['get_expired'] = TRUE;
		return( $this->getList( $pParamHash ));
	}

	/**
	* Generates the URL to the article
	* @return the link to the full article
	*/
	function getDisplayUrl( $pArticleId = NULL, $pParamHash = NULL ) {
		global $gBitSystem;

		$ret = NULL;
		if( !@BitBase::verifyId( $pArticleId ) && $this->isValid() ) {
			$pArticleId = $this->mArticleId;
		}

		if( @$this->verifyId( $pArticleId ) ) {
			if( $gBitSystem->isFeatureActive( 'pretty_urls_extended' ) ) {
				// Not needed since it's a number:  $ret = ARTICLES_PKG_URL."view/".$this->mArticleId;
				$ret = ARTICLES_PKG_URL.$pArticleId;
			} else if( $gBitSystem->isFeatureActive( 'pretty_urls' ) ) {
				$ret = ARTICLES_PKG_URL.$pArticleId;
			} else {
				$ret = ARTICLES_PKG_URL."read.php?article_id=$pArticleId";
			}
		} else {
			$ret = LibertyContent::getDisplayUrl( NULL, $pParamHash );
		}

		return $ret;
	}

	/**
	* get a list of all available statuses
	* @return an array of available statuses
	* @access public
	**/
	function getStatusList() {
		global $gBitSystem;
		$query = "SELECT * FROM `".BIT_DB_PREFIX."article_status`";
		$result = $gBitSystem->mDb->query( $query );
		return $result->getRows();
	}

	/**
	* set the status of an article
	* @param $pStatusId new status id of the article
	* @param $pArticleId of the article that is being changed - if not set, it will attemtp to change the currently loaded article
	* @return new status of article on success - else returns NULL
	* @access public
	**/
	function setStatus( $pStatusId, $pArticleId = NULL, $pContentId = NULL ) {
		global $gBitSystem;
		$validStatuses = array( ARTICLE_STATUS_DENIED, ARTICLE_STATUS_DRAFT, ARTICLE_STATUS_PENDING, ARTICLE_STATUS_APPROVED, ARTICLE_STATUS_RETIRED );

		if( !in_array( $pStatusId, $validStatuses ) ) {
			$this->mErrors[] = "Invalid article status";
			return FALSE;
		}

		if(  empty( $pContentId ) and  $this->isValid()) $pContentId = $this->mContentId ;
		if(  empty( $pArticleId ) and  $this->isValid()) $pArticleId = $this->mArticleId ;
		if( !empty( $pContentId ) and !$this->isValid()) $this->mContentId = $pContentId ;
		if( !empty( $pArticleId ) and !$this->isValid()) $this->mArticleId = $pArticleId ;

		if( empty( $pArticleId ) && $this->isValid() ) {
			$pArticleId = $this->mArticleId;
		}

		if( @$this->verifyId( $pArticleId ) ) {
			$sql = "UPDATE `".BIT_DB_PREFIX."articles` SET `status_id` = ? WHERE `article_id` = ?";
			$rs = $this->mDb->query( $sql, array( $pStatusId, $pArticleId ));
			// Calling the index function for approved articles ...
			if( $gBitSystem->isPackageActive( 'search' ) ) {
				include_once( SEARCH_PKG_PATH.'refresh_functions.php' );
				if ($pStatusId == ARTICLE_STATUS_APPROVED) {
					refresh_index($this);
				} elseif (!$pStatusId == ARTICLE_STATUS_RETIRED) {
					delete_index($pContentId); // delete it from the search index unless retired ...
				}
			}
			return $pStatusId;
		}
	}
}

?>
