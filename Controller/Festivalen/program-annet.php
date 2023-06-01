<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Hendelser;
use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Post;
use UKMNorge\DesignWordpress\Environment\Posts;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Filmer\UKMTV\Direkte\Sendinger;

$arrangement = new Arrangement( get_option('pl_id') );

Wordpress::addViewData('arrangement',$arrangement);

## Skal hente ut programmet for en forestilling
$id = Wordpress::getLastParameter();


$visInterne = defined('DELTAKERPROGRAM') && DELTAKERPROGRAM;
$hendelser = $visInterne ? $arrangement->getProgram()->getAllInkludertInterne() : $arrangement->getProgram()->getAll();

$gyldigeHendelser = [];

// OBS!!!!!! 
// Her sjekker vi navn av hendelsen. Kan være farlig fordi navnet kan inneholde "Forestilling" hvor som helst og da blir det ikke nødvendigvis Forestilling 1, Forestilling 2 osv.
foreach($hendelser as $hendelse) {
    if(strpos($hendelse->navn, 'Forestilling') !== false 
    || strpos($hendelse->navn, 'utstilling') !== false
    || strpos($hendelse->navn, 'visning') !== false
    ) {
        $gyldigeHendelser[] = $hendelse;    
    }
}

# Skal vise program for arrangementet
$program = Hendelser::sorterPerDag( $gyldigeHendelser );


Wordpress::setView('Arrangement/Program/Oversikt');

Wordpress::addViewData(
    [
        'visInterne' =>$visInterne,
        'program' => $program,
        'isUKMFestivalen' => true
    ]
);
