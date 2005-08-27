<?php

class ArtLib extends BitBase {
    function ArtLib() {
        BitBase::BitBase();
    }
    // 29-Jun-2003, by zaufi
    // The 2 functions below contain duplicate code
    // to remove <PRE> tags... (moreover I copy this code
    // from tikilib.php, and paste to artlib.php, bloglib.php
    // and wikilib.php)
    // TODO: it should be separate function to avoid
    // maintain 3 pieces... (but I don't know PHP and TIKI
    // architecture very well yet to make this :()
    // Special parsing for multipage articles
    function get_number_of_pages( $data ) {
        // Temporary remove <PRE></PRE> secions to protect
        // from broke <PRE> tags and leave well known <PRE>
        // behaviour (i.e. type all text inside AS IS w/o
        // any interpretation)
        $preparsed = array();

        preg_match_all( "/(<[Pp][Rr][Ee]>)((.|\n)*?)(<\/[Pp][Rr][Ee]>)/", $data, $preparse );
        $idx = 0;

        foreach ( array_unique( $preparse[2] )as $pp ) {
            $key = md5( $this->genPass() );

            $aux["key"] = $key;
            $aux["data"] = $pp;
            $preparsed[] = $aux;
            $data = str_replace( $preparse[1][$idx] . $pp . $preparse[4][$idx], $key, $data );
            $idx = $idx + 1;
        }

        $parts = explode( "...page...", $data );
        return count( $parts );
    }

    function get_page( $data, $i ) {
        // Temporary remove <PRE></PRE> secions to protect
        // from broke <PRE> tags and leave well known <PRE>
        // behaviour (i.e. type all text inside AS IS w/o
        // any interpretation)
        $preparsed = array();

        preg_match_all( "/(<[Pp][Rr][Ee]>)((.|\n)*?)(<\/[Pp][Rr][Ee]>)/", $data, $preparse );
        $idx = 0;

        foreach ( array_unique( $preparse[2] )as $pp ) {
            $key = md5( $this->genPass() );

            $aux["key"] = $key;
            $aux["data"] = $pp;
            $preparsed[] = $aux;
            $data = str_replace( $preparse[1][$idx] . $pp . $preparse[4][$idx], $key, $data );
            $idx = $idx + 1;
        }
        // Get slides
        $parts = explode( "...page...", $data );

        if ( substr( $parts[$i - 1], 1, 5 ) == "<br/>" )
            $ret = substr( $parts[$i - 1], 6 );
        else
            $ret = $parts[$i - 1];
        // Replace back <PRE> sections
        foreach ( $preparsed as $pp )
        $ret = str_replace( $pp["key"], "<pre>" . $pp["data"] . "</pre>", $ret );

        return $ret;
    }

    function approve_submission( $sub_id ) {
        $data = $this->get_submission( $sub_id );

        if ( !$data )
            return false;

        if ( !$data["image_x"] )
            $data["image_x"] = 0;

        if ( !$data["image_y"] )
            $data["image_y"] = 0;

        $this->replace_article( $data["title"], $data["author_name"], $data["topic_id"], $data["use_image"], $data["image_name"],
            $data["image_size"], $data["image_type"], $data["image_data"], $data["heading"], $data["body"], $data["publish_date"], $data["expire_date"],
            $data["author"], 0, $data["image_x"], $data["image_y"], $data["type"], $data["rating"] );
        $this->remove_submission( $sub_id );
    }

    function add_article_hit( $articleId ) {
        global $count_admin_pvs, $bit_p_admin;

        global $user;

        if ( $count_admin_pvs == 'y' || $bit_p_admin != 'y' ) {
            $query = "update `" . BIT_DB_PREFIX . "tiki_articles` set `reads`=`reads`+1 where `article_id`=?";

            $result = $this->mDb->query( $query, array( $article_id ) );
        }

        return true;
    }

    function remove_article( $article_id ) {
        if ( $article_id ) {
            $query = "delete from `" . BIT_DB_PREFIX . "tiki_articles` where `article_id`=?";

            $result = $this->mDb->query( $query, array( $article_id ) );
            $this->remove_object( 'article', $article_id );
            return true;
        }
    }

