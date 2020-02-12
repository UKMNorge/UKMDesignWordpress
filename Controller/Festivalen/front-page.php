<?php

use UKMNorge\DesignWordpress\Environment\Wordpress;

if( date('m') < 4 ) {
    Wordpress::requireController('Festivalen','plakat');
} else {
    Wordpress::requireController('Festivalen','underveis');
}

/*
case 'festival/plakat':
    $view_template = 'Festival/plakat';
    #require_once('UKMNorge/Wordpress/Controller/festival/juni.controller.php');
    break;
case 'festival/juni':
    $view_template = 'Festival/juni';
    require_once('UKMNorge/Wordpress/Controller/festival/juni.controller.php');
    break;
case 'festival/underveis':
    $view_template = 'Festival/underveis';
    require_once('UKMNorge/Wordpress/Controller/festival/underveis.controller.php');
    break;
*/