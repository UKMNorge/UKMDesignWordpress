<?php

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Utils\RandomTextService;
use UKMNorge\DesignWordpress\Environment\Wordpress;

UKMDesign::getHeader()->hideSectionTitle();
UKMDesign::getHeader()->getLogo()->hide();

Wordpress::setView(
    'Festivalen/Nyhetsbrev/2019_'. Wordpress::getPage()->getMeta('dato',true)
);

Wordpress::addViewData(
    'currentText',
    RandomTextService::getText()
);