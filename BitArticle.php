<?php
/**
* $Header: /cvsroot/bitweaver/_bit_articles/BitArticle.php,v 1.1 2005/06/30 01:10:45 bitweaver Exp $
*
* Copyright (c) 2004 bitweaver.org
* Copyright (c) 2003 tikwiki.org
* Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
* All Rights Reserved. See copyright.txt for details and a complete list of authors.
* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
*
* $Id: BitArticle.php,v 1.1 2005/06/30 01:10:45 bitweaver Exp $
*/

/**
* Article class is used when accessing BitArticles. It is based on TikiSample
* and builds on core bitweaver functionality, such as the Liberty CMS engine.
*
* @date created 2004/8/15
*
* @author wolffy <wolff_borg@yahoo.com.au>
*
* @version $Revision: 1.1 $ $Date: 2005/06/30 01:10:45 $ $Author: bitweaver $
*
* @class BitArticle
*/

require_once(LIBERTY_PKG_PATH.'LibertyAttachable.php' );
require_once(ARTICLES_PKG_PATH.'BitArticleTopic.php' );
require_once(ARTICLES_PKG_PATH.'BitArticleType.php' );
require_once(LIBERTY_PKG_PATH.'LibertyComment.php' );

define('BITARTICLE_CONTENT_TYPE_GUID', 'bitarticle' );

define('ARTICLE_STATUS_DENIED', 0);
define('ARTICLE_STATUS_DRAFT', 100);
define('ARTICLE_STATUS_PENDING', 200);
define('ARTICLE_STATUS_APPROVED', 300);
define('ARTICLE_STATUS_RETIRED', 400);

define('DEFAULT_ARTICLE_DESCR_LEN', 512);
class BitArticle extends LibertyAttachable {
    /**
* Primary key for articles
* @public
*/
    var $mArticleId;
    var $mTypeId;
	var $mTopicId;
    /**
* During initialisation, be sure to call our base constructors
**/
    function BitArticle($pArticleId=NULL, $pContentId=NULL )
    {
        $this->mArticleId = $pArticleId;
		$this->mTypeId  = NULL;
		$this->mTopicId = NULL;
		
        LibertyAttachable::LibertyAttachable();
        $this->registerContentType(BITARTICLE_CONTENT_TYPE_GUID, array('content_description' => 'Article',
        'handler_class' => 'BitArticle',
        'handler_package' => 'articles',
        'handler_file' => 'BitArticle.php',
        'maintainer_url' => 'http://www.bitweaver.org'
        ) );
        $this->mContentId = $pContentId;
        $this->mContentTypeGuid = BITARTICLE_CONTENT_TYPE_GUID;
}

