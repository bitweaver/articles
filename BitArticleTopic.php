<?php
require_once( KERNEL_PKG_PATH."BitBase.php" );
require_once( ARTICLES_PKG_PATH.'BitArticle.php' );

define( 'ARTICLE_TOPIC_THUMBNAIL_SIZE', 100 );

class BitArticleTopic extends BitBase
{
	var $mTopicId;

	function BitArticleTopic($iTopicId = NULL, $iTopicName = NULL) {
		$this->mTopicId = NULL;
		BitBase::BitBase();
		if ($iTopicId || $iTopicName) {
			$this->loadTopic(array('topic_id'=>$iTopicId, 'topic_name'=>$iTopicName));
		}
	}

	function isValid() {
		return (!empty($this->mTopicId));
	}

	function loadTopic($iParamHash = NULL) {
		$whereSQL = ' WHERE at.';
		$ret = NULL;

		if (!empty($iParamHash['topic_id']) || !empty($iParamHash['topic_name'])) {
			$whereSQL .= "`".((!empty($iParamHash['topic_id']) || $this->mTopicId) ? 'topic_id' : 'topic_name')."` = ?";
			$bindVars = array((!empty($iParamHash['topic_id']) ? (int)$iParamHash['topic_id'] : ($this->mTopicId ? $this->mTopicId : $iParamHash['topic_name'])) );

			$sql = "SELECT at.*".
				   "FROM `".BIT_DB_PREFIX."tiki_article_topics` at ".
			   	   $whereSQL;
			$rs = $this->mDb->query($sql, $bindVars);

			$this->mInfo = $rs->fields;
			$this->mTopicId = $this->mInfo['topic_id'];

			if ($this->mInfo['has_topic_image']) {
				$this->mInfo['topic_image_url'] = $this->getTopicImageStorageUrl(NULL, FALSE, TRUE);
			} else {
				$this->mInfo['topic_image_url'] = NULL;
			}
		}

		return $ret;
	}

	function verify(&$iParamHash) {
		// Validate the (optional) topic_id parameter
		if (!empty($iParamHash['topic_id'])) {
			$cleanHash['topic_id'] = (int)$iParamHash['topic_id'];
		} else {
			$cleanHash['topic_id'] = NULL;
		}

		// Was an acceptable name given?
		if (empty($iParamHash['topic_name']) || ($iParamHash['topic_name'] == '')) {
			$this->mErrors['topic_name'] = tra("Invalid or blank topic name supplied");
		} else {
			$cleanHash['topic_name'] = $iParamHash['topic_name'];
		}

		// Whether the topic is active or not
		if ( empty($iParamHash['active']) || (strtoupper($iParamHash['active']) != 'CHECKED' && strtoupper($iParamHash['active']) != 'ON' && strtoupper($iParamHash['active']) != 'Y')) {
			if (!empty($cleanHash['topic_id'])) {
				$cleanHash['active'] = 'n';
			} else {
				// Probably a new topic so lets go ahead and enable it
				$cleanHash['active'] = 'y';
			}
		} else {
			$cleanHash['active'] = 'y';
		}

		if (empty($iParamHash['created'])) {
			$cleanHash['created'] = date("U");
		}

		$iParamHash = $cleanHash;

		return(count($this->mErrors) == 0);
	}

	function storeTopic($iParamHash = NULL) {
		global $gLibertySystem;
		global $gBitUser;

		if ($this->verify($iParamHash)) {
			if (!$iParamHash['topic_id']) {
				$topicId = $this->mDb->GenID('tiki_article_topics_topic_id_seq');
			} else {
				$topicId = $this->mTopicId;
			}

			if (!empty($_FILES['upload']) && $_FILES['upload']['tmp_name']) {
				$tmpImagePath = $this->getTopicImageStoragePath($topicId, TRUE).$_FILES['upload']['name'];
				if (!move_uploaded_file($_FILES['upload']['tmp_name'], $tmpImagePath)) {
					$this->mErrors['topic_image'] = "Error during attachment of topic image";
				} else {
					global $gBitSystem;
					$resizeFunc = ($gBitSystem->getPreference( 'image_processor' ) == 'imagick' ) ? 'liberty_imagick_resize_image' : 'liberty_gd_resize_image';
					$pFileHash['dest_base_name'] = 'topic_'.$topicId;
					$pFileHash['max_width'] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
					$pFileHash['max_height'] = ARTICLE_TOPIC_THUMBNAIL_SIZE;
					$pFileHash['source_file'] = $tmpImagePath; //$this->getTopicImageStoragePath($newTopicId);
					$pFileHash['dest_path'] = '/storage/articles/';
					$pFileHash['type'] = $_FILES['upload']['type'];

					if (!($resizeFunc( $pFileHash ))) {
						$this->mErrors[] = 'Error while resizing topic image';
					}
					@unlink($tmpImagePath);
					$iParamHash['has_topic_image'] = 'y';
				}
			}

			if ($iParamHash['topic_id']) {
				$this->mDb->associateUpdate(BIT_DB_PREFIX."tiki_article_topics", $iParamHash, array('name' => 'topic_id', 'value'=> $iParamHash['topic_id']));
			} else {
				$iParamHash['topic_id'] = $topicId;
				$this->mDb->associateInsert(BIT_DB_PREFIX."tiki_article_topics", $iParamHash);
			}
		}
		$this->mTopicId = $iParamHash['topic_id'];
	}

