<?php

use UKMNorge\DesignWordpress\Environment\Wordpress;

// Hvis vi skal ha plakat, fjern false
if( false && date('m') < 3 ) {
    Wordpress::requireController('Festivalen','plakat');
} else {
    // Hvis det er superadmin vis nettsiden
    if(is_super_admin()) {
        Wordpress::requireController('Festivalen','deltaker-publikum');
    }
    // Ellers info om nettsiden
    else {
        Wordpress::requireController('Festivalen','ferdig');
    }
    
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