    function remove_submission( $sub_id ) {
        if ( $sub_id ) {
            $query = "delete from `" . BIT_DB_PREFIX . "tiki_submissions` where `sub_id`=?";

            $result = $this->mDb->query( $query, array( ( int ) $sub_id ) );

            return true;
        }
    }

    function replace_submission( $title, $author_name, $topic_id, $use_image, $imgname, $imgsize, $imgtype, $imgdata, $heading, $body, $publish_date, $expire_date, $user, $sub_id, $image_x, $image_y, $type, $rating = 0, $isfloat = 'n' ) {
        global $smarty;

        global $dbTiki;
        global $sender_email;
        if ( $expire_date < $publish_date ) {
            $expire_date = $publish_date;
        }
        if( empty( $imgdata ) ) $imgdata = '';
        include_once( KERNEL_PKG_PATH . 'notification_lib.php' );
        $hash = md5( $title . $heading . $body );
        $now = date( "U" );
        $query = "select `name` from `" . BIT_DB_PREFIX . "tiki_topics` where `topic_id` = ?";
        $topic_name = $this->mDb->getOne( $query, array( ( int ) $topic_id ) );
        $size = strlen( $body );

        if ( $sub_id ) {
            // Update the article
            $query = "update `" . BIT_DB_PREFIX . "tiki_submissions` set
                `title` = ?,
                `author_name` = ?,
                `topic_id` = ?,
                `topic_name` = ?,
                `size` = ?,
                `use_image` = ?,
                `isfloat` = ?,
                `image_name` = ?,
                `image_type` = ?,
                `image_size` = ?,
                `image_data` = ?,
                `image_x` = ?,
                `image_y` = ?,
                `heading` = ?,
                `body` = ?,
                `publish_date` = ?,
                `created` = ?,
                `author` = ? ,
                `type` = ?,
                `rating` = ?
                where `sub_id` = ?";

            $result = $this->mDb->query( $query, array( $title, $author_name, ( int ) $topic_id, $topic_name, ( int ) $size, $use_image, $isfloat, $imgname, $imgtype, ( int ) $imgsize, $this->mDb->db_byte_encode( $imgdata ), ( int ) $image_x, ( int ) $image_y, $heading, $body, ( int ) $publish_date, ( int ) $now, $user, $type, ( float ) $rating, ( int ) $sub_id ) );
        } else {
            // Insert the article
            $query = "insert into `" . BIT_DB_PREFIX . "tiki_submissions`(`title`,`author_name`,`topic_id`,`use_image`,`image_name`,`image_size`,`image_type`,`image_data`,`publish_date`,`created`,`heading`,`body`,`hash`,`author`,`reads`,`votes`,`points`,`size`,`topic_name`,`image_x`,`image_y`,`type`,`rating`,`isfloat`)
                         values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            $result = $this->mDb->query( $query, array( $title, $author_name, ( int ) $topic_id, $use_image, $imgname, ( int ) $imgsize, $imgtype, $this->mDb->db_byte_encode( $imgdata ), ( int ) $publish_date, ( int ) $now, $heading, $body, $hash, $user, 0, 0, 0, ( int ) $size, $topic_name, ( int ) $image_x, ( int ) $image_y, $type, ( float ) $rating, $isfloat ) );
        }

        $query = "select max(`sub_id`) from `" . BIT_DB_PREFIX . "tiki_submissions` where `created` = ? and `title`=? and `hash`=?";
        $id = $this->mDb->getOne( $query, array( ( int ) $now, $title, $hash ) );
        $emails = $notificationlib->get_mail_events( 'article_submitted', '*' );
        $foo = parse_url( $_SERVER["REQUEST_URI"] );
        $machine = httpPrefix() . $foo["path"];

        foreach ( $emails as $email ) {
            $smarty->assign( 'mail_site', $_SERVER["SERVER_NAME"] );

            $smarty->assign( 'mail_user', $user );
            $smarty->assign( 'mail_title', $title );
            $smarty->assign( 'mail_heading', $heading );
            $smarty->assign( 'mail_body', $body );
            $smarty->assign( 'mail_date', date( "U" ) );
            $smarty->assign( 'mail_machine', $machine );
            $smarty->assign( 'mail_sub_id', $id );
            $mail_data = $smarty->fetch( 'tikipackge:articles/submission_notification.tpl' );
            @mail( $email, tra( 'New article submitted at ' ) . $_SERVER["SERVER_NAME"], $mail_data,
                "From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n" );
        }

        return $id;
    }

