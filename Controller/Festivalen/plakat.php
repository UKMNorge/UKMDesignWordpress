<?php

use UKMNorge\DesignWordpress\Environment\Front\OmradeFront;
use UKMNorge\DesignWordpress\Environment\Wordpress;

Wordpress::setView('Festivalen/Front/Plakat');

Wordpress::addViewData(
    [
        'arrangement' => OmradeFront::getArrangement(),
        'festival_leave_plakat' => get_option('festival_leave_plakat')
    ]
);