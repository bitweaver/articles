<?php
	global $gBitSystem, $gBitUser, $smarty;
	$gBitSystem->registerPackage( 'articles', dirname( __FILE__ ).'/' );

	if( $gBitSystem->isPackageActive( 'articles' ) ) {
		$gBitSystem->registerAppMenu( 'articles', 'Articles', ARTICLES_PKG_URL.'index.php', 'bitpackage:articles/menu_articles.tpl', 'articles' );

		$gBitSystem->registerNotifyEvent( array( "article_submitted" => tra("A user submits an article") ) );

/*	
		// **********  ARTICLES  ************
		$cms_spellcheck = 'n';
		$smarty->assign('cms_spellcheck', $cms_spellcheck);
		$smarty->assign('art_list_title', 'y');
		$smarty->assign('art_list_topic', 'y');
		$smarty->assign('art_list_date', 'y');
		$smarty->assign('art_list_author', 'y');
		$smarty->assign('art_list_reads', 'y');
		$smarty->assign('art_list_size', 'y');
		$smarty->assign('art_list_img', 'y');
		$smarty->assign('art_view_title', 'y');
		$smarty->assign('art_view_topic', 'y');
		$smarty->assign('art_view_date', 'y');
		$smarty->assign('art_view_author', 'y');
		$smarty->assign('art_view_reads', 'y');
		$smarty->assign('art_view_size', 'y');
		$smarty->assign('art_view_img', 'y');
		$smarty->assign('article_comments_default_ordering', 'points_desc');
		$smarty->assign('article_comments_per_page', 10);

		if ( $gBitUser->isAdmin() || $gBitUser->hasPermission('bit_p_admin_cms') ) {
			// Now get all the permissions that are set for this type of permission
			$perms = $gBitUser->getPermissions('', 'articles');

			foreach ($perms["data"] as $perm) {
				$perm_name = $perm["perm_name"];

				if ($gBitUser->object_has_permission($user, $_REQUEST["blog_id"], 'blog', $perm_name)) {
					$$perm_name = 'y';

					$smarty->assign("$perm_name", 'y');
				} else {
					$$perm_name = 'n';

					$smarty->assign("$perm_name", 'n');
				}
			}
		}

		$maxArticles = $gBitSystem->getPreference("maxArticles", 10);
		$smarty->assign('maxArticles', $maxArticles);
*/
	}

?>
