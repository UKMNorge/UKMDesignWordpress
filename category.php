<?php

use UKMNorge\DesignWordpress\Environment\Wordpress;

require_once('header.php');

Wordpress::requireController('Kategori','page');

/**
 * EXPORT MODE
 * Export basic page data as json
 */
if( isset( $_GET['exportContent'] ) ) {
    Wordpress::addViewData('export', Wordpress::getPosts());
    echo Wordpress::render('Export/content');
	die();
}

/**
 * DEFAULT RENDER MODE
 */
require_once('render.php');