    /**
* Load the data from the database
* @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
**/
    function load() {
        if (!empty($this->mArticleId ) || !empty($this->mContentId ) ) {
            // LibertyContent::load() assumes you have joined already, and will not execute any sql!
            // This is a significant performance optimization
            $lookupColumn = !empty($this->mArticleId )? 'article_id' : 'content_id';
            $lookupId = !empty($this->mArticleId )? $this->mArticleId : $this->mContentId;
            $query = "select ta.*, tc.*, tatype.*, tatopic.*, " .
            "uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name, " .
            "uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name ," .
			"tf.storage_path as image_storage_path " .
            "FROM `".BIT_DB_PREFIX."tiki_articles` ta " .
			"LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_article_types` tatype ON (tatype.`article_type_id` = ta.`article_type_id`) ".
			"LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_article_topics` tatopic ON (tatopic.`topic_id` = ta.`topic_id`) ".
            "INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON (tc.`content_id` = ta.`content_id`) " .
            "LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON (uue.`user_id` = tc.`modifier_user_id`) " .
            "LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON (uuc.`user_id` = tc.`user_id`) " .
			"LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_attachments` tat ON (tat.attachment_id = ta.image_attachment_id) " .
			"LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_files` tf ON (tf.file_id = tat.foreign_id) " .
            "WHERE ta.`$lookupColumn`=?";
            $result = $this->query($query, array($lookupId ) );
            
            if ($result && $result->numRows() ) {
                $this->mInfo = $result->fields;
                $this->mContentId = $result->fields['content_id'];
                $this->mArticleId = $result->fields['article_id'];
                $this->mTopicId   = $result->fields['topic_id'];
				$this->mTypeId	  = $result->fields['article_type_id'];
				
                $this->mInfo['creator'] = (isset($result->fields['creator_real_name'] ) ? $result->fields['creator_real_name'] : $result->fields['creator_user'] );
                $this->mInfo['editor'] = (isset($result->fields['modifier_real_name'] ) ? $result->fields['modifier_real_name'] : $result->fields['modifier_user'] );
                $this->mInfo['display_url'] = $this->getDisplayUrl();
				$this->mInfo['parsed_data'] = $this->parseData($this->mInfo['data']);
				$this->mInfo['parsed_description'] = $this->parseData($this->mInfo['description']);

				global $gBitSystem;
                if ($gBitSystem->isPackageActive('categories' ) ) {
                    /*global $categlib;
                    $this->mInfo['categs'] = $categlib->get_object_categories_details('blogpost',$this->mInfo['post_id']);*/
                }
                
			$this->mInfo['publish_date_string'] = date('Y-m-d H:i', $this->mInfo['publish_date']);
			$this->mInfo['publish_year'] = date('Y', $this->mInfo['publish_date']);
			$this->mInfo['publish_month'] = date('m', $this->mInfo['publish_date']);
			$this->mInfo['publish_day'] = date('d', $this->mInfo['publish_date']);
			$this->mInfo['publish_hour'] = date('H', $this->mInfo['publish_date']);
			$this->mInfo['publish_minute'] = date('i', $this->mInfo['publish_date']);
			
			$this->mInfo['expire_date_string'] = date('Y-m-d H:i', $this->mInfo['expire_date']);
			$this->mInfo['expire_year'] = date('Y', $this->mInfo['expire_date']);
			$this->mInfo['expire_month'] = date('m', $this->mInfo['expire_date']);
			$this->mInfo['expire_day'] = date('d', $this->mInfo['expire_date']);
			$this->mInfo['expire_hour'] = date('H', $this->mInfo['expire_date']);
			$this->mInfo['expire_minute'] = date('i', $this->mInfo['expire_date']);
			$comment = &new LibertyComment();
			$this->mInfo['num_comments'] = $comment->getNumComments($this->mInfo['content_id']);	
                LibertyAttachable::load();
            }
        }
        return(count($this->mInfo ) );
    }
    
	function setStatus($pStatusId) {
		$validStatuses = array( ARTICLE_STATUS_DENIED, ARTICLE_STATUS_DRAFT, ARTICLE_STATUS_PENDING, ARTICLE_STATUS_APPROVED, ARTICLE_STATUS_RETIRED );
		
		if (!in_array($pStatusId, $validStatuses)) {
			$this->mErrors[] = "Invalid article status";
			return FALSE;
		}
		
		if (!empty($this->mArticleId)) {
			$sql = "UPDATE `".BIT_DB_PREFIX."tiki_articles` SET `status_id` = ? WHERE `article_id` = ?";
			$rs = $this->query($sql, array($pStatusId, $this->mArticleId));
			return $pStatusId;
		}
	}
	
    /**
* Any method named Store inherently implies data will be written to the database
* @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
* This is the ONLY method that should be called in order to store (create or update) an sample!
* It is very smart and will figure out what to do for you. It should be considered a black box.
*
* @param array pParams hash of values that will be used to store the page
*
* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
*
* @access public
**/
    function store(&$pParamHash )
    {
        if ($this->verify($pParamHash ) && LibertyAttachable::store($pParamHash ) ) {
            $table = BIT_DB_PREFIX."tiki_articles";
            $this->mDb->StartTrans();
            if ($this->mArticleId ) {
                $locId = array("name" => "article_id", "value" => $pParamHash['article_id'] );
                $result = $this->associateUpdate($table, $pParamHash['article_store'], $locId );
            } else {
                $pParamHash['article_store']['content_id'] = $pParamHash['content_id'];
                if (isset($pParamHash['article_id'] ) && is_numeric($pParamHash['article_id'] ) ) {
                    // if pParamHash['article_id'] is set, some is requesting a particular article_id. Use with caution!
                    $pParamHash['article_store']['article_id'] = $pParamHash['article_id'];
                } else {
                    $pParamHash['article_store']['article_id'] = $this->GenID('tiki_articles_article_id_seq');
                }
                $this->mArticleId = $pParamHash['article_store']['article_id'];
                
                $result = $this->associateInsert($table, $pParamHash['article_store'] );
            }
            
            
            $this->mDb->CompleteTrans();
            $this->load();
        }
        return(count($this->mErrors ) == 0 );
    }
    
