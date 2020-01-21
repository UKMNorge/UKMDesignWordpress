<?php

use UKMNorge\Nettverk\Omrade;
use UKMNorge\Wordpress\Blog;

require_once('class/geoController.class.php');

class fylkeController extends geoController {
    public static function setView( $state ) {
        switch( $state ) {
            case 'arkiv':
                static::$view = '';
                break;
            case 'lokal':
                static::$view = 'Fylke/FrontLokal';
                break;
        }
    }
}

// Init
fylkeController::setOmrade( Omrade::getByFylke( get_option('fylke') ) );
fylkeController::isActiveArkiv( $WP_TWIG_DATA );

// Manuelt set state
fylkeController::setState('lokal');

$view_template = fylkeController::getView();
$WP_TWIG_DATA['omrade'] = geoController::getOmrade();
$WP_TWIG_DATA['sesong'] = geoController::getSesong();
$WP_TWIG_DATA['pamelding'] = geoController::getPamelding();

global $blog_id;
if( Blog::harSide($blog_id,'info')) {
    $WP_TWIG_DATA['har_infoside'] = true;
    $WP_TWIG_DATA['infoside'] = Blog::hentSideByPath($blog_id,'info')->post_content;
} else {
    $WP_TWIG_DATA['har_infoside'] = false;
}