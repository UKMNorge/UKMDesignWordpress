<?php

use UKMNorge\DesignWordpress\Environment\Front\Front;
use UKMNorge\DesignWordpress\Environment\Wordpress;

require_once('header.php');
#UKMDesign::getHeader()::getSEO()->setCanonical($WP_TWIG_DATA['blog_url']);

# Dette er en forside - bruk støtteklasse
Front::init();

Wordpress::setView('Front/Info');

# Find and use header image
Wordpress::requireController('Front', 'banner');

# If this page uses a template, run its controller
# The controller will then set the correct view
$has_template_controller = Wordpress::requireTemplateController();

if (!$has_template_controller) {
    switch (get_option('site_type')) {
        case 'fylke':
            Wordpress::requireController('Fylke','front-page'); 
        break;
        case 'kommune':
            if( get_option('pl_id') ) {
                Wordpress::requireController('Arrangement','front-page');
            } else {
                Wordpress::requireController('Kommune','front-page');
            }
        break;
        case 'arrangement':
            Wordpress::requireController('Arrangement','front-page');
        break;
        case 'norge':
            Wordpress::requireController('Norge','front-page');
            break;
        default:
            Wordpress::requireController('Front', 'front-page');
            break;
    }
}

require_once('render.php');


die();


switch (get_option('site_type')) {
    case 'land':
        switch ($page_template) {
            case 'festival/plakat':
                $view_template = 'Festival/plakat';
                break;
            case 'festival/juni':
                $view_template = 'Festival/juni';
                require_once('UKMNorge/Wordpress/Controller/festival/juni.controller.php');
                break;
            case 'festival/underveis':
                $view_template = 'Festival/underveis';
                require_once('UKMNorge/Wordpress/Controller/festival/underveis.controller.php');
                break;
            default:
                $view_template = 'Page/fullpage';
                break;
        }
        break;
    case 'ego';
}