    /**
* Make sure the data is safe to store
* @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
* This function is responsible for data integrity and validation before any operations are performed with the $pParamHash
* NOTE: This is a PRIVATE METHOD!!!! do not call outside this class, under penalty of death!
*
* @param array pParams reference to hash of values that will be used to store the page, they will be modified where necessary
*
* @return bool TRUE on success, FALSE if verify failed. If FALSE, $this->mErrors will have reason why
*
* @access private
**/
    function verify(&$pParamHash )
    {
        global $gBitUser, $gBitSystem;
        
        // make sure we're all loaded up of we have a mArticleId
        if ($this->mArticleId && empty($this->mInfo ) ) {
            $this->load();
        }
        
        if (!empty($this->mInfo['content_id'] ) ) {
            $pParamHash['content_id'] = $this->mInfo['content_id'];
        }
        
        // It is possible a derived class set this to something different
        if (empty($pParamHash['content_type_guid'] ) ) {
            $pParamHash['content_type_guid'] = $this->mContentTypeGuid;
        }
        
        if (!empty($pParamHash['content_id'] ) ) {
            $pParamHash['article_store']['content_id'] = $pParamHash['content_id'];
        }
        
		if (!empty($pParamHash['author_name']) ) {
			$pParamHash['article_store']['author_name'] = $pParamHash['author_name'];
		}
		
		if (!empty($pParamHash['topic_id']) ) {
			$pParamHash['article_store']['topic_id'] = (int)$pParamHash['topic_id'];
		}
		
		if (!empty($pParamHash['article_type_id']) ) {
			$pParamHash['article_store']['article_type_id'] = (int)$pParamHash['article_type_id'];
		}
		
		// check some lengths, if too long, then truncate
		/* DrewSlater - Killed article description storage. Any reason to use this instead of just a substr of the article body?
        if ($this->isValid() && !empty($this->mInfo['description'] ) && empty($pParamHash['description'] ) ) {
            // someone has deleted the description, we need to null it out
            $pParamHash['article_store']['description'] = '';
        } else if (empty($pParamHash['description'] ) ) {
            $bodyText = $pParamHash['content_store']['data'];
			$pParamHash['article_store']['description'] = substr($bodyText,0, DEFAULT_ARTICLE_DESCR_LEN).(strlen($bodyText) > DEFAULT_ARTICLE_DESCR_LEN ? '...' : '');
        } else {
            $pParamHash['article_store']['description'] = $pParamHash['description'];
        }*/
		
		if (!empty($pParamHash['rating'])) {
			$pParamHash['article_store']['rating'] = (int)($pParamHash['rating']);
		}
		
		if (!empty($_FILES['article_image'])) {
			// need to store the article image attachment
			
		}
		
        // check for name issues, first truncate length if too long
        if (!empty($pParamHash['title']) ) {
            if (empty($this->mArticleId ) ) {
                if (empty($pParamHash['title'] ) ) {
                    $this->mErrors['title'] = 'You must enter a name for this page.';
                } else {
                    $pParamHash['content_store']['title'] = substr($pParamHash['title'], 0, 160 );
                }
            } else {
                $pParamHash['content_store']['title'] = ( isset($pParamHash['title'] ) ) ? substr($pParamHash['title'], 0, 160 ) : '';
            }
        } else if (empty($pParamHash['title'] ) ) {
            // no name specified
            $this->mErrors['title'] = 'You must specify a name';
        }
		
		if (!empty($pParamHash['publish_Month'])) {
			$dateString = $pParamHash['publish_Year'].'-'.$pParamHash['publish_Month'].'-'.$pParamHash['publish_Day'].' '.$pParamHash['publish_Hour'].':'.$pParamHash['publish_Minute'];
			$timestamp = strtotime($dateString);
			if ($timestamp !== -1) {
				$pParamHash['publish_date'] = $timestamp;
			}
		}
		
		if (!empty($pParamHash['expire_Month'])) {
			$dateString = $pParamHash['expire_Year'].'-'.$pParamHash['expire_Month'].'-'.$pParamHash['expire_Day'].' '.$pParamHash['expire_Hour'].':'.$pParamHash['expire_Minute'];
			$timestamp = strtotime($dateString);
			if ($timestamp !== -1) {
				$pParamHash['expire_date'] = $timestamp;
			}
		}
		
		if (!empty($pParamHash['publish_date'])) {
			$pParamHash['article_store']['publish_date'] = $pParamHash['publish_date'];
		}
		if (!empty($pParamHash['expire_date'])) {
			$pParamHash['article_store']['expire_date'] = $pParamHash['expire_date'];
		}
		
		if (!empty($pParamHash['status_id'])) {
			if ($pParamHash['status_id'] > ARTICLE_STATUS_PENDING) {
				if ($gBitUser->hasPermission('bit_p_approve_submission') || 
					$gBitUser->hasPermission('bit_p_admin_received_articles') || 
					$gTikiuser->hasPermission('bit_p_autoapprove_submission') ) {
					$pParamHash['article_store']['status_id'] = (int)($pParamHash['status_id']);
				} else {
					$pParamHash['article_store']['status_id'] = ARTICLE_STATUS_PENDING;
				}
			} else {
				$pParamHash['article_store']['status_id'] = (int)($pParamHash['status_id']);
			}
		} elseif (!empty($this->mInfo['status_id'])) {
			$pParamHash['article_store']['status_id'] = $this->mInfo['status_id'];
		} else {
			if ($gBitUser->hasPermission('bit_p_approve_submission') || 
				$gBitUser->hasPermission('bit_p_admin_received_articles') || 
				$gBitUser->hasPermission('bit_p_autoapprove_submission') ) {
				$pParamHash['article_store']['status_id'] = ARTICLE_STATUS_APPROVED;
			} else {
				$pParamHash['article_store']['status_id'] = ARTICLE_STATUS_PENDING;		// Default status
			}
		}
        
        return(count($this->mErrors ) == 0 );
    }
    
