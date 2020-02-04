<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Wordpress;

// Hvis det er GDPR-siden er det bare å redirecte til forsiden
if( get_option('site_type') == 'gdpr' ) {
	header("Location: ". get_bloginfo('url'));
}

require_once('header.php');

Wordpress::setView('404/404');

// SET OPENGRAPH AND SEARCH OPTIMIZATION INFOS
Wordpress::getPage()
    ->setTitle(UKMDesign::getConfig('firenullfire')['title'] )
    ->setDescription(UKMDesign::getConfig('firenullfire')['text'] );

require('render.php');