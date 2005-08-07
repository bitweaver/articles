<?php
require_once (KERNEL_PKG_PATH."BitBase.php");
require_once(ARTICLES_PKG_PATH.'BitArticle.php');

class BitArticleType extends BitBase
{
	var $mTypeId;

	function BitArticleType($iTypeId = NULL) {
		$this->mTypeId = NULL;
		BitBase::BitBase();
		if ($iTypeId) {
			$this->loadType($iTypeId);
		}
	}
	
	function isValid() {
		return (!empty($this->mTypeId));
	}
	
	function loadType($iTypeId) {
		$ret = NULL;
		
		if (!$this->mTypeId) {
			$this->mTypeId = $iTypeId;
		}
		
		if ($this->mTypeId) {
			$sql = "SELECT * FROM `".BIT_DB_PREFIX."tiki_article_types` WHERE `article_type_id` = ?";
			$rs = $this->mDb->query($sql, array($this->mTypeId));
			
			$ret = array();			
			if (!empty($rs->fields)) {
				$ret = $rs->fields;
				$ret['num_articles'] = $this->mDb->getOne('SELECT COUNT(*) FROM `'.BIT_DB_PREFIX.'tiki_articles` WHERE `article_type_id` = ?', array($ret['article_type_id']));
			}
		}
		$this->mInfo = $ret;
		
		return $ret;
	}

	function verify(&$iParamHash) {
		$isNewType = FALSE;
		
		// Validate the (optional) topic_id parameter
		if (!empty($iParamHash['article_type_id'])) {
			$cleanHash['article_type_id'] = (int)$iParamHash['article_type_id'];
		} else {
			$isNewType = TRUE;
			$cleanHash['article_type_id'] = NULL;
		}
		
		if (!$isNewType) { 
			$cleanHash['use_ratings'] 		= (!empty($iParamHash['use_ratings']) ? ($iParamHash['use_ratings']) : 'n');
			$cleanHash['show_pre_publ'] 	= (!empty($iParamHash['show_pre_publ']) ? ($iParamHash['show_pre_publ']) : 'n');
			$cleanHash['show_post_expire'] 	= (!empty($iParamHash['show_post_expire']) ? ($iParamHash['show_post_expire']) : 'n');
			$cleanHash['heading_only'] 		= (!empty($iParamHash['heading_only']) ? ($iParamHash['heading_only']) : 'n');
			$cleanHash['allow_comments'] 	= (!empty($iParamHash['allow_comments']) ? ($iParamHash['allow_comments']) : 'n');
			$cleanHash['comment_can_rate_article'] = (!empty($iParamHash['comment_can_rate_article']) ? ($iParamHash['comment_can_rate_article']) : 'n');
			$cleanHash['show_image'] 		= (!empty($iParamHash['show_image']) ? ($iParamHash['show_image']) : 'n');
			$cleanHash['show_avatar'] 		= (!empty($iParamHash['show_avatar']) ? ($iParamHash['show_avatar']) : 'n');
			$cleanHash['show_author']		= (!empty($iParamHash['show_author']) ? ($iParamHash['show_author']) : 'n');
			$cleanHash['show_pubdate'] 		= (!empty($iParamHash['show_pubdate']) ? ($iParamHash['show_pubdate']) : 'n');			
			$cleanHash['show_expdate'] 		= (!empty($iParamHash['show_expdate']) ? ($iParamHash['show_expdate']) : 'n');
			$cleanHash['show_reads'] 		= (!empty($iParamHash['show_reads']) ? ($iParamHash['show_reads']) : 'n');
			$cleanHash['show_size'] 		= (!empty($iParamHash['show_size']) ? ($iParamHash['show_size']) : 'n');
			$cleanHash['creator_edit'] 		= (!empty($iParamHash['creator_edit']) ? ($iParamHash['creator_edit']) : 'n');
			$topicName = (!empty($iParamHash['topic_name']) ? $iParamHash['topic_name'] : NULL);
			if ($topicName) {
				$cleanHash['topic_name'] = $topicName;
			}
		} else {		
			// Was an acceptable name given?
			if (empty($iParamHash['type_name']) || ($iParamHash['type_name'] == '')) {
				$this->mErrors['type_name'] = tra("Invalid or blank article type name supplied");
			} else {	
				$cleanHash['type_name'] = $iParamHash['type_name'];
			}
		}
		
		$iParamHash = $cleanHash;	
		return(count($this->mErrors) == 0);
	}
	
	function storeType(&$iParamHash) {
		global $gLibertySystem;
		global $gBitUser;
		
		if ($this->verify($iParamHash)) {
			if (!$iParamHash['article_type_id']) {
				if (empty($this->mTopicId)) {
					$typeId = $this->mDb->GenID('tiki_article_types_article_type_id_seq');
				} else {
					$typeId = $this->mTopicId;
				}
			} else {
				$typeId = $iParamHash['article_type_id'];
			}
			
			if ($iParamHash['article_type_id']) {
				$this->mDb->associateUpdate(BIT_DB_PREFIX."tiki_article_types", $iParamHash, array('name' => 'article_type_id', 'value'=> $iParamHash['article_type_id']));				
			} else {
				$iParamHash['article_type_id'] = $typeId;
				$this->mDb->associateInsert(BIT_DB_PREFIX."tiki_article_types", $iParamHash);	
			}			
		}
		$this->mTypeId = $iParamHash['article_type_id'];
	}
		
	function removeType($iTypeId = NULL) {
		if (!$iTypeId) {
		 	if (!$this->mTypeId) {
				$this->mErrors[] = tra("Invalid type id given");
				return NULL;
			} else {
				$iTypeId = $this->mTypeId;
			}			
		} else {
			$iTypeId = (int)($iTypeId);
		}
		
		$sql = "DELETE FROM `".BIT_DB_PREFIX."tiki_article_types` WHERE `article_type_id` = ?";
		$rs = $this->mDb->query($sql, array($iTypeId));
	}
	
	function listTypes() {
		global $gBitSystem;
		
		$query = "SELECT * FROM `" . BIT_DB_PREFIX . "tiki_article_types`";
        $result = $gBitSystem->query( $query, array() );
        $ret = array();

        while ( $res = $result->fetchRow() ) {
			$res['article_cnt'] = $gBitSystem->getOne( "select count(*) from `" . BIT_DB_PREFIX . "tiki_articles` where `article_type_id` = ?", array( $res['article_type_id'] ) );
            $ret[] = $res;
        }

        return $ret;
	}
}