	function viewerCanEdit() {
		global $gBitUser;
		
		if ($gBitUser->isAdmin() || $gContent->mUserId == $gBitUser->mUserId) {
			return TRUE;
		}
		
		return FALSE;	
	}
	
	function preparePreview($previewData) {
		global $gBitUser;
		
		$data = $previewData;
		$this->verify($data);
		$data = array_merge($data['content_store'], $data['article_store']);
		if (empty($data['user_id'])) {
			$data['user_id'] = $gBitUser->mUserId;
		}
		if (empty($data['hits'])) {
			$data['hits'] = 0;
		}
		if (empty($data['publish_date'])) {
			$data['publish_date'] = date('U');
		}
		if (empty($data['article_type_id'])) {
			$data['article_type_id'] = 1;
		}
		if (empty($data['topic_id'])) {
			$data['topic_id'] = 1;
		}
		if (empty($data['parsed_data'])) {
			$data['parsed_data'] = $this->parseData($data['data']);
		}
		
		$articleType = &new BitArticleType($data['article_type_id']);
		$articleTopic = &new BitArticleTopic($data['topic_id']);
		$data = array_merge($data, $articleType->mInfo, $articleTopic->mInfo);
		
		return $data;
		
	}
	
    function expunge()
    {
        $ret = FALSE;
        if ($this->isValid() ) {
           /* $this->mDb->StartTrans();
            $query = "DELETE FROM `".BIT_DB_PREFIX."tiki_samples` WHERE `content_id` = ?";
            $result = $this->query($query, array($this->mContentId ) );
            if (LibertyAttachable::expunge() ) {
                $ret = TRUE;
                $this->mDb->CompleteTrans();
            } else {
                $this->mDb->RollbackTrans();
            }*/
        }
        return $ret;
    }
    
    function isValid()
    {
        return(!empty($this->mArticleId ) );
    }
    
