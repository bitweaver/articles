<?php
/**
 * @version $Header$
 */
global $gBitInstaller;

$infoHash = array(
	'package'      => ARTICLES_PKG_NAME,
	'version'      => str_replace( '.php', '', basename( __FILE__ )),
	'description'  => "Fix the names of sequence tables to be standardized.",
	'post_upgrade' => NULL,
);

$gBitInstaller->registerPackageUpgrade( $infoHash, array(
	array( 'DATADICT' => array(
		array( 'RENAMESEQUENCE' => array(
			"article_types_a_t_id_seq" => "article_types_id_seq",
			"articles_topics_id_seq" => "article_topics_id_seq",
		)),
		array( 'DROPSEQUENCE' => array( 'article_topics_t_id_seq' ) ),
	)),
));
