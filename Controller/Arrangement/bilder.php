<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\DesignWordpress\Environment\Wordpress;

Wordpress::setView('Arrangement/Bilder');

if (isset($_GET['hendelse'])) {
    Wordpress::addViewData('vis', $_GET['hendelse']);
}

Wordpress::addViewData(
    [
        'visAlle' => !isset($_GET['hendelse']),
        'arrangement' => new Arrangement(intval(get_option('pl_id')))
    ]
);
