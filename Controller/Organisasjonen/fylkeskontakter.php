<?php

use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Geografi\Fylker;


Wordpress::setView('Organisasjonen/Fylkeskontakter');

Wordpress::addViewData(
    ['fylker' => Fylker::getAll()]
);