	function getTopicImageStoragePath($iTopicId = NULL, $iBasePathOnly = FALSE) {
		$relativeUrl = BitArticleTopic::getTopicImageStorageUrl($iTopicId, $iBasePathOnly);
		$ret = NULL;
		if ($relativeUrl) {
			$ret = BIT_ROOT_PATH.$relativeUrl;
		}
		return $ret;
	}

	function getTopicImageStorageUrl($iTopicId = NULL, $iBasePathOnly = FALSE, $iForceRefresh = NULL) {
		if (!is_dir(BIT_ROOT_PATH.'/storage/articles')) {
			mkdir(BIT_ROOT_PATH.'/storage/articles');
		}

		if ($iBasePathOnly) {
			return '/storage/articles';
		}

		$ret = NULL;
		if (!$iTopicId) {
			if ($this->mTopicId) {
				$iTopicId = $this->mTopicId;
			} else {
				return NULL;
			}
		}

		return '/storage/articles/topic_'.$iTopicId.'.jpg'.($iForceRefresh ? "?".date('U') : '');
	}

	function listTopics() {
		global $gBitSystem;

        $query = "SELECT tat.* " .
				 "FROM `".BIT_DB_PREFIX."tiki_article_topics` tat " .
				 "ORDER BY tat.`topic_name`";

		$result = $gBitSystem->mDb->query( $query, array() );

        $ret = array();

        while ( $res = $result->fetchRow() ) {
			$res["num_articles"] = $gBitSystem->mDb->getOne( "SELECT COUNT(*) ".
												  "FROM `" . BIT_DB_PREFIX . "tiki_articles` ".
												  "WHERE `topic_id`= ?", array( $res["topic_id"] ) );
			if ($res['has_topic_image'] == 'y') {
				$res['topic_image_url'] = BitArticleTopic::getTopicImageStorageUrl($res['topic_id']);
			}

            $ret[] = $res;
        }

        return $ret;
    }

	function removeTopicImage() {
		if ($this->mTopicId) {
			if (file_exists($this->getTopicImageStoragePath())) {
				@unlink($this->getTopicImageStoragePath());
			}
			$sql = "UPDATE `".BIT_DB_PREFIX."tiki_article_topics` SET `has_topic_image` = 'n' WHERE `topic_id` = ?";
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
		$sql = "UPDATE `".BIT_DB_PREFIX."tiki_article_topics` SET `active` = '".($iIsActive ? 'y' : 'n')."' WHERE `topic_id` = ?";
		$rs = $this->mDb->query($sql, array($this->mTopicId));
		$this->mInfo['active'] = ($iIsActive ? 'y' : 'n');
	}

	function getTopicArticles() {
		if (!$this->mTopicId) {
			return NULL;
		}

		$sql = "SELECT `article_id` FROM `".BIT_DB_PREFIX."tiki_articles` WHERE `topic_id` = ?";
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
			$sql = "UPDATE `".BIT_DB_PREFIX."tiki_articles` SET `topic_id` = ? WHERE `topic_id` = ?";
			$rs = $this->mDb->query($sql, array(NULL, $this->mTopicId));
		}

		$sql = "DELETE FROM `".BIT_DB_PREFIX."tiki_article_topics` WHERE `topic_id` = ?";
		$rs = $this->mDb->query($sql, array($this->mTopicId));
	}
}

?>
