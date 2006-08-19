<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_articles/BitArticleTopic.php,v 1.27 2006/08/19 20:34:26 sylvieg Exp $
 * @package article
 */

/**
 * Required setup
 */
global $gBitSystem;
require_once( KERNEL_PKG_PATH."BitBase.php" );
require_once( ARTICLES_PKG_PATH.'BitArticle.php' );

define( 'ARTICLE_TOPIC_THUMBNAIL_SIZE', $gBitSystem->getConfig( 'article_topic_thumbnail_size', 160 ) );

/**
 * @package article
 */
class BitArticleTopic extends BitBase {
	var $mTopicId;

	function BitArticleTopic($iTopicId = NULL, $iTopicName = NULL) {
		$this->mTopicId = NULL;
		BitBase::BitBase();
		if ($iTopicId || $iTopicName) {
			$this->loadTopic(array('topic_id'=>$iTopicId, 'topic_name'=>$iTopicName));
		}
	}

	function isValid() {
		return ($this->verifyId($this->mTopicId));
	}

	function loadTopic($iParamHash = NULL) {
		$whereSQL = ' WHERE at.';
		$ret = NULL;

		if (@$this->verifyId($iParamHash['topic_id']) || !empty($iParamHash['topic_name'])) {
			$whereSQL .= "`".((@$this->verifyId($iParamHash['topic_id']) || $this->mTopicId) ? 'topic_id' : 'topic_name')."` = ?";
			$bindVars = array((@$this->verifyId($iParamHash['topic_id']) ? (int)$iParamHash['topic_id'] : ($this->mTopicId ? $this->mTopicId : $iParamHash['topic_name'])) );

			$sql = "SELECT at.*".
				   "FROM `".BIT_DB_PREFIX."article_topics` at ".
			   	   $whereSQL;
			$this->mInfo = $this->mDb->getRow($sql, $bindVars);

			if( !empty( $this->mInfo['topic_id'] ) ) {
				$this->mTopicId = $this->mInfo['topic_id'];

				if ($this->mInfo['has_topic_image']) {
					$this->mInfo['topic_image_url'] = $this->getTopicImageStorageUrl(NULL, FALSE, TRUE);
				} else {
					$this->mInfo['topic_image_url'] = NULL;
				}
			}
		}

		return $ret;
	}

	function verify(&$iParamHash) {
		// Validate the (optional) topic_id parameter
		if (@$this->verifyId($iParamHash['topic_id'])) {
			$cleanHash['topic_id'] = (int)$iParamHash['topic_id'];
		} else {
			$cleanHash['topic_id'] = NULL;
		}

		// Was an acceptable name given?
		if (empty($iParamHash['topic_name']) || ($iParamHash['topic_name'] == '')) {
			$this->mErrors['topic_name'] = tra("Invalid or blank topic name supplied");
		} else {
			$ret = $this->getTopicList( array( 'topic_name' => $iParamHash['topic_name'] ) );
			if ( sizeof( $ret ) ) {
				$this->mErrors['topic_name'] = 'Topic "'.$iParamHash['topic_name'].'" already exists. Please choose a different name.';
			} else {
				$cleanHash['topic_name'] = $iParamHash['topic_name'];
			}
		}

		// Whether the topic is active or not
		if ( empty($iParamHash['active_topic']) || (strtoupper($iParamHash['active_topic']) != 'CHECKED' && strtoupper($iParamHash['active_topic']) != 'ON' && strtoupper($iParamHash['active_topic']) != 'Y')) {
			if (@$this->verifyId($cleanHash['topic_id'])) {
				$cleanHash['active_topic'] = 'n';
			} else {
				// Probably a new topic so lets go ahead and enable it
				$cleanHash['active_topic'] = 'y';
			}
		} else {
			$cleanHash['active_topic'] = 'y';
		}

		if (empty($iParamHash['created'])) {
			global $gBitSystem;
			$cleanHash['created'] = $gBitSystem->getUTCTime();
		}

		$iParamHash = $cleanHash;

		return(count($this->mErrors) == 0);
	}

	function storeTopic($iParamHash = NULL) {
		global $gLibertySystem;
		global $gBitUser;

		if ($this->verify($iParamHash)) {
			if (!$iParamHash['topic_id']) {
				$topicId = $this->mDb->GenID('articles_topics_id_seq');
			} else {
				$topicId = $this->mTopicId;
			}

			if( !empty( $_FILES['upload'] ) && $_FILES['upload']['tmp_name'] ) {
				$tmpImagePath = $this->getTopicImageStoragePath( $topicId, TRUE).$_FILES['upload']['name'];
				global $gBitSystem;
				$pFileHash = $_FILES['upload'];
				$resizeFunc = ($gBitSystem->getConfig( 'image_processor' ) == 'magickwand' ) ? 'liberty_magickwand_resize_image' : 'liberty_gd_resize_image';
				$pFileHash['max_width'] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
				$pFileHash['max_height'] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
				$pFileHash['source_file'] = $pFileHash['tmp_name'];
				$pFileHash['dest_path'] = STORAGE_PKG_NAME.'/'.ARTICLES_PKG_NAME.'/';
				$pFileHash['dest_base_name'] = 'topic_'.$topicId;

				if( !( $resizeFunc( $pFileHash ) ) ) {
					$this->mErrors[] = 'Error while resizing topic image';
				}
				@unlink( $tmpImagePath );
				$iParamHash['has_topic_image'] = 'y';
			}

			if( $iParamHash['topic_id'] ) {
				$this->mDb->associateUpdate( BIT_DB_PREFIX."article_topics", $iParamHash, array( 'topic_id' => $iParamHash['topic_id'] ) );
			} else {
				$iParamHash['topic_id'] = $topicId;
				$this->mDb->associateInsert( BIT_DB_PREFIX."article_topics", $iParamHash );
			}
		}
		$this->mTopicId = $iParamHash['topic_id'];
	}

