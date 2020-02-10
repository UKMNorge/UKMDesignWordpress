<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\DesignWordpress\Environment\Wordpress;

$arrangement = new Arrangement( get_option('pl_id' ) );

Wordpress::setView('Arrangement/Kontaktpersoner');
Wordpress::addViewData('arrangement', $arrangement);

Wordpress::getPage()
    ->setTitle('Kontaktpersoner for '. $arrangement->getNavn())
    ->setDescription( 'Vi gleder oss til du tar kontakt!' );