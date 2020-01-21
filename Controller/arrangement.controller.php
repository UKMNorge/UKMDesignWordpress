<?php

use UKMNorge\Wordpress\Blog;

require_once('class/geoController.class.php');

class arrangementController extends geoController {
    public static function setView( $state ) {
        switch( $state ) {
            case 'arkiv':
                static::$view = 'Arkiv';
                break;
            case 'arrangement':
                static::$view = 'Arrangement/FrontArrangement';
                break;
            default:
                static::$view = 'Arrangement/FrontPamelding';
                break;
        }
    }

    public static function tillaterTittellose() {
        foreach( static::getArrangement()->getInnslagTyper() as $tillatt_type ) {
            if( $tillatt_type->harTitler() ) {
                continue;
            }
            return true;
        }
        return false;
    }

    public static function harPameldte() {
        return static::getArrangement()->getInnslag()->harInnslag(false);
    }

    public static function harProgram() {
        return static::getArrangement()->getProgram()->getAntall() > 0;
    }
}

$arrangement = arrangementController::getArrangement();
// Init
arrangementController::setState( 'front' );
arrangementController::isActiveArkiv( $WP_TWIG_DATA );

if( new DateTime('now') > arrangementController::getPamelding()->starter && !$arrangement->erPameldingApen() ) {
    arrangementController::setState('arrangement');
}

$view_template = arrangementController::getView();
$WP_TWIG_DATA['sesong'] = arrangementController::getSesong();
$WP_TWIG_DATA['arrangement'] = arrangementController::getArrangement();
$WP_TWIG_DATA['arrangement_tillater_tittellose'] = arrangementController::tillaterTittellose();
$WP_TWIG_DATA['fylke'] = arrangementController::getArrangement()->getFylke();
$WP_TWIG_DATA['pamelding'] = arrangementController::getPamelding();
$WP_TWIG_DATA['har_pameldte'] = arrangementController::harPameldte();
$WP_TWIG_DATA['har_program'] = arrangementController::harProgram();

global $blog_id;
if( Blog::harSide($blog_id,'info')) {
    $WP_TWIG_DATA['har_infoside'] = true;
    $WP_TWIG_DATA['infoside'] = Blog::hentSideByPath($blog_id,'info')->post_content;
} else {
    $WP_TWIG_DATA['har_infoside'] = false;
}

$WP_TWIG_DATA['HEADER']->hideSection = true;
