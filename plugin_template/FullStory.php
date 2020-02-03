<?php

use UKMNorge\DesignBundle\Utils\Sitemap;
use UKMNorge\DesignBundle\Utils\SEO;

require_once( rtrim( get_template_directory(), '/') .'/header.php');

require_once( FULLSTORY_PATH .'template/_extend.php' );


if( is_array( $FullStoryData ) ) {
	$RENDERDATA = array_merge( $WP_TWIG_DATA, $FullStoryData );
}

echo WP_TWIG::render( 'FullStory/index', $RENDERDATA );

wp_footer();
?>