    function add_topic( $name, $imagename, $imagetype, $imagesize, $imagedata ) {
        $now = date( "U" );

        $query = "insert into `" . BIT_DB_PREFIX . "tiki_topics`(`name`,`image_name`,`image_type`,`image_size`,`image_data`,`active`,`created`)
                     values(?,?,?,?,?,?,?)";
        $result = $this->mDb->query( $query, array( $name, $imagename, $imagetype, ( int ) $imagesize, $this->mDb->db_byte_encode( $imagedata ), 'y', ( int ) $now ) );

        $query = "select max(`topic_id`) from `" . BIT_DB_PREFIX . "tiki_topics` where `created`=? and `name`=?";
        $topic_id = $this->mDb->getOne( $query, array( ( int ) $now, $name ) );
        return $topic_id;
    }

    function remove_topic( $topic_id, $all = 0 ) {
        $query = "delete from `" . BIT_DB_PREFIX . "tiki_topics` where `topic_id`=?";

        $result = $this->mDb->query( $query, array( $topic_id ) );

        if ( $all == 1 ) {
            $query = "delete from `" . BIT_DB_PREFIX . "tiki_articles` where `topic_id`=?";
            $result = $this->mDb->query( $query, array( $topic_id ) );
        } else {
            $query = "update `" . BIT_DB_PREFIX . "tiki_articles` set `topic_id`=?, `topic_name`=? where `topic_id`=?";
            $result = $this->mDb->query( $query, array( null, null, $topic_id ) );
        }

        return true;
    }

    function replace_topic_name( $topic_id, $name ) {
        $query = "update `" . BIT_DB_PREFIX . "tiki_topics` set `name` = ? where
			`topic_id` = ?";
        $result = $this->mDb->query( $query, array( $name, $topic_id ) );

        return true;
    }

    function replace_topic_image( $topic_id, $imagename, $imagetype,
        $imagesize, $imagedata ) {
        $topic_id = ( int )$topic_id;
        $query = "update `" . BIT_DB_PREFIX . "tiki_topics` set `image_name` = ?,
			`image_type` = ?, `image_size` = ?,  `image_data` = ?
				where `topic_id` = ?";
        $result = $this->mDb->query( $query, array( $imagename, $imagetype,
                $imagesize, $this->mDb->db_byte_encode( $imagedata ), $topic_id ) );

        return true;
    }

    function activate_topic( $topic_id ) {
        $query = "update `" . BIT_DB_PREFIX . "tiki_topics` set `active`=? where `topic_id`=?";

        $result = $this->mDb->query( $query, array( 'y', $topic_id ) );
    }

    function deactivate_topic( $topic_id ) {
        $query = "update `" . BIT_DB_PREFIX . "tiki_topics` set `active`=? where `topic_id`=?";

        $result = $this->mDb->query( $query, array( 'n', $topic_id ) );
    }

    function get_topic( $topic_id ) {
        $query = "select `topic_id`,`name`,`image_name`,`image_size`,`image_type` from `" . BIT_DB_PREFIX . "tiki_topics` where `topic_id`=?";

        $result = $this->mDb->query( $query, array( $topic_id ) );

        $res = $result->fetchRow();
        return $res;
    }

	function get_topic_id($topic) {
    	$topic_id = '';
	    $query = "select `topic_id`  from `tiki_topics` where `name` = ?";
    	$topic_id = $this->mDb->getOne($query, array($topic) );
	    return $topic_id;
	}