	function getTopicImageStoragePath( $iTopicId = NULL, $iBasePathOnly = FALSE ) {
		$relativeUrl = BitArticleTopic::getTopicImageStorageUrl( $iTopicId, $iBasePathOnly );
		$ret = NULL;
		if( $relativeUrl ) {
			$ret = BIT_ROOT_PATH.$relativeUrl;
		}
		return $ret;
	}

	function getTopicImageStorageUrl( $iTopicId = NULL, $iBasePathOnly = FALSE, $iForceRefresh = NULL ) {
		global $gBitSystem;
		if( !is_dir( STORAGE_PKG_PATH.ARTICLES_PKG_NAME ) ) {
			mkdir( STORAGE_PKG_PATH.ARTICLES_PKG_NAME );
		}

		if( $iBasePathOnly ) {
			return STORAGE_PKG_NAME.'/'.ARTICLES_PKG_NAME.'/';
		}

		$ret = NULL;
		if( !$iTopicId ) {
			if( $this->mTopicId ) {
				$iTopicId = $this->mTopicId;
			} else {
				return NULL;
			}
		}

		return STORAGE_PKG_URL.ARTICLES_PKG_NAME.'/topic_'.$iTopicId.'.jpg'.($iForceRefresh ? "?".$gBitSystem->getUTCTime() : '');
	}

	function getTopicList( $pOptionHash=NULL ) {
		global $gBitSystem;

		$where = '';
		$bindVars = array();
		if( !empty( $pOptionHash['active_topic'] ) ) {
			$where = " WHERE la.`active_topic` = 'y' ";
		}
		if ( !empty(  $pOptionHash['topic_name'] ) ) {
			$where = " WHERE la.`topic_name` = ? ";
			$bindVars[] = $pOptionHash['topic_name'];
		}

		$query = "SELECT la.*
				 FROM `".BIT_DB_PREFIX."article_topics` la
				 $where ORDER BY la.`topic_name`";

		$result = $gBitSystem->mDb->query( $query, $bindVars );

        $ret = array();

        while( $res = $result->fetchRow() ) {
			$res["num_articles"] = $gBitSystem->mDb->getOne( "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."articles` WHERE `topic_id`= ?", array( $res["topic_id"] ) );
			if( $res['has_topic_image'] == 'y' ) {
				$res['topic_image_url'] = BitArticleTopic::getTopicImageStorageUrl( $res['topic_id'] );
			}

            $ret[] = $res;
        }

        return $ret;
    }

	function removeTopicImage() {
		if( $this->mTopicId ) {
			if( file_exists($this->getTopicImageStoragePath() ) ) {
				@unlink( $this->getTopicImageStoragePath() );
			}
			$sql = "UPDATE `".BIT_DB_PREFIX."article_topics` SET `has_topic_image` = 'n' WHERE `topic_id` = ?";
			$rs = $this->mDb->query($sql, array($this->mTopicId));
			$this->mInfo['has_topic_image'] = 'n';
		}
	}

	function activateTopic() {
		$this->setActivation(TRUE);
	}

	function deactivateTopic() {
		$this->setActivation(FALSE);
	}

	function setActivation($iIsActive = FALSE) {
		$sql = "UPDATE `".BIT_DB_PREFIX."article_topics` SET `active_topic` = '".($iIsActive ? 'y' : 'n')."' WHERE `topic_id` = ?";
		$rs = $this->mDb->query($sql, array($this->mTopicId));
		$this->mInfo['active_topic'] = ($iIsActive ? 'y' : 'n');
	}

	function getTopicArticles() {
		if (!$this->mTopicId) {
			return NULL;
		}

		$sql = "SELECT `article_id` FROM `".BIT_DB_PREFIX."articles` WHERE `topic_id` = ?";
		$rs = $this->mDb->query($sql, array($this->mTopicId));

		$ret = array();
		while ($row = $rs->fetchRow()) {
			$tmpArticle = new BitArticle($row['article_id']);
			$tmpArticle->load();
			$ret[] = $tmpArticle;
		}
	}

	function removeTopic($iRemoveArticles = FALSE) {
		if (!$this->mTopicId) {
			return NULL;
		}

		$this->removeTopicImage();

		if ($iRemoveArticles == TRUE) {
			$topicArticles = $this->getTopicArticles();
			for ($articleCount = 0; $articleCount < count($topicArticles); $articleCount++) {
				$topicArticles[$articleCount]->expunge();
			}
		} else {
			$sql = "UPDATE `".BIT_DB_PREFIX."articles` SET `topic_id` = ? WHERE `topic_id` = ?";
			$rs = $this->mDb->query($sql, array(NULL, $this->mTopicId));
		}

		$sql = "DELETE FROM `".BIT_DB_PREFIX."article_topics` WHERE `topic_id` = ?";
		$rs = $this->mDb->query($sql, array($this->mTopicId));
	}

}

?>
