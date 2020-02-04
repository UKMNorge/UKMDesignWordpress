<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Wordpress;

require_once('header.php');

Wordpress::setView('404/404');
Wordpress::setPage(null);
#$WP_TWIG_DATA['page'] = new page();

// Hvis det er GDPR-siden er det bare å redirecte til forsiden
if( get_option('site_type') == 'gdpr' ) {
	header("Location: ". get_bloginfo('url'));
}

// SET OPENGRAPH AND SEARCH OPTIMIZATION INFOS
Wordpress::getPage()
    ->setTitle(UKMDesign::getConfig('firenullfire')['title'] )
    ->setDescription(UKMDesign::getConfig('firenullfire')['text'] );



/*
// CHECK TO FIND CUSTOM PAGE CONTROLLER AND VIEW ISSET
if( isset( $WP_TWIG_DATA['page']->getPage()->meta->UKMviseng ) ) {
	$page_template = $WP_TWIG_DATA['page']->getPage()->meta->UKMviseng;
} else {
	$page_template = false;
}
*/

require('render.php');