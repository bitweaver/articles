<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/BitArticle.php,v 1.109 2007/03/01 20:39:48 squareing Exp $
 * @package article
 *
 * Copyright( c )2004 bitweaver.org
 * Copyright( c )2003 tikwiki.org
 * Copyright( c )2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: BitArticle.php,v 1.109 2007/03/01 20:39:48 squareing Exp $
 *
 * Article class is used when accessing BitArticles. It is based on TikiSample
 * and builds on core bitweaver functionality, such as the Liberty CMS engine.
 *
 * created 2004/8/15
 * @author wolffy <wolff_borg@yahoo.com.au>
 * @version $Revision: 1.109 $ $Date: 2007/03/01 20:39:48 $ $Author: squareing $
 */

/**
 * Required setup
 */
require_once( LIBERTY_PKG_PATH.'LibertyAttachable.php' );
require_once( ARTICLES_PKG_PATH.'BitArticleTopic.php' );
require_once( ARTICLES_PKG_PATH.'BitArticleType.php' );
require_once( LIBERTY_PKG_PATH.'LibertyComment.php' );
// feature incomplete require_once( ARTICLES_PKG_PATH.'BitArticleStatistics.php' );

define( 'ARTICLE_SPLIT_REGEX', "/\.{3}split\.{3}[\r\n]?/i" );

/**
 * @package article
 */
class BitArticle extends LibertyAttachable {
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
		$this->mArticleId = $pArticleId;
		$this->mTypeId  = NULL;
		$this->mTopicId = NULL;

