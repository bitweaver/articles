<?php
// Initialization
require_once( ARTICLES_PKG_PATH.'BitArticle.php' );

class BitArticleStatistics {

	function BitArticleStatistics() {

	}

	/**
	 * Enter description here...
	 *
	 * @param string $basefield
	 * @param string $data
	 * @param integer $size
	 */
	function spiltEncodeData($basefield, $data, $size = 200) {
		global $gBitSystem;
		$str_A = str_split($data,$size);
		if (count($str_A)>0) {
			$gBitSystem->storeConfig($basefield."_size", count($str_A));
			foreach ($str_A as $i => $part) {
				$gBitSystem->storeConfig($basefield."_".$i, $part);
			}
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param string $basefield
	 * @param string $default
	 * @return string
	 */
	function splitDecodeData($basefield,$default='') {
		global $gBitSystem;
		$len = intval( $gBitSystem->getConfig($basefield."_size",'0'));
		if ($len==0) {
			return $default;
		}
		$data ='';
		for ($i = 0; $i < $len; $i++) {
			$data .= $gBitSystem->getConfig($basefield."_".$i, '');
		}
		return $data;
	}

	/**
	 * Enter description here...
	 *
	 * @param boolean $pForce
	 * @param array $paramHash
	 * @return array
	 */
	function load($pForce=false,$paramHash=null) {
		static $info = null;
		if (empty($info) || $pForce) {
			if (empty($paramHash)) {
				$info = eval('return '.$this->splitDecodeData('articles_statistics','array()').';');
			} else {
				$info = $paramHash;
			}
		}
		return $info;
	}

	/**
	 * Enter description here...
	 *
	 * @param array $paramHash
	 */
	function store(&$paramHash) {
		$this->spiltEncodeData('articles_statistics',str_replace(array(' ',"\n"),"",var_export($paramHash,true)));
		$this->load(true,$paramHash);
	}

	/**
	 * Enter description here...
	 *
	 */
	function conditionalUpdate() {
		$d = $this->load();
		if (empty($d) || ($d['t']+$d['mt'])<time()) {
			$this->update();
		}
	}

	/**
	 * Enter description here...
	 *
	 * @return integer
	 */
	function update() {
		global $gLibertySystem;
		$t = microtime(true);
		$time = time();

		$d = $this->load();

		if (!empty($d['mt'])) {
			$min_c_time = $time-($d['mt']*10); // ~last 10 entries or 6hours which ever is the longer
			if ($min_c_time<21600) {
				$min_c_time=21600;
			}
		} else {
			$min_c_time= 0;
		}

		$rating_vars = $gLibertySystem->invokeServiceFunctionR('content_get_rating_field_function',array(null,false,false,false));
		$rating_count_vars = $gLibertySystem->invokeServiceFunctionR('content_get_rating_field_function',array(null,false,true,false));

		$pListHash = array('max_records'=>-1,'content_type_guid'=>BITARTICLE_CONTENT_TYPE_GUID, 'sort_mode'=>'created_desc');
		$obj = new LibertyContent();//BitArticle();
		$l = $obj->getContentList($pListHash);
		$l=$l['data'];

		$last_t = $time;
		$info = array();
		foreach ($l as $i) {
			{
				$dt = $last_t-$i['created'];
				if ($dt<0) $dt = -$dt;
				if (empty($info['t']['count'])) {$info['mt']['sum'] =0; $info['mt']['count']=0;}
				$info['mt']['sum'] = $info['mt']['sum'] + $dt;
				$info['mt']['count'] ++;
			}
			if ($i['created']>$min_c_time) {
				foreach (array_keys($rating_vars) as $serv) {
					$rateing = $i[$rating_vars[$serv]];
					$rate_count = null;
					if (!empty($rating_count_vars[$serv])) {
						$rate_count = $i[$rating_count_vars[$serv]];
					}

					if (!empty($rateing)) {
						if (empty($info['r'][$serv]['r']['count'])) { $info['r'][$serv]['r']['count']=0; $info['r'][$serv]['r']['sum']=0;}
						$info['r'][$serv]['r']['sum'] = $info['r'][$serv]['r']['sum']+$rateing;
						$info['r'][$serv]['r']['count'] ++;
					}
					if (!empty($rate_count)) {
						if (empty($info['r'][$serv]['r_c']['count'])) { $info['r'][$serv]['r_c']['count']=0; $info['r'][$serv]['r_c']['sum']=0;}
						$info['r'][$serv]['r_c']['sum'] = $info['r'][$serv]['r_c']['sum']+$rate_count;
						$info['r'][$serv]['r_c']['count'] ++;
					}
				}

				if (!empty($i['last_hit'])) {
					$hr = $i['hits']/($i['last_hit']/60);
					if (empty($info['hr']['count'])) {$info['hr']['sum'] =0; $info['hr']['count']=0;}
					$info['hr']['sum'] = $info['hr']['sum']+$hr;
					$info['hr']['count'] ++;
				}
				if (!empty($i['created'])) {
					$age =  $time - $i['created'];
					if (empty($info['age']['count'])) {$info['age']['sum'] =0; $info['age']['count']=0;}
					$info['age']['sum'] = $info['age']['sum'] + $age;
					$info['age']['count'] ++;
				}
			}
		}

		foreach (array_keys($rating_vars) as $serv) {
			if (!empty($info['r'][$serv]['r']['count'])) {
				$info['r'][$serv]['r']=$info['r'][$serv]['r']['sum']/$info['r'][$serv]['r']['count'];
			}
			if (!empty($info['r'][$serv]['r_c']['count'])) {
				$info['r'][$serv]['r_c']=$info['r'][$serv]['r_c']['sum']/$info['r'][$serv]['r_c']['count'];
			}
		}

		if (!empty($info['mt']['count'])) {
			$info['mt']=$info['mt']['sum']/$info['mt']['count'];
		}
		if (!empty($info['age']['count'])) {
			$info['age']=$info['age']['sum']/$info['age']['count'];
		}
		if (!empty($info['hr']['count'])) {
			$info['hr']=round($info['hr']['sum']/$info['hr']['count'],3);
		}

		$info['t']=$time;

		$this->store($info);
		return (microtime(true)-$t);
	}

	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	function getStatistics(){
		$this->conditionalUpdate();
		return $this->load();
	}

	/*function rankContent(&$pObjHash) {
	$rating_vars = $gLibertySystem->invokeServiceFunctionR('content_get_rating_field_function',array(false,false,false));
	$rating_count_vars = $gLibertySystem->invokeServiceFunctionR('content_get_rating_field_function',array(false,true,false));
	foreach (array_keys($rating_vars) as $serv) {
	$rateing = intval($pObjHash[$rating_vars[$serv]]);
	$rate_count = 0;
	if (!empty($rating_count_vars[$serv])) {
	$rate_count = intval($pObjHash[$rating_count_vars[$serv]]);
	}


	}
	}*/

	/**
	 * Enter description here...
	 *
	 * @param array $pObj
	 * @return string
	 */
	function getSQLRank(&$pObj) {
		$stat = $this->getStatistics();
		global $gLibertySystem;

		$rating_vars = $gLibertySystem->invokeServiceFunctionR('content_get_rating_field_function',array($pObj,false,false,true));
		$rating_count_vars = $gLibertySystem->invokeServiceFunctionR('content_get_rating_field_function',array($pObj,false,true,true));

		$orderSSQL = '';
		$selectSql = ", ( ";
		foreach (array_keys($rating_vars) as $serv) {
			$rate_var = $rating_vars[$serv];
			$rate_c_var = $rating_count_vars[$serv];
			if(!empty($orderSSQL)) {
				$orderSSQL .=" + ";
			}
			if (empty($stat['r'])) {
				$stat['r']=array();
			}
			if (empty($stat['r'][$serv])) {
				$stat['r'][$serv]=array('r_c'=>0,'r'=>0);
			}
			if (!empty($rate_c_var)) {
				$factor = $stat['r'][$serv]['r_c'] * $stat['r'][$serv]['r'];
				if ($factor==0) { $factor = 1; }
				$orderSSQL .= "(( COALESCE($rate_var,0) * COALESCE($rate_c_var,0)) / $factor)";
			} else {
				$factor = $stat['r'][$serv]['r'];
				if ($factor==0) { $factor = 1; }
				$orderSSQL .= "(COALESCE($rate_var,0) / $factor )";
			}
		}

		if(!empty($orderSSQL)) {
			$orderSSQL .=" + ";
		}
		$orderSSQL .= "(CAST(( lc.`created` - ". time() . ") AS FLOAT) / ({$stat['age']}) )";

		if (empty($stat['hr'])) {
			$stat['hr']=0;
		}
		if ($stat['hr']>0) {
			$orderSSQL .= " + ((CAST( lch.`hits` AS FLOAT) / CAST( lch.`last_hit` AS FLOAT) / 60) / {$stat['hr']} )";
		} else {
			$orderSSQL .= " + (CAST( lch.`hits` AS FLOAT) / CAST( lch.`last_hit` AS FLOAT) / 60)";
		}

		$selectSql .= " $orderSSQL ) AS order_key ";
		return $selectSql;
	}

	function autoApprove() {
		global $gLibertySystem, $gBitSystem;
		$auto_approve_count= $gBitSystem->getConfig('articles_auto_approve_count',10);
		$auto_approve_min_r= $gBitSystem->getConfig('articles_auto_approve_min_r',10);
		$auto_approve_min_r_c=  $gBitSystem->getConfig('articles_auto_approve_min_r_c',1);
		$auto_approve_ids =array();


		$pListHash = array('max_records'=>$auto_approve_count,'content_type_guid'=>BITARTICLE_CONTENT_TYPE_GUID, 'sort_mode'=>'order_key_desc', 'no_update'=>true);
		$a = new BitArticle();
		$list = $a->getList($pListHash);

		$rating_vars = $gLibertySystem->invokeServiceFunctionR('content_get_rating_field_function',array($this,false,false,false));
		$rating_count_vars = $gLibertySystem->invokeServiceFunctionR('content_get_rating_field_function',array($this,false,true,false));

		foreach ($list['data'] as $article) {
			if ($article['status_id']!=ARTICLE_STATUS_APPROVED) {
				$approve = true;
				foreach (array_keys($rating_vars) as $serv) {
					$rate_var = $rating_vars[$serv];
					$rate_c_var = $rating_count_vars[$serv];
					if ($article[$rate_var]<$auto_approve_min_r){
						$approve = false;
					}
					if (!empty($rate_c_var) && isset($res[$rate_c_var]) && $article[$rate_c_var]<$auto_approve_min_r_c) {
						$approve = false;
					}
				}
				if ($approve) {
					$auto_approve_ids[] =$article['content_id'];
				}
			}
		}
		if ($gBitSystem->isFeatureActive('articles_auto_approve') && !empty($auto_approve_ids)) {
			$query = 'UPDATE `'.BIT_DB_PREFIX.'articles` SET `status_id`=' .ARTICLE_STATUS_APPROVED. ' WHERE `content_id`='.implode(' OR `content_id`=',$auto_approve_ids);
			$result = $a->mDb->query( $query );
		}
	}

}

?>