	/*function prepGetList(&$pParamHash) {
		
		parent::prepGetList($pParamHash);
	}*/
    /**
* This function generates a list of records from the tiki_content database for use in a list page
**/
    function getList(&$pParamHash ) {
        LibertyContent::prepGetList($pParamHash );
        
        $find = $pParamHash['find'];
        $sort_mode = $pParamHash['sort_mode'];
        $max_records = $pParamHash['max_records'];
        $offset = $pParamHash['offset'];
        
        if (is_array($find)) {
            // you can use an array of pages
            $mid = " WHERE tc.`title` IN (".implode(',',array_fill(0,count($find),'?')).")";
            $bindvars = $find;
        } else if (is_string($find)) {
            // or a string
            $mid = " WHERE UPPER(tc.`title`) like ? ";
            $bindvars = array( '%' . strtoupper( $find ) . '%' );
        } else if (!empty($pUserId ) ) {
            // or a string
            $mid = " WHERE tc.`creator_user_id` = ? ";
            $bindvars = array($pUserId );
        } else {
            $mid = "";
            $bindvars = array();
        }
        
		if (!empty($pParamHash['show_expired'])) {
			
		} else {
			$timestamp = date("");
			$artMid = " AND ta.`publish_date` < $timestamp AND ta.`expire_date` > $timestamp ";
		}
		
		if (!empty($pParamHash['status_id'])) {
			if ($mid == "") {
				$mid .= " WHERE ";
			} else {
				$mid .= " AND ";
			}
			$mid .= " ta.`status_id` = ? ";
			if (is_array($bindvars)) {
				$bindvars[] = $pParamHash['status_id'];
			} else {
				$bindvars = array($pParamHash['status_id']);
			}
		}
		
        $query = "SELECT ta.*, tc.`content_id`, tc.`title`, tc.`hits`, tc.`data`, top.* , type.*, tas.status_name
				  FROM `".BIT_DB_PREFIX."tiki_articles` ta 
				  	INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON (tc.`content_id` = ta.`content_id`)
					INNER JOIN `".BIT_DB_PREFIX."tiki_article_status` tas ON (tas.`status_id` = ta.`status_id`) 					
					LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_article_topics` top ON (top.topic_id = ta.topic_id) 
					LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_article_types` type ON (type.article_type_id = ta.article_type_id)
					".(!empty($mid ) ? $mid.' AND ' : ' WHERE ')." tc.`content_type_guid` = '".BITARTICLE_CONTENT_TYPE_GUID."'   
					ORDER BY ".$this->convert_sortmode($sort_mode);
		
        $query_cant = "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."tiki_articles` ta 
						INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON (tc.`content_id` = ta.`content_id`) ".
						(!empty($mid ) ? $mid.' AND ' : ' WHERE ')." tc.`content_type_guid` = '".BITARTICLE_CONTENT_TYPE_GUID."'";
        $result = $this->query($query,$bindvars,$max_records,$offset);
        $ret = array();
		$comment = &new LibertyComment();
        while ($res = $result->fetchRow()) {
			if ($res['image_attachment_id']) {
				$res['img_url'] = '';
			} elseif($res['has_topic_image'] == 'y') {
				$res['img_url'] = BitArticleTopic::getTopicImageStorageUrl($res['topic_id']);
			}
			$res['parsed_data'] = $this->parseData($res['data']);
			$res['parsed_description'] = $this->parseData($res['description']);
			$res['num_comments'] = $comment->getNumComments($res['content_id']);
            $ret[] = $res;
        }
        $pParamHash["data"] = $ret;
        
        $pParamHash["cant"] = $this->getOne($query_cant,$bindvars);
        
        LibertyContent::postGetList($pParamHash );
		
        return $pParamHash;
    }

    /**
* Generates the URL to the sample page
* @param pExistsHash the hash that was returned by LibertyContent::pageExists
* @return the link to display the page.
*/
    function getDisplayUrl()
    {
        $ret = NULL;
        if (!empty($this->mArticleId ) ) {
            $ret = ARTICLES_PKG_URL."read.php?article_id=".$this->mArticleId;
        }
        return $ret;
    }
    
    

    function listTypes() {
        $query = "SELECT * FROM `" . BIT_DB_PREFIX . "tiki_article_types`";
        $result = $this->query( $query, array() );
        $ret = array();

        while ( $res = $result->fetchRow() ) {
			$res['article_cnt'] = $this->getOne( "select count(*) from `" . BIT_DB_PREFIX . "tiki_articles` where `article_type_id` = ?", array( $res['article_type_id'] ) );
            $ret[] = $res;
        }

        return $ret;
    }
	
}
?>
