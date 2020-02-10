<?php

use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Geografi\Fylker;

Wordpress::setView('Norge/Velg_fylke');
Wordpress::addViewData('fylker', Fylker::getAll());