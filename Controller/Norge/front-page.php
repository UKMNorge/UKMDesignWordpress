<?php

use UKMNorge\DesignWordpress\Environment\Front;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Geografi\Fylker;

Wordpress::setView('Norge/Front/Standard');
Wordpress::addViewData(
    [
        'pamelding_apen' => Front::erNasjonalPameldingApen(),
        'fylker' => Fylker::getAll()
    ]
);