    function list_active_topics() {
        $query = "select * from `" . BIT_DB_PREFIX . "tiki_topics` where `active`=?";

        $result = $this->mDb->query( $query, array( 'y' ) );

        $ret = array();

        while ( $res = $result->fetchRow() ) {
            $ret[] = $res;
        }

        return $ret;
    }
    // Article Type functions
    function add_type( $type ) {
        /*
		if ($use_ratings == 'on') {$use_ratings = 'y';} else {$use_ratings = 'n';}
		if ($show_pre_publ == 'on') {$show_pre_publ = 'y';} else {$show_pre_publ = 'n';}
		if ($show_post_expire == 'on') {$show_post_expire = 'y';} else {$show_post_expire = 'n';}
		if ($heading_only == 'on') {$heading_only = 'y';} else {$heading_only = 'n';}
		if ($allow_comments == 'on') {$allow_comments = 'y';} else {$allow_comments = 'n';}
		if ($comment_can_rate_article == 'on') {$comment_can_rate_article = 'y';} else {$comment_can_rate_article = 'n';}
		if ($show_image == 'on') {$show_image = 'y';} else {$show_image = 'n';}
		if ($show_avatar == 'on') {$show_avatar = 'y';} else {$show_avatar = 'n';}
		if ($show_author == 'on') {$show_author = 'y';} else {$show_author = 'n';}
		if ($show_pubdate == 'on') {$show_pubdate = 'y';} else {$show_pubdate = 'n';}
		if ($show_expdate == 'on') {$show_expdate = 'y';} else {$show_expdate = 'n';}
		if ($show_reads == 'on') {$show_reads = 'y';} else {$show_reads = 'n';}
		if ($show_size == 'on') {$show_size = 'y';} else {$show_size = 'n';}
		if ($creator_edit == 'on') {$creator_edit = 'y';} else {$creator_edit = 'n';}

		$query = "select count(*) from `".BIT_DB_PREFIX."tiki_article_types` where `type`=?";
		$rowcnt = $this->mDb->getOne($query,array($type));

		// if the type already exists, delete it first
		if ($rowcnt > 0) {
			$query = "delete from `".BIT_DB_PREFIX."tiki_article_types` where `type`=?";
			$result = $this->mDb->query($query,array($type));
		}
*/
        $result = $this->mDb->query( "insert into `" . BIT_DB_PREFIX . "tiki_article_types`(`type`) values(?)", array( $type ) );

        return true;
    }

    function edit_type( $type, $use_ratings, $show_pre_publ, $show_post_expire, $heading_only, $allow_comments, $comment_can_rate_article, $show_image, $show_avatar, $show_author, $show_pubdate, $show_expdate, $show_reads, $show_size, $creator_edit ) {
        if ( $use_ratings == 'on' ) {
            $use_ratings = 'y';
        } else {
            $use_ratings = 'n';
        }
        if ( $show_pre_publ == 'on' ) {
            $show_pre_publ = 'y';
        } else {
            $show_pre_publ = 'n';
        }
        if ( $show_post_expire == 'on' ) {
            $show_post_expire = 'y';
        } else {
            $show_post_expire = 'n';
        }
        if ( $heading_only == 'on' ) {
            $heading_only = 'y';
        } else {
            $heading_only = 'n';
        }
        if ( $allow_comments == 'on' ) {
            $allow_comments = 'y';
        } else {
            $allow_comments = 'n';
        }
        if ( $comment_can_rate_article == 'on' ) {
            $comment_can_rate_article = 'y';
        } else {
            $comment_can_rate_article = 'n';
        }
        if ( $show_image == 'on' ) {
            $show_image = 'y';
        } else {
            $show_image = 'n';
        }
        if ( $show_avatar == 'on' ) {
            $show_avatar = 'y';
        } else {
            $show_avatar = 'n';
        }
        if ( $show_author == 'on' ) {
            $show_author = 'y';
        } else {
            $show_author = 'n';
        }
        if ( $show_pubdate == 'on' ) {
            $show_pubdate = 'y';
        } else {
            $show_pubdate = 'n';
        }
        if ( $show_expdate == 'on' ) {
            $show_expdate = 'y';
        } else {
            $show_expdate = 'n';
        }
        if ( $show_reads == 'on' ) {
            $show_reads = 'y';
        } else {
            $show_reads = 'n';
        }
        if ( $show_size == 'on' ) {
            $show_size = 'y';
        } else {
            $show_size = 'n';
        }
        if ( $creator_edit == 'on' ) {
            $creator_edit = 'y';
        } else {
            $creator_edit = 'n';
        }
        $query = "update `" . BIT_DB_PREFIX . "tiki_article_types` set
			`use_ratings` = ?,
			`show_pre_publ` = ?,
			`show_post_expire` = ?,
			`heading_only` = ?,
			`allow_comments` = ?,
			`comment_can_rate_article` = ?,
			`show_image` = ?,
			`show_avatar` = ?,
			`show_author` = ?,
			`show_pubdate` = ?,
			`show_expdate` = ?,
			`show_reads` = ?,
			`show_size` = ?,
			`creator_edit` = ?
			where `type` = ?";
        $result = $this->mDb->query( $query, array( $use_ratings, $show_pre_publ, $show_post_expire, $heading_only, $allow_comments, $comment_can_rate_article, $show_image, $show_avatar, $show_author, $show_pubdate, $show_expdate, $show_reads, $show_size, $creator_edit, $type ) );
    }

