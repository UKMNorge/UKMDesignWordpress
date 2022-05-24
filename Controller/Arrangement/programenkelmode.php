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

$program = Hendelser::sorterPerDag( $hendelser );
Wordpress::setView('Arrangement/Program/EnkelVisning');
Wordpress::getPage()
    ->setTitle( 'Program for '.( $arrangement->getEierType() == 'kommune' ? 'UKM ' : ''). $arrangement->getNavn() )
    ->setDescription( 'Vi starter '. $arrangement->getStart()->format('j. M \k\l. H:i') );

Wordpress::addViewData(
    [
        'visInterne' =>$visInterne,
        'program' => $program
    ]
);