		LibertyAttachable::LibertyAttachable();
		$this->registerContentType( BITARTICLE_CONTENT_TYPE_GUID, array(
			'content_description' => 'Article',
			'handler_class' => 'BitArticle',
			'handler_package' => 'articles',
			'handler_file' => 'BitArticle.php',
			'maintainer_url' => 'http://www.bitweaver.org'
		) );
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = BITARTICLE_CONTENT_TYPE_GUID;
		if( ! @$this->verifyId( $this->mArticleId ) ) {
			$this->mArticleId = NULL;
		}
		if( ! @$this->verifyId( $this->mContentId ) ) {
			$this->mContentId = NULL;
		}
	}

	/**
	* Load the data from the database
	* @access public
	**/
	function load( $pVersion = 0 ) {
		$pVersion = intval( $pVersion );
		if( @$this->verifyId( $this->mArticleId ) || @$this->verifyId( $this->mContentId ) ) {
			// LibertyContent::load()assumes you have joined already, and will not execute any sql!
			// This is a significant performance optimization
			$lookupColumn = @$this->verifyId( $this->mArticleId ) ? 'article_id' : 'content_id';
			$bindVars[] = $lookupId = @BitBase::verifyId( $this->mArticleId ) ? $this->mArticleId : $this->mContentId;
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "SELECT a.*, ".( !empty( $pVersion ) ? "lch.`hits`, lc.`title`" : "lc.*" ).", atype.*, atopic.*,
				uue.`login` AS `modifier_user`, uue.`real_name` AS `modifier_real_name`,
				uuc.`login` AS `creator_user`, uuc.`real_name` AS `creator_real_name` ,
				lf.`storage_path` as `image_storage_path` $selectSql
				FROM `".BIT_DB_PREFIX."articles` a
					LEFT OUTER JOIN `".BIT_DB_PREFIX."article_types` atype ON( atype.`article_type_id` = a.`article_type_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."article_topics` atopic ON( atopic.`topic_id` = a.`topic_id` )
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = a.`content_id` ) ".
					( !empty( $pVersion ) ? "LEFT JOIN `".BIT_DB_PREFIX."liberty_content_history` lchy ON( lchy.`content_id` = a.`content_id` )" : "" ) . "
					LEFT JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON( lch.`content_id` = lc.`content_id` )
					LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON( uue.`user_id` = lc.`modifier_user_id` )
					LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON( uuc.`user_id` = lc.`user_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` la ON( la.`attachment_id` = a.`image_attachment_id` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lf ON( lf.`file_id` = la.`foreign_id` ) $joinSql
				WHERE a.`$lookupColumn`=? $whereSql ".( !empty( $pVersion ) ? " AND lchy.version = ?" : "" );
			if( !empty( $pVersion ) ) {
				$bindVars[]=$pVersion;
			}
			$result = $this->mDb->query( $query, $bindVars );

			global $gBitSystem;
			if( $result && $result->numRows() ) {
				$this->mInfo = $result->fetchRow();

				// if a custom image for the article exists, use that, then use an attachment, then use the topic image
				$this->mInfo['image_url'] = BitArticle::getImageUrl( $this->mInfo );

				$this->mContentId = $this->mInfo['content_id'];
				$this->mArticleId = $this->mInfo['article_id'];
				$this->mTopicId   = $this->mInfo['topic_id'];
				$this->mTypeId	  = $this->mInfo['article_type_id'];

				$this->mInfo['creator']     = ( isset( $this->mInfo['creator_real_name'] )? $this->mInfo['creator_real_name'] : $this->mInfo['creator_user'] );
				$this->mInfo['editor']      = ( isset( $this->mInfo['modifier_real_name'] )? $this->mInfo['modifier_real_name'] : $this->mInfo['modifier_user'] );
				$this->mInfo['display_url'] = $this->getDisplayUrl();
				$this->mInfo['raw']         = $this->mInfo['data'];
				$this->mInfo['data']        = preg_replace( ARTICLE_SPLIT_REGEX, "", $this->mInfo['data'] );

				$comment = &new LibertyComment();
				$this->mInfo['num_comments'] = $comment->getNumComments( $this->mInfo['content_id'] );

				LibertyAttachable::load();

				$this->mInfo['parsed_data'] = $this->parseData();
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
		if( $this->verify( $pParamHash )&& LibertyAttachable::store( $pParamHash ) ) {
			$table = BIT_DB_PREFIX."articles";
			$this->mDb->StartTrans();

			if( $this->isValid() ) {
				$result = $this->mDb->associateUpdate( $table, $pParamHash['article_store'], array( "article_id" => $pParamHash['article_id'] ) );
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

			// we need to store any custom image that has been uploaded
			if( !empty( $_FILES['article_image']['name'] ) ) {
				$this->storeImage( $pParamHash, $_FILES['article_image'] );
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

		// if no image attachment id is given, we set it null. this way a user can remove an attached image
		// TODO: since we allow custom image size for article images, we should create a resized image of the original here.
		if( @$this->verifyId( $pParamHash['image_attachment_id'] ) ) {
			$pParamHash['article_store']['image_attachment_id'] = ( int )$pParamHash['image_attachment_id'];
		} else {
			$pParamHash['article_store']['image_attachment_id'] = NULL;
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
			$dateString = $pParamHash['publish_Year'].'-'.$pParamHash['publish_Month'].'-'.$pParamHash['publish_Day'].' '.$pParamHash['publish_Hour'].':'.$pParamHash['publish_Minute'];
			//$timestamp = strtotime( $dateString );
			$timestamp = $gBitSystem->mServerTimestamp->getUTCFromDisplayDate( strtotime( $dateString ) );
			if( $timestamp !== -1 ) {
				$pParamHash['publish_date'] = $timestamp;
			}
		}
		if( !empty( $pParamHash['publish_date'] ) ) {
			$pParamHash['article_store']['publish_date'] = $pParamHash['publish_date'];
		}

		if( !empty( $pParamHash['expire_Month'] ) ) {
			$dateString = $pParamHash['expire_Year'].'-'.$pParamHash['expire_Month'].'-'.$pParamHash['expire_Day'].' '.$pParamHash['expire_Hour'].':'.$pParamHash['expire_Minute'];
			//$timestamp = strtotime( $dateString );
			$timestamp = $gBitSystem->mServerTimestamp->getUTCFromDisplayDate( strtotime( $dateString ) );
			if( $timestamp !== -1 ) {
				$pParamHash['expire_date'] = $timestamp;
			}
		}
		if( !empty( $pParamHash['expire_date'] ) ) {
			$pParamHash['article_store']['expire_date'] = $pParamHash['expire_date'];
		}

		if( @$this->verifyId( $pParamHash['status_id'] ) ) {
			if( $pParamHash['status_id'] > ARTICLE_STATUS_PENDING ) {
				if( $gBitUser->hasPermission( 'p_articles_approve_submission' ) || $gTikiuser->hasPermission( 'p_articles_auto_approve' ) ) {
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

		return( count( $this->mErrors )== 0 );
	}

	/**
	* Store article image
	* @param array pParamHash of values that will be used to store the page
	* @param array pFileHash hash returned by $_FILES[<name>]
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	* @access public
	**/
	function storeImage( &$pParamHash, $pFileHash ) {
		global $gBitSystem;
		if( $this->isValid() ) {
			if( !empty( $pFileHash['tmp_name'] ) ) {
				$tmpImagePath = $this->getArticleImageStoragePath( $this->mArticleId, TRUE ).$pFileHash['name'];
				if( !move_uploaded_file( $pFileHash['tmp_name'], $tmpImagePath ) ) {
					$this->mErrors['article_image'] = "Error during attachment of article image";
				} else {
					$resizeFunc = liberty_get_function( 'resize' );
					$storeHash['source_file'] = $tmpImagePath;
					$storeHash['dest_path'] = STORAGE_PKG_NAME.'/'.ARTICLES_PKG_NAME.'/';
					$storeHash['dest_base_name'] = 'article_'.$this->mArticleId;
					$storeHash['max_width'] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
					$storeHash['max_height'] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
					$storeHash['type'] = $pFileHash['type'];

					if( !( $resizeFunc( $storeHash ) ) ) {
						$this->mErrors[] = 'Error while resizing article image';
					}
					@unlink( $tmpImagePath );
				}
			} elseif( !empty( $pParamHash['preview_image_path'] ) && is_file( $pParamHash['preview_image_path'] ) ) {
				// if this article has been previewed with an image, we can copy it to the destination place
				rename( $pParamHash['preview_image_path'], STORAGE_PKG_PATH.ARTICLES_PKG_NAME.'/article_'.$this->mArticleId.'.jpg' );
			}
		}
		return ( count( $this->mErrors ) == 0 );
	}

	/**
	 * Get the name of the article image file
	 * 
	 * @param array $pArticleId article id
	 * @access public
	 * @return TRUE on success, FALSE on failure
	 */
	function getArticleImageStorageName( $pArticleId = NULL ) {
		if( !@BitBase::verifyId( $pArticleId ) ) {
			if( $this->isValid() ) {
				$pArticleId = $this->mArticleId;
			} else {
				return NULL;
			}
		}

		return "article_$pArticleId.jpg";
	}

	/**
	* Work out the path to the image for this article
	* @param $pArticleId id of the article we need the image path for
	* @param $pBasePathOnly bool TRUE / FALSE - specify whether you want full path or just base path
	* @return path on success, FALSE on failure
	* @access public
	**/
	function getArticleImageStoragePath( $pArticleId = NULL, $pBasePathOnly = FALSE ) {
		$path = STORAGE_PKG_PATH.ARTICLES_PKG_NAME.'/';
		if( !is_dir( $path ) ) {
			mkdir_p( $path );
		}

		if( $pBasePathOnly ) {
			return $path;
		}

		if( !@BitBase::verifyId( $pArticleId ) ) {
			if( $this->isValid() ) {
				$pArticleId = $this->mArticleId;
			} else {
				return NULL;
			}
		}

		if( !empty( $pArticleId ) ) {
			return $path.BitArticle::getArticleImageStorageName( $pArticleId );
		} else {
			return FALSE;
		}
	}

	/**
	* Work out the URL to the image for this article
	* @param $pArticleId id of the article we need the image path for
	* @param $pBasePathOnly bool TRUE / FALSE - specify whether you want full path or just base path
	* @return URL on success, FALSE on failure
	* @access public
	**/
	function getArticleImageStorageUrl( $pArticleId = NULL, $pBasePathOnly = FALSE, $pForceRefresh = FALSE ) {
		global $gBitSystem;
		$url = STORAGE_PKG_URL.ARTICLES_PKG_NAME.'/';
		if( $pBasePathOnly ) {
			return $url;
		}

		if( !@BitBase::verifyId( $pArticleId ) ) {
			if( $this->isValid() ) {
				$pArticleId = $this->mArticleId;
			} else {
				return NULL;
			}
		}

		if( is_file( BitArticle::getArticleImageStoragePath( NULL, TRUE ).BitArticle::getArticleImageStorageName( $pArticleId ) ) ) {
			return $url.BitArticle::getArticleImageStorageName( $pArticleId ).( $pForceRefresh ? "?".$gBitSystem->getUTCTime() : '' );
		} else {
			return FALSE;
		}
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

		if( empty( $data['parsed_data'] ) ) {
			$data['no_cache']    = TRUE;
			$data['parsed_data'] = $this->parseData( $data );
			// replace the split syntax with a horizontal rule
			$data['parsed_data'] = preg_replace( ARTICLE_SPLIT_REGEX, "<hr />", $data['parsed_data'] );
		}

		if( @$this->verifyId( $data['image_attachment_id'] ) ) {
			$data['image_attachment_id'] = ( int )$data['image_attachment_id'];
			$query = "SELECT lf.storage_path AS image_storage_path
				FROM `".BIT_DB_PREFIX."liberty_attachments` a
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lf ON( lf.file_id = a.foreign_id )
				WHERE a.attachment_id=?";
			$data['image_storage_path'] = $this->mDb->getOne( $query, array( $data['image_attachment_id'] ) );
			$data['image_url'] = BitArticle::getImageUrl( $data );
		}

		if( !empty( $_FILES['article_image']['name'] ) ) {
			// store the image in temp/articles/
			$tmpImagePath = TEMP_PKG_PATH.ARTICLES_PKG_NAME.'/'.'temp_'.$_FILES['article_image']['name'];
			$tmpImageName = preg_replace( "/(.*)\..*?$/", "$1", $_FILES['article_image']['name'] );
			if( !is_dir( TEMP_PKG_PATH.ARTICLES_PKG_NAME ) ) {
				mkdir( TEMP_PKG_PATH.ARTICLES_PKG_NAME );
			}

			if( !move_uploaded_file( $_FILES['article_image']['tmp_name'], $tmpImagePath ) ) {
				$this->mErrors['article_image'] = "Error during attachment of article image";
			} else {
				$resizeFunc = liberty_get_function( 'resize' );
				$pFileHash['source_file'] = $tmpImagePath;
				$pFileHash['dest_path'] = preg_replace( '!/+!', '', str_replace( BIT_ROOT_PATH, '', TEMP_PKG_PATH ) ).'/'.ARTICLES_PKG_NAME.'/';
				// remove the extension
				$pFileHash['dest_base_name'] = $tmpImageName;
				$pFileHash['max_width'] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
				$pFileHash['max_height'] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
				$pFileHash['type'] = $_FILES['article_image']['type'];

				if( !( $resizeFunc( $pFileHash ) ) ) {
					$this->mErrors[] = 'Error while resizing article image';
				}
				@unlink( $tmpImagePath );
				$data['image_url'] = $data['preview_image_url'] = TEMP_PKG_URL.ARTICLES_PKG_NAME.'/'.$tmpImageName.'.jpg';
				$data['show_image'] = TRUE;
				$data['preview_image_path'] = TEMP_PKG_PATH.ARTICLES_PKG_NAME.'/'.$tmpImageName.'.jpg';
			}
		} elseif( !empty( $data['preview_image_path'] ) && is_file( $data['preview_image_path'] ) ) {
			$data['image_url'] = $data['preview_image_url'];
		}

		$articleType = &new BitArticleType( $data['article_type_id'] );
		$articleTopic = &new BitArticleTopic( $data['topic_id'] );
		$data = array_merge( $data, $articleType->mInfo, $articleTopic->mInfo );

		return $data;
	}

	/**
	* Get the URL for any given article image
	* @param $pParamHash pass in full set of data returned from article query
	* @return url to image
	* @access public
	**/
	function getImagePath() {
		$ret = NULL;
		if( $this->isValid() && BitArticle::getArticleImageStoragePath( $this->mArticleId ) ) {
			$ret = BitArticle::getArticleImageStoragePath( $this->mArticleId );
		}
		return $ret;
	}

	/**
	* Get the URL for any given article image
	* @param $pParamHash pass in full set of data returned from article query
	* @return url to image
	* @access public
	**/
	function getImageUrl( $pParamHash ) {
		$ret = NULL;
		// if a custom image for the article exists, use that, then use an attachment, then use the topic image
		if( @$this->verifyId( $pParamHash['article_id'] ) && BitArticle::getArticleImageStorageUrl( $pParamHash['article_id'] ) ) {
			$ret = BitArticle::getArticleImageStorageUrl( $pParamHash['article_id'] );
		} elseif( @$this->verifyId( $pParamHash['image_attachment_id'] ) && $pParamHash['image_attachment_id'] ) {
			// TODO: clean up the small url stuff. shouldn't be hardcoded.
			// perhaps we should make a copy of the image file and reduce it to article size settings.
			// this will be necessary as soon as we allow custom image sizes for article image
			//$image = preg_replace( "/(.*)\/.*?$/", "$1/small.jpg", $pParamHash['image_storage_path'] );
			$image = basename( $pParamHash['image_storage_path'] )."/small.jpg";
			if( is_file( BIT_ROOT_PATH.$image ) ) {
				$ret =  BIT_ROOT_URL.$image;
			}
		} elseif( !empty( $pParamHash['has_topic_image'] ) && $pParamHash['has_topic_image'] == 'y' ) {
			$ret = BitArticleTopic::getTopicImageStorageUrl( $pParamHash['topic_id'] );
		}
		return $ret;
	}

	/**
	* Remove a custom article image - will result in the usage of the default image if a topic with image is selected
	* @param $pArticleId ID of the article that needs the image removed
	* @param $pImagePath path to the image that needs removing - generally used during preview - will override article id
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	* @access public
	**/
	function expungeImage( $pArticleId = NULL, $pImagePath = NULL ) {
		if( !empty( $pImagePath ) && is_file( $pImagePath ) && !@unlink( $pImagePath ) ) {
			$this->mErrors['remove_image'] = tra( 'The image could not be removed because we don\'t have the correct permission to do so.' );
		}

		if( empty( $pArticleId ) && $this->isValid() ) {
			$pArticleId = $this->mArticleId;
		}

		if( is_file( $image = BitArticle::getArticleImageStoragePath( $pArticleId ) ) ) {
			if( !@unlink( $image ) ) {
				$this->mErrors['remove_image'] = tra( 'The article image could not be removed because this article doesn\'t seem to have an image associated with it.' );
			}
		}
		return ( count( $this->mErrors ) == 0 );
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
			// remove article image if it exists
			$this->expungeImage();
			if( LibertyAttachable::expunge() ) {
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
	* @return array
	* ['data'] which contains all articles that match pParamHash conditions
	* ['cant'] which contains the number of articles that matched the pParamHash conditions
	* @access public
	**/
	function getList( &$pParamHash ) {
		global $gBitSystem, $gBitUser, $gLibertySystem;

/*
feature incomplete
		if ($gBitSystem->isFeatureActive('articles_auto_approve') && empty($pParamHash['no_update'])) {
			BitArticleStatistics::autoApprove();
		}
*/

		if( empty( $pParamHash['sort_mode'] ) ) {
			$pParamHash['sort_mode'] = $gBitSystem->isFeatureActive('articles_auto_approve') ? 'order_key_desc' : 'publish_date_desc';
		}

		LibertyContent::prepGetList( $pParamHash );

		$joinSql = '';
		$selectSql = '';
		$bindVars = array();
		array_push( $bindVars, $this->mContentTypeGuid );
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

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
			$whereSql .= " AND lc.`creator_user_id` = ? ";
			$bindVars[] = array( $pParamHash['user_id'] );
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
		if( empty( $pParamHash['show_expired'] ) ) {
			$timestamp = $gBitSystem->getUTCTime();
			$whereSql .= " AND a.`publish_date` < ? AND a.`expire_date` > ? ";
			$bindVars[] = ( int )$timestamp;
			$bindVars[] = ( int )$timestamp;
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

/*
feature incomplete
		if ($gBitSystem->isFeatureActive('articles_auto_approve')) {
			$as = new BitArticleStatistics();
			$obj = null;
			if (isset($this)) {
				$obj = $this;
			}
			$selectSql .= $as->getSQLRank($obj);
		}
*/

		// Oracle is very particular about naming multiple columns, so need to explicity name them ORA-00918: column ambiguously defined
		$query = "
			SELECT
				a.`article_id`, a.`description`, a.`author_name`, a.`image_attachment_id`, a.`publish_date`, a.`expire_date`, a.`rating`,
				atopic.`topic_id`, atopic.`topic_name`, atopic.`has_topic_image`, atopic.`active_topic`,
				astatus.`status_id`, astatus.`status_name`,
				lch.`hits`, lf.`storage_path` as `image_storage_path`,
				atype.*, lc.* $selectSql
			FROM `".BIT_DB_PREFIX."articles` a
				INNER JOIN      `".BIT_DB_PREFIX."liberty_content`       lc ON lc.`content_id`         = a.`content_id`
				INNER JOIN      `".BIT_DB_PREFIX."article_status`   astatus ON astatus.`status_id`     = a.`status_id`
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON lc.`content_id`         = lch.`content_id`
				LEFT OUTER JOIN `".BIT_DB_PREFIX."article_topics`    atopic ON atopic.`topic_id`       = a.`topic_id`
				LEFT OUTER JOIN `".BIT_DB_PREFIX."article_types`      atype ON atype.`article_type_id` = a.`article_type_id`
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments`   la ON la.`attachment_id`      = a.`image_attachment_id`
				LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files`         lf ON lf.`file_id`            = la.`foreign_id`  $joinSql
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY ".$this->mDb->convertSortmode( $pParamHash['sort_mode'] );

		$query_cant = "SELECT COUNT( * )FROM `".BIT_DB_PREFIX."articles` a
			INNER JOIN      `".BIT_DB_PREFIX."liberty_content`    lc ON lc.`content_id`   = a.`content_id`
			LEFT OUTER JOIN `".BIT_DB_PREFIX."article_topics` atopic ON atopic.`topic_id` = a.`topic_id` $joinSql
			WHERE lc.`content_type_guid` = ? $whereSql";

		$result = $this->mDb->query( $query, $bindVars, $pParamHash['max_records'], $pParamHash['offset'] );
		$ret = array();
		$comment = &new LibertyComment();
		while( $res = $result->fetchRow() ) {
			$res['image_url'] = BitArticle::getImageUrl( $res );

			// deal with the parsing
			$parseHash['format_guid']     = $res['format_guid'];
			$parseHash['content_id']      = $res['content_id'];
			$parseHash['cache_extension'] = 'desc';
			if( preg_match( ARTICLE_SPLIT_REGEX, $res['data'] ) ) {
				$res['man_split'] = TRUE;
				$parts = preg_split( ARTICLE_SPLIT_REGEX, $res['data'] );
				if( empty( $parts[1] ) ) {
					$res['has_more'] = FALSE;
				}
				$parseHash['data'] = $parts[0];
			} else {
				$parseHash['data'] = substr( $res['data'], 0, $gBitSystem->getConfig( 'articles_description_length' ) );
			}
			// description shouldn't contain {maketoc}
			$parseHash['data'] = preg_replace( "/\{maketoc[^\}]*\}/i", "", $parseHash['data'] );
			$res['parsed_description'] = $this->parseData( $parseHash );

			// this is needed to remove trailing stuff from the parser and insert a link to the actual article			
			$trailing_junk_pattern = "/(<br[^>]*>)*$/i";
			$res['parsed_description'] = preg_replace( $trailing_junk_pattern, "", $res['parsed_description'] );
			if( preg_replace( "/\{maketoc[^\}]*\}/i", "", $res['data'] ) != $parseHash['data'] && empty( $res['man_split'] )) {
				// we append ... when the split was generated automagically
				$res['parsed_description'] .= '&hellip;';
				$res['has_more'] = TRUE;
			} elseif( preg_replace( "/\{maketoc[^\}]*\}/i", "", $res['data'] ) != $parseHash['data'] ) {
				$res['has_more'] = TRUE;
			}

			$res['num_comments'] = $comment->getNumComments( $res['content_id'] );
			$res['display_url'] = $this->getDisplayUrl( $res['article_id'] );
			$res['display_link'] = $this->getDisplayLink( $res['title'], $res );
			$ret[] = $res;
		}

		$pParamHash["data"] = $ret;
		$pParamHash["cant"] = $this->mDb->getOne( $query_cant, $bindVars );

		LibertyContent::postGetList( $pParamHash );

		return $pParamHash;
	}

	/**
	* Generates the URL to the article
	* @return the link to the full article
	*/
	function getDisplayUrl( $pArticleId = NULL) {
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