    function remove_type( $type ) {
        $query = "delete from `" . BIT_DB_PREFIX . "tiki_article_types` where `type`=?";
        $result = $this->mDb->query( $query, array( $type ) );
    }

    function get_type( $type ) {
        $query = "select * from `" . BIT_DB_PREFIX . "tiki_article_types` where `type`=?";

        $result = $this->mDb->query( $query, array( $type ) );

        $res = $result->fetchRow();
        return $res;
    }

	// CMS functions -ARTICLES- & -SUBMISSIONS- ////
	/*shared*/
	function list_articles( $offset = 0, $maxRecords = -1, $sort_mode = 'publish_date_desc', $find = '', $date = '', $type = '', $topic_id = '', $pUser = NULL ) {
		global $gBitUser, $gBitSystem;

		$mid = " WHERE art.`article_type_id` = arttype.`article_type_id` AND tc.`user_id` = u.`user_id` ";
		$bindvars=array();
		if( !empty( $find ) ) {
			$findesc = '%' . strtoupper( $find ) . '%';
			$mid .= " and (upper(`title`) like ? or upper(`heading`) like ? or upper(`body`) like ?) ";
			$bindvars=array( $findesc, $findesc, $findesc );
		}
		if ( $type ) {
			$bindvars[] = $type ;
			$mid .= " and art.`article_type_id`=? ";
		}

		if( $topic_id ) {
			$bindvars[] = (int) $topic_id;
			$mid .= " AND `topic_id`=? ";
		}

		if( isset( $pUser ) ) {
			$mid .= ' AND art.`author`=? ';
			$bindvars[] = $pUser;
		}

		$query = "SELECT art.*,	u.`avatar_attachment_id`, arttype.`use_ratings`, arttype.`show_pre_publ`, arttype.`show_post_expire`, arttype.`heading_only`, arttype.`allow_comments`, arttype.`show_image`, arttype.`show_avatar`, arttype.`show_author`, arttype.`show_pubdate`, arttype.`show_expdate`, arttype.`show_reads`, arttype.`show_size`, arttype.`creator_edit`
				  FROM `".BIT_DB_PREFIX."tiki_articles` art INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON ( art.`content_id`=tc.`content_id` ), `".BIT_DB_PREFIX."tiki_article_types` arttype, `".BIT_DB_PREFIX."users_users` u $mid
				  ORDER BY ".$this->mDb->convert_sortmode( $sort_mode );

		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_articles` art  INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON ( art.`content_id`=tc.`content_id` ), `".BIT_DB_PREFIX."tiki_article_types` arttype, `".BIT_DB_PREFIX."users_users` u $mid";
		$result = $this->mDb->query( $query, $bindvars, $maxRecords, $offset );
		$cant = $this->mDb->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["entrating"] = floor($res["rating"]);

			$add = 1;

			if ($gBitUser->object_has_one_permission($res["topic_id"], 'topic')) {
				if (!$gBitUser->object_has_permission($user, $res["topic_id"], 'topic', 'bit_p_topic_read')) {
					$add = 0;
				}
			}
			if (empty($res["body"])) {
				$res["isEmpty"] = 'y';
			} else {
				$res["isEmpty"] = 'n';
			}
			if( !empty( $res["image_data"] ) ) {
				$res['image_data'] = $this->mDb->db_byte_decode( $res['image_data'] );
				$res["hasImage"] = 'y';
			} else {
				$res["hasImage"] = 'n';
			}
			$res['count_comments'] = 0;

			// Determine if the article would be displayed in the view page
			$res["disp_article"] = 'y';
			$now = $gBitSystem->getUTCTime();
			//if ($date) {
			   if (($res["show_pre_publ"] != 'y') and ($now < $res["publish_date"])) {
				   $res["disp_article"] = 'n';
			   }
			   if (($res["show_post_expire"] != 'y') and ($now > $res["expire_date"])) {
				   $res["disp_article"] = 'n';
			   }
			//}

			if( $add ) {
				$ret[] = $res;
			}
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/*shared*/
	function list_submissions($offset = 0, $maxRecords = -1, $sort_mode = 'publish_date_desc', $find = '', $date = '') {

		if ($find) {
		$findesc = '%' . strtoupper( $find ) . '%';
		$mid .= " and (upper(`title`) like ? or upper(`heading`) like ? or upper(`body`) like ?) ";
		$bindvars = array($findesc,$findesc,$findesc);
		} else {
		$mid = '';
		$bindvars = array();
		}

		if ($date) {
		if ($mid) {
			$mid .= " and `publish_date` <= ? ";
		} else {
			$mid = " where `publish_date` <= ? ";
		}
		$bindvars[] = $date;
		}

		$query = "select * from `".BIT_DB_PREFIX."tiki_submissions` $mid order by ".$this->mDb->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_submissions` $mid";
		$result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->mDb->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
		$res["entrating"] = floor($res["rating"]);

		if (empty($res["body"])) {
			$res["isEmpty"] = 'y';
		} else {
			$res["isEmpty"] = 'n';
		}

		if (strlen($res["image_data"]) > 0) {
			$res['image_data'] = $this->mDb->db_byte_decode( $res['image_data'] );
			$res["hasImage"] = 'y';
		} else {
			$res["hasImage"] = 'n';
		}

		$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_article($article_id) {
		$query = "SELECT art.*, uu.`avatar_attachment_id`, arttype.*
				  FROM `".BIT_DB_PREFIX."tiki_articles` art, `".BIT_DB_PREFIX."tiki_article_types` arttype, `".BIT_DB_PREFIX."users_users` uu
				   where art.`type` = arttype.`type` AND art.`author` = uu.`login` AND art.`article_id`=?";
		//$query = "select * from `".BIT_DB_PREFIX."tiki_articles` where `article_id`=?";
		$result = $this->mDb->query($query,array((int)$article_id));
		if ($result->numRows()) {
		$res = $result->fetchRow();
		$res["image_data"] = $this->mDb->db_byte_decode( $res["image_data"] );
		$res["entrating"] = floor($res["rating"]);
		} else {
		return false;
		}
		return $res;
	}

	function get_submission($sub_id) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_submissions` where `sub_id`=?";
		$result = $this->mDb->query($query,array((int) $sub_id));
		if ($result->numRows()) {
		$res = $result->fetchRow();
		$res["entrating"] = floor($res["rating"]);
		} else {
		return false;
		}
		return $res;
	}

	function replace_article($title, $author_name, $topic_id, $use_image, $imgname, $imgsize, $imgtype, $imgdata, $heading, $body, $publish_date, $expire_date, $user, $article_id, $image_x, $image_y, $type, $rating = 0, $isfloat = 'n') {
		global $gBitSystem;
		
		if ($expire_date < $publish_date) {
		   $expire_date = $publish_date;
		}
		$hash = md5($title . $heading . $body);
		$now = $gBitSystem->getUTCTime();
		if(empty($imgdata)) $imgdata='';
		// Fixed query. -rlpowell
		$query = "select `name`  from `".BIT_DB_PREFIX."tiki_topics` where `topic_id` = ?";
		$topic_name = $this->mDb->getOne($query, array($topic_id) );
		$size = strlen($body);

		// Fixed query. -rlpowell
		if ($article_id) {
			// Update the article
			$query = "update `".BIT_DB_PREFIX."tiki_articles` set `title` = ?, `author_name` = ?, `topic_id` = ?, `topic_name` = ?, `size` = ?, `use_image` = ?, `image_name` = ?, ";
			$query.= " `image_type` = ?, `image_size` = ?, `image_data` = ?, `isfloat` = ?, `image_x` = ?, `image_y` = ?, `heading` = ?, `body` = ?, ";
			$query.= " `publish_date` = ?, `expire_date` = ?, `created` = ?, `author` = ?, `type` = ?, `rating` = ?  where `article_id` = ?";

			$result = $this->mDb->query($query, array( $title, $author_name, (int) $topic_id, $topic_name, (int) $size, $use_image, $imgname, $imgtype, (int) $imgsize, $this->mDb->db_byte_encode( $imgdata ), $isfloat, (int) $image_x, (int) $image_y, $heading, $body, (int) $publish_date, (int) $expire_date, (int) $now, $user, $type, (float) $rating, (int) $article_id ) );
		} else {
		// Fixed query. -rlpowell
		// Insert the article
		$query = "insert into `".BIT_DB_PREFIX."tiki_articles` (`title`, `author_name`, `topic_id`, `use_image`, `image_name`, `image_size`, `image_type`, `image_data`, ";
		$query.= " `publish_date`, `expire_date`, `created`, `heading`, `body`, `hash`, `author`, `reads`, `votes`, `points`, `size`, `topic_name`, `image_x`, `image_y`, `type`, `rating`, `isfloat`) ";
		$query.= " values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$result = $this->mDb->query($query, array( $title, $author_name, (int) $topic_id, $use_image, $imgname, (int) $imgsize, $imgtype, $this->mDb->db_byte_encode( $imgdata ), (int) $publish_date, (int) $expire_date, (int) $now, $heading, $body, $hash, $user, 0, 0, 0, (int) $size, $topic_name, (int) $image_x, (int) $image_y, $type, (float) $rating, $isfloat));

		// Fixed query. -rlpowell
		$query2 = "select max(`article_id`) from `".BIT_DB_PREFIX."tiki_articles` where `created` = ? and `title`=? and `hash`=?";
		$article_id = $this->mDb->getOne($query2, array( (int) $now, $title, $hash ) );
		}

		return $article_id;
	}

	/*shared*/
	function get_topic_image($topic_id) {
		// Fixed query. -rlpowell
		$query = "select `image_name` ,`image_size`,`image_type`, `image_data` from `".BIT_DB_PREFIX."tiki_topics` where `topic_id`=?";
		$result = $this->mDb->query($query, array((int) $topic_id));
		$res = $result->fetchRow();
		$res['image_data'] = $this->mDb->db_byte_decode( $res['image_data'] );
		return $res;
	}

	/*shared*/
	function get_article_image($id) {
		$query = "select `image_name` ,`image_size`,`image_type`, `image_data` from `".BIT_DB_PREFIX."tiki_articles` where `article_id`=?";
		$result = $this->mDb->query($query, array((int) $id));
		$res = $result->fetchRow();
		$res['image_data'] = $this->mDb->db_byte_decode( $res['image_data'] );
		return $res;
	}



}

global $artlib;
$artlib = new ArtLib();
?>
