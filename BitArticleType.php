<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/BitArticleType.php,v 1.23 2009/10/01 14:16:57 wjames5 Exp $
 * @package articles
 *
 * @copyright Copyright (c) 2004-2006, bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 */

/**
 * Required setup
 */
require_once (KERNEL_PKG_PATH."BitBase.php");
require_once(ARTICLES_PKG_PATH.'BitArticle.php');

/**
 * @package articles
 */
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
		return (@BitBase::verifyId($this->mTypeId));
	}

	function loadType($iTypeId) {
		$ret = NULL;

		if (!$this->mTypeId) {
			$this->mTypeId = $iTypeId;
		}

		if ($this->mTypeId) {
			$sql = "SELECT * FROM `".BIT_DB_PREFIX."article_types` WHERE `article_type_id` = ?";

			if( $ret = $this->mDb->getRow( $sql, array( $this->mTypeId ) ) ) {
				$ret['num_articles'] = $this->mDb->getOne('SELECT COUNT(*) FROM `'.BIT_DB_PREFIX.'articles` WHERE `article_type_id` = ?', array($ret['article_type_id']));
			}
		}
		$this->mInfo = $ret;

		return $ret;
	}

	function verify(&$iParamHash) {
		$isNewType = FALSE;

		// Validate the (optional) topic_id parameter
		if (@BitBase::verifyId($iParamHash['article_type_id'])) {
			$cleanHash['article_type_id'] = (int)$iParamHash['article_type_id'];
		} else {
			$isNewType = TRUE;
			$cleanHash['article_type_id'] = NULL;
		}

		if (!$isNewType) {
			$cleanHash['use_ratings']              = (!empty($iParamHash['use_ratings'])              ? ($iParamHash['use_ratings'])              : 'n');
			$cleanHash['show_pre_publ']            = (!empty($iParamHash['show_pre_publ'])            ? ($iParamHash['show_pre_publ'])            : 'n');
			$cleanHash['show_post_expire']         = (!empty($iParamHash['show_post_expire'])         ? ($iParamHash['show_post_expire'])         : 'n');
			$cleanHash['heading_only']             = (!empty($iParamHash['heading_only'])             ? ($iParamHash['heading_only'])             : 'n');
			$cleanHash['allow_comments']           = (!empty($iParamHash['allow_comments'])           ? ($iParamHash['allow_comments'])           : 'n');
			$cleanHash['comment_can_rate_article'] = (!empty($iParamHash['comment_can_rate_article']) ? ($iParamHash['comment_can_rate_article']) : 'n');
			$cleanHash['show_image']               = (!empty($iParamHash['show_image'])               ? ($iParamHash['show_image'])               : 'n');
			$cleanHash['show_avatar']              = (!empty($iParamHash['show_avatar'])              ? ($iParamHash['show_avatar'])              : 'n');
			$cleanHash['show_author']              = (!empty($iParamHash['show_author'])              ? ($iParamHash['show_author'])              : 'n');
			$cleanHash['show_pubdate']             = (!empty($iParamHash['show_pubdate'])             ? ($iParamHash['show_pubdate'])             : 'n');
			$cleanHash['show_expdate']             = (!empty($iParamHash['show_expdate'])             ? ($iParamHash['show_expdate'])             : 'n');
			$cleanHash['show_reads']               = (!empty($iParamHash['show_reads'])               ? ($iParamHash['show_reads'])               : 'n');
			$cleanHash['show_size']                = (!empty($iParamHash['show_size'])                ? ($iParamHash['show_size'])                : 'n');
			$cleanHash['creator_edit']             = (!empty($iParamHash['creator_edit'])             ? ($iParamHash['creator_edit'])             : 'n');
			$cleanHash['type_name']                = (!empty($iParamHash['type_name'])                ? ($iParamHash['type_name'])                : NULL);
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
					$typeId = $this->mDb->GenID('article_types_a_t_id_seq');
				} else {
					$typeId = $this->mTopicId;
				}
			} else {
				$typeId = $iParamHash['article_type_id'];
			}

			if ($iParamHash['article_type_id']) {
				$this->mDb->associateUpdate(BIT_DB_PREFIX."article_types", $iParamHash, array( 'article_type_id'=> $iParamHash['article_type_id']));
			} else {
				$iParamHash['article_type_id'] = $typeId;
				$this->mDb->associateInsert(BIT_DB_PREFIX."article_types", $iParamHash);
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

		$sql = "DELETE FROM `".BIT_DB_PREFIX."article_types` WHERE `article_type_id` = ?";
		$rs = $this->mDb->query($sql, array($iTypeId));
	}

	function getTypeList() {
		global $gBitSystem;

		$query = "SELECT * FROM `" . BIT_DB_PREFIX . "article_types`";
		$result = $gBitSystem->mDb->query( $query, array() );
        $ret = array();

        while ( $res = $result->fetchRow() ) {
			$res['article_cnt'] = $gBitSystem->mDb->getOne( "select count(*) from `" . BIT_DB_PREFIX . "articles` where `article_type_id` = ?", array( $res['article_type_id'] ) );
            $ret[] = $res;
        }

        return $ret;
	}
}
