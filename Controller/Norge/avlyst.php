<?php

use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Geografi\Fylker;

Wordpress::setView('Norge/avlyst');
Wordpress::addViewData('fylker', Fylker::getAll());