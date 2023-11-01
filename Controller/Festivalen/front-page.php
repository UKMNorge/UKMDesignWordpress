<?php

use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Arrangement\Arrangement;


$arrangement = new Arrangement(get_option('pl_id'));

// Arrangement er ferdig
if($arrangement->erFerdig()) {
    Wordpress::requireController('Festivalen','etter-festivalen');
}
else {
    Wordpress::requireController('Festivalen','deltaker-publikum');
}




// Hvis vi skal ha plakat, fjern false
// if(date('m') >= 6  && date('d') > 27) {

// if(true) {
    // Wordpress::requireController('Festivalen','plakat');
    // Wordpress::requireController('Festivalen','etter-festivalen');

// } else {
    // Hvis det er superadmin vis nettsiden
    // if(is_super_admin()) {
        // Wordpress::requireController('Festivalen','deltaker-publikum');
    // }
    // Ellers info om nettsiden
    // else {
    //     Wordpress::requireController('Festivalen','ferdig');
    // }
    
// }

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
