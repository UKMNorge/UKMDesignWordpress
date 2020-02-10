<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Wordpress;

require_once('header.php');

/**
 * SEO INFOS
 */
# Author
UKMDesign::getHeader()::getSEO()->setAuthor( Wordpress::getPage()->author->display_name );

# Lead or default-description
if( !empty( strip_tags( Wordpress::getPage()->lead ) ) ) {
    Wordpress::getPage()->setDescription(
        strip_tags(Wordpress::getPage()->lead)
    );
} else {
    Wordpress::getPage()->setDescription(
        UKMDesign::getConfig('hvaerukm.slogan_alt')
    );
}if( Wordpress::getPage()->hasMenu() ) {
    Wordpress::addViewData(
        'meny',
        wp_get_nav_menu_items(Wordpress::getPage()->getMeta('UKM_nav_menu'))
    );    
}

# Set default-view
Wordpress::setView('Page/Fullpage');

# If this page uses a template, run its controller
# The controller will then set the correct view
Wordpress::requireTemplateController();


/**
 * EXPORT MODE
 * Export basic page data as json
 */
if( isset( $_GET['exportContent'] ) ) {
    Wordpress::addViewData('export', Wordpress::getPage());
    echo Wordpress::render('Export/content');
	die();
}

/**
 * DEFAULT RENDER MODE
 */
require_once('render.php');

// TODO 👇🏼
/*
// SELECT CORRECT TEMPLATE, INCLUDE AND RUN CONTROLLER
switch( Wordpress::getPage()->getTemplate() ) {
    case 'statistikk/frister':
    case 'statistikk/monstringer':
        require_once('UKMNorge/Wordpress/Controller/menu.controller.php');
        $view_menu_template = 'Statistikk/monstringer';
        $view_template = &$view_menu_template;
        require_once('UKMNorge/Wordpress/Controller/statistikk/monstringer.controller.php');
        break;
*/