<?php

use UKMNorge\Nettverk\Omrade;
use UKMNorge\Samtykke\Kommunikasjon;
use UKMNorge\Wordpress\Blog;

require_once('class/geoController.class.php');

class kommuneController extends geoController {
    public static function setView( $state ) {
        switch( $state ) {
            case 'arkiv':
                static::$view = '';
                break;
            case 'uten_arrangement':
                static::$view = 'Kommune/FrontUtenArrangement';
                break;
            case 'med_arrangementer':
                // Med flere arrangement. Med ett arrangement
                // håndteres av arrangement.controller.php
                static::$view = 'Kommune/FrontMedArrangement';
                break;
        }
    }
}

// INIT

$kommune_id = get_option('pl_owner_id');
if( !$kommune_id ) {
    $kommune_id = explode(',', get_option('kommuner'));
    if( is_array( $kommune_id ) ) {
        $kommune_id = $kommune_id[0];
    }
}
if(!$kommune_id ) {
    $kommune_id = get_option('kommune');
}

if( !$kommune_id ) {
    // Nå er det vel på tide å markere denne som slettet, og refreshe
    // deleted.php bør være i stand til å finne ut hvor brukeren skal,
    // eller kaste en seriøs exception
    $path = str_replace(
        [
            'https://',
            'http://',
            UKM_HOSTNAME
        ],
        '',
        $WP_TWIG_DATA['blog_url']
    );
    Blog::deaktiver( 
        Blog::getIdByPath( $path )
    );
    header("Location: ". $WP_TWIG_DATA['blog_url'] );
    exit();
}
$kommune_id = (Int) $kommune_id;

$omrade = Omrade::getByKommune( $kommune_id );

if( $omrade->getArrangementer( geoController::getSesong() )->getAntall() == 1 ) {
    $arrangement = $omrade->getArrangementer( geoController::getSesong() )->getFirst();
    if( $arrangement->erFellesmonstring() ) {
        header("Location: ". $arrangement->getLink());
        echo '<script type="text/javascript">window.location.href = "'. $arrangement->getLink() .'";</script>';
        exit();
    }
}
// her har kommunen alltid 1 id, da flere ID'er kun er mulig i arrangement,
// som nå blir håndtert av arrangement.controller.php
kommuneController::setOmrade( $omrade );
kommuneController::isActiveArkiv( $WP_TWIG_DATA );

// Manuelt set init-state
kommuneController::setState('uten_arrangement');
// Har kommunen flere arrangementer, er det egen state
if( kommuneController::getOmrade()->getArrangementer( kommuneController::getSesong() )->har() ) {
    kommuneController::setState('med_arrangementer');
}
#kommuneController::setState('pamelding');

$view_template = kommuneController::getView();
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