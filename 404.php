<?php

use UKMNorge\DesignBundle\Utils\Sitemap;
use UKMNorge\DesignBundle\Utils\SEO;


require_once('header.php');

require_once('UKMNorge/Wordpress/Utils/page.class.php');
$WP_TWIG_DATA['page'] = new page();

// Hvis det er GDPR-siden er det bare å redirecte til forsiden
if( get_option('site_type') == 'gdpr' ) {
	header("Location: ". get_bloginfo('url'));
}

// SET OPENGRAPH AND SEARCH OPTIMIZATION INFOS
SEO::setTitle( WP_CONFIG::get('firenullfire')['title'] );
SEO::setDescription( WP_CONFIG::get('firenullfire')['text'] );
SEO::setAuthor( 'UKM Norge' );


// CHECK TO FIND CUSTOM PAGE CONTROLLER AND VIEW ISSET
if( isset( $WP_TWIG_DATA['page']->getPage()->meta->UKMviseng ) ) {
	$page_template = $WP_TWIG_DATA['page']->getPage()->meta->UKMviseng;
} else {
	$page_template = false;
}

// RENDER
echo WP_TWIG::render( '404/404', $WP_TWIG_DATA );

wp_footer();
if(is_user_logged_in() ) {
	echo '<style>body {margin-top: 33px;} @media (max-width:782px) {body {margin-top: 48px;}}</style>';
}

if( WP_ENV == 'dev' ) {
	echo '<script language="javascript">console.debug("'.basename(__FILE__).'");</script>';
}