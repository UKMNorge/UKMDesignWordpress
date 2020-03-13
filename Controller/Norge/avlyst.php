<?php

use UKMNorge\Arrangement\Load;
use UKMNorge\DesignWordpress\Environment\Wordpress;
use UKMNorge\Geografi\Fylker;

Wordpress::setView('Norge/Avlyst');
Wordpress::addViewData('fylker', Fylker::getAll());
Wordpress::addViewData('arrangementLoader', new Load());