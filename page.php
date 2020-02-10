<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Wordpress;

require_once('header.